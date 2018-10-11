<?php
SESSION_start();

// Globale Variablen einbinden
include 'functions.php';

// Name des Skripts
$skript_name = "turnier_tabellen.php";

// Login pruefen
pruefe_login($skript_name);


/*
 * Modus aus evtl. Parametern setzen
 */

$Modus = 0;  // keiner, Auswahlliste
if (isset($_POST["MODUS_AUSWAHL"])) {
    $Modus = 0;  // Darstellung eines Datensatzes
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


$Titel = TURNIERART_KURZ . "-Tabellen";





/*
 * Aktionen auf DB (SELECT, INSERT, UPDATE) vorbereiten und
 * anschlie&szlig;end ausfuehren
 */

// vielleicht kann man diese Statements nochmal verwenden ...
$query = "SELECT gruppe.bezeichnung, mannschaft.mannschaft_key, mannschaft.mannschaftsname, mannschaft.punkte" // 0, 1, 2, 3
        . "      ,mannschaft.anz_spiele ,mannschaft.plusTore ,mannschaft.minusTore ,mannschaft.tordifferenz" // 4, 5, 6, 7
        . "      ,mannschaft.info_link, mannschaft.flagge" // 8, 9
        . "  FROM mannschaft, gruppe "
        . " WHERE mannschaft.gruppe=gruppe.gruppe_key ";

$order_klausel = " ORDER BY gruppe.sortierung, mannschaft.punkte DESC, mannschaft.tordifferenz DESC, mannschaft.plusTore DESC";


// Statement nur bei Bedarf ausfuehren !!!
if (isset($query)) {
//    echo $query;
    $result = mysqli_query($connection, $query . $order_klausel) or die("Abfrage fehlgeschlagen!");
}

frame_header($skript_name);
?>
<H3> <?php echo $Titel ?> </H3>
<H4> <?php echo $Meldung ?> </H4>

<!-- in allen Modi ist der Inhalt ein Formular -->
<p><form action = '<?php echo $skript_name; ?>' method = 'POST'>

    <!--  Knoeppe, die immer erscheinen sollen werden oben angezeigt:      -->
    <!--  Neu, Auswahl, Uebersicht                                         -->
    <p>
<?php
if ($Modus == 0) {
    $result = mysqli_query($connection, $query . $order_klausel) or die("Abfrage fehlgeschlagen!");
    $alte_gruppe = "";
    $wechsel = 0;
    while ($line = mysqli_fetch_array($result)) {
        if ($line[0] != $alte_gruppe) {
            if ($wechsel == 2) {
                echo "</table>";
            }
            echo "<h4>$line[0]</h4>";
            $alte_gruppe = $line[0];
            $wechsel = 1;
        }
        if ($wechsel == 1) {
            echo "<table><tr><th></th> <th> Mannschaft </th> <th> Spiele </th> <th> Punkte </th> <th>Tore</th> <th>Differenz</th></tr>";
            $wechsel = 2;
        }
        echo "<tr> <td align='LEFT'> <img border=\"1\" src=" . FLAGS_PATH . $line[9] . "></td><td> <a href='$line[8]'>$line[2]</a></td> <td align='CENTER'> $line[4]</td> ";
        echo "<td align='CENTER'>$line[3]</td> <td align='RIGHT'>$line[5] - $line[6]</td> <td align='CENTER'> $line[7]</td> </td> </tr>";
    }
    if ($wechsel == 2) {
        echo "</table>";
    }
}



echo" </form>\n";
frame_footer();


/*
 * DB-Verbindung sauber beenden
 */
mysqli_close($connection);
?>