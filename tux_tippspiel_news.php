<?php

session_start();

// Globale Variablen einbinden
include 'functions.php';

echo '<?xml version="1.0" encoding="ISO-8859-1" ?> ';
echo "\n";
echo '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom"> ';
echo "\n";

echo "<!-- ";
echo "* Die Nutzung dieser Inhalte ist ausschliesslich nicht-kommerziellen Internet- ";
echo "* Angeboten erlaubt, die Nutzung kann jederzeit von abels.kicks-ass.net untersagt ";
echo "* werden. abels.kicks-ass.net &uuml;bernimmt keinen Support, zudem wird keine Verf&uuml;gbarkeit ";
echo "* dieser XML-Datei gew&auml;hrleistet. Die Inhalte d&uuml;rfen nicht archiviert werden. ";
echo "* Weitere Informationen http://www.abels.kicks-ass.net/EM08 ";
echo "// ";

echo "  --> \n ";
echo " <channel> \n ";
echo " <atom:link href=\"" . TIPPSPIEL_HOME . "tux_tippspiel_news.php\" rel=\"self\" type=\"application/rss+xml\" />";
echo "  <title> \n" . TITEL_TIPPSPIEL . ": Aktuelle Infos</title> \n ";
echo "  <link> \n" . TIPPSPIEL_HOME . "</link> \n ";
echo "  <description> \n" . TITEL_TIPPSPIEL . ": Aktuelle Infos</description> \n ";

/*
 * Aktionen auf DB (SELECT, INSERT, UPDATE) vorbereiten und
 * anschliessend ausfuehren
 */

/*
 * 	Die letzten Logins anzeigen
 */
$query_max = "SELECT spieler, max(nummer) AS nummer from logging group by spieler order by nummer desc LIMIT 0,8";
$result_max = mysqli_query($connection, $query_max) or die("Abfrage fehlgeschlagen!");
echo " <item> \n <title> \n DIE LETZTEN LOGINS </title> \n<link> \n </link> \n<description> \n </description> \n</item> \n";
while ($line_max = mysqli_fetch_array($result_max)) {
    $query = "SELECT spieler, DATE_FORMAT(datum, '%d.%m'), TIME_FORMAT(zeit, '%H:%i'), spieler.login from logging, spieler where spieler.spieler_key=spieler AND nummer=" . $line_max[1];
    $result = mysqli_query($connection, $query) or die("Abfrage fehlgeschlagen!");

    while ($line = mysqli_fetch_array($result)) {
        echo " <item> \n <title> \n";
        echo "$line[3] am $line[1] um $line[2] ";
        echo "</title> \n";
        echo "  <link> \n  </link> \n <description> \n  </description> \n  </item> \n ";
    }
}
if (mysqli_num_rows($result) == 0) {
    echo "<title> \n keine Aktivitaet! ### ";
    echo "</title> \n";
    echo "  <link> \n  </link> \n <description> \n  </description> \n  </item> \n ";
}


/*
 * 	Die naechsten Spiele
 */
$query = "SELECT mann1.mannschaft_key, mann1.mannschaftsname, gruppenspiel.tore_ms1" // 0, 1, 2
        . "      ,mann2.mannschaft_key, mann2.mannschaftsname, gruppenspiel.tore_ms2" // 3, 4, 5
        . "      , DATE_FORMAT(gruppenspiel.datum, '%d.%m')" // 6
        . "      ,TIME_FORMAT(gruppenspiel.zeit, '%H:%i'), gruppenspiel.spiel_key, runde.freigabe " // 7, 8
        . "  FROM gruppenspiel, mannschaft mann1, mannschaft mann2, runde "
        . " WHERE mann1.mannschaft_key=gruppenspiel.key_ms1 "
        . "   AND mann2.mannschaft_key=gruppenspiel.key_ms2 "
        . "   AND gruppenspiel.status=0 "
        . "   AND gruppenspiel.spiel_art=runde.runde_key "
        . " ORDER BY datum ASC, zeit ASC  LIMIT 0,2";
