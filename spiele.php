<?php
SESSION_start();

// Globale Variablen einbinden
include 'functions.php';

// Name des Skripts
$skript_name = "spiele.php";

// Login pruefen
pruefe_login($skript_name);

/*
 * Modus aus evtl. Parametern setzen
 */

$Modus = 0;  // keiner, Auswahlliste
if (isset($_POST["MODUS_AUSWAHL"])) {
    $Modus = 0;  // Auswahlliste wurde  explizit gewaehlt
};
if (isset($_POST["MODUS_AENDERN"])) {
    $Modus = 2;  // Gruppenspiel aendern
};
if (isset($_POST["MODUS_LOESCHEN"])) {
    $Modus = 3;  // Gruppenspiel loeschen
};
if (isset($_POST["MODUS_SPEICHERN_NEU"])) {
    $Modus = 4;  // Einfuegen eines neuen Datensatzes
};
if (isset($_POST["MODUS_SPEICHERN_AENDERN"])) {
    $Modus = 5;  // Aendern eines Datensatzes
};
if (isset($_POST["MODUS_ANZEIGE"])) {
    $Modus = 6;  // Darstellung aller Datensaetze
};
if (isset($_POST["MODUS_ANZEIGE_SINGLE"])) {
    $Modus = 7;  // Darstellung eines Datensatzes
};
if (isset($_POST["MODUS_DELETE_BLITZTABELLE"])) {
    $Modus = 8;  // Blitztabelle loeschen
};
if (isset($_POST["MODUS_STATISTIK_ERNEUERN"])) {
    $Modus = 9;  // Tabellen erneuern
};

$spieltag_von = 1;
$spieltag_bis = 10;
if (isset($_POST["NAV_GO"])) {
    $Modus = 6;  // Darstellung aller Datensaetze
    $spieltag_von = $_POST["spieltag_von"];  // Intervall merken
    $spieltag_bis = $_POST["spieltag_bis"];  //
};

// pruefen, ob Anzeigemodus gewuenscht ist
if (isset($_REQUEST["anzeige"]) OR isset($_REQUEST["druck"])) {
    $Modus = 6;
} else {

    //nur Goetter und Halbgoetter duerfen hier herumfummeln
    if (!isset($_SESSION["rolle"]) OR $_SESSION["rolle"] == TIPPERROLLE) {
        $Titel = TURNIERART_KURZ . "-Begegnungen";
        $Meldung = "Dieser Bereich ist nur f&uuml;r den Admin erreichbar! <br>Finger weg!";
        $Modus = 999;
    }

    // Pruefung bei Einfuegen, ob die notwendigen Daten eingegeben wurden
    if ($Modus == 4 OR $Modus == 5) {
        if (!isset($_POST["mannschaft1"]) OR ! isset($_POST["mannschaft2"]) OR ! isset($_POST["runde"])) {
            $Meldung = "Es fehlen notwendige Angaben!";
            $Modus = 0;  // Auswahlliste erneut anzeigen
        }
    };

    // Pruefung bei Loeschen / Einzelanzeige, ob ein Datensatz ausgewaehlt wurde
    if (($Modus == 3 OR $Modus == 7) AND ! isset($_POST["spiel_key"])) {
        $Meldung = "Es wurde keine Begegnung ausgew&auml;hlt!";
        $Modus = 0;  // Auswahlliste erneut anzeigen
    };
}

/*
 * Aktionen auf DB (SELECT, INSERT, UPDATE) vorbereiten und
 * anschlie&szlig;end ausfuehren
 */

