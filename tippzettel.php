<?php
session_start();

// Globale Variablen einbinden
include 'functions.php';

// Name des Skripts
$skript_name = "tippzettel.php";

// Login pruefen
pruefe_login($skript_name);


/*
 * Modus aus evtl. Parametern setzen
 */

$Modus = 0;  // keiner, Auswahlliste
if (isset($_POST["MODUS_AUSWAHL"])) {
    $Modus = 0;  // Darstellung eines Datensatzes
};
if (isset($_POST["MODUS_AENDERN"])) {
    $Modus = 1;  // Gruppenspiel loeschen
};
if (isset($_POST["MODUS_SPEICHERN"])) {
    $Modus = 2;  // Einfuegen eines neuen Datensatzes
};
if (isset($_POST["MODUS_DRUCK"])) {
    $Modus = 10;  // Einfuegen eines neuen Datensatzes
};


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
    $user_key = $_SESSION["spieler_key"];
} else {
    $user_key = "nix";
    $Meldung = "Zugriff verweigert - Du bist nicht registriert!";
    $Modus = 999;
}

// fuer Nachzuegler ein Tuer auflassen
/*
  if ($user_key==49 OR $user_key==57){
  $user_rolle=ADMINROLLE;
  $aktueller_spieler = $user_key;
  }
 */


// per GET oder POST uebergebene Spieler auswerten
if (!empty($_REQUEST["spieler"])) {
    $aktueller_spieler = $_REQUEST["spieler"];
} else { // wenn kein Spieler gew&auml;hlt, aber aktueller Spieler registriert, dann den nehmen
    if ($user_key != 'nix') {
        $aktueller_spieler = $user_key;
    } else {
        $aktueller_spieler = -1;
        $Modus = 999;
    }
}


// sicherstellen, dass ein User nicht Daten von anderen aendert
if ($Modus != 999 AND $Modus != 0 AND $user_rolle != ADMINROLLE AND $user_key != $aktueller_spieler) {
    $Meldung = "Du darfst nur eigene Daten &auml;ndern!";
    $aktueller_spieler = $user_key;
}


$Titel = TURNIERART_KURZ . "-Tippschein";


/*
 *  zuerst mal den aktuellen Status des Tippspiel ermitteln
 */

// feststellen, ob bereits ein Ergebnis eingetragen ist
// falls ja, darf der Europameistertipp nicht mehr geaendert werden
$query = "SELECT 1 FROM gruppenspiel WHERE status=1";
$result = mysqli_query($connection, $query) or die("Abfrage fehlgeschlagen!" . $query);
if (mysqli_num_rows($result) > 0) {
    $turnier_start = 1;
} else {
    $turnier_start = 0;
}

// feststellen, ob ob ueberhaupt eine Runde freigegeben ist
$query = "SELECT 1 FROM runde WHERE freigabe=1";
$result = mysqli_query($connection, $query) or die("Abfrage fehlgeschlagen!" . $query);
if (mysqli_num_rows($result) > 0) {
    $runde_freigabe = 1;
} else {
    $runde_freigabe = 0;
}


// als Admin wuerde ich gerne machen was ich will
if ($user_rolle == ADMINROLLE) {
    $runde_freigabe = 1;
    $turnier_start = 0;
}

/*
 * Aktionen auf DB (SELECT, INSERT, UPDATE) vorbereiten und
 * anschliessend ausfuehren
 */

