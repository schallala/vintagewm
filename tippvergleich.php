<?php
SESSION_start();

// Globale Variablen einbinden
include 'functions.php';

// Name des Skripts
$skript_name = "tippvergleich.php";

// Login pruefen
pruefe_login($skript_name);


/*
 * Modus aus evtl. Parametern setzen
 */

$Modus = 0;  // keiner, Auswahlliste
if (isset($_POST["MODUS_AUSWAHL"])) {
    $Modus = 0;  // Darstellung eines Datensatzes
};
if (isset($_POST["MODUS_DRUCK"])) {
    $Modus = 10;  // Darstellung eines Datensatzes
};

/*
 * einige Infos zum User und angeforderten Spieler ermitteln
 */

$aktuelles_spiel = -1;
// ein Spiel muss ausgewaehlt sein
if (isset($_REQUEST["spiel"])) {
    $aktuelles_spiel = $_REQUEST["spiel"];
}

// falls kein Spiel selektiert wurde, das zeitlich letzte waehlen
if ($aktuelles_spiel == -1) {
    // Das letzte Spiel ermitteln
    $result = mysqli_query($connection, "SELECT spiel_key FROM gruppenspiel WHERE datum<=CURRENT_DATE ORDER BY datum desc, abs(hour(current_time)-(hour(zeit) + 1)) aSC, spiel_key DESC")
            or die("Abfrage Vorselektion fehlgeschlagen!");
    if (mysqli_num_rows($result) > 0) {
        $line = mysqli_fetch_array($result);
        $aktuelles_spiel = $line[0];
    }
}

$aktuelle_sortierung = 1;
// ein Spiel muss ausgewaehlt sein
if (isset($_POST["sortierung"])) {
    $aktuelle_sortierung = $_POST["sortierung"];
}

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
}

if ($user_rolle == 'nix') {
    $Meldung = "Nicht angemeldete User haben hier keinen Zugriff!";
    $Modus = 999;
}

$Titel = "Tippvergleich";





/*
 * SELECTs vorbereiten und
 */

// vielleicht kann man diese Statements nochmal verwenden ...
$query_spiel = "SELECT spiel.spiel_key" // 0
        . "      ,mann1.mannschaft_key, mann1.mannschaftsname, spiel.tore_ms1" // 1, 2, 3
        . "      ,mann2.mannschaft_key, mann2.mannschaftsname, spiel.tore_ms2" // 3, 5, 6
        . "      ,runde.bezeichnung, gruppe.bezeichnung, DATE_FORMAT(spiel.datum, '%d.%m.%Y')" // 7, 8, 9
        . "      ,TIME_FORMAT(spiel.zeit, '%H:%i'), spiel.status, mann1.info_link, mann2.info_link" // 10, 11, 12, 13
        . "      ,runde.freigabe" // 14
        . "      , spiel.bezeichnung, spiel.auto_ko_fk_ms1, spiel.auto_ko_fk_ms2 " // 13, 14, 15
        . "      , spiel.auto_gruppe_fk_ms1, spiel.auto_gruppe_fk_ms2 " // 16, 17
        . "      , spiel.auto_platz_ms1, spiel.auto_platz_ms2 " // 18, 19
        . "      , mann1.is_null, mann2.is_null " // 20, 21
        . "  FROM gruppenspiel AS spiel JOIN runde JOIN mannschaft mann1 JOIN mannschaft mann2 "
        . "  LEFT JOIN gruppe ON (spiel.gruppe=gruppe.gruppe_key) "
        . " WHERE runde.runde_key=spiel.spiel_art "
        . "   AND mann1.mannschaft_key=spiel.key_ms1 "
        . "   AND mann2.mannschaft_key=spiel.key_ms2 ";

$query_spiel_einzel = "   AND spiel.spiel_key=" . $aktuelles_spiel;

$order_spiel = " ORDER BY spiel.datum ,spiel.zeit";

$query_tipps = "SELECT spieler.spieler_key, spieler.vorname, spieler.spielername, spieler.freigabe "
        . "      ,spieler.punkte, tips.tore_ms1, tips.tore_ms2, tips.erreichte_punkte, tips.tendenz "
        . "  FROM spieler "
        . " LEFT JOIN tips ON (spieler.spieler_key=tips.key_spieler AND tips.key_spiel=$aktuelles_spiel) ";

