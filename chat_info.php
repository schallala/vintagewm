<?php
SESSION_start();

// Globale Variablen einbinden
include 'functions.php';

// Name des Skripts
$skript_name = "chatinfo.php";

// Login pruefen
pruefe_login($skript_name);


// Die Rolle wird noch oefter benoetigt ...
if (!empty($_SESSION["rolle"])) {
    $user_rolle = $_SESSION["rolle"];
} else {
    $user_rolle = "nix";
}

$Modus = 0;

$Titel = "Infos zum Chat bei " . TITEL_TIPPSPIEL;
//nur registrierte User duerfen hier arbeiten
if (empty($_SESSION["spieler_key"])) {
    $Titel = "Infos zum Chat bei " . TITEL_TIPPSPIEL;
    $Meldung = "Dieser Bereich ist nur f&uuml;r registrierte Benutzer erreichbar! <br>Finger weg!";
    $Modus = 999;
}


/*
 * Aktionen auf DB (SELECT, INSERT, UPDATE) vorbereiten und
 * anschlie&szlig;end ausfuehren
 */

// vielleicht kann man diese Statements nochmal verwenden ...
$query = "SELECT spieler_key, vorname, spielername, email, login, icq_nummer FROM spieler WHERE  length(icq_nummer)>0 ORDER BY login";
$result = mysqli_query($connection, $query) or die("Abfrage fehlgeschlagen!bla");
frame_header($skript_name);
?>
<H3> <?php echo $Titel ?> </H3>
<H4> <?php echo $Meldung ?> </H4>


<!--  Knoeppe, die immer erscheinen sollen werden oben angezeigt:      -->
<!--  Neu, Auswahl, Uebersicht                                         -->
<p>
<?php
/*
 *
 * Fall 0: Uebersicht aller Spieler
 *
 */
if ($Modus == 0) {
    echo "<table><th> </th></tr><td align=left>Da es nur unbefriedigende L&ouml;sungen zur Realisierung von Chatsystemen im Web gibt,
   	       habe ich mich entschlossen f&uuml;r Chat-Willige eine Organisierung per ICQ durchzuf&uuml;ren.
   	       Ich habe mir das etwa so vorgestellt:<br>Leute, die sich gerne per Chat austauschen wollen, k&ouml;nnen
   	       ab sofort im Bereich [Spielerinfo] ihre ICQ-Nummer eintragen. Diese Nummer wird dann auf dieser Seite in
   	       der Liste unten ver&ouml;ffentlicht und kann von den Usern (nur registrierte) eingesehen werden.
   	       Jeder Teilnehmer mit ICQ kann nun &uuml;ber die Nummer den Mittiper in seine Favoritenliste aufnehmen
   	       (eventuell nach Authorisierung). Durch die Kontakt-Liste kann man dann feststellen, ob der betreffende
   	       Benutzer zur Zeit online ist und ihn dann in den geschlossen ICQ-Chat einladen.<p>
   	       <i>Diejenigen, die nicht wissen was ICQ ist sollten sich mal auf der <a href=http://www.icq.com>ICQ-Homepage</a>
   	       umsehen und k&ouml;nnen dort auch eine passende Version kostenlos herunterladen.</i></td><p>";
    echo "<table> <tr> <th>Login</th> <th>Name</th>\n";
    echo "<th>Email</th> <th>ICQ-Nummer</th>";
    while ($line = mysqli_fetch_array($result)) {
        echo "<tr> <td align='LEFT'>$line[4]</td> <td align='LEFT'><a href='spieler.php?spieler=$line[0]'>$line[1] $line[2]</a></td>";
        echo "<td align='LEFT'>$line[3]</td> <td><b> $line[5] </b></td>";
    }
    echo "</table>\n";
}

frame_footer();


/*
 * DB-Verbindung sauber beenden
 */
mysqli_close($connection);
?>