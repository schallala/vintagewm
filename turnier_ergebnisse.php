<?php
SESSION_start();

// Globale Variablen einbinden
include 'functions.php';

// Name des Skripts
$skript_name = "turnier_ergebnisse.php";

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


$Titel = TURNIERART_KURZ . "-Ergebnisse";





/*
 * Aktionen auf DB (SELECT, INSERT, UPDATE) vorbereiten und
 * anschlie&szlig;end ausfuehren
 */

// vielleicht kann man diese Statements nochmal verwenden ...
$query = "SELECT gruppenspiel.spiel_key" // 0
        . "      ,mann1.mannschaft_key, mann1.mannschaftsname, gruppenspiel.tore_ms1" // 1, 2, 3
        . "      ,mann2.mannschaft_key, mann2.mannschaftsname, gruppenspiel.tore_ms2" // 3, 5, 6
        . "      ,runde.bezeichnung, gruppe.bezeichnung, DATE_FORMAT(gruppenspiel.datum, '%d.%m.%Y')" // 7, 8, 9
        . "      ,TIME_FORMAT(gruppenspiel.zeit, '%H:%i'), gruppenspiel.status, mann1.info_link, mann2.info_link" // 10, 11, 12, 13
        . "      , mann1.flagge, mann2.flagge" // 14, 15
        . "      , gruppenspiel.bezeichnung, gruppenspiel.auto_ko_fk_ms1, gruppenspiel.auto_ko_fk_ms2 " // 16, 17, 18
        . "      , gruppenspiel.auto_gruppe_fk_ms1, gruppenspiel.auto_gruppe_fk_ms2 " // 19, 20
        . "      , gruppenspiel.auto_platz_ms1, gruppenspiel.auto_platz_ms2 " // 21, 22
        . "      , mann1.is_null, mann2.is_null " // 23, 24
        . "  FROM gruppenspiel JOIN runde JOIN mannschaft mann1 JOIN mannschaft mann2 "
        . "  LEFT JOIN gruppe ON (gruppenspiel.gruppe=gruppe.gruppe_key) "
        . " WHERE runde.runde_key=gruppenspiel.spiel_art "
        . "   AND mann1.mannschaft_key=gruppenspiel.key_ms1 "
        . "   AND mann2.mannschaft_key=gruppenspiel.key_ms2 ";

$order_klausel = " ORDER BY runde.sortierung, gruppe.sortierung, gruppenspiel.datum, gruppenspiel.zeit";


// Statement nur bei Bedarf ausfuehren !!!
if (isset($query)) {
//    echo $query;
    $result = mysqli_query($connection, $query) or die("Abfrage fehlgeschlagen!");
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
<p><form action = '<?php echo $skript_name; ?>' method = 'POST' >

    <!--  Knoeppe, die immer erscheinen sollen werden oben angezeigt:      -->
    <!--  Neu, Auswahl, Uebersicht                                         -->
    <p>
<?php
if ($Modus != 10) {
    echo "<p><INPUT  type='SUBMIT' name='MODUS_DRUCK' value='Druckansicht'><p>\n";
}

$result = mysqli_query($connection, $query . $order_klausel) or die("Abfrage fehlgeschlagen!");
$alte_runde = "";
$alte_gruppe = "";
$wechsel = 0;
while ($line = mysqli_fetch_array($result)) {
    if ($line[7] != $alte_runde) {
        if ($wechsel == 2) {
            echo "</table>";
        }
        echo "<h3>$line[7]</h3>";
        $alte_runde = $line[7];
        $wechsel = 1;
    }
    if ($line[8] != $alte_gruppe) {
        if ($wechsel == 2) {
            echo "</table>";
        }
        echo "<h4>$line[8]</h4>";
        $alte_gruppe = $line[8];
        $wechsel = 1;
    }
    if ($wechsel == 1) {
        // Name, Mannschaft1, Flagge1, - , Flagge2, Mannschaft2, Ergebnis, Ansto&szlig;
        echo "<table><tr> <th></th> <th>Begegnung</th> <th> </th><th> </th><th> </th><th> </th> <th>Ergebnis</th> <th>Ansto&szlig;</th>";
        $wechsel = 2;
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
    if ($line[16] != NULL)
        echo "$line[16] ... ";
    echo "</td><td align='LEFT'>";
    if ($line[23] == 0) { // entspricht der Mannschaftskey dem NULL-Wert?
        echo "<a href='$line[12]' target='_blank'>$line[2]</a></td> <td> <img border=\"1\" src=" . FLAGS_PATH . $line[14] . "> </td>";
    } else {
        echo "<b>";
        if ($line[17] != NULL) { // KO-Spiel
            // Bezeichnung des referenzierten Spiels ermitteln
            if ($line[21] == 1) // 1. PLatz=Sieger, sonst Verlierer
                echo " Sieger Spiel " . ermittle_spiel_bezeichnung($line[17]);
            else
                echo " Verlierer Spiel " . ermittle_spiel_bezeichnung($line[17]);
        }else { // KO-Spiel nach Gruppenphase
            // Bezeichnung der referenzierten Gruppe ermitteln
            echo " $line[21]. " . ermittle_gruppen_bezeichnung($line[19]);
        }
        echo "</b></td> <td> </td>";
    }
    echo "<td> - </td><td>";
    /* Manschaft 2 */
    if ($line[24] == 0) { // entspricht der Mannschaftskey dem NULL-Wert?
        echo "<img border=\"1\" src=" . FLAGS_PATH . $line[15] . "> </td> <td align='LEFT'><a href='$line[13]' target='_blank'> $line[5]</a></td>";
    } else {
        echo "<b></td><td>";
        if ($line[18] != NULL) { // KO-Spiel
            // Bezeichnung des referenzierten Spiels ermitteln
            if ($line[22] == 1) // 1. PLatz=Sieger, sonst Verlierer
                echo " Sieger Spiel " . ermittle_spiel_bezeichnung($line[18]);
            else
                echo " Verlierer Spiel " . ermittle_spiel_bezeichnung($line[18]);
        }else { // KO-Spiel nach Gruppenphase
            // Bezeichnung der referenzierten Gruppe ermitteln
            echo " $line[22]. " . ermittle_gruppen_bezeichnung($line[20]);
        }
        echo "</b></td>";
    }

    if ($line[11] == 1)
        echo "<td align='CENTER'><b><a href='tippvergleich.php?spiel=$line[0]'>$line[3] - $line[6]</a></b>"; // Ergebnis anzeigen, falls Spiel ausgetragen
    else
        echo "<td align='CENTER'><b><a href='tippvergleich.php?spiel=$line[0]'> ? - ? </a></b>"; // Ergebnis anzeigen, falls Spiel ausgetragen


    echo "</td> <td>$line[9],  $line[10] Uhr</td> </tr>";
}
if ($wechsel == 2) {
    echo "</table>";
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