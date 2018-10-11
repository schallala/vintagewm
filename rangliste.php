<?php
SESSION_start();

// Globale Variablen einbinden
include 'functions.php';

// Name des Skripts
$skript_name = "rangliste.php";

// Login pruefen
pruefe_login($skript_name);


/*
 * Modus aus evtl. Parametern setzen
 */

$Modus = 0;  // keiner, Auswahlliste
if (isset($_POST["MODUS_ANZEIGE"])) {
    $Modus = 0;  // Anzeigemodus
};

// ausgewaehlte Benutzergruppe auslesen
if (!empty($_REQUEST["benutzergruppe"]))
    $aktuelle_gruppe = $_REQUEST["benutzergruppe"];
else
    $aktuelle_gruppe = 0;

/*
 * einige Infos zum User und angeforderten Spieler ermitteln
 */

// Die Rolle wird noch oefter benoetigt ...
if (!empty($_SESSION["rolle"])) {
    $user_rolle = $_SESSION["rolle"];
} else {
    $user_rolle = "nix";
}

$Titel = "Tipper-Rangliste";




/*
 * Aktionen auf DB (SELECT, INSERT, UPDATE) vorbereiten und
 * anschlie&szlig;end ausfuehren
 */

// Auswahl aller ausgetragenen Spiele
$query = "SELECT spieler.spieler_key, spieler.vorname, spieler.spielername  "
        . "     , spieler.punkte, spieler.exakte_tips, spieler.richtige_tendenz "
        . "     , mannschaft.mannschaftsname, mannschaft.info_link, mannschaft.STATUS, mannschaft.flagge "
        . "  FROM spieler "
        . "  LEFT JOIN mannschaft ON (mannschaft.mannschaft_key=spieler.weltmeister) ";

$order_klausel = " ORDER BY spieler.punkte DESC, spieler.richtige_tendenz DESC, spieler.spielername, spieler.vorname";

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
 * Fall 0: Auswahlanzeige
 *
 */
if ($Modus == 0) {
    // Randgruppenauswahl
    $result_lov = mysqli_query($connection, "SELECT gruppen_key, bezeichnung FROM benutzergruppe ORDER BY position")
            or die("Abfrage LOV Benutzergruppe fehlgeschlagen!");
    // LOV-Anzeige aufbauen
    echo "Jetzt mit Randgruppen-Feature! <SELECT  name='benutzergruppe' > \n";
    while ($line_lov = mysqli_fetch_array($result_lov)) {
        echo "<option value='$line_lov[0]' ";
        // Der eventuell bereits eingetragene Spieler wird vorselektiert
        if ($line_lov[0] == $aktuelle_gruppe) {
            echo "selected";
        }
        echo ">$line_lov[1] </option>\n ";
    }
    echo "</SELECT> \n";
    echo "<input type = 'SUBMIT' name = 'MODUS_AUSWAHL' value = 'Auswahl'>";

    // abhaengig von Benutzergruppe Abfrage anpassen
    if ($aktuelle_gruppe != 0) {
        $query = $query . " WHERE spieler.spieler_key IN (SELECT key_spieler FROM spieler_benutzergruppe WHERE key_gruppe = " . $aktuelle_gruppe . ")";
    }
    // Abfrage basteln: Status nicht gesetzt, naechste Spiele zuerst anzeigen
    $query = $query . $order_klausel;
    $result = mysqli_query($connection, $query) or die("Abfrage fehlgeschlagen!");

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
        echo "<td align='CENTER'>$line[5]</td> <td><img border=\"1\" src=" . FLAGS_PATH . "$line[9] ></td><td><a href='$line[7]'>$line[6]</a></td>";
        // falls Team ausgeschieden, kleine Grafik anzeigen
        if ($line[8] == 0) {
            echo "<td><img src='" . GRAFIK_AUSGESCHIEDEN . "' height=15 width=15> <b>R.I.P.</b></td>";
        }
        echo "</tr>";
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