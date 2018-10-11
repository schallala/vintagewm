<?php
session_start();

// Globale Variablen einbinden
include 'functions.php';

// Name des Skripts
$skript_name = "blitzrangliste.php";

// Login pruefen
pruefe_login($skript_name);


/*
 * Modus aus evtl. Parametern setzen
 *
 * Modi: nix/0 = normale Blitztabelle im Liveticker
 *       1     = Eingabe von Wunschergebnissen auf Tippbasis des Users
 *       2     = Auswertung der Wunschergebnisse (anschlie&szlig;end Anzeige der Rangliste)
 */

$Titel = "Blitz-Rangliste";

$Modus = 0;  // keiner, Auswahlliste
if (isset($_POST["MODUS_ANZEIGE"])) {
    $Modus = 0;  // Anzeigemodus
};
if (isset($_REQUEST["MODUS_WUNSCH"])) {
    $Modus = 1;  // Gruppenspiel loeschen
    $Titel = "Eingabe Wunschergebnisse";
};
if (isset($_POST["MODUS_SPEICHERN"])) {
    $Modus = 2;  // Einfuegen der Ergebnisse, Anzeige der berechneten Rangliste
    $Titel = "Wunsch-Rangliste";
};
//$Modus = 0;  // keiner, Auswahlliste


/*
 * einige Infos zum User und angeforderten Spieler ermitteln
 */

// Die Rolle wird noch oefter benoetigt ...
if (!empty($_SESSION["rolle"])) {
    $user_rolle = $_SESSION["rolle"];
} else {
    $user_rolle = "nix";
}

// Der eingeloggte User wird noch oefter benoetigt ...
if (!empty($_SESSION["spieler_key"])) {
    $aktueller_spieler = $_SESSION["spieler_key"];
} else {
    $user_key = "nix";
    $Meldung = "Zugriff verweigert - Du bist nicht registriert!";
    $Modus = 999;
}




/*
 * Aktionen auf DB (SELECT, INSERT, UPDATE) vorbereiten und
 * anschliessend ausfuehren
 */


frame_header($skript_name);

$blitz_ergebnis_tabelle = 'blitz_ergebnis'; // Default-Tabelle
?>
<H3> <?php echo $Titel ?> </H3>
<H4> <?php echo $Meldung ?> </H4>

<!-- in allen Modi ist der Inhalt ein Formular -->
<p><form action = '<?php echo $skript_name; ?>' method = 'POST'>

    <!--  Knoeppe, die immer erscheinen sollen werden oben angezeigt:      -->
    <!--  Neu, Auswahl, Uebersicht                                         -->
    <p>
