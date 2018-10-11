<?php
SESSION_start();

// Globale Variablen einbinden
include 'functions.php';

// Name des Skripts
$skript_name = "mannschaft.php";

// Login pruefen
pruefe_login($skript_name);

/*
 * Modus aus evtl. Parametern setzen
 */

$Modus = 0;  // keiner, Auswahlliste
if (isset($_POST["MODUS_AUSWAHL"])) {
    $Modus = 0;  // Auswahlliste wurde  explizit gewaehlt
};
if (isset($_POST["MODUS_NEU"])) {
    $Modus = 1;  // Neueingabe
};
if (isset($_POST["MODUS_AENDERN"])) {
    $Modus = 2;  // Mannschaft aendern
};
if (isset($_POST["MODUS_LOESCHEN"])) {
    $Modus = 3;  // Mannschaft loeschen
};
if (isset($_POST["MODUS_SPEICHERN_NEU"])) {
    $Modus = 4;  // Einfuegen eines neuen Datensatzes
};
if (isset($_POST["MODUS_SPEICHERN_AENDERUNG"])) {
    $Modus = 5;  // Datensatz-Aenderungen speichern
};
if (isset($_POST["MODUS_ANZEIGE"])) {
    $Modus = 6;  // Darstellung aller Datensaetze
};
if (isset($_POST["MODUS_ANZEIGE_SINGLE"])) {
    $Modus = 7;  // Darstellung eines Datensatzes
};
if (isset($_POST["MODUS_MEISTER"])) {
    $Modus = 8;  // Darstellung aller Datensaetze
};
if (isset($_POST["MODUS_MEISTER_LOESCHEN"])) {
    $Modus = 9;  // Darstellung aller Datensaetze
};


//nur Goetter duerfen hier arbeiten
if (!isset($_SESSION["rolle"]) OR $_SESSION["rolle"] != ADMINROLLE) {
    $Titel = "Mannschaftsdaten";
    $Meldung = "Dieser Bereich ist nur f&uuml;r den Admin erreichbar! <br>Finger weg!";
    $Modus = 999;
}

// Pruefung bei Aendern / Loeschen / Einzelanzeige, ob ein Datensatz ausgewaehlt wurde
if (($Modus == 2 OR $Modus == 3 OR $Modus == 7) AND ( !isset($_POST["mannschaft_key"]))) {
    $Meldung = "Es wurde keine Mannschaft ausgew&auml;hlt!";
    $Modus = 0;  // Auswahlliste erneut anzeigen
};


/*
 * Aktionen auf DB (SELECT, INSERT, UPDATE) vorbereiten und
 * anschlie&szlig;end ausfuehren
 */

// vielleicht kann man diese Statements nochmal verwenden ...
$mutter_aller_abfragen = "SELECT mannschaft_key, mannschaftsname, info_link, gruppe, bezeichnung, status, Gewinner "
        . "  FROM mannschaft "
        . "  LEFT JOIN gruppe ON (mannschaft.gruppe=gruppe.gruppe_key) ";
$order_klausel = " ORDER BY mannschaftsname";

// Im Insert- und Update-Modus m&ouml;gliche Leerfelder abfangen
IF ($Modus == 4 OR $Modus == 5) {
    if (empty($_POST["mannschaftsname"])) {
        $Meldung = "Es muss eine Mannschaftsbezeichnung angegeben werden!";
        $Modus = 999;
    };
};

// Falls ein ganz sch
if (!isset($_POST["status"])) {
    $_POST["status"] = 0;
}

