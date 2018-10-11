<?php
/* * ***************
 *
 *  START HAUPTTEIL
 *
 * *************** */

session_start();

// Globale Variablen einbinden
include 'functions.php';

// Name des Skripts
$skript_name = "ergebnisse.php";

// Login pruefen
pruefe_login($skript_name);


/*
 * Modus aus evtl. Parametern setzen
 */

$Modus = 0;  // keiner, Auswahlliste
if (isset($_POST["MODUS_AUSWAHL"])) {
    $Modus = 0;  // Auswahlmodus
};
if (isset($_POST["MODUS_EINTRAGEN"])) {
    $Modus = 1;  // Spielergebnis eintragen
    $Meldung = "Spielergebnis eingetragen!";
    if (!empty($_POST["spiel_key_neu"])) {
        $gewaehltes_spiel = $_POST["spiel_key_neu"];
    } else {
        $Meldung = "Es wurde kein Spiel ausgew&auml;hlt!";
        $Modus = 0;
    }
};
if (isset($_POST["MODUS_LOESCHEN"])) {
    $Modus = 2;  // Spielergebnis loeschen
    $Meldung = "Spielergebnis gel&ouml;scht!";
    if (!empty($_POST["spiel_key_loeschen"])) {
        $gewaehltes_spiel = $_POST["spiel_key_loeschen"];
    } else {
        $Meldung = "Es wurde kein Spiel ausgew&auml;hlt!";
        $Modus = 0;
    }
};
if (isset($_POST["MODUS_NEU_BERECHNUNG"])) {
    $Meldung = "Alle Tipper-Punkte wurden neu berechnet!";
    auswertung_spielerpunkte(-1);
    $Modus = 0;  // Auswahlmodus
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

$Titel = "Spielergebnisse bearbeiten";


// sicherstellen, dass nur Admins und Hiwis arbeiten koennen
if ($user_rolle != ADMINROLLE AND $user_rolle != HIWIROLLE) {
    $Meldung = "Spielergebnisse d&uuml;rfen nur durch Admins bearbeitet werden!";
    $Modus = 999;
}




/*
 * Aktionen auf DB (SELECT, INSERT, UPDATE) vorbereiten und
 * anschliessend ausfuehren
 */

// Auswahl aller ausgetragenen Spiele
$query_spiele = "SELECT mann1.mannschaft_key, mann1.mannschaftsname, gruppenspiel.tore_ms1" // 0, 1, 2
        . "      ,mann2.mannschaft_key, mann2.mannschaftsname, gruppenspiel.tore_ms2" // 3, 4, 5
        . "      ,runde.bezeichnung, gruppe.bezeichnung, DATE_FORMAT(gruppenspiel.datum, '%d.%m.%Y')" // 6, 7, 8
        . "      ,TIME_FORMAT(gruppenspiel.zeit, '%H:%i'), gruppenspiel.spiel_key, gruppenspiel.spiel_art" // 9, 10, 11
        . "      ,runde.runde_key" // 12
        . "  FROM gruppenspiel JOIN runde JOIN mannschaft mann1 JOIN mannschaft mann2 "
        . "  LEFT JOIN gruppe ON (gruppenspiel.gruppe=gruppe.gruppe_key) "
        . " WHERE runde.runde_key=gruppenspiel.spiel_art "
        . "   AND mann1.mannschaft_key=gruppenspiel.key_ms1 "
        . "   AND mann2.mannschaft_key=gruppenspiel.key_ms2 ";



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
$punkte_sieg = PUNKTE_SIEG;
$punkte_remis = PUNKTE_REMIS;

/*
 *
 * Fall 1: Ergebnis eintragen / Tipppunkte vergeben
 *
 */
if ($Modus == 1 AND isset($gewaehltes_spiel)) {
    // Ergebnis-Variablen analysieren
    if (!empty($_POST["tore_mannschaft_1"])) {
        $tore_mannschaft1 = $_POST["tore_mannschaft_1"];
    } else {
        $tore_mannschaft1 = 0;
    }
    if (!empty($_POST["tore_mannschaft_2"])) {
        $tore_mannschaft2 = $_POST["tore_mannschaft_2"];
    } else {
        $tore_mannschaft2 = 0;
    }

    // Ergebnis eintrage und alle abhaengigen Massnahmen einleiten
    eintragung_spielergebnis($gewaehltes_spiel, $tore_mannschaft1, $tore_mannschaft2);


    // danach noch Spieler-Punkte updaten
    $Modus = 998;
} // Ende Modus 1



/*
 *
 * Fall 2: Ergebnis annulieren / Tipppunkte zuruecknehmen
 *
 */

if ($Modus == 2) {
    // fehlende Infos ermitteln
    $query = "SELECT key_ms1, key_ms2, spiel_art, tore_ms1, tore_ms2, tendenz, gruppe " // 0, 1, 2, 3, 4, 5
            . "    FROM gruppenspiel WHERE spiel_key=" . $gewaehltes_spiel;
    $result = mysqli_query($connection, $query) or die("Abfrage fehlgeschlagen!");
    $line = mysqli_fetch_array($result);
    $mannschaft1 = $line[0];
    $mannschaft2 = $line[1];
    $turnierrunde = $line[2];
    $tore_mannschaft1 = $line[3];
    $tore_mannschaft2 = $line[4];
    $tendenz = $line[5];
    $gruppe = $line[6];
    // Punkte vergeben
    if ($tendenz == 1) {
        $punkte_mannschaft1 = $punkte_sieg;
        $punkte_mannschaft2 = 0;
    };
    if ($tendenz == 2) {
        $punkte_mannschaft1 = 0;
        $punkte_mannschaft2 = $punkte_sieg;
    };
    if ($tendenz == 0) {
        $punkte_mannschaft1 = $punkte_remis;
        $punkte_mannschaft2 = $punkte_remis;
    };

    $null_mannschaft = ermittle_null_mannschaft();

    // bei KO-Spielen, die automatische Substitution der Mannschaft rueckgaengig machen
    $query = "UPDATE gruppenspiel SET key_ms1=$null_mannschaft WHERE auto_ko_fk_ms1=$gewaehltes_spiel";
    $result = mysqli_query($connection, $query) or die("UPDATE gruppenspiel! (UNDO KO");
    $query = "UPDATE gruppenspiel SET key_ms2=$null_mannschaft WHERE auto_ko_fk_ms2=$gewaehltes_spiel";
    $result = mysqli_query($connection, $query) or die("UPDATE gruppenspiel! (UNDO KO");

    // falls es sich um ein Gruppenspiel handelt, alle Folgepartien ruecksetzen
    if ($gruppe != NULL) {
        $query = "UPDATE gruppenspiel SET key_ms1=$null_mannschaft WHERE auto_gruppe_fk_ms1=$gruppe";
        $result = mysqli_query($connection, $query) or die("UPDATE gruppenspiel! (UNDO Gruppe");
        $query = "UPDATE gruppenspiel SET key_ms2=$null_mannschaft WHERE auto_gruppe_fk_ms2=$gruppe";
        $result = mysqli_query($connection, $query) or die("UPDATE gruppenspiel! (UNDO Gruppe");
    }

    // Mannschaftsstatistik aktualisieren
    if ($gruppe != NULL) {
        $query = "UPDATE mannschaft SET anz_spiele = anz_spiele -1, punkte = punkte - $punkte_mannschaft1, plusTore=plusTore - $tore_mannschaft1"
                . "       , minusTore = minusTore - $tore_mannschaft2, tordifferenz = tordifferenz - $tore_mannschaft1 + $tore_mannschaft2"
                . " WHERE mannschaft_key = $mannschaft1";
        $result = mysqli_query($connection, $query) or die("UPDATE mannschaft fehlgeschlagen!");
        $query = "UPDATE mannschaft SET anz_spiele = anz_spiele -1, punkte = punkte - $punkte_mannschaft2, plusTore=plusTore - $tore_mannschaft2"
                . "       , minusTore = minusTore - $tore_mannschaft1, tordifferenz = tordifferenz - $tore_mannschaft2 + $tore_mannschaft1"
                . " WHERE mannschaft_key = $mannschaft2";
        $result = mysqli_query($connection, $query) or die("UPDATE mannschaft fehlgeschlagen!");
    }

    // Tipps zuruecksetzen
    $query = "UPDATE tips SET erreichte_punkte=0 "
            . " WHERE key_spiel = " . $gewaehltes_spiel;
    $result = mysqli_query($connection, $query) or die("Abfrage fehlgeschlagen!");
    // Spiel aktualisieren
    $query = "UPDATE gruppenspiel SET tore_ms1=NULL, tore_ms2=NULL "
            . "                       ,tendenz=NULL ,status=0 "
            . " WHERE spiel_key = " . $gewaehltes_spiel;
    $result = mysqli_query($connection, $query) or die("Abfrage fehlgeschlagen!");


    // danach noch Spieler-Punkte updaten (ALLE!)
    auswertung_spielerpunkte(-1);
    $Modus = 0;
};  // Ende Modus 2


/*
 *
 * Modus998: Update der Spieler-Punkte vornehmen
 *
 */
if ($Modus == 998) {

    // Spieler-Statistik auf aktuellen Stand bringen
    auswertung_spielerpunkte($gewaehltes_spiel);

    // danach wieder in Anzeige-Modus zurueckkehren
    $Modus = 0;
} // Ende Modus 1


/*
 *
 * Fall 0: Auswahlanzeige
 *
 */
if ($Modus == 0) {
    // Auswahlabschnitt: Neues Ergebnis eintragen
    echo "<hr><h3> Neues Ergebnis eintragen</h3>\n";
    echo " <input type = 'SUBMIT' name = 'MODUS_NEU_BERECHNUNG' value = 'Spielerpunkte neu berechnen!'><br><br>\n";
    echo "Bitte zun&auml;chst die Partie in der Liste ausw&auml;hlen, deren Ergebnis gespeichert werden soll.\n";
    echo "Anschlie&szlig;end die Anzahl der Tore der erst genannten Mannschaft in das Feld <i>Tore MS 1</i> eintragen und analog dazu die Tore der zweiten Mannschaft in das andere Feld eingeben.<br>\n";
//    echo "Durch Bet&auml;tigen des Buttons <i>Ergebnis speichern</i> wird die Eingabe abgeschlossen und das Ergebnis gespeichert.<br><br>\n";
    // Abfrage basteln: Status nicht gesetzt, naechste Spiele zuerst anzeigen
    $query = $query_spiele
            . "   AND gruppenspiel.status =0 " // Spiel wurde noch nicht ausgetragen
            . " ORDER BY gruppenspiel.datum asc, gruppenspiel.zeit asc";
    $result = mysqli_query($connection, $query) or die("Abfrage fehlgeschlagen!");
    // Auswahlliste anzeigen
    echo "<table>";
    echo "<tr><td><select size='10' name='spiel_key_neu'>\n";
    while ($line = mysqli_fetch_array($result)) {
        echo "<option value=$line[10]>$line[8] -- $line[6]"; // Runde
        // Falls Gruppenspiel, dann Gruppe anzeigen
        if (!empty($line[7])) {
            echo " / $line[7]";
        }
        echo ": $line[1] - $line[4] </option>\n";
    }
    echo "</select><td>\n";
    // Eingeabefelder und Button
    echo "<td align='CENTER'>";
    echo "Tore MS 1: <INPUT type='TEXT' name='tore_mannschaft_1' size='2'> <br>";
    echo "Tore MS 2: <INPUT type='TEXT' name='tore_mannschaft_2' size='2'> <br>";
    echo " <input type = 'SUBMIT' name = 'MODUS_EINTRAGEN' value = 'Ergebnis eintragen'> </td></tr>\n";
    echo "</table>";


    // Auswahlabschnitt: Zweite Box mit bereits ausgetragenen Spielen
    echo "<hr><h3> Ergebnis annulieren</h3>\n";
    echo "Zum L&ouml;schen eines Ergebnisses die entsprechende Partie in der Box markieren und anschlie&szlig;end den Button <i>Ergebnis l&ouml;schen</i> bet&auml;tigen.<br><br>\n";
    // Abfrage basteln: Status gesetzt, zuletztausgetragene Spiele zuerst anzeigen
    $query = $query_spiele
            . "   AND gruppenspiel.status = 1 " // Spiel wurde ausgetragen
            . " ORDER BY gruppenspiel.datum desc, gruppenspiel.zeit desc";
    $result = mysqli_query($connection, $query) or die("Abfrage fehlgeschlagen!");
    // Auswahlliste anzeigen
    echo "<table>";
    echo "<tr><td><select size='10' name='spiel_key_loeschen'>\n";
    while ($line = mysqli_fetch_array($result)) {
        echo "<option value=$line[10]>$line[8] -- $line[6]"; // Runde
        // Falls Gruppenspiel, dann Gruppe anzeigen
        if (!empty($line[7])) {
            echo " / $line[7]";
        }
        echo ": $line[1] - $line[4] ($line[2] - $line[5]) </option>\n";
    }
    echo "</select></td>\n";
    // Eingeabefelder und Button
    echo "<td align='CENTER'>";
    echo " <input type = 'SUBMIT' name = 'MODUS_LOESCHEN' value = 'Ergebnis l&ouml;schen'> </td></tr>\n";
    echo "</table>";
    echo "</hr>";
};  // Ende Modus 0



echo" </form>\n";
frame_footer();


/*
 * DB-Verbindung sauber beenden
 */
mysqli_close($connection);
?>