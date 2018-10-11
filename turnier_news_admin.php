<?php
SESSION_start();

// Globale Variablen einbinden
include 'functions.php';

// Name des Skripts
$skript_name = "turnier_news_admin.php";

// Login pruefen
pruefe_login($skript_name);

/*
 * Modus aus evtl. Parametern setzen
 */

/*
 * Der Kniff bei Uebergabe des Spiel-Keys ist der Umweg ueber die Bezeichnung
 * der Buttons. Das Spiel kann somit erkannt werden, indem der
 * Schluessel aus dem Namen des Buttons quasi extrahiert wird (allerdings
 * sehr brute-force). Um die Suche korrekt einzuschraenken wird deshalb
 * das aktuell in der DB vorhandene Key-Intervall uebergeben.
 * Vielleicht zu kompliziert gedacht, funktioniert aber...
 */
$min_news = 0;
$max_news = 0;

if (isset($_POST["min_news_key"])) {
    $min_news = $_POST["min_news_key"];
}
if (isset($_POST["max_news_key"])) {
    $max_news = $_POST["max_news_key"];
}

// Selektion des Modus
$Modus = 0;  // keiner, Auswahlliste
$Titel = "Nachricht bearbeiten";
if (isset($_POST["MODUS_AUSWAHL"])) {
    $Modus = 0;  // Auswahlmodus
};
for ($i = $min_news; $i <= $max_news; $i++) {
    if (isset($_POST["MODUS_AENDERN_" . $i])) {
        $Modus = 2;  // News aendern
        $Titel = "Nachricht &auml;ndern";
        $news_key = $i;
    };
}
for ($i = $min_news; $i <= $max_news; $i++) {
    if (isset($_POST["MODUS_LOESCHEN_" . $i])) {
        $Modus = 3;  // News loeschen
        $Titel = "Nachricht gel&ouml;scht";
        $news_key = $i;
    };
}
if (isset($_POST["MODUS_SPEICHERN_NEU"])) {
    $Modus = 4;  // News speichern
    $Titel = "Nachricht gespeichert";
};
if (isset($_POST["MODUS_SPEICHERN_AENDERN"])) {
    $Modus = 5;  // News speichern
    $Titel = "Nachricht gespeichert";
    $news_key = $_POST["news_key"];
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

if ($user_rolle != ADMINROLLE) {
    $Modus = 999;
    $Meldung = "Nur Administratoren d&uuml;rfen die News bearbeiten!";
}

// Der eingeloggte User wird noch oefter benoetigt ...
if (!empty($_SESSION["spieler_key"])) {
    $user_key = $_SESSION["spieler_key"];
} else {
    $user_key = "nix";
}


// puefen, ob notwendige Auswahl getroffen wurde (aendern / loeschen)
if (!isset($news_key) AND ( $Modus == 2 OR $Modus == 3)) {
    $Modus = 999;
    $Meldung = "Keine News ausgew&auml;hlt!";
}


/*
 * Aktionen auf DB (SELECT, INSERT, UPDATE) vorbereiten und
 * anschlie&szlig;end ausfuehren
 */

// vielleicht kann man diese Statements nochmal verwenden ...
$hauptabfrage = "SELECT news_key, titel, text, datum, zeit" // 0, 1, 2, 3, 4
        . "  FROM news";

$order_klausel = " ORDER BY datum desc, zeit desc";

// CRs werden in <BR>s umgewandelt
if (isset($_POST["text"])) {
    $text = $_POST["text"];
    $text = str_replace("\n", "<br>", $text);
}

switch ($Modus) {
    case 0: // nix zu tun
        $query = $hauptabfrage . $order_klausel;
        break;
    case 2: $query = $hauptabfrage . " WHERE news_key = $news_key";
        break;
    case 3: $query = "DELETE FROM news WHERE news_key = $news_key";
        $Modus = 99; // danach wieder Auswahl-Modus
        break;
    case 4: $query = "INSERT INTO news (titel, text, datum, zeit)"
                . " VALUES ('" . $_POST["titel"] . "', '" . $text . "', CURRENT_DATE, CURRENT_TIME)";
        $Modus = 99; // danach wieder Auswahl-Modus
        break;
    case 5: $query = "UPDATE news SET titel = '" . $_POST["titel"]
                . "', text = '" . $text
                . "', datum = '" . $_POST["datum"]
                . "', zeit = CURRENT_TIME "
                . " WHERE news_key = $news_key";
        $Modus = 99; // danach wieder Auswahl-Modus
        break;
}


// Statement nur bei Bedarf ausfuehren !!!
if (isset($query)) {
//    echo $query;
    $result = mysqli_query($connection, $query) or die("Abfrage fehlgeschlagen!");
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
        /**
         *
         * Neueingabe immer au&szlig;er im Aendern-Modus
         *
         */
        if ($Modus != 2 AND $Modus != 999) {
            echo "<table>";

            // TITEL
            echo "<tr><td></td><td>Sowohl im Titel als auch im Text k&ouml;nnen HTML-Tags verwendet werden!</td></tr>";
            echo "<tr> <td align='LEFT'> Titel: </td> <td align='LEFT'>";
            echo "<INPUT type='TEXT' size='60' name='titel' > </td></tr>\n";
            // TEXT
            echo "<tr> <td align='LEFT'>Text: </td> <td align='LEFT'>";
            echo "<textarea cols='60' rows='5' name='text' > </textarea></td></tr>\n";

            echo "</table>";

            echo "<input type = 'SUBMIT' name = 'MODUS_SPEICHERN_NEU' value =	 'Neue Nachricht'>";
        }


        /**
         *
         * Modus 2: Aendern
         *
         */
        if ($Modus == 2) {
            $line = mysqli_fetch_array($result);
            echo "<table>";
            // TITEL
            echo "<tr><td></td><td>Sowohl im Titel als auch im Text k&ouml;nnen HTML-Tags verwendet werden!</td></tr>";
            echo "<tr> <td align='LEFT' >Titel: </td> <td bgcolor='lightgrey' align='LEFT'>";
            echo "<INPUT type='TEXT' size='60' name='titel' VALUE='$line[1]'> </td></tr>\n";
            // TEXT
            echo "<tr> <td align='LEFT'>Text: </td> <td width='500' bgcolor='lightblue' align='LEFT'>";
            // <BR>s durch CRs ersetzen
            $text = str_replace("<br>", "\n", $line[2]);
            echo "<textarea cols='60' rows='5' name='text' > $text </textarea> </td></tr>\n";
            // Datum
            echo "<tr> <td align='LEFT'>Datum: </td> <td align='LEFT' bgcolor='lightgrey'>";
            echo "<INPUT type='TEXT' size='10' name='datum' VALUE='$line[3]'> </td></tr>\n";
            echo "</table>";

            echo "<input type = 'SUBMIT' name = 'MODUS_SPEICHERN_AENDERN' value = '&Auml;nderung Speichern'>";
            echo "<input type = 'HIDDEN' name = 'news_key' value = '$line[0]'>";
            $Modus = 99;
        }


        /**
         *
         * Nach Datenmanipulation gleich wieder in den Auswahl-Modus
         *
         */
        if ($Modus == 99) {
            $result = mysqli_query($connection, $hauptabfrage . $order_klausel) or die("Abfrage fehlgeschlagen!");
            $Modus = 0;
        }



        /**
         *
         * Modus 0: Anzeige-Modus
         *
         */
        $min_news_key = 1000000000;
        $max_news_key = 0;

        if ($Modus == 0) {
            while ($line = mysqli_fetch_array($result)) {
                echo "<hr><table>";

                // TITEL
                echo "<tr> <td align='LEFT' bgcolor='lightgrey'><b><i> $line[1] </i></b></td> <td></td></tr>\n";
                // TEXT
                echo "<tr> <td align='LEFT' width='500' bgcolor='lightblue' > $line[2] </td> <td align='CENTER'>";
                // ACHTUNG !!! Ueber die Namen der Buttons wird quasi der News-Schluessel uebergeben !!!
                echo "<input type = 'SUBMIT' name = 'MODUS_AENDERN_" . $line[0] . "' value = '&Auml;ndern'><br>";
                echo "<input type = 'SUBMIT' name = 'MODUS_LOESCHEN_" . $line[0] . "' value = 'L&ouml;schen'>";
                // Ermittlung des minimalen und maximalen Keys
                if ($line[0] > $max_news_key) {
                    $max_news_key = $line[0];
                }
                if ($line[0] < $min_news_key) {
                    $min_news_key = $line[0];
                }
                echo "</td></tr>\n";
                // Datum
                echo "<tr> <td align='LEFT' bgcolor='lightgrey'>Datum: $line[3]</td>";

                echo "</table>";
            }
        }

        echo "<input type = 'HIDDEN' name = 'min_news_key' value = '$min_news_key'>";
        echo "<input type = 'HIDDEN' name = 'max_news_key' value = '$max_news_key'>";


        echo" </form>\n";
        frame_footer();


        /*
         * DB-Verbindung sauber beenden
         */
        mysqli_close($connection);
        ?>