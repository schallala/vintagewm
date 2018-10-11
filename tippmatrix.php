<?php
SESSION_start();

// Globale Variablen einbinden
include 'functions.php';

// Name des Skripts
$skript_name = "tippmatrix.php";

// Anzeige Begrenzung
$max_anzahl_spiele = 15;
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
if (isset($_REQUEST["spiele"])) {
    $aktuelle_spiele = $_REQUEST["spiele"];
}

// falls kein Spiel selektiert wurde, das zeitlich letzte waehlen
if ($aktuelles_spiel == -1) {
    // Das letzte Spiel ermitteln
    $result = mysqli_query($connection, "SELECT spiel_key FROM gruppenspiel WHERE datum<=CURRENT_DATE ORDER BY datum DESC, spiel_key DESC")
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

$Titel = "Tipp-Matrix";





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
        . " LEFT  JOIN tips ON (spieler.spieler_key=tips.key_spieler ) ";


// Sortierung setzen
switch ($aktuelle_sortierung) {
    case 1: $order_tipps = " ORDER by spieler.spielername, spieler.vorname, gruppenspiel.datum, gruppenspiel.zeit";
        break;
    case 2: $order_tipps = " ORDER by spieler.punkte DESC,spieler.richtige_tendenz, spieler.spielername, spieler.vorname, gruppenspiel.datum, gruppenspiel.zeit";
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
<p><H4> Tippvergleich von bis zu <?php echo $max_anzahl_spiele ?>  Partien gleichzeitig.</H4>

<!-- in allen Modi ist der Inhalt ein Formular -->
<p><form action = '<?php echo $skript_name; ?>' method = 'POST'>

    <!--  Knoeppe, die immer erscheinen sollen werden oben angezeigt:      -->
    <!--  Neu, Auswahl, Uebersicht                                         -->
    <p>
<?php
if ($Modus != 999) {
    // falls nicht in der Druckansicht
    if ($Modus != 10) {
//			echo "<INPUT  type='SUBMIT' name='MODUS_DRUCK' value='Druckansicht'>\n"	;

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
        echo "<table>\n<td><b>Auswahl Begegnungen</b><br><SELECT name='spiele[]'  size=5 multiple>";
        $aktuelles_spiel = reset($aktuelle_spiele);
        while ($line = mysqli_fetch_array($result)) {
            echo "<OPTION value=$line[0]";
            if ($line[0] == $aktuelles_spiel) {
                echo " selected ";
                next($aktuelle_spiele);
                $aktuelles_spiel = pos($aktuelle_spiele);
            }
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
        echo "</SELECT></td></tr>\n";
        echo "<tr><td><b>Sortierung</b><br><SELECT name='sortierung' >";
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
        echo "</SELECT></td></tr></table>\n<INPUT  align=center type='SUBMIT' name='egal' value='Anzeigen'>\n";
    }

    /*
     *
     *  Anzeige aller ausgewaehlten Spiele
     *
     */
    $tmp_key_liste = "";
    $endergebnis = "*";
    if (isset($aktuelle_spiele)) {
        $sel_counter = 0;
        $query_selected = $query_spiel;
        $tmp_key_liste = "";
        foreach ($aktuelle_spiele as $spielkey) {
            $sel_counter++;
            if (strcmp($tmp_key_liste, "") != 0) {
                $tmp_key_liste = $tmp_key_liste . ", ";
            }
            $tmp_key_liste = $tmp_key_liste . $spielkey;
            if ($sel_counter >= $max_anzahl_spiele)
                break;
        }
        if (strcmp($tmp_key_liste, "") != 0) {
            $query_selected = $query_selected . " AND spiel.spiel_key in(" . $tmp_key_liste . ") ";
        }
        $result = mysqli_query($connection, $query_selected . $order_spiel);
        $sel_counter = 0;

        echo "<p><h4>Ausgew&auml;hlte Spiele:</h4><table border=3 rules=none >";
        $rowflag = 1;
        while ($line = mysqli_fetch_array($result)) {
            $sel_counter = $sel_counter + 1;
            // Modifizierte Darstellung bei noch nicht feststehenden Gegnern
            if ($rowflag == 1) {
                echo "<tr bgcolor=lightblue>";
                $rowflag = 0;
            } else {
                echo "<tr bgcolor=lightgray>";
                $rowflag = 1;
            }

            echo "<td><b>($sel_counter)</b></td><td> $line[7] $line[15]: "; // Spielbezeichnung
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
            echo "</td>";
            if ($line[11] == 1) {
                echo "<td>$line[3] - $line[6]</td>";
            } else {
                echo "<td>$line[9], $line[10] Uhr</td>";
            }
            echo "</tr>";
        }
        echo "</table><p>";

        /*
         *
         *  Anzeige der Tippuebersicht
         *
         */
        $endergebnis = "*";

        // Anzeige der (freigegebenen) Tipps aller Spiele
        //echo $query_tipps .  $query_tipps . " AND tips.key_spiel in(" . $tmp_key_liste . ") " 
        //. " AND gruppenspiel.spiel_key in(" . $tmp_key_liste . ") " . $order_tipps;
        $result = mysqli_query($connection, $query_tipps . " AND tips.key_spiel in(" . $tmp_key_liste . ") "
                . " LEFT JOIN gruppenspiel ON(tips.key_spiel=gruppenspiel.spiel_key ) "
                . " AND gruppenspiel.spiel_key in(" . $tmp_key_liste . ") "
                . $order_tipps) or die("Abfrage fehlgeschlagen!");
        // noch ein paar statistische Daten zaehlen
        $sieg_ms1 = 0;
        $sieg_ms2 = 0;
        $remis = 0;
        $punkter = 0;
        $hoechstpunkter = 0;
        $rowflag = 1;
        echo "<table border=3 rules=none >";
        echo "<tr  bgcolor=lightgreen> <th>Tipper</th>";
        for ($i = 1; $i <= $sel_counter; $i++) {
            echo "<th>Spiel<br>($i)</th>";
        }
        echo "</tr>";
        $letzter_spieler = -1;
        while ($line_tipps = mysqli_fetch_array($result)) {
            if ($letzter_spieler != $line_tipps[0]) {
                if ($letzter_spieler > 0) {
                    echo "</tr>";
                }
                if ($rowflag == 1) {
                    echo "<tr bgcolor=lightblue>";
                    $rowflag = 0;
                } else {
                    echo "<tr bgcolor=lightgray>";
                    $rowflag = 1;
                }
                echo "<td align='LEFT'><b><a href='tippzettel.php?spieler=$line_tipps[0]'>$line_tipps[1] $line_tipps[2]</a> </td> ";
            }
            if ($line_tipps[3] == 1 OR $line[11] == 1 OR $line[14] == 1 OR $line_tipps[0] == $user_key
                    OR $user_rolle == ADMINROLLE) { // Freigabe oder bereits ausgetragen oder Runde ist sowieso gesperrt
                if ($line_tipps[5] > -1) {
                    echo "<td  align='CENTER'>$line_tipps[5]:$line_tipps[6]</td> ";
                }
            } else {
                echo "<td  align='CENTER'> *** </td>";
            }
            $letzter_spieler = $line_tipps[0];
        }
        echo "</tr></table>";
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