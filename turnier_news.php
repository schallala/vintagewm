<?php
SESSION_start();

// Globale Variablen einbinden
include 'functions.php';

// Name des Skripts
$skript_name = "turnier_news.php";

// Login pruefen
pruefe_login($skript_name);

$Titel = TURNIERART_KURZ . "-News";



/*
 * einige Infos zum User und angeforderten Spieler ermitteln
 */

// Die Rolle wird noch oefter benoetigt ...
if (!empty($_SESSION["rolle"])) {
    $user_rolle = $_SESSION["rolle"];
} else {
    $user_rolle = "nix";
}


/*
 * Aktionen auf DB (SELECT, INSERT, UPDATE) vorbereiten und
 * anschlie&szlig;end ausfuehren
 */

// vielleicht kann man diese Statements nochmal verwenden ...
$hauptabfrage = "SELECT news_key, titel, text, DATE_FORMAT(datum, '%d.%m.%Y'), zeit" // 0, 1, 2, 3, 4
        . "  FROM news";

$order_klausel = " ORDER BY datum desc, zeit desc";

$query = $hauptabfrage . $order_klausel;

$result = mysqli_query($connection, $query) or die("Abfrage fehlgeschlagen!");


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
/**
 *
 * Anzeige
 *
 */
while ($line = mysqli_fetch_array($result)) {
    echo "<p><center><table border='black'>";

    // TITEL
    echo "<tr> <td align='LEFT' bgcolor='lightgrey'><b><i> $line[1] </i></b></td> <td></td></tr>\n";
    // TEXT
    echo "<tr> <td width='500' align='LEFT' bgcolor='lightblue'> $line[2] </td> </tr>\n";
    // Datum
    echo "<tr> <td align='LEFT' bgcolor='lightgrey'>$line[3]</td>";

    echo "</table></center>";
}


echo" </form>\n";
frame_footer();


/*
 * DB-Verbindung sauber beenden
 */
mysqli_close($connection);
?>