// Sortierung setzen
switch ($aktuelle_sortierung) {
    case 1: $order_tipps = " ORDER by spieler.spielername, spieler.vorname";
        break;
    case 2: $order_tipps = " ORDER by spieler.punkte DESC,spieler.richtige_tendenz, spieler.spielername, spieler.vorname";
        break;
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
        echo "<INPUT  type='SUBMIT' name='MODUS_DRUCK' value='Druckansicht'>\n";

        /*
         *
         *  Auswahlboxen Spiel und Sortierung
         *
         */
        // Das letzte Spiel ermitteln
        $result = mysqli_query($connection, "SELECT spiel_key FROM gruppenspiel WHERE datum<=CURRENT_DATE AND zeit<=CURRENT_TIME ORDER BY datum DESC, zeit DESC")
                or die("Abfrage Vorselektion fehlgeschlagen!");
        if (mysqli_num_rows($result) > 0) {
            $line = mysqli_fetch_array($result);
            if ($aktuelles_spiel == -1) {
                $aktuelles_spiel = $line[0];
            }
        }

        $result = mysqli_query($connection, $query_spiel . $order_spiel)
                or die("Abfrage fehlgeschlagen!");
        echo "<table>\n<td><b>Begegnung</b><br><SELECT name='spiel'>";
        while ($line = mysqli_fetch_array($result)) {
            echo "<OPTION value=$line[0]";
            if ($line[0] == $aktuelles_spiel)
                echo " selected ";

            // Modifizierte Darstellung bei noch nicht feststehenden Gegnern
            echo ">$line[7] $line[15]: "; // Spielbezeichnung
            if ($line[22] == 0) { // entspricht der Mannschaftskey dem NULL-Wert?
                echo $line[2];
            } else {
                if ($line[16] != NULL) { // KO-Spiel
                    // Bezeichnung des referenzierten Spiels ermitteln
                    if ($line[20] == 1) // 1. PLatz=Sieger, sonst Verlierer
                        echo "Sieger Spiel " . ermittle_spiel_bezeichnung($line[16]);
                    else
                        echo "Verlierer Spiel " . ermittle_spiel_bezeichnung($line[16]);
                }else { // KO-Spiel nach Gruppenphase
                    // Bezeichnung der referenzierten Gruppe ermitteln
                    echo "$line[20]. " . ermittle_gruppen_bezeichnung($line[18]);
                }
            }
            echo " -- ";
            if ($line[23] == 0) { // entspricht der Mannschaftskey dem NULL-Wert?
                echo " $line[5] ";
            } else {
                if ($line[17] != NULL) { // KO-Spiel
                    // Bezeichnung des referenzierten Spiels ermitteln
                    if ($line[20] == 1) // 1. PLatz=Sieger, sonst Verlierer
                        echo " Sieger " . ermittle_spiel_bezeichnung($line[17]);
                    else
                        echo " Verlierer " . ermittle_spiel_bezeichnung($line[17]);
                }else { // KO-Spiel nach Gruppenphase
                    // Bezeichnung der referenzierten Gruppe ermitteln
                    echo " $line[21]. " . ermittle_gruppen_bezeichnung($line[19]);
                }
            }
            echo "</OPTION>\n";
        }
        echo "</SELECT></td>\n";
        echo "<td><b>Sortierung</b><br><SELECT name='sortierung'>";
        echo "<OPTION value='1'";
        if ($aktuelle_sortierung == 1) {
            echo " selected ";
        }
        echo ">Tipper alphabetisch</OPTION>\n";
        echo "<OPTION value='2'";
        if ($aktuelle_sortierung == 2) {
            echo " selected ";
        }
        echo ">Tipper Punkte</OPTION>\n";
        echo "</SELECT></td>\n<td><INPUT  type='SUBMIT' name='egal' value='Start'></td></tr>\n</table>\n";
    }

    /*
     *
     *  Anzeige der Tippuebersicht
     *
     */
    $endergebnis = "*";
    if ($aktuelles_spiel > -1) {
        // Der Kopf mit Daten zum Spiel
        $result = mysqli_query($connection, $query_spiel . $query_spiel_einzel) or die("Abfrage fehlgeschlagen! " . $query_spiel . $query_spiel_einzel);
        $line = mysqli_fetch_array($result);
        // Begegnung (ggf. mit Ergebnis
        echo "<h3> ";
        // Modifizierte Darstellung bei noch nicht feststehenden Gegnern
        if ($line[22] == 0) { // entspricht der Mannschaftskey dem NULL-Wert?
            echo $line[2];
        } else {
            if ($line[16] != NULL) { // KO-Spiel
                // Bezeichnung des referenzierten Spiels ermitteln
                if ($line[20] == 1) // 1. PLatz=Sieger, sonst Verlierer
                    echo "Sieger Spiel " . ermittle_spiel_bezeichnung($line[16]);
                else
                    echo "Verlierer Spiel " . ermittle_spiel_bezeichnung($line[16]);
            }else { // KO-Spiel nach Gruppenphase
                // Bezeichnung der referenzierten Gruppe ermitteln
                echo "$line[20]. " . ermittle_gruppen_bezeichnung($line[18]);
            }
        }
        echo " - ";
        if ($line[23] == 0) { // entspricht der Mannschaftskey dem NULL-Wert?
            echo " $line[5] ";
        } else {
            if ($line[17] != NULL) { // KO-Spiel
                // Bezeichnung des referenzierten Spiels ermitteln
                if ($line[20] == 1) // 1. PLatz=Sieger, sonst Verlierer
                    echo " Sieger " . ermittle_spiel_bezeichnung($line[17]);
                else
                    echo " Verlierer " . ermittle_spiel_bezeichnung($line[17]);
            }else { // KO-Spiel nach Gruppenphase
                // Bezeichnung der referenzierten Gruppe ermitteln
                echo " $line[21]. " . ermittle_gruppen_bezeichnung($line[19]);
            }
        }
        if ($line[11] == 1) {
            $endergebnis = "$line[3] - $line[6]";
            echo "... $endergebnis";
        }
        // Runde (ggf. mit Gruppe)
        echo "</h3>\n<h4>Runde: $line[7]";
        if (!empty($line[8])) {
            echo " - $line[8]";
        }
        echo "</h4>";
        // Datum und Zeit
        echo "<h4>Ansto&szlig;: $line[9],  $line[10]</h4>\n";

        // Anzeige der (freigegebenen) Tipps aller Spiele
        $result = mysqli_query($connection, $query_tipps . $order_tipps) or die("Abfrage fehlgeschlagen!");
        // noch ein paar statistische Daten zaehlen
        $sieg_ms1 = 0;
        $sieg_ms2 = 0;
        $remis = 0;
        $punkter = 0;
        $hoechstpunkter = 0;
        echo "<table>";
        echo "<tr> <th>Tipper</th> <th>$line[2] - $line[5]</th> <th>Tendenz</th> <th>erreichte<br>Punkte</th> <th>Punkte<br>Gesamt</th><th> . </th></tr>";
        while ($line_tipps = mysqli_fetch_array($result)) {
            echo "<tr> <td align='LEFT'><b><a href='tippzettel.php?spieler=$line_tipps[0]'>$line_tipps[1] $line_tipps[2]</a> </td> <td  align='CENTER'>";
            if ($line_tipps[3] == 1 OR $line[11] == 1 OR $line[14] == 0 OR $user_rolle == ADMINROLLE) { // Freigabe oder bereits ausgetragen oder Runde ist sowieso gesperrt
                echo "$line_tipps[5] - $line_tipps[6]</td><td  align='CENTER'>$line_tipps[8]</td> ";
            } else {
                echo "* * *</td><td align='CENTER'> * </td>";
            }
            echo " <td align='CENTER'>";
            // Punkte grafisch anzeigen
            echo "$line_tipps[7]</td>";
            echo "<td align='CENTER'>$line_tipps[4]</td>\n";
            if ($line_tipps[7] >= PUNKTE_RICHTIGE_TENDENZ) {
                $punkter ++;
                if ($line_tipps[7] == PUNKTE_KOMPLETT) {
                    $hoechstpunkter ++;
                }
            }
            switch ($line_tipps[8]) {
                case 0: $remis++;
                    break;
                case 1: $sieg_ms1++;
                    break;
                case 2: $sieg_ms2++;
                    break;
            }
            echo "<td>\n";
            if ($line[11] == 1) {
                if ($line_tipps[7] == 0) {  // keine Punkte
                    echo "<img src='" . GRAFIK_NULL_PUNKTE . "'>";
                } else if ($line_tipps[7] < PUNKTE_RICHTIGE_TENDENZ) { // Gummi-Punkt
                    echo " <img src='" . GRAFIK_GUMMI_PUNKT . "'>";
                } else if ($line_tipps[7] == PUNKTE_KOMPLETT) {   // richtig!
                    echo " <img src='" . GRAFIK_EXAKTER_TIPP . "'>";
                } else {
                    echo " <img src='" . GRAFIK_RICHTIGE_TENDENZ . "'>"; // wenigstens etwas
                }
            }
            echo "</td> </tr>\n";
        }
        echo "</table>";
        // Kleine Punkte-Zusammenfassung falls Spiel stattgefunden hat
        if ($line[11] == 1) {
            echo "<p><i>$punkter Tipper lagen in der Tendenz richtig, davon sagten $hoechstpunkter das richtige Ergebnis voraus!";
        }
        if (!empty($sieg_ms1) OR ! empty($sieg_ms2) OR ! empty($remis)) {
            echo "<p><b><i>Tippzusammenfassung:</i></b><br>";
            echo "<table><tr><td align='LEFT'>";
            echo "<p><b>$sieg_ms1</b> Leute setzten auf Sieg f&uuml;r <b>$line[2]</b>.<br>";
            echo "<b>$sieg_ms2</b> Leute setzten auf Sieg f&uuml;r <b>$line[5]</b>.<br>";
            echo "<b>$remis</b> Leute tippten auf ein <b>Unentschieden</b>.";
            echo "</td</tr></table>";
            // Tipp-Statistik
            $result = mysqli_query($connection, "SELECT concat( tore_ms1, ' - ', tore_ms2 ) , count( 1 ) FROM tips WHERE key_spiel=$aktuelles_spiel GROUP BY 1 ORDER BY 2 DESC  ") or die("Abfrage fehlgeschlagen!");
            echo "<p><b><i>Die Ergebnisse sind folgenderma&szlig;en verteilt:</i></b><br>";
            echo "<table><tr><td align='LEFT'>";
            while ($line = mysqli_fetch_array($result)) {
                echo "Das Ergebnis <b>$line[0]</b> wurde <b>$line[1]</b> -mal getippt. </font><br>";
            }
            echo "</td</tr></table>";
        }
    }
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