// SQL-Statements zu jedem Fall erzeugen
switch ($Modus) {
    case 1 :
        $Titel = "Mannschaft neu eintragen";
        break;
    case 0:
        // Abfrage aller Datens&auml;tze
        $query = $mutter_aller_abfragen . $order_klausel;
        $Titel = "Mannschaft ausw&auml;hlen";
        break;
    case 6:
        // Abfrage aller Datens&auml;tze
        $query = $mutter_aller_abfragen . $order_klausel;
        $Titel = "&Uuml;bersicht der Mannschaften";
        break;
    case 7:
        // Abfrage eines Datensatzes
        $Titel = "Mannschaft anzeigen";
        $query = $mutter_aller_abfragen . " WHERE mannschaft_key=" . $_POST["mannschaft_key"] . $order_klausel;
        break;
    case 2:
        // Seitentitel und Meldung setzen
        $Titel = "Mannschaft bearbeiten";
        // Abfrage eines Datensatzes ueber Primary Key
        $query = $mutter_aller_abfragen . " WHERE mannschaft_key=" . $_POST["mannschaft_key"] . $order_klausel;
        break;
    case 3:
        // einen Datensatz loeschen
        $query = "DELETE FROM mannschaft WHERE mannschaft_key=" . $_POST["mannschaft_key"];
        // Seitentitel und Meldung setzen
        $Titel = "Mannschaft gel&ouml;scht";
        $Meldung = "Die Mannschaft wurde gel&ouml;scht!";
        // erneute Anzeige der Auswahlliste einleiten
        $Modus = 99;
        break;
    case 4:
        // einen neuen Datensatz in der DB speichern
        // ACHTUNG!!! Hier wird mit dem MySQL-Feature "auto-increment"
        //            ein INSERT gemacht!!!
        $query = "INSERT INTO mannschaft (mannschaftsname, info_link, gruppe, anz_spiele "
                . "                        , punkte, plusTore, minusTore, tordifferenz, status) "
                . "VALUES('" . $_POST["mannschaftsname"]
                . "', '" . $_POST["info_link"]
                . "', " . $_POST["gruppe"]
                . ", 0, 0, 0, 0, 0, 1)"; // Statistik-Werte initialisieren
        // Seitentitel und Meldung setzen
        $Titel = "Neue Mannschaft gespeichert";
        $Meldung = "Die Mannschaft wurde neu eingetragen!";
        // erneute Anzeige der Auswahlliste einleiten
        $Modus = 99;
        break;
    case 5:
        // einen bestehenden  Datensatz aktualisieren

        $query = "UPDATE mannschaft "
                . "SET mannschaftsname = '" . $_POST["mannschaftsname"]
                . "'  ,info_link       = '" . $_POST["info_link"]
                . "'  ,gruppe = " . $_POST["gruppe"]
                . "   ,STATUS = " . $_POST["status"]
                . " WHERE mannschaft_key=" . $_POST["mannschaft_key"];
        // Seitentitel und Meldung setzen
        $Titel = "Mannschaft aktualisiert";
        $Meldung = "Die Daten der Mannchaft wurden aktualisiert.";
        // erneute Anzeige der Auswahlliste einleiten
        $Modus = 99;
        break;
    case 8:
        // Meister eintragen
        $query = "UPDATE mannschaft "
                . "   SET GEWINNER = 1"
                . " WHERE mannschaft_key=" . $_POST["mannschaft_key"];
        $result = mysqli_query($connection, $query) or die("Abfrage fehlgeschlagen!bla");
        // Punkte vergeben
        $query = "UPDATE spieler "
                . "   SET punkte = punkte + 10 "
                . " WHERE weltmeister =" . $_POST["mannschaft_key"];

        // Seitentitel und Meldung setzen
        $Titel = "Meister eingetragen";
        $Meldung = "Die Daten der Mannchaft wurden aktualisiert.";
        // erneute Anzeige der Auswahlliste einleiten
        $Modus = 99;
        break;
    case 9:
        // Meister aufheben
        // Meister eintragen
        $query = "UPDATE mannschaft "
                . "   SET GEWINNER = 0"
                . " WHERE mannschaft_key=" . $_POST["mannschaft_key"];
        $result = mysqli_query($connection, $query) or die("Abfrage fehlgeschlagen!bla");
        // Punkte vergeben
        $query = "UPDATE spieler "
                . "   SET punkte = punkte - 10 "
                . " WHERE weltmeister =" . $_POST["mannschaft_key"];
        // Seitentitel und Meldung setzen
        $Titel = "Meister r&uuml;ckg&auml;ngig gemacht";
        $Meldung = "Die Daten der Mannchaft wurden aktualisiert.";
        // erneute Anzeige der Auswahlliste einleiten
        $Modus = 99;
        break;
}