<?php
if ($Modus == 1) {
    // Zeige Eingabeformular mit Wunschergebnissen
    echo "Hier k&ouml;nnt ihr eure Wunschergebnisse eingeben und daraus ein virtuelle Rangliste generieren lassen. "
    . "Standardm&auml;&szlig;ig voreingestellt sind dabei eure eigenen Tipps. Wenn ihr Ergebnis-Eingabefelder "
    . "nicht ausf&uuml;llt werden die betroffenen Partien nicht ausgewertet. ";
    // Abfrage der relevanten Spiele generieren
    $query = "SELECT gruppenspiel.spiel_key" // 0
            . "      ,mann1.mannschaft_key, mann1.mannschaftsname, tips.tore_ms1" // 1, 2, 3
            . "      ,mann2.mannschaft_key, mann2.mannschaftsname, tips.tore_ms2" // 4, 5, 6
            . "      ,runde.bezeichnung " // 7
            . "      , mann1.flagge, mann2.flagge " // 8, 9
            . "  FROM gruppenspiel JOIN runde JOIN mannschaft mann1 JOIN mannschaft mann2 "
            . "  LEFT JOIN tips ON (gruppenspiel.spiel_key=tips.key_spiel "
            . "                 AND tips.key_spieler=$aktueller_spieler) "
            . " WHERE runde.runde_key=gruppenspiel.spiel_art "
            . "   AND mann1.mannschaft_key=gruppenspiel.key_ms1 "
            . "   AND mann2.mannschaft_key=gruppenspiel.key_ms2 "
            . "   AND gruppenspiel.status=0 "; // nur Spiele der aktuellen Runde anzeigen
//				. "   AND tips.key = " . $aktueller_spieler;  // Sicht auf einen Spieler einschraenken

    $result = mysqli_query($connection, $query) or die("Abfrage Spiele + Tipps fehlgeschlagen! - " . $query);

    $min_spiel_key = 1000000;
    $max_spiel_key = -1;

    echo "<table><tr><th> Begegnung </th> <th> Ergebnis </th></tr>";
    while ($line = mysqli_fetch_array($result)) {

        // Intervall eingrenzen
        if ($line[1] < $min_spiel_key) {
            $min_spiel_key = $line[1];
        }
        if ($line[1] > $max_spiel_key) {
            $max_spiel_key = $line[1];
        }
        echo "<tr><td> $line[2] - $line[5] </td>";
        // Tore Mannschaft 1
        echo "<td align='CENTER'><input type='TEXT' NAME='ergebnis$line[1]A' SIZE='2' ";
        if (isset($line[3])) {
            echo "VALUE='$line[3]'";
        } // vorbelegen, wenn Tipp vorhanden
        echo ">";
        // Tore Mannschaft 2
        echo " - <input type='TEXT' NAME='ergebnis$line[1]B' SIZE='2' ";
        if (isset($line[6])) {
            echo "VALUE='$line[6]'";
        } // vorbelegen, wenn Tipp vorhanden
        // jetzt noch wichtige Infos versteckt uebergeben
        echo "<INPUT TYPE='HIDDEN' NAME='min_spiel' VALUE='$min_spiel_key'>";
        echo "<INPUT TYPE='HIDDEN' NAME='max_spiel' VALUE='$max_spiel_key'>";
    }

    // Button zum Starten der Auswertung der Wunschergebnisse
    echo "</tr><input type = 'SUBMIT' name = 'MODUS_SPEICHERN' value = 'Auswertung'>";
    echo "</table>";
} else {
    // Anzeige der Ergebnisse Blitztabelle bzw. Wunschergebnisse
    /**
     * Tempor&auml;re User-Tabelle erstellen
     */
    $blitz_ergebnis_tabelle = 'blitz_ergebnis'; // Default-Tabelle
    $name_blitz_tabelle = "blitzrangliste";
    if (isset($_SESSION["spieler_key"]))
        $name_blitz_tabelle = $name_blitz_tabelle . $_SESSION["spieler_key"];
    // neu erstellen
    $query = "CREATE TABLE " . $name_blitz_tabelle . " AS  SELECT * FROM spieler";
    $result = mysqli_query($connection, $query) or die("Erstellen BLITZRANGLISTE fehlgeschlagen! - " . $query);

    $punkte_tendenz = PUNKTE_RICHTIGE_TENDENZ;
    $punkte_differenz = PUNKTE_RICHTIGE_DIFFERENZ;
    $punkte_exakte_tore = PUNKTE_RICHTIGE_TORANZAHL;
    $bonus_exakter_tipp = PUNKTE_EXAKTER_TIPP;

    // falls Wunschergenisse ausgewertet werden sollen, dann Tabelle erstellen und Zugriff fuer
    // folgende Schritte umbiegen
    if ($Modus == 2) {
        $blitz_ergebnis_tabelle = $blitz_ergebnis_tabelle . $_SESSION["spieler_key"];
        // Eintragen der uebergebenen Ergebnisse in die temporaere BLitztabelle
        // leere Kopie
        $query = "CREATE TEMPORARY TABLE " . $blitz_ergebnis_tabelle . " AS SELECT * FROM blitz_ergebnis WHERE 1=2";
        $result = mysqli_query($connection, $query) or die("Erstellen Wunsch-Ergebnistabelle fehlgeschlagen! - " . $query);

        // uebertragen der Wunschergebnisse in Blitz-Ergebnis-Tabelle
        for ($i = $_POST["min_spiel"]; $i <= $_POST["max_spiel"]; $i++) {
            // Auswertung der Tipps: leere Felder werden auf 0 gesetzt
            if (!empty($_POST["ergebnis" . $i . "A"])) {
                $tore_mannschaft1 = $_POST["ergebnis" . $i . "A"];
            } else {
                $tore_mannschaft1 = -1;
            }
            if (!empty($_POST["ergebnis" . $i . "B"])) {
                $tore_mannschaft2 = $_POST["ergebnis" . $i . "B"];
            } else {
                $tore_mannschaft2 = -1;
            }

            // Tendenz ermitteln
            if ($tore_mannschaft1 > $tore_mannschaft2) {
                $tendenz = 1;
            };
            if ($tore_mannschaft1 < $tore_mannschaft2) {
                $tendenz = 2;
            };
            if ($tore_mannschaft1 == $tore_mannschaft2) {
                $tendenz = 0;
            };

            // nur eingegebene Ergebnisse beruecksichtigen
            if ($tore_mannschaft1 != -1 OR $tore_mannschaft2 != -1) {
                $query = "INSERT INTO $blitz_ergebnis_tabelle VALUES ($i, $tore_mannschaft1, $tore_mannschaft2, $tendenz, 1)";
                $result = mysqli_query($connection, $query) or die("Eintragen Wunsch-Ergebnisse fehlgeschlagen! - " . $query);
            }
        }
    }


    // Auswertung der Spielerpunkte auf Basis der Blitzergebnisse / Wunschergebnisse
    $query_spieler = "SELECT spieler_key, vorname, spielername, punkte "
            . "  FROM " . $name_blitz_tabelle
            . " ORDER BY punkte desc";
    $result_spieler = mysqli_query($connection, $query_spieler) or die("Abfrage fehlgeschlagen!");

    while ($line_spieler = mysqli_fetch_array($result_spieler)) {
        $punkte_spieler = 0;
        $anz_exakt = 0;  // Zaehler fuer genaue Treffer
        $anz_tendenz = 0; // Zaehler fuer Tendenzen

        $query_tipps = " SELECT tips.key_spiel, gruppenspiel.key_ms1, man1.mannschaftsname " // 0, 1, 2
                . "			 , gruppenspiel.key_ms2, man2.mannschaftsname, tips.tore_ms1, tips.tore_ms2 " // 3, 4, 5, 6
                . "			 , tips.tendenz, blitz_ergebnis.tore_ms1, blitz_ergebnis.tore_ms2, blitz_ergebnis.tendenz " //7, 8, 9, 10
                . "		FROM tips, gruppenspiel, mannschaft AS man1, mannschaft AS man2, $blitz_ergebnis_tabelle AS blitz_ergebnis "
                . "	 WHERE tips.key_spiel = gruppenspiel.spiel_key  "
                . "		 AND gruppenspiel.key_ms1 = man1.mannschaft_key  "
                . "		 AND gruppenspiel.key_ms2 = man2.mannschaft_key  "
                . "		 AND gruppenspiel.spiel_key = blitz_ergebnis.key_spiel "
                . "		 AND blitz_ergebnis.status = 1 " // das Spiel laeuft aktuell
                . "		 AND tips.key_spieler =$line_spieler[0]	 ";

        $result_tipps = mysqli_query($connection, $query_tipps) or die("Abfrage Tipps fehlgeschlagen! - " . $query_tipps);

        // Punkteverteilung berechnen
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
        }

        // Update der Gesamtpunktzahl des Spielers
        $query_update = "UPDATE " . $name_blitz_tabelle . " SET punkte  = punkte + " . $punkte_spieler
                . "        ,exakte_tips = exakte_tips + " . $anz_exakt
                . "        ,richtige_Tendenz = richtige_Tendenz + " . $anz_tendenz
                . "  WHERE spieler_key = " . $line_spieler[0];
        $result_update = mysqli_query($connection, $query_update) or die("Update fehlgeschlagen! - " . $query_update);
    }
    $Modus == 0;
}