// vielleicht kann man diese Statements nochmal verwenden ...
$mutter_aller_abfragen = "SELECT spiel_key, key_ms1, man1.mannschaftsname "  // 0, 1, 2
        . "       , key_ms2, man2.mannschaftsname"    // 3, 4
        . "       , spiel_art, runde.bezeichnung"    // 5, 6
        . "       , spiel.gruppe, gruppe.bezeichnung, datum , zeit" // 7, 8, 9, 10
        . "       , man1.flagge, man2.flagge" // 11. 12
        . "      , spiel.bezeichnung, spiel.auto_ko_fk_ms1, spiel.auto_ko_fk_ms2 " // 13, 14, 15
        . "      , spiel.auto_gruppe_fk_ms1, spiel.auto_gruppe_fk_ms2 " // 16, 17
        . "      , spiel.auto_platz_ms1, spiel.auto_platz_ms2 " // 18, 19
        . "      , man1.is_null, man2.is_null " // 20, 21
        . "      , spiel.ticker_url, spiel.ticker_spiel_id " // 22, 23
        . "      , man1.info_link, man1.info_link " // 24, 25
        . "  FROM mannschaft AS man1 JOIN mannschaft AS man2 JOIN gruppenspiel AS spiel"
        . "       JOIN runde"
        . "  LEFT JOIN gruppe ON (spiel.gruppe=gruppe.gruppe_key) "
        . " WHERE man1.mannschaft_key=key_ms1 AND man2.mannschaft_key=key_ms2 "
        . "   AND runde_key=spiel_art ";

$mannschaft_abfrage = "SELECT mannschaft_key, mannschaftsname"
        . "  FROM mannschaft  ORDER BY mannschaftsname";



