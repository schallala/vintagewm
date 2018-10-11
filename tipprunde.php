<?php
SESSION_start();

// Globale Variablen einbinden
include 'functions.php';

// Name des Skripts
$skript_name = "tipprunde.php";

// Login pruefen
pruefe_login($skript_name);


/*
 * Modus aus evtl. Parametern setzen
 */

$Modus = 0;  // keiner, Auswahlliste
if (isset($_POST["MODUS_AUSWAHL"])) {
    $Modus = 0;  // Darstellung eines Datensatzes
};
if (isset($_POST["MODUS_RUNDE_SETZEN"])) {
    $Modus = 1;  // Darstellung eines Datensatzes
    $Meldung = "Die aktuelle Runde wurde gesetzt!";
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


$Titel = "Tipprunde festlegen";

if ($user_rolle != ADMINROLLE) {
    $Meldung = "Nur Administratoren d&uuml;rfen die Tipprunde festlegen!";
    $Modus = 999;
}



/*
 * SELECTs vorbereiten und
 */

// vielleicht kann man diese Statements nochmal verwenden ...
$query_runde = "SELECT runde_key, bezeichnung, freigabe" // 0, 1, 2
        . "  FROM runde "
        . " ORDER BY sortierung";



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
/*
 *
 *  Auswahlboxen Spiel und Sortierung
 *
 *  ACHTUNG AENDERUNG: Runden werden nicht mehr einzeln freigegeben sondern immer ab einer
 *                     bestimmten Runde, d.h. wenn die Vorrunde freigegeben ist, dann auch alle
 * 					   folgenden. WICHTIG: Hierdurch muessen die Runden-Keys in ihrer zeitlichen
 *                     Abfolge definiert werden (aufsteigend)
 *
 */
if ($Modus == 1) {
    // allen die Freigabe entziehen
    $sql = "UPDATE runde SET freigabe = 0";
    $result = mysqli_query($connection, $sql)
            or die("Abfrage fehlgeschlagen!");
    // Freigabe fuer die gewaehlte Runde setzen
    $sql = "UPDATE runde SET freigabe = 1 WHERE runde_key >= " . $_POST["tipprunde"];
    $result = mysqli_query($connection, $sql)
            or die("Abfrage fehlgeschlagen!");
    // alle Tipps zur Einsicht freigeben
    // muss ja gar nicht - toll wie weotsichtig ich programmiere ;-)
    /*
      $sql = "UPDATE spieler SET freigabe = 1";
      $result = mysqli_query($connection, $sql)
      or die ("Abfrage fehlgeschlagen!");
     */
    // Auswhl wieder anzeigen
    $Modus = 0;
}

/*
 *
 *  Auswahlboxen Spiel und Sortierung
 *
 */
if ($Modus == 0) {
    $result = mysqli_query($connection, $query_runde) or die("Abfrage fehlgeschlagen!");
    echo "<table>\n<tr><td align='LEFT'><b>Bitte die aktuelle Tipprunde w&auml;hlen, f&uuml;r die <br>die Teilnehmer ihre Tipps abgeben sollen.<br> Alle davor stattfindenden Runden sind fortan f&uuml;r &Auml;nderungen gesperrt.</b></td></tr>";
    echo "<td align='LEFT'><SELECT name='tipprunde'>";
    echo "<OPTION value='999'>Tipps komplett sperren</OPTION>";
    while ($line = mysqli_fetch_array($result)) {
        echo "<OPTION value=$line[0]";
        if ($line[2] == 1) {
            echo " selected ";
        }
        echo ">ab $line[1]</OPTION>\n";
    }
    echo "</SELECT></td></tr>\n";
    echo "<tr>Aktuell gesetzt sind:<br>";
    $result = mysqli_query($connection, $query_runde) or die("Abfrage fehlgeschlagen!");
    while ($line = mysqli_fetch_array($result)) {
        echo "$line[1] -> $line[2]<br>";
    }
    echo "</tr><tr><td><INPUT  type='SUBMIT' name='MODUS_RUNDE_SETZEN' value='Auswahl setzen'></td></tr>\n</table>\n";
}


echo" </form>\n";
frame_footer();


/*
 * DB-Verbindung sauber beenden
 */
mysqli_close($connection);
?>