// vielleicht kann man diese Statements nochmal verwenden ...
$mutter_aller_abfragen = "SELECT spieler.spieler_key,gruppenspiel.spiel_key" // 0, 1
        . "      ,mann1.mannschaft_key, mann1.mannschaftsname, tips.tore_ms1" // 2, 3, 4
        . "      ,mann2.mannschaft_key, mann2.mannschaftsname, tips.tore_ms2" // 5, 6, 7
        . "      ,tips.erreichte_punkte, spieler.vorname, spieler.spielername " // 8, 9, 10
        . "      ,runde.bezeichnung, gruppenspiel.gruppe, gruppenspiel.datum" // 11, 12, 13
        . "      , gruppenspiel.zeit, spieler.freigabe, gruppenspiel.status" // 14, 15, 16
        . "      , gruppenspiel.tore_ms1, gruppenspiel.tore_ms2, runde.freigabe" // 17, 18, 19
        . "      , spieler.weltmeister, mann1.info_link, mann2.info_link " // 20, 21, 22
        . "      , mann1.flagge, mann2.flagge " // 23, 24
        . "      , gruppenspiel.bezeichnung, gruppenspiel.auto_ko_fk_ms1, gruppenspiel.auto_ko_fk_ms2 " // 25, 26, 27
        . "      , gruppenspiel.auto_gruppe_fk_ms1, gruppenspiel.auto_gruppe_fk_ms2 " // 28, 29
        . "      , gruppenspiel.auto_platz_ms1, gruppenspiel.auto_platz_ms2 " // 30, 31
        . "      , mann1.is_null, mann2.is_null " // 32, 33
        . "  FROM gruppenspiel JOIN runde JOIN spieler JOIN mannschaft mann1 JOIN mannschaft mann2 "
        . "  LEFT JOIN tips ON (gruppenspiel.spiel_key=tips.key_spiel "
        . "                 AND spieler.spieler_key=tips.key_spieler) "
        . " WHERE runde.runde_key=gruppenspiel.spiel_art "
        . "   AND mann1.mannschaft_key=gruppenspiel.key_ms1 "
        . "   AND mann2.mannschaft_key=gruppenspiel.key_ms2 "
        . "   AND spieler.spieler_key = " . $aktueller_spieler;  // Sicht auf einen Spieler einschraenken

if ($Modus == 1 AND $user_rolle != ADMINROLLE) {
    $mutter_aller_abfragen .= "   AND runde.freigabe=1 ";
}


$order_klausel = " ORDER BY runde.sortierung, gruppenspiel.gruppe, gruppenspiel.datum, gruppenspiel.zeit";

// Die Spielerauswahlliste wird auf die freigegebenen Tipper beschraenkt (falls User kein Admin)
$spieler_abfrage = "SELECT spieler_key, vorname, spielername FROM spieler ";
if ($user_rolle != ADMINROLLE) {
    if ($turnier_start == 0 AND $runde_freigabe == 1) { // Der Auswahlfilter nur falls EM noch nicht gestartet und Tippen moeglich
        $spieler_abfrage .= " WHERE freigabe=1 OR spieler_key = '$user_key'";
    }
}
$spieler_abfrage .= " ORDER BY spielername, vorname";

// Daten f&uuml;r die Europameister-Auswahlbox
// SQL-Statements zu jedem Fall erzeugen
switch ($Modus) {
    case 10:
        $Titel = "";
    case 0:
        // Abfrage aller Datens&auml;tze
        $query = $mutter_aller_abfragen . $order_klausel;
        break;
    case 1 :
        $Titel = "Tippschein ausf&uuml;llen";
        $query = $mutter_aller_abfragen . $order_klausel;
        break;
    case 2:
        // Abfrage aller Datens&auml;tze
        $Titel = "Tippschein gespeichert";
        break;
}

// Statement nur bei Bedarf ausfuehren !!!
if (isset($query)) {
    //   echo $query;
    $result = mysqli_query($connection, $query) or die("Abfrage fehlgeschlagen!" . $query);
}

// falls Druckansicht anderer Header
if ($Modus == 10) {
    druck_header($skript_name);
} else {
    frame_header($skript_name);
}
?>
<H3> <?php echo $Titel ?> </H3>
<H4> <?php echo $Meldung ?> </H4>

<!-- in allen Modi ist der Inhalt ein Formular -->
<p><form action = '<?php echo $skript_name; ?>' method = 'POST'>

    <!--  Knoeppe, die immer erscheinen sollen werden oben angezeigt:      -->
    <!--  Neu, Auswahl, Uebersicht                                         -->
    <p>