// Statement nur bei Bedarf ausfuehren !!!
if (isset($query)) {
//  echo $query;
    $result = mysqli_query($connection, $query) or die("Abfrage fehlgeschlagen!hurz");
}

frame_header($skript_name);
?>
<H3> <?php echo $Titel ?> </H3>
<H4> <?php echo $Meldung ?> </H4>

<!-- in allen Modi ist der Inhalt ein Formular -->
<p><form action = '<?php echo $skript_name ?>' method = 'POST'>

    <!--  Knoeppe, die immer erscheinen sollen werden oben angezeigt:      -->
    <!--  Neu, Auswahl, Uebersicht                                         -->
    <p>
<?php
echo "        <input type = 'SUBMIT' name = 'MODUS_AUSWAHL' value = 'Auswahl'>";
if (isset($_SESSION["rolle"]) AND $_SESSION["rolle"] == ADMINROLLE) { //nur Goetter duerfen neue User anlegen
    echo "        <input type = 'SUBMIT' name = 'MODUS_NEU' value = 'Neu'>";
}
echo "        <input type = 'SUBMIT' name = 'MODUS_ANZEIGE' value = '&Uuml;bersicht'><p>";


/*
 *
 * Fall 1 UND 2: Erstellen / Bearbeiten eines Datensatzes
 * MIT Vorbelegung der Datenfelder
 *
 */
if ($Modus == 1 OR $Modus == 2) {

    echo "<table>\n";  // aus Formatierungsgruenden eine tabelle definieren
    // Im Bearbeiten-Modus werden die Felder vorbelegt
    if ($Modus == 2) {
        $line = mysqli_fetch_array($result);
        // den Primary Key versteckt uebergeben
        printf("<input type='HIDDEN' name='mannschaft_key' value='%s'>", $line[0]);
    };

    // einfache Textfelder
    echo "  <tr><td align=\"left\"> Bezeichnung</td> <td align=\"left\"> <INPUT type='TEXT' name='mannschaftsname' size='30' ";
    if (isset($line[1])) {
        echo "value='$line[1]'";
    };
    echo "> </td> </tr>\n";

    echo "  <tr><td align=\"left\"> Web-Link</td> <td align=\"left\"> <INPUT type='TEXT' name='info_link' size='50' ";
    if (isset($line[2])) {
        echo "value='$line[2]'";
    };
    echo "> </td> </tr>\n";

    // LOV fuer die EM-Gruppe erstellen
    $query_lov = "SELECT gruppe_key, bezeichnung FROM gruppe";
    $result_lov = mysqli_query($connection, $query_lov) or die("Abfrage fehlgeschlagen!");

    // LOV-Anzeige aufbauen
    echo "  <tr><td align=\"left\"> " . TURNIERART_KURZ . "-Gruppe </td> <td align=\"left\"> <SELECT  name='gruppe' size='1'> \n";
    //
    while ($line_lov = mysqli_fetch_array($result_lov)) {
        echo "<option value='$line_lov[0]' ";
        // Die eventuell bereits eingetragene Gruppe wird vorselektiert
        if (!empty($line[3]) AND $line_lov[0] == $line[3]) {
            echo "selected";
        }
        echo ">$line_lov[1]</option>\n ";
    }

    echo "</select></td>";

    // noch im Rennen?
    echo "  <tr><td align=\"left\"> Noch im Turnier?</td> <td align=\"left\"> ";
    echo "<input type='checkbox' name='status' value='1'";
    if (!empty($line[5]) AND $line[5] == 1) {
        echo " checked";
    }
    echo "> </td>\n";

    echo "</tr></table>\n";
};  // Ende Modus 1




/*
 *
 * Fall 6: Uebersicht aller Mannschaften
 *
 */