$result = mysqli_query($connection, $query) or die("Abfrage2 fehlgeschlagen!");
echo " <item> \n <title> \n DIE N&auml;CHSTEN SPIELE </title> \n<link> \n </link> \n<description> \n </description> \n</item> \n";
while ($line = mysqli_fetch_array($result)) {
    echo " <item> \n <title> \n";
    echo " $line[1] gegen $line[4] spielen am $line[6] um $line[7] Uhr";
    echo "</title> \n";
    echo "  <link> \n</link> \n <description> \nDie Tipps: ";

    $query_tipps = "SELECT spieler.spieler_key, spieler.vorname, spieler.spielername, spieler.freigabe "
            . "      ,spieler.punkte, tips.tore_ms1, tips.tore_ms2, tips.erreichte_punkte, tips.tendenz "
            . "  FROM spieler "
            . " LEFT JOIN tips ON (spieler.spieler_key=tips.key_spieler AND tips.key_spiel=$line[8]) ";
    // zusaetzlich individuelle Freigabe pruefen, falls Runde noch getippt werden kann
    if ($line[9] == 1) {
        $query_tipps = $query_tipps . " WHERE spieler.freigabe = 1 ";
    }
    $query_tipps = $query_tipps . " ORDER BY punkte DESC ";
    $result2 = mysqli_query($connection, $query_tipps) or die("Abfrage2 fehlgeschlagen!");
    while ($line2 = mysqli_fetch_array($result2)) {
        echo "$line2[1] $line2[2] ($line2[4])  $line2[5]-$line2[6] ++ ";
    }
    echo "</description> \n  </item> \n ";
}
if (mysqli_num_rows($result) == 0) {
    echo "<title> \nDas Turnier ist beendet!";
    echo "</title> \n";
    echo "  <link> \n</link> \n <description> \n</description> \n  </item> \n ";
}


/*
 * 	Die letzten Ergebnisse
 */
$query = "SELECT mann1.mannschaft_key, mann1.mannschaftsname, gruppenspiel.tore_ms1" // 0, 1, 2
        . "      ,mann2.mannschaft_key, mann2.mannschaftsname, gruppenspiel.tore_ms2" // 3, 4, 5
        . "      , DATE_FORMAT(gruppenspiel.datum, '%d.%m')" // 6
        . "      ,TIME_FORMAT(gruppenspiel.zeit, '%H:%i'), gruppenspiel.spiel_key"
        . "  FROM gruppenspiel, mannschaft mann1, mannschaft mann2 "
        . " WHERE mann1.mannschaft_key=gruppenspiel.key_ms1 "
        . "   AND mann2.mannschaft_key=gruppenspiel.key_ms2 "
        . "   AND gruppenspiel.status=1 "
        . " ORDER BY gruppenspiel.datum DESC, zeit DESC LIMIT 0,2";
$result = mysqli_query($connection, $query) or die("Abfrage1 fehlgeschlagen!");
echo " <item> \n <title> \n DIE LETZTEN ERGEBNISSE </title> \n<link> \n </link> \n<description> \n Der aktuelle Ergebnisdienst </description> \n</item> \n";
while ($line = mysqli_fetch_array($result)) {

    echo " <item> \n <title> \n";
    echo " $line[1] gegen $line[4] ... $line[2] - $line[5] ";
    echo "</title> \n";

    echo "  <link> \n</link> \n <description> \nDie Tipps: ";
    $query_tipps = "SELECT spieler.spieler_key, spieler.vorname, spieler.spielername, spieler.freigabe "
            . "      ,spieler.punkte, tips.tore_ms1, tips.tore_ms2, tips.erreichte_punkte, tips.tendenz "
            . "  FROM spieler "
            . " LEFT JOIN tips ON (spieler.spieler_key=tips.key_spieler  AND tips.key_spiel=$line[8]) "
            . " ORDER BY punkte DESC ";
    $result2 = mysqli_query($connection, $query_tipps) or die("Abfrage2 fehlgeschlagen!");
    while ($line2 = mysqli_fetch_array($result2)) {
        echo "$line2[1] $line2[2] ($line2[4])  $line2[5]-$line2[6] ($line2[7]) ++ ";
    }
    echo "</description> \n  </item> \n ";
}
if (mysqli_num_rows($result) == 0) {
    echo "<item><title> \nDas Turnier wurde noch nicht gestartet!";
    echo "</title> \n";
    echo "  <link> \n  </link> \n <description> \n  </description> \n  </item> \n ";
}