<?php
if ($Modus != 999) {

    // falls nicht in der Druckansicht
    if ($Modus != 10) {
        echo "<table>";

        // Auswahl-Knopf zum Selektieren eines Benutzers
        echo "<tr><td align='CENTER'>        <input type = 'SUBMIT' name = 'MODUS_AUSWAHL' value = 'Tipper-Auswahl'></td>";
        // Button zum Aendern nur im normalen Anzeige-Modus --> auch nach dem Speichern
        if ($Modus != 1 AND $runde_freigabe == 1) {
            echo "<td align='CENTER'>        <input type = 'SUBMIT' name = 'MODUS_AENDERN' value = 'Tippzettel ausf&uuml;llen'></td></tr>";
        }
        // Button zum Speichern nur im Aenderungs-Modus
        if ($Modus == 1 AND $runde_freigabe == 1) {
            echo "<td align='CENTER'>        <input type = 'SUBMIT' name = 'MODUS_SPEICHERN' value = 'Tipps Speichern'></td></tr>";
        }
        // Spieler-Auswahllbox anzeigen
        $result_lov = mysqli_query($connection, $spieler_abfrage)
                or die("Abfrage1 fehlgeschlagen!");

        // LOV-Anzeige aufbauen
        echo "  <tr><td> <SELECT  name='spieler' > \n";
        //
        while ($line_lov = mysqli_fetch_array($result_lov)) {
            echo "<option value='$line_lov[0]' ";
            // Der eventuell bereits eingetragene Spieler wird vorselektiert
            if ($line_lov[0] == $aktueller_spieler) {
                echo "selected";
            }
            echo ">$line_lov[1] $line_lov[2]</option>\n ";
        }
        echo "</td></tr>";
        echo " </table><p>";
        if ($Modus == 0) {
            echo "<INPUT  type='SUBMIT' name='MODUS_DRUCK' value='Druckansicht'><hr><p>\n";
        }
    }



    /*
     *
     * Tipps eintragen
     *
     */
    if ($Modus == 2) {
        $anzahl_tipps = 0;
        $tipZ_gefaked = 0; // zum Spass auch mal die Betrugsversuche zaehlen
        // Den Europameister eintragen, falls gesetzt
        if (isset($_POST["weltmeister"]) AND $turnier_start == 0 AND $runde_freigabe == 1) {
            $result = mysqli_query($connection, "UPDATE spieler SET weltmeister = " . $_POST["weltmeister"]
                    . " WHERE spieler_key = " . $aktueller_spieler)
                    or die("UPDATE fehlgeschlagen!");
        }
        // Freigabe der Tipps festlegen
        if (isset($_POST["freigabe"])) {
            $result = mysqli_query($connection, "UPDATE spieler SET freigabe = " . $_POST["freigabe"]
                    . " WHERE spieler_key = " . $aktueller_spieler)
                    or die("UPDATE fehlgeschlagen!");
        }
        // In dem uebergebenen Intervall werden die belegten Tipps in die
        // DB eingetragen
        $idiotenzaehler = 0;
        $inkorrekt = 0;
        for ($i = $_POST["min_spiel"]; $i <= $_POST["max_spiel"]; $i++) {
            // Auswertung der Tipps: leere Felder werden auf 0 gesetzt
            if (!empty($_POST["tipp" . $i . "A"])) {
                $tore_mannschaft1 = intval($_POST["tipp" . $i . "A"]);
            } else {
                $tore_mannschaft1 = 0;
            }
            if (!empty($_POST["tipp" . $i . "B"])) {
                $tore_mannschaft2 = intval($_POST["tipp" . $i . "B"]);
            } else {
                $tore_mannschaft2 = 0;
            }

            // Ingos Idioten-Abfrage
            if ($tore_mannschaft1 < 0) {
                $tore_mannschaft1 = 0;
                $idiotenzaehler = $idiotenzaehler + 1;
            }
            if ($tore_mannschaft1 > 99) { // nur zweistellige Ergebnisse bitte
                $tore_mannschaft1 = 99;
                $idiotenzaehler = $idiotenzaehler + 1;
            }
            if ($tore_mannschaft2 < 0) {
                $tore_mannschaft2 = 0;
                $idiotenzaehler = $idiotenzaehler + 1;
            }
            if ($tore_mannschaft2 > 99) { // nur zweistellige Ergebnisse bitte
                $tore_mannschaft2 = 99;
                $idiotenzaehler = $idiotenzaehler + 1;
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


            // nur wenn ein Spieler eingetragen ist, handelt es sich
            // um einen validen Datensatz
            if (!empty($_POST["tipper" . $i])) {

                // Spieler
                $tipper = $_POST["tipper" . $i];

                if ($user_rolle != ADMINROLLE) {
                    // testen, ob Tipp noch ge&auml;ndert werden darf
                    $query = "SELECT count(1) "
                            . "  FROM gruppenspiel, runde "
                            . " WHERE gruppenspiel.spiel_art=runde.runde_key "
                            . "   AND runde.freigabe=1 "
                            . "   AND gruppenspiel.status=0 "
                            . "   AND gruppenspiel.spiel_key=$i";
                    $result = mysqli_query($connection, $query) or die("Abfrage fehlgeschlagen!" . $query);
                    $line = mysqli_fetch_array($result);
                    $darf_getippt_werden = $line[0]; // hier entscheidet sich ob getippt werden kann
                } else {
                    $darf_getippt_werden = 1;  // Admins duerfen alles
                }
                if ($darf_getippt_werden == 1 AND ( $tipper == $user_key OR $user_rolle == ADMINROLLE)) {
                    // Einfuegen der Tipps
                    $query = "DELETE FROM tips WHERE key_spieler=$tipper AND key_spiel=$i";
                    $result = mysqli_query($connection, $query) or die("Abfrage fehlgeschlagen!" . $query);
                    $query = "INSERT INTO tips (key_spieler, key_spiel, tore_ms1, tore_ms2,tendenz) "
                            . "VALUES ($tipper, $i, $tore_mannschaft1, $tore_mannschaft2, $tendenz)";
                    $result = mysqli_query($connection, $query) or die("Abfrage fehlgeschlagen!" . $query);
                    $anzahl_tipps ++; // schoen mitzaehlen
                } else {
                    $tipZ_gefaked ++;
                }
            }
        }
        // kleines Resumee
        echo "<br><i><b>Es wurden $anzahl_tipps Tipps gespeichert!</b></i><br>";
        if ($idiotenzaehler > 0) {
            echo "<br><i><b>$idiotenzaehler eingegebene Werte waren zu sinnlos und wurden deshalb eigenm&auml;chtig ge&auml;ndert!</b></i>";
            echo "<br>Auch wenn ich gar nicht raten muss: Ich glaube du hei&szlig;t Ingo ... oder Reiners<br>";
        }

        if ($tipZ_gefaked > 0) {
            echo "$tipZ_gefaked Betrugsversuche habe ich dabei ignoriert!<br>";
        }

        // wenn alles eingetragen ist, Tippschein normal anzeigen
        $query = $mutter_aller_abfragen . $order_klausel;
        $result = mysqli_query($connection, $query) or die("Abfrage fehlgeschlagen!" . $query);
        $Modus = 0;
    }



    /*
     *
     * Fall 0 UND 1: Tipps einsehen oder aendern
     *
     */
    if ($Modus == 1 OR $Modus == 0 OR $Modus == 10) {

        // Variablen um das Intervall bei der Auswertung einzugrenzen
        $min_spiel_key = 1000000;
        $max_spiel_key = -1;

        // Variablen Abschnittswechsel bemerken
        $alter_tipper = "";
        $alte_runde = "";
        $alte_gruppe = 0;

        $gruppe_abfrage = "SELECT bezeichnung FROM gruppe WHERE gruppe_key=";
        // Wechsel-Flag zeigt an, ob eine Tabelle gestartet oder beendet werden muss
        $wechsel_flag = 0;

        while ($line = mysqli_fetch_array($result)) {

            // Intervall eingrenzen
            if ($line[1] < $min_spiel_key) {
                $min_spiel_key = $line[1];
            }
            if ($line[1] > $max_spiel_key) {
                $max_spiel_key = $line[1];
            }



            // weil ich faul bin, mache ich das so (eine Abfrage gespart)
            // Wechsel des Spieler
            if ($line[0] != $alter_tipper) {
                // Tabelle schlie&szlig;en
                if ($wechsel_flag == 2) {
                    echo "</table>";
                }
                // tabellenstart einleiten
                $wechsel_flag = 1;
                $alter_tipper = $line[0];
                if ($Modus != 10) {
                    echo "<br><i><b><a href='hypo_tabellen.php?spieler=$line[0]'>Tipptabellen anzeigen</a></b></i><br>\n";
                }
                echo "<br><h4>Tipper: <i><b><a href='spieler.php?spieler=$line[0]'>$line[9] $line[10]</a></b></i></h4>\n";

                // im Aendern-Modus die Auswahl des Europameisters erm&ouml;glichen
                $query_lov = "SELECT mannschaft_key, mannschaftsname FROM mannschaft where is_null=0";
                if ($Modus == 1) {
                    // LOV fuer den Europameister-Tipp erstellen
                    $query_lov = "SELECT mannschaft_key, mannschaftsname, info_link FROM mannschaft WHERE is_null=0";
                    // LOV-Anzeige aufbauen
                    echo "  <b>Cupsieger: </b>";
                    if ($turnier_start == 0) {
                        $result_lov = mysqli_query($connection, $query_lov) or die("Abfrage3 fehlgeschlagen!");
                        echo "<blink> <b>Bitte " . TURNIER_SIEGER_ART . " eintragen! >>></b></blink> <SELECT  name='weltmeister' size='1'> \n";
                        while ($line_lov = mysqli_fetch_array($result_lov)) {
                            echo "<option value='$line_lov[0]' ";
                            // Der eventuell bereits eingetragene Europameister wird vorselektiert
                            if (!empty($line[20]) AND $line_lov[0] == $line[20]) {
                                echo "selected";
                            }
                            echo ">$line_lov[1]</option>\n ";
                        }
                        echo "</select>\n";
                    } else {
                        // falls EM schon gestartet kein Aenderung mehr moeglich
                        $result_lov = mysqli_query($connection, $query_lov . " AND mannschaft_key=$line[20]")
                                or die("Abfrage3 fehlgeschlagen!");
                        $line_lov = mysqli_fetch_array($result_lov);
                        echo " <b><a href='$line_lov[2]'>$line_lov[1]</a></b> ";
                    }

                    // Freigabe
                    echo "  <b> Tipps freigeben? </b><SELECT name='freigabe' size='1'>";
                    echo "<OPTION VALUE='0' ";
                    if (!empty($line[15]) AND $line[15] == 0) {
                        echo " SELECTED";
                    }
                    echo "> Nein! </option>\n ";
                    echo "<OPTION VALUE='1' ";
                    if (!empty($line[15]) AND $line[15] == 1) {
                        echo " SELECTED";
                    }
                    echo "> Ja! </option>\n ";
                    echo "</SELECT>";
                } else { // nur einfache Anzeige
                    if (!empty($line[20])) {
                        $result_lov = mysqli_query($connection, $query_lov . " AND mannschaft_key=" . $line[20])
                                or die("Abfrage4 fehlgeschlagen!");
                        $line_lov = mysqli_fetch_array($result_lov);
                        echo "  <b>Cupsieger: <i>$line_lov[1]</i></b><br>\n";
                    } else {
                        echo "  <b>Cupsieger: <i>noch nicht ausgew&auml;hlt!</i></b><br>\n";
                    }
                }
            }


            // bei Aenderung Runde Absatz erzeugen
            if ($line[11] != $alte_runde) {
                // Tabelle schlie&szlig;en
                if ($wechsel_flag == 2) {
                    echo "</table>";
                }
                // tabellenstart einleiten
                $wechsel_flag = 1;
                $alte_runde = $line[11];
                echo "<br><h3>$alte_runde</h3>";
                // Berechtigung auf Rundenbasis ermitteln
                if ($user_rolle == ADMINROLLE) {
                    $runde_freigabe = 1;
                } else {
                    $runde_freigabe = $line[19];
                }
                // neue Tabelle starten
            }

            // bei Aenderung Gruppe Absatz erzeugen
            if (!empty($line[12]) AND $line[12] != $alte_gruppe) {
                // Tabelle schlie&szlig;en
                if ($wechsel_flag == 2) {
                    echo "</table>";
                }
                // tabellenstart einleiten
                $wechsel_flag = 1;
                $alte_gruppe = $line[12];
                // Bezeichnung abfragen
                $result_gruppe = mysqli_query($connection, $gruppe_abfrage . $alte_gruppe) or die("Abfrage5 fehlgeschlagen!");
                $line_gruppe = mysqli_fetch_array($result_gruppe);
                echo "<br><h3>$line_gruppe[0]</h3>";
                // neue Tabelle starten
            }

            // Tabelle starten?
            if ($wechsel_flag == 1) {
                // falls notwendig Tabelle schlie&szlig;en
                echo "<table>";
                // Name, Mannschaft1, Flagge1, - , Flagge2, Mannschaft2, Tipp, Ergebnis, Punkte
                echo "<tr><th></th><th>Begegnung</th> <th> </th> <th> </th> <th> </th> <th> </th> <th> Spielertipp </th> <th> </th>"
                . "<th> Endergebnis </th> <th> </th> <th> Punkte </th> <tr>";
                $wechsel_flag = 2;
            }

            /**
              Begegnung anzeigen
              Es  gibt drei Faelle
              - Gruppenspiel
              - erste KO-Runde nach Gruppenphase (Gegner errechnen sich aus Tabellen)
              - KO-Spiel

              Format: <Mannschaft mit Link> <Flagge> - <Flagge> <Mannschaft mit Link>
             * */
            /* Manschaft 1 */
            echo "<tr><td>";
            if ($line[25] != NULL)
                echo "$line[25] ... ";
            echo "</td><td align='LEFT'>";
            if ($line[32] == 0) { // entspricht der Mannschaftskey dem NULL-Wert?
                echo "<a href='$line[21]' target='_blank'>$line[3]</a></td> <td> <img border=\"1\" src=" . FLAGS_PATH . $line[23] . "></td>";
            } else {
                echo "<b>";
                if ($line[26] != NULL) { // KO-Spiel
                    // Bezeichnung des referenzierten Spiels ermitteln
                    if ($line[30] == 1) // 1. PLatz=Sieger, sonst Verlierer
                        echo " Sieger Spiel " . ermittle_spiel_bezeichnung($line[26]);
                    else
                        echo " Verlierer Spiel " . ermittle_spiel_bezeichnung($line[26]);
                }else { // KO-Spiel nach Gruppenphase
                    // Bezeichnung der referenzierten Gruppe ermitteln
                    echo " $line[30]. " . ermittle_gruppen_bezeichnung($line[28]);
                }
                echo "</b></td> <td></td>";
            }
            echo "<td> - </td>";
            /* Manschaft 2 */
            if ($line[33] == 0) { // entspricht der Mannschaftskey dem NULL-Wert?
                echo "<td><img border=\"1\" src=" . FLAGS_PATH . $line[24] . "> </td> <td align='LEFT'><a href='$line[22]' target='_blank'> $line[6]</a></td>";
            } else {
                echo "<b><td></td><td>";
                if ($line[27] != NULL) { // KO-Spiel
                    // Bezeichnung des referenzierten Spiels ermitteln
                    if ($line[31] == 1) // 1. PLatz=Sieger, sonst Verlierer
                        echo " Sieger Spiel " . ermittle_spiel_bezeichnung($line[27]);
                    else
                        echo " Verlierer Spiel " . ermittle_spiel_bezeichnung($line[27]);
                }else { // KO-Spiel nach Gruppenphase
                    // Bezeichnung der referenzierten Gruppe ermitteln
                    echo " $line[31]. " . ermittle_gruppen_bezeichnung($line[29]);
                }
                echo "</b></td>";
            }
//	  }
            // im Aenderungsmodus Eingabefelder anzeigen, wenn Freigabe vorhanden oder ausgetragen
            if ($Modus == 1 AND $runde_freigabe == 1 AND $line[16] != 1) {
                // Tore Mannschaft 1
                echo "<td align='CENTER'><input type='TEXT' NAME='tipp$line[1]A' SIZE='2' ";
                if (isset($line[4])) {
                    echo "VALUE='$line[4]'";
                } // vorbelegen, wenn Tipp vorhanden
                echo ">";
                // Tore Mannschaft 2
                echo " - <input type='TEXT' NAME='tipp$line[1]B' SIZE='2' ";
                if (isset($line[7])) {
                    echo "VALUE='$line[7]'";
                } // vorbelegen, wenn Tipp vorhanden
                // Tipper versteckt &uuml;bergeben
                echo "></td><INPUT TYPE='HIDDEN' NAME='tipper$line[1]' VALUE='$line[0]'>";
                // im Anzeigemodus Tipps nur als Text ausgeben
            } else {
                echo "<td align='CENTER'>";
                if (isset($line[4])) {
                    // entweder Spielerfreigabe, Runde freigegeben, schon ausgetragen oder User ist Admin
                    if ($line[15] == 1 OR $line[19] == 0 OR $user_rolle == ADMINROLLE OR $line[16] == 1 OR $line[0] == $user_key) {
                        echo "<a href='tippvergleich.php?spiel=$line[1]'> $line[4] - $line[7] </a>";
                    } else {
                        echo "* - *";
                    }
                }
                echo "</td>";
            }
            echo "<td> </td><td align='CENTER'>";
            // Endergebnis
            if (isset($line[17])) {
                echo "$line[17] - $line[18]";
            }
            echo "</td>";
            // erreichte Punkte beim Tippen als Pseudo-Balken-Diagramm
            echo "<td> </td> <td align='CENTER'>";
            for ($i = 1; $i <= $line[8]; $i++) {
                echo "o";
            }
            echo "</td></tr>";
        }
        // Bei Bedarf Tabelle schliessen und dann noch eine Statistik
        if ($wechsel_flag == 2) {
            echo "</table><br>";

            // Statistik funktioniert nur, wenn nur ein Spieler dargestellt wird
            // (ist momentan wurscht)
            $spieler_abfrage = "SELECT vorname, spielername, punkte, exakte_tips, richtige_tendenz "
                    . "  FROM spieler "
                    . " WHERE spieler_key = " . $aktueller_spieler;
            $spieler_result = mysqli_query($connection, $spieler_abfrage) or die("Abfrage6 fehlgeschlagen!");
            $line = mysqli_fetch_array($spieler_result);
            // nur Anzeigen, wenn auch schon Punkte vergeben wurden
            if ($line[2] > 0) {
                echo "<hr><table><tr><th>Spieler</th><th>Punkte</th><th>exakte Tipps</th>"
                . "<th>Tendenzen</th></tr>\n";
                echo "<tr><td align='LEFT'>$line[0] $line[1]</td><td  align='CENTER'>$line[2]</td><td align='CENTER'>$line[3]</td>"
                . "<td align='CENTER'>$line[4]</td></tr></table><hr><br>\n";
            }
        }

        // jetzt noch wichtige Infos versteckt uebergeben
        echo "<INPUT TYPE='HIDDEN' NAME='min_spiel' VALUE='$min_spiel_key'>";
        echo "<INPUT TYPE='HIDDEN' NAME='max_spiel' VALUE='$max_spiel_key'>";
    };  // Ende Modus 0,1
    // hier sollte zu Abschluss die Bilanz (Punkte,exakte Tipps, tendenzen) stehen
}
echo" </form>\n";

// falls Druckansicht anderer Header
if ($Modus == 10) {
    druck_footer();
} else {
    frame_footer();
}



/*
 * DB-Verbindung sauber beenden
 */
mysqli_close($connection);
?>