/*
 *
 * Fall 0: Ergebnis-Darstellung
 *
 */
if ($Modus == 0) {
    // anzeigen, welche Zwischenstaende berechnet werden
    // Anzeige der letzten naechsten Spiele
    $query = "SELECT mann1.mannschaft_key, mann1.mannschaftsname, blitz_ergebnis.tore_ms1" // 0, 1, 2
            . "      ,mann2.mannschaft_key, mann2.mannschaftsname, blitz_ergebnis.tore_ms2" // 3, 4, 5
            . "  FROM gruppenspiel, mannschaft mann1, mannschaft mann2, $blitz_ergebnis_tabelle AS blitz_ergebnis "
            . " WHERE mann1.mannschaft_key=gruppenspiel.key_ms1 "
            . "   AND mann2.mannschaft_key=gruppenspiel.key_ms2 "
            . "	 AND blitz_ergebnis.status = 1 " // das Spiel laeuft aktuell
            . "   AND gruppenspiel.spiel_key=blitz_ergebnis.key_spiel "
            . " ORDER BY gruppenspiel.datum DESC, zeit DESC";
    $result = mysqli_query($connection, $query) or die("Abfrage1 fehlgeschlagen!");
    echo "<h4><i><center>Aktuelle Zwischenst&auml;nde:</center></i></h4><table >";
    while ($line = mysqli_fetch_array($result)) {
        echo "<tr> <td align='LEFT'><b>$line[1] gegen $line[4]</b></td><td><b> $line[2] : $line[5]</b></td></b></tr> ";
    }
    echo "</table><br>";


    // Abfrage basteln: Status nicht gesetzt, naechste Spiele zuerst anzeigen
    // Auswahl aller Spieler
    $query = "SELECT spieler.spieler_key, spieler.vorname, spieler.spielername  "
            . "     , spieler.punkte, spieler.exakte_tips, spieler.richtige_tendenz "
            . "     , mannschaft.mannschaftsname, mannschaft.info_link, mannschaft.STATUS, mannschaft.flagge "
            . "  FROM " . $name_blitz_tabelle . " AS spieler "
            . "  LEFT JOIN mannschaft ON (mannschaft.mannschaft_key=spieler.weltmeister) ";

    $order_klausel = " ORDER BY spieler.punkte DESC, spieler.richtige_tendenz DESC, spieler.spielername, spieler.vorname";
    $query = $query . $order_klausel;
    $result = mysqli_query($connection, $query) or die("Abfrage BLITZRANGLISTE fehlgeschlagen!");

    // Auswahlliste anzeigen
    echo "<table>";
    echo "<tr> <th></th><th> Tipper </th> <th> Punkte </th> <th> exakte Tips </th> <th> Tendenzen </th> <th> </th> <th> Cupsieger </th></tr>";
    $counter = 0;
    $punkte_vorher = -1;
    while ($line = mysqli_fetch_array($result)) {
        echo "<tr><td>";
        $counter++;
        if ($punkte_vorher != $line[3]) {
            echo $counter;
            $punkte_vorher = $line[3];
        }
        echo" </td><td align='LEFT'><a href='tippzettel.php?spieler=$line[0]'>$line[1] $line[2]</a></td>";
        echo "<td align='CENTER'>$line[3]</td> <td align='CENTER'>$line[4]</td> ";
        echo "<td align='CENTER'>$line[5]</td> <td><img src=" . FLAGS_PATH . "$line[9] ></td><td><a href='$line[7]'>$line[6]</a></td>";
        // falls Team ausgeschieden, kleine Grafik anzeigen
        if ($line[8] == 0) {
            echo "<td><img src='" . GRAFIK_AUSGESCHIEDEN . "' height=15 width=15> <b>R.I.P.</b></td>";
        }
        echo "</tr>";
    }
    // temporaere Tabelle Blitzrangliste loeschen
    $query = "DROP TABLE " . $name_blitz_tabelle;
    $result = mysqli_query($connection, $query) or die("Loeschen BLITZRANGLISTE fehlgeschlagen! - " . $query);
    // temporaere Tabelle Blitz-Ergebnis-Tabelle loeschen (wenn nicht Standardtabelle)
    if ($blitz_ergebnis_tabelle != "blitz_ergebnis") { // Default-Tabelle
        $query = "DROP TABLE " . $blitz_ergebnis_tabelle;
        $result = mysqli_query($connection, $query) or die("Loeschen BLITZ-Ergbnis-Tablle fehlgeschlagen! - " . $query);
    }

    echo "</table>";
};  // Ende Modus 0


echo" </form>\n";
frame_footer();


/*
 * DB-Verbindung sauber beenden
 */
mysqli_close($connection);
?>