/*
 * 	Die letzten News
 */
$query = "SELECT titel, DATE_FORMAT(datum, '%d.%m.%Y'), text FROM news ORDER BY datum DESC, zeit DESC LIMIT 0,2";
$result = mysqli_query($connection, $query) or die("Abfrage News fehlgeschlagen!");
echo " <item> \n <title> \n DIE LETZTEN TIPPSPIEL-NEWS </title> \n<link> \n </link> \n<description> \n Die letzten Nachrichten an alle Mittipper </description> \n</item> \n";
while ($line = mysqli_fetch_array($result)) {
    /* Umwandlung in richtige Zeilenumbrueche */
    $text = str_replace("<br>", "  ", $line[2]);
    $text = strip_tags($text);
    echo " <item> \n <title> \n";
    echo "$line[1]: $line[0]";
    echo "</title> \n";
    echo "  <link> \n  </link> \n <description> \n $text </description> \n  </item> \n ";
}
if (mysqli_num_rows($result) == 0) {
    echo "<title> \n ### keine News eingetragen! ### ";
    echo "</title> \n";
    echo "  <link> \n  </link> \n <description> \n  </description> \n  </item> \n ";
}


/*
 * 	Die letzten Beitraege im Stammtisch anzeigen
 */
if (LINK_STAMMTISCH != "") { // de-/aktivieren
    $query = "SELECT name, date, time, text FROM st_entries ORDER BY id DESC LIMIT 0,4";
    $result = mysqli_query($connection, $query) or die("Abfrage News fehlgeschlagen!");
    echo " <item> \n <title> \n LETZTE STAMMTISCH-BEITRAEGE </title> \n<link> \n </link> \n<description> \n Die letzten Nachrichten an alle Mittipper </description> \n</item> \n";
    while ($line = mysqli_fetch_array($result)) {
        echo " <item> \n <title> \n";
        echo " von $line[0] am $line[1] um $line[2]";
        echo "</title> \n";
        echo "  <link> \n  </link> \n <description> \n" . strip_tags($line[3]) . " </description> \n  </item> \n ";
    }
}

/*
 *  Die Rangliste anzeigen
 */

$query = "SELECT spieler.spieler_key, spieler.vorname, spieler.spielername  "
        . "     , spieler.punkte, spieler.exakte_tips, spieler.richtige_tendenz "
        . "     , mannschaft.mannschaftsname, mannschaft.info_link, mannschaft.STATUS "
        . "  FROM spieler "
        . "  LEFT JOIN mannschaft ON (mannschaft.mannschaft_key=spieler.weltmeister) ";

$order_klausel = " ORDER BY spieler.punkte DESC, spieler.richtige_tendenz DESC, spieler.spielername, spieler.vorname";

$query = $query . $order_klausel;
$result = mysqli_query($connection, $query) or die("Abfrage fehlgeschlagen!");
echo " <item> \n <title> \n DIE RANGLISTE </title> \n<link> \n </link> \n<description> \n </description> \n</item> \n";

$counter = 0;
$punkte_vorher = -1;
while ($line = mysqli_fetch_array($result)) {
    echo " <item> \n ";
    $counter++;
    echo "<title> \n";
    if ($punkte_vorher != $line[3]) {
        echo " Platz $counter:";
        $punkte_vorher = $line[3];
    } else {
        echo " und ";
    }
    echo " $line[1] $line[2] - $line[3] Punkte";
    echo "</title> \n";
    echo "  <link> \n</link> \n <description> \n</description> \n  </item> \n ";
}
if (mysqli_num_rows($result) == 0) {
    echo "<title> \n ### keine Spieler eingetragen! ### ";
    echo "</title> \n";
    echo "  <link> \n  </link> \n <description> \n  </description> \n  </item> \n ";
}

echo "  </channel> \n ";
echo "  </rss> \n ";


/*
 * DB-Verbindung sauber beenden
 */
mysqli_close($connection);
?>