// SQL-Statements zu jedem Fall erzeugen
switch ($Modus) {
    case 2 :
        $Titel = "Begegnung &auml;ndern";
        $query = $mutter_aller_abfragen . " AND spiel_key=" . $_POST["spiel_key"];
        break;
    case 0:
        // Abfrage aller Datens&auml;tze
        $query = $mutter_aller_abfragen . " ORDER BY datum, zeit";
        $Titel = "Begegnungen";
        break;
    case 6:
        // Abfrage aller Datens&auml;tze
        $query = $mutter_aller_abfragen . " AND runde.runde_key>=$spieltag_von  AND runde.runde_key<=$spieltag_bis ORDER BY DATUM, ZEIT";
        $Titel = TURNIERART_KURZ . " - Fahrplan";
        break;
    case 7:
        // Abfrage eines Datensatzes
        $Titel = "Begegnung anzeigen";
        $query = $mutter_aller_abfragen . " AND spiel_key=" . $_POST["spiel_key"];
        break;
    case 3:
        // einen Datensatz loeschen
        $query = "DELETE FROM gruppenspiel WHERE spiel_key=" . $_POST["spiel_key"];
        // Seitentitel und Meldung setzen
        $Titel = "Begegnung gel&ouml;scht";
        $Meldung = "Die Begegnung wurde gel&ouml;scht!";
        // erneute Anzeige der Auswahlliste einleiten
        $Modus = 99;
        break;
    case 4:
        // einen neuen Datensatz in der DB speichern
        // ACHTUNG!!! Hier wird mit dem MySQL-Feature "auto-increment"
        //            ein INSERT gemacht!!!
        $query = "INSERT INTO gruppenspiel (spiel_art, gruppe, key_ms1, key_ms2, status, datum, zeit, ticker_url, ticker_spiel_id) "
                . "VALUES(" . $_POST["runde"]
                . ",  " . $_POST["gruppe"]
                . ",  " . $_POST["mannschaft1"]
                . ",  " . $_POST["mannschaft2"]
                . ",  0" // Spielstatus: noch nicht ausgetragen
                . ", '" . $_POST["datum"]
                . "', '" . $_POST["zeit"]
                . "', '" . $_POST["ticker-url"]
                . "', '" . $_POST["ticker-spiel-id"]
                . "')";
        // Seitentitel und Meldung setzen
        $Titel = "Begegnung gespeichert";
        $Meldung = "Die Begegnung wurde eingetragen!";
        // erneute Anzeige der Auswahlliste einleiten
        $Modus = 99;
        break;
    case 5:
        setlocale(LC_ALL, 'de_DE');

        echo strtotime($_POST["datum"]);
        // Datensatz aendern
        $query = "UPDATE gruppenspiel  "
                . "SET  spiel_art=" . $_POST["runde"]
                . ",gruppe   = " . $_POST["gruppe"]
                . ",key_ms1  = " . $_POST["mannschaft1"]
                . ",key_ms2  = " . $_POST["mannschaft2"]
                . ",datum    = '" . $_POST["datum"]
//                . ",datum    = DATE_FORMAT('" . $_POST["datum"] . "', '%d.%m.%Y')"
                . "',zeit    ='" . $_POST["zeit"]
                . "',ticker_url    ='" . $_POST["ticker-url"]
                . "',ticker_spiel_id    ='" . $_POST["ticker-spiel-id"]
                . "' WHERE spiel_key=" . $_POST["spiel_key"];
        // Seitentitel und Meldung setzen
        $Titel = "Begegnung gespeichert";
        $Meldung = "Die Begegnung wurde ge&auml;ndert!";
        // erneute Anzeige der Auswahlliste einleiten
        $Modus = 99;
        break;
    case 8:
        // Blitzergebnisse loeschen

        $query = "DELETE FROM blitz_ergebnis";
        // Seitentitel und Meldung setzen
        $Titel = "Blitzergebnisse gel&ouml;scht";
        $Meldung = "Die Tabelle der Blitzergebnisse wurde komplett geleert!";
        // erneute Anzeige der Auswahlliste einleiten
        $Modus = 99;
        break;
    case 9:
        // Mannschaftsstatistiken neu berechnen
        $query = "update mannschaft m1 join\n"
                . "(SELECT m.mannschaft_key ,count(1) spiele, sum(if(key_ms1 = m.mannschaft_key, g1.tore_ms1, g1.tore_ms2)) as plus, sum(if(key_ms1 = m.mannschaft_key, g1.tore_ms2, g1.tore_ms1)) as minus\n"
                . ", sum(if((key_ms1 = m.mannschaft_key and g1.tendenz=1) or (key_ms2 = m.mannschaft_key and g1.tendenz=2), 3, 0)) + sum(if (g1.tendenz=0, 1, 0)) punkte\n"
                . "FROM mannschaft m, `gruppenspiel` g1\n"
                . "where (key_ms1=m.mannschaft_key or key_ms2=m.mannschaft_key) and g1.tendenz is not null\n"
                . "group by m.mannschaft_key) as x on m1.mannschaft_key = x.mannschaft_key\n"
                . "set m1.anz_spiele=x.spiele, m1.punkte=x.punkte, m1.plustore=x.plus, m1.minustore=x.minus, m1.tordifferenz=x.plus-x.minus\n"
                . "";
        // Seitentitel und Meldung setzen
        $Titel = "Statistik neu berechnet";
        $Meldung = "Die Tabellenstatistik wurde komplett erneuert!";
        // erneute Anzeige der Auswahlliste einleiten
        $Modus = 99;
        break;
}

// Statement nur bei Bedarf ausfuehren !!!
if (isset($query)) {
//   echo $query;
    $result = mysqli_query($connection, $query) or die("Abfrage fehlgeschlagen!");
}