if ($Modus == 6) {
    echo "<table> <tr> <th>Bezeichnung</th> <th>Web-Link</th> <th>Gruppe</th></tr>\n";
    while ($line = mysqli_fetch_array($result)) {
        echo "<tr> <td>$line[1]</td> <td><a href='$line[2]' target='_blank'>$line[2]</a></td> <td>$line[4]</td> </tr>\n";
    }
    echo "</table>\n";
}

/*
 *
 * Fall 7: Anzeige einer Mannschaft
 *
 */
if ($Modus == 7) {
    // Daten abholen
    $line = mysqli_fetch_array($result);

    // einfache Textfelder in Tabellenform
    echo "<table>\n";
    echo "  <tr><td align=\"left\"> Bezeichnung</td> <td align=\"left\"> <b>$line[1]</b> </td> </tr>\n";
    echo "  <tr><td align=\"left\"> Web-Link   </td> <td align=\"left\"> <a href=\"$line[2]\">$line[2]</a> </td> </tr>\n";
    echo "  <tr><td align=\"left\"> Gruppe     </td> <td align=\"left\"> <b>$line[4]</b> </td> </tr>\n";
    echo "</table>\n";
}

/*
 * MODUSWECHSEL
 *
 * nach bestimmten Aktionen soll gleich wiieder die Auswahlbox erscheinen;
 * dazu muss eine erneute Abfrage der Daten stattfinden und der Modus auf 0 gesetzt werden
 */
if ($Modus == 99) {
    $result = mysqli_query($connection, $mutter_aller_abfragen . $order_klausel) or die("Abfrage fehlgeschlagen!");
    $Modus = 0;
};


/*
 *
 * Fall 0: einfache Auswahliste aller Mannschaften
 *
 */
if ($Modus == 0) {
    echo "<select size='10' name='mannschaft_key'>\n";
    while ($line = mysqli_fetch_array($result)) {
        echo "<option value=$line[0]>" . $line[1] . " -- " . $line[4] . "</option>\n";
    }
    echo "</select>\n";
}

echo "<p><p>\n";  // ein bisschen Platz bitte
/*
 *
 * Jetzt wird bestimmt welche Buttons in den einzelnen Modi dargestellt werden
 *
 */

switch ($Modus) {
    // Bei Anzeige der Auswahllisten sind folgende Aktionen moeglich:
    case 0 :
        echo " <input type = 'SUBMIT' name = 'MODUS_AENDERN' value = '&Auml;ndern'> \n";
        echo " <input type = 'SUBMIT' name = 'MODUS_LOESCHEN' value = 'L&ouml;schen'> \n";
        echo " <input type = 'SUBMIT' name = 'MODUS_ANZEIGE_SINGLE' value = 'Anzeigen'> \n";
        break;
    // Es gibt zwei verschiedene Buttons zum Speichern, da der vorige Modus bekannt sein muss
    case 1 :
        // Neue Eintraege speichern
        echo " <input type = 'SUBMIT' name = 'MODUS_SPEICHERN_NEU' value = 'Speichern'> \n";
        break;
    case 2;
        // Aenderungen speichern
        echo " <input type = 'SUBMIT' name = 'MODUS_SPEICHERN_AENDERUNG' value = 'Speichern'> \n";
        break;
}
IF ($Modus == 2 OR $Modus == 7) {
    // testen, ob schon ein Gewinner eingetragen
    $query_gewinner = "SELECT count(1) FROM mannschaft WHERE gewinner=1";
    mysqli_query($connection, $query_gewinner) or die("Abfrage fehlgeschlagen!");
    $line_gewinner = mysqli_fetch_array($result);
    IF ($line_gewinner[0] == 0) {
        echo "<br>        <input type = 'SUBMIT' name = 'MODUS_MEISTER' value = '$line[1] ist Meister!'><p>";
    } elseif ($line[6] == 1) {
        echo "<br>        <input type = 'SUBMIT' name = 'MODUS_MEISTER_LOESCHEN' value = '$line[1] ist doch kein Meister'><p>";
    }
}

echo" </form>\n";
frame_footer();

/*
 * DB-Verbindung sauber beenden
 */
mysqli_close($connection);
?>