// falls Druckansicht anderer Header
if (isset($_REQUEST["druck"])) {
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
if ($Modus != 6 AND ! isset($_REQUEST["anzeige"]) AND ! isset($_REQUEST["druck"])) { // nicht bei Anzeige-Flag gesetzt (Fahrplan)
    echo "        <input type = 'SUBMIT' name = 'MODUS_AUSWAHL' value = 'Auswahl'>";
}
// Anzeige Neu oder Speichern nach aktuellem Modus
if ($Modus == 0 OR $Modus == 3 OR $Modus == 4 OR $Modus == 5 OR $Modus == 8 OR $Modus == 9 OR $Modus == 99) {
    echo "        <input type = 'SUBMIT' name = 'MODUS_DELETE_BLITZTABELLE' value = 'L&ouml;sche Blitzergebnisse'>";
    echo "        <input type = 'SUBMIT' name = 'MODUS_STATISTIK_ERNEUERN' value = 'Neuberechnung Statistik'>";
    echo "        <input type = 'SUBMIT' name = 'MODUS_SPEICHERN_NEU' value = 'Neu'>";
} else {
    if ($Modus == 2) {
        echo "        <input type = 'SUBMIT' name = 'MODUS_SPEICHERN_AENDERN' value = 'Speichern'>";
    }
}

if (isset($_REQUEST["anzeige"])) {
    echo "        <a href='spiele.php?druck' target='_blank'>Druckansicht</a><p>";
}


/*
 *
 * Der Modus fragt die Spiele neu ab
 *
 */

if ($Modus == 99) {
    $query = $mutter_aller_abfragen . " ORDER BY datum, ZEIT";
    $result = mysqli_query($connection, $query) or die("Abfrage fehlgeschlagen!");
    $line = mysqli_fetch_array($result);
}

/*
 *
 * Fall 0,2,3 UND 4: Erstellen / Bearbeiten eines Datensatzes
 * Eine Vorbelegung der Datenfelder soll NICHT stattfinden, wenn Modus=99!!!
 *
 */
if ($Modus == 0 OR $Modus == 2 OR $Modus == 99) {


    // Im Bearbeiten-Modus werden die Felder vorbelegt
    if ($Modus == 2) {
        $line = mysqli_fetch_array($result);
        // den Primary Key versteckt uebergeben
        printf("<input type='HIDDEN' name='spiel_key' value='%s'>", $line[0]);
    };
    echo "<table>\n";
    echo "<tr> <td>";

    // Eingabfelder fuer Rahmendaten
    echo "  <table>\n";

    // LOV fuer die Runde erstellen
    $query_lov = "SELECT runde_key, bezeichnung FROM runde";
    $result_lov = mysqli_query($connection, $query_lov) or die("Abfrage fehlgeschlagen!");

    // LOV-Anzeige aufbauen
    echo "  <tr><td align=\"left\"> " . TURNIERART_KURZ . "-Runde </td> <td align=\"left\"> <SELECT  name='runde' size='1'> \n";
    while ($line_lov = mysqli_fetch_array($result_lov)) {
        echo "<option value='$line_lov[0]' ";
        // Die eventuell bereits eingetragene Runde wird vorselektiert
        if ($Modus != 99 AND ! empty($line[5]) AND $line_lov[0] == $line[5]) {
            echo "selected";
        }
        echo ">$line_lov[1]</option>\n ";
    }
    echo "</select></TD></tr>";

    // LOV fuer die Gruppe erstellen
    $query_lov = "SELECT gruppe_key, bezeichnung FROM gruppe";
    $result_lov = mysqli_query($connection, $query_lov) or die("Abfrage fehlgeschlagen!");

    // LOV-Anzeige aufbauen
    echo "  <tr><td align=\"left\"> Gruppe </td> <td align=\"left\"> <SELECT  name='gruppe' size='1'> \n";
    // die Gruppe ist otional also auch ein Leereintrag
    echo "<option value='NULL'></option> ";
    while ($line_lov = mysqli_fetch_array($result_lov)) {
        echo "<option value='$line_lov[0]' ";
        // Die eventuell bereits eingetragene Runde wird vorselektiert
        if ($Modus != 99 AND ! empty($line[7]) AND $line_lov[0] == $line[7]) {
            echo "selected";
        }
        echo ">$line_lov[1]</option>\n ";
    }
    echo "</select></TD></tr>";

    //Datum
    echo "  <tr> <td align=\"left\"> Datum (Format: yyyy-mm-dd)</td> <td align=\"left\"> <INPUT type='TEXT' name='datum' size='10' ";
    if ($Modus != 99 AND isset($line[9])) {
        echo "value=\"$line[9]\"";
    };
    echo "></td> </tr>\n";
    // Uhrzeit
    echo "  <tr> <td align=\"left\"> Uhrzeit (Format: HH:ii:ss)</td> <td align=\"left\"> <INPUT type='TEXT' name='zeit' size='5'";
    if ($Modus != 99 AND isset($line[10])) {
        echo "value=\"$line[10]\"";
    };
    echo "></td> </tr>\n";
    echo "  <tr> <td align=\"left\"> Ticker-ID</td> <td align=\"left\"> <INPUT type='TEXT' name='ticker-spiel-id' size='5'";
    if ($Modus != 99 AND isset($line[10])) {
        echo "value=\"$line[23]\"";
    };
    echo "></td> </tr>\n";
    echo "  </table></td><td>\n";

    // Auswahl Mannschaft 1
    $result = mysqli_query($connection, $mannschaft_abfrage) or die("Abfrage fehlgeschlagen!");
    echo "<select size='10' name='mannschaft1'>\n";
    while ($line_man = mysqli_fetch_array($result)) {
        echo "<option value=$line_man[0]";
        if ($Modus != 99 AND isset($line[1]) AND $line_man[0] == $line[1]) {
            echo " selected";
        }  //Vorauswahl
        echo ">" . $line_man[1] . "</option>\n";
    }
    echo "</select></td><td>\n";
    echo "</td><td> --- </td><td>\n";

    // Auswahl Mannschaft 2
    $result = mysqli_query($connection, $mannschaft_abfrage) or die("Abfrage fehlgeschlagen!bla");
    echo "<select size='10' name='mannschaft2'>\n";
    while ($line_man = mysqli_fetch_array($result)) {
        echo "<option value=$line_man[0]";
        if ($Modus != 99 AND isset($line[3]) AND $line_man[0] == $line[3]) {
            echo " selected";
        }  //Vorauswahl
        echo ">" . $line_man[1] . "</option>\n";
    }
    echo "</select>\n";
    echo "</td></tr></table>\n<p>";
    echo "  Ticker-URL: <INPUT type='TEXT' name='ticker-url' size='90'";
    if ($Modus != 99 AND isset($line[10])) {
        echo "value=\"$line[22]\"";
    };
    echo "><br>\n";

    // Auswahlbox nicht anzeigen, wenn Aenderungen
    if ($Modus != 2) {

        echo "<table> <tr><td>";
        // Bei Anzeige der Auswahlliste sind folgende Aktionen moeglich:
        echo " <input type = 'SUBMIT' name = 'MODUS_AENDERN' value = '&Auml;ndern' size='10'> \n";
        echo " <br><input type = 'SUBMIT' name = 'MODUS_LOESCHEN' value = 'L&ouml;schen' size='10'> \n";
        echo " <br><input type = 'SUBMIT' name = 'MODUS_ANZEIGE_SINGLE' value = 'Anzeigen' size='10'> \n";
        echo "</td><td>\n";

        // Auswahlbox Spiele
        $result = mysqli_query($connection, $mutter_aller_abfragen . " ORDER BY datum,zeit") or die("Abfrage fehlgeschlagen!bla");
        echo "<select size='10' name='spiel_key'>\n";
        while ($line = mysqli_fetch_array($result)) {
            echo "<option value=$line[0]>$line[9] --  $line[6] ";
            if (!empty($line[8])) {
                echo "/ $line[8] ";
            }
            echo "$line[13]: "; // Spielbezeichnung
            if ($line[20] == 0) { // entspricht der Mannschaftskey dem NULL-Wert?
                echo $line[2];
            } else {
                if ($line[14] != NULL) { // KO-Spiel
                    // Bezeichnung des referenzierten Spiels ermitteln
                    if ($line[18] == 1) // 1. PLatz=Sieger, sonst Verlierer
                        echo "Sieger Spiel " . ermittle_spiel_bezeichnung($line[14]);
                    else
                        echo "Verlierer Spiel " . ermittle_spiel_bezeichnung($line[14]);
                }else { // KO-Spiel nach Gruppenphase
                    // Bezeichnung der referenzierten Gruppe ermitteln
                    echo "$line[18]. " . ermittle_gruppen_bezeichnung($line[16]);
                }
            }
            echo " -- ";
            if ($line[21] == 0) { // entspricht der Mannschaftskey dem NULL-Wert?
                echo " $line[4] ";
            } else {
                if ($line[15] != NULL) { // KO-Spiel
                    // Bezeichnung des referenzierten Spiels ermitteln
                    if ($line[19] == 1) // 1. PLatz=Sieger, sonst Verlierer
                        echo " Sieger " . ermittle_spiel_bezeichnung($line[15]);
                    else
                        echo " Verlierer " . ermittle_spiel_bezeichnung($line[15]);
                }else { // KO-Spiel nach Gruppenphase
                    // Bezeichnung der referenzierten Gruppe ermitteln
                    echo " $line[19]. " . ermittle_gruppen_bezeichnung($line[17]);
                }
            }

            echo "</option>\n";
        }
        echo "</select></td></tr></table>\n";
    }
};  // Ende Modus 0,1,2




/*
 *
 * Fall 6: Uebersicht aller Spiele
 *
 */
if ($Modus == 6) {
    // Spieltags-Auswahllbox anzeigen 1
    $query_lov = "SELECT runde_key, bezeichnung FROM runde ORDER BY sortierung";
    $result_lov = mysqli_query($connection, $query_lov)
            or die("Abfrage Spieltag-Auswahl fehlgeschlagen!");

    echo "        <input type = 'HIDDEN' name = 'anzeige' value = 'egal'>";
    // LOV-Anzeige aufbauen
    echo " Anzeige von <SELECT  name='spieltag_von' > \n";
    //
    while ($line_lov = mysqli_fetch_array($result_lov)) {
        echo "<option value='$line_lov[0]' ";
        // Der eventuell bereits eingetragene Spieler wird vorselektiert
        if ($line_lov[0] == $spieltag_von) {
            echo "selected";
        }
        echo ">$line_lov[1]</option>\n ";
    }
    echo "</SELECT>";
    // Spieltags-Auswahllbox anzeigen 2
    $query_lov = "SELECT runde_key, bezeichnung FROM runde ORDER BY sortierung";
    $result_lov = mysqli_query($connection, $query_lov)
            or die("Abfrage Spieltag-Auswahl fehlgeschlagen!");

    // LOV-Anzeige aufbauen
    echo " bis  <SELECT  name='spieltag_bis' > \n";
    //
    while ($line_lov = mysqli_fetch_array($result_lov)) {
        echo "<option value='$line_lov[0]' ";
        // Der eventuell bereits eingetragene Spieler wird vorselektiert
        if ($line_lov[0] == $spieltag_bis) {
            echo "selected";
        }
        echo ">$line_lov[1]</option>\n ";
    }
    echo "</SELECT><input type = 'SUBMIT' name = 'NAV_GO' value = ' GO! '><br><p>\n";

    // Bezeichnung, Mannschaft1, Flagge1, - , Flagge2, Mannschaft2, Ansto&szlig;, Runde, Gruppe
    echo "<table> <tr> <th></th> <th>Begegnung</th> <th> </th> <th> </th> <th> </th> <th> </th> <th>Ansto&szlig;</th> ";
    echo "<th></th> <th>Runde</th></tr>\n";
    while ($line = mysqli_fetch_array($result)) {
        /**
          Begegnung anzeigen
          Es  gibt drei Faelle
          - Gruppenspiel
          - erste KO-Runde nach Gruppenphase (Gegner errechnen sich aus Tabellen)
          - KO-Spiel

          Format: <Mannschaft mit Link> <Flagge> - <Flagge> <Mannschaft mit Link>
         * */
        /* Manschaft 1 */
        echo "<tr><td align=right>";
        if ($line[13] != NULL)
            echo "$line[13] ... ";
        echo "</td><td align='LEFT'>";
        if ($line[20] == 0) { // entspricht der Mannschaftskey dem NULL-Wert?
            echo "<a href='$line[24]' target='_blank'>$line[2]</a></td> <td> <img border=\"1\" src=" . FLAGS_PATH . $line[11] . "></td>";
        } else {
            echo "<b>";
            if ($line[14] != NULL) { // KO-Spiel
                // Bezeichnung des referenzierten Spiels ermitteln
                if ($line[18] == 1) // 1. PLatz=Sieger, sonst Verlierer
                    echo " Sieger Spiel " . ermittle_spiel_bezeichnung($line[14]);
                else
                    echo " Verlierer Spiel " . ermittle_spiel_bezeichnung($line[14]);
            }else { // KO-Spiel nach Gruppenphase
                // Bezeichnung der referenzierten Gruppe ermitteln
                echo " $line[18]. " . ermittle_gruppen_bezeichnung($line[16]);
            }
            echo "</b></td><td></td>";
        }
        echo "<td> - </td>";
        /* Manschaft 2 */
        if ($line[21] == 0) { // entspricht der Mannschaftskey dem NULL-Wert?
            echo "<td><img border=\"1\" src=" . FLAGS_PATH . $line[12] . "> </td> <td align='LEFT'><a href='$line[25]' target='_blank'> $line[4]</a></td>";
        } else {
            echo "<td></td><td><b>";
            if ($line[15] != NULL) { // KO-Spiel
                // Bezeichnung des referenzierten Spiels ermitteln
                if ($line[19] == 1) // 1. PLatz=Sieger, sonst Verlierer
                    echo " Sieger Spiel " . ermittle_spiel_bezeichnung($line[15]);
                else
                    echo " Verlierer Spiel " . ermittle_spiel_bezeichnung($line[15]);
            }else { // KO-Spiel nach Gruppenphase
                // Bezeichnung der referenzierten Gruppe ermitteln
                echo " $line[19]. " . ermittle_gruppen_bezeichnung($line[17]);
            }
            echo "</b></td>";
        }

        echo "<td>$line[9],  $line[10]</td>";
        echo "<td></td><td>$line[6]";
        if ($line[8] != NULL)
            echo " / $line[8]";
        echo "</td> </tr>\n";
    }
    echo "</table>\n";
}

/*
 *
 * Fall 7: Anzeige einer Mannschaft
 *
 */
if ($Modus == 7) {
    // Daten abholen
    $line = mysqli_fetch_array($result);

    // einfache Textfelder in Tabellenform
    echo "<table>\n";
    if ($line[13] != NULL)
        echo "  <tr><td align=\"left\">Bezeichnung: </td><td align=\"left\"> <b>$line[13] </b> </td> </tr>\n";
    echo "  <tr><td align=\"left\">Begegnung: </td><td align=\"left\"> <b>$line[2] -- $line[4]</b> </td> </tr>\n";
    echo "  <tr><td align=\"left\">Runde   </td> <td align=\"left\"> <b>$line[6]</b> </td> </tr>\n";
    echo "  <tr><td align=\"left\">Gruppe  </td> <td align=\"left\"> <b>$line[8]</b> </td> </tr>\n";
    echo "  <tr><td align=\"left\">Datum   </td> <td align=\"left\"> <b>$line[9]</b> </td> </tr>\n";
    echo "  <tr><td align=\"left\">Uhrzeit </td> <td align=\"left\"> <b>$line[10]</b> </td> </tr>\n";
    echo "</table>\n";
}





echo" </form>\n";

// falls Druckansicht anderer Footer
if (isset($_REQUEST["druck"])) {
    druck_footer($skript_name);
} else {
    frame_footer($skript_name);
}



/*
 * DB-Verbindung sauber beenden
 */
mysqli_close($connection);
?>