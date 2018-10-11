<?php
SESSION_start();

// Globale Variablen einbinden
include 'functions.php';

// Name des Skripts
$skript_name = "message.php";

// Login pruefen
pruefe_login($skript_name);

/*
 * Modus aus evtl. Parametern setzen
 */

/*
 * Der Kniff bei Uebergabe des message-Keys ist der Umweg ueber die Bezeichnung
 * der Buttons. Das Spiel kann somit erkannt werden, indem der
 * Schluessel aus dem Namen des Buttons quasi extrahiert wird (allerdings
 * sehr brute-force). Um die Suche korrekt einzuschraenken wird deshalb
 * das aktuell in der DB vorhandene Key-Intervall uebergeben.
 * Vielleicht zu kompliziert gedacht, funktioniert aber...
 */
$min_message = 0;
$max_message = 0;
if (isset($_POST["min_message_key"])) {
    $min_message = $_POST["min_message_key"];
}
if (isset($_POST["max_message_key"])) {
    $max_message = $_POST["max_message_key"];
}

// in welchem Korb befinden wir uns?
if (isset($_POST["postkorb"])) {
    $postkorb = $_POST["postkorb"];
} else { // falls nicht angegeben --> Eingangspostkasten
    $postkorb = "Posteingang";
}

// Selektion des Modus
$Modus = 0;  // keiner, Auswahlliste
$Titel = TURNIERART_KURZ . "-Postkasten";
if (isset($_POST["MODUS_POSTEINGANG"])) {
    $Modus = 0;  // Auswahlmodus
    $postkorb = "Posteingang";
};
if (isset($_POST["MODUS_POSTAUSGANG"])) {
    $Modus = 1;  // Auswahlmodus
    $postkorb = "Postausgang";
};
for ($i = $min_message; $i <= $max_message; $i++) {
    if (isset($_POST["MODUS_LOESCHEN_" . $i])) {
        $Modus = 3;  // message loeschen
        $Meldung = "Nachricht gel&ouml;scht!";
        $message_key = $i;
    };
}
for ($i = $min_message; $i <= $max_message; $i++) {
    if (isset($_POST["MODUS_REPLY_" . $i])) {
        $Modus = 2;  // message loeschen
        $Meldung = "Nachricht beantworten";
        $message_key = $i;
    };
}
if (isset($_POST["MODUS_SPEICHERN_NEU"])) {
    $Modus = 4;  // News speichern
    $Meldung = "Nachricht versendet!";
};


/*
 * einige Infos zum User und angeforderten Spieler ermitteln
 */

// Die Rolle wird noch oefter benoetigt ...
if (!empty($_SESSION["rolle"])) {
    $user_rolle = $_SESSION["rolle"];
} else {
    $user_rolle = "nix";
    $Modus = 999;
    $Meldung = "Nur registrierte Benutzer k&ouml;nnen Post senden und empfangen!";
}

// Der eingeloggte User wird noch oefter benoetigt ...
if (!empty($_SESSION["spieler_key"])) {
    $user_key = $_SESSION["spieler_key"];
} else {
    $user_key = "nix";
}


// puefen, ob notwendige Auswahl getroffen wurde (loeschen)
if (!isset($message_key) AND ( $Modus == 3)) {
    $Modus = 999;
    $Meldung = "Keine Nachricht ausgew&auml;hlt!";
}


/*
 * Aktionen auf DB (SELECT, INSERT, UPDATE) vorbereiten und
 * anschlie&szlig;end ausfuehren
 */

// vielleicht kann man diese Statements nochmal verwenden ...
$hauptabfrage = "SELECT message.message_key, message.titel, message.text, " // 0, 1, 2
        . "       DATE_FORMAT(message.datum, '%d.%m.%Y'), TIME_FORMAT(message.zeit, '%H:%i Uhr')"
        . "     , spieler.vorname, spieler.spielername" // 3, 4, 5, 6
        . "  FROM message, spieler ";

$where_eingang = " WHERE message.user_nach = $user_key "
        . "   AND spieler.spieler_key = message.user_von ";

$where_ausgang = " WHERE message.user_von = $user_key "
        . "   AND spieler.spieler_key = message.user_nach ";

$order_klausel = " ORDER BY message.datum desc, message.zeit desc";

// Textfeld sollte erst einmal leer sein
$erster_versuch = "";
// Suject sollte erst einmal leer sein
$subject = "";
// Empfaengervariable definieren
$empfaenger = -1;


switch ($Modus) {
    case 0: // Posteingang
        $query = $hauptabfrage . $where_eingang . $order_klausel;
        break;
    case 1: // Postausgang
        $query = $hauptabfrage . $where_ausgang . $order_klausel;
        break;
    case 2: // Antwort-Modus: Es wird der Empfaenger vorselektiert
        $result = mysqli_query($connection, "SELECT user_von, titel FROM message WHERE message_key = $message_key")
                or die("Abfrage1 fehlgeschlagen!");
        $line = mysqli_fetch_array($result);
        $empfaenger = $line[0];
        $subject = "RE: $line[1]";
        $Modus = 99; // danach wieder Auswahl-Modus
        break;
    case 3: $query = "DELETE FROM message WHERE message_key = $message_key";
        $Modus = 99; // danach wieder Auswahl-Modus
        break;
    case 4: // falls kein Empfaenger angegeben
        if ($_POST["adressat"] == -1) {
            $erster_versuch = $_POST["text"]; // uebergebenen Text wiederholen
            $Meldung = "Bitte Empf&auml;nger angeben! Nachricht wurde nicht verschickt!";
        } else { // alles OK, kann eingetragen werden
            // Problemzeichen ersetzen
            $titeltext = $_POST["titel"];
            $titeltext = str_replace("'", "\"", $titeltext); // ' bringt Probleme
            ersetze_umlaute($titeltext);
            $text = $_POST["text"];
            $text = str_replace("'", "\"", $text); // ' bringt Probleme
            $text = str_replace("\n", "<br>", $text);
            ersetze_umlaute($text);
            $query = "INSERT INTO message (user_von, user_nach, titel, text, datum, zeit)"
                    . " VALUES ('$user_key', '" . $_POST["adressat"] . "', '" . $titeltext . "', '" . $text . "', CURRENT_DATE, CURRENT_TIME)";
        }
        $Modus = 99; // danach wieder Auswahl-Modus
        break;
}


// Statement nur bei Bedarf ausfuehren !!!
if (isset($query)) {
//    echo $query;
    $result = mysqli_query($connection, $query) or die("Abfrage fehlgeschlagen! Query=$query");
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
        echo "<br><h4><i>$postkorb</i></h4>";
        echo "<input type = 'SUBMIT' name = 'MODUS_POSTEINGANG' value =	 'Posteingang'>";
        echo "<input type = 'SUBMIT' name = 'MODUS_POSTAUSGANG' value =	 'Postausgang'>";

        /**
         *
         * Neueingabe
         *
         */
        if ($Modus != 999) {
            echo "<table>";
            echo "<tr><td></td><td>Sowohl im Titel als auch im Text k&ouml;nnen HTML-Tags verwendet werden!</td></tr>";
            // ADRESSAT
            $result_lov = mysqli_query($connection, "SELECT spieler_key, vorname, spielername FROM spieler ORDER BY spielername, vorname")
                    or die("Abfrage2 fehlgeschlagen!");
            // LOV-Anzeige aufbauen
            echo "<tr> <td align='LEFT'>Empf&auml;nger: </td> <td align='LEFT'>";
            echo "<SELECT  name='adressat' > \n";
            // einen Dummy-Eintrag, damit Fehladressaten vermieden werden
            echo "<option value='-1'>!!! bitte w&auml;hlen !!!</option>\n";
            while ($line_lov = mysqli_fetch_array($result_lov)) {
                echo "<option value='$line_lov[0]' ";
                // vorselektieren, falls Antwort
                if ($empfaenger == $line_lov[0]) {
                    echo " selected ";
                }
                echo ">$line_lov[1] $line_lov[2]</option>\n ";
            }
            echo "</select></td></tr>";
            // TITEL
            echo "<tr> <td align='LEFT'> Titel: </td> <td align='LEFT'>";
            echo "<INPUT type='TEXT' size='60' name='titel' value = '$subject'> </td></tr>\n";
            // TEXT
            echo "<tr> <td align='LEFT'>Text: </td> <td align='LEFT'>";
            echo "<textarea cols='60' rows='5' name='text' >$erster_versuch</textarea></td></tr>\n";

            echo "</table>";

            echo "<input type = 'SUBMIT' name = 'MODUS_SPEICHERN_NEU' value =	 'Nachricht senden'>";
        }


        /**
         *
         * Nach Datenmanipulation gleich wieder in den Auswahl-Modus
         *
         */
        if ($Modus == 99) {
            // den Postkorb beruecksichtigen
            if ($postkorb == "Posteingang") {
                $abfrage = $hauptabfrage . $where_eingang . $order_klausel;
            } else {
                $abfrage = $hauptabfrage . $where_ausgang . $order_klausel;
            }
            $result = mysqli_query($connection, $abfrage) or die("Abfrage3 fehlgeschlagen!");
            $Modus = 0;
        }



        /**
         *
         * Modus 0: Anzeige-Modus
         *
         */
        $min_message_key = 1000000000;
        $max_message_key = 0;

        if ($Modus == 0 OR $Modus == 1) {
            //echo "<div align='LEFT'>";
            while ($line = mysqli_fetch_array($result)) {
                echo "<hr><table>";

                // SENDER / EMPFAENGE
                echo "<tr> <td align='LEFT' bgcolor='lightgrey'><b><i>";
                if ($postkorb == "Posteingang") {
                    echo "Von: ";
                } else {
                    echo "An: ";
                }
                echo "$line[5] $line[6] </i></b></td> <td></td></tr>\n";
                // TITEL
                echo "<tr> <td align='LEFT' bgcolor='lightgrey'><b> $line[1] </b></td> <td></td></tr>\n";
                // TEXT
                echo "<tr> <td align='LEFT' width='500' bgcolor='lightblue' > $line[2] </td> <td align='CENTER'>";
                // ACHTUNG !!! Ueber die Namen der Buttons wird quasi der message-Schluessel uebergeben !!!
                echo "<input type = 'SUBMIT' name = 'MODUS_LOESCHEN_" . $line[0] . "' value = 'L&ouml;schen'>";
                // im Posteingang Antworten ermoeglichen
                if ($postkorb == "Posteingang") {
                    echo "<br><input type = 'SUBMIT' name = 'MODUS_REPLY_" . $line[0] . "' value = 'Antworten'>";
                }
                // Ermittlung des minimalen und maximalen Keys
                if ($line[0] > $max_message_key) {
                    $max_message_key = $line[0];
                }
                if ($line[0] < $min_message_key) {
                    $min_message_key = $line[0];
                }
                echo "</td></tr>\n";
                // Datum
                echo "<tr> <td align='LEFT' bgcolor='lightgrey'>$line[3], $line[4]</td>";

                echo "</table>";
            }
            //echo "</div>";
        }

        if ($postkorb == "Posteingang") {
            // alle Post als gelesen markieren
            $result = mysqli_query($connection, "UPDATE message SET gelesen=1 WHERE user_nach = $user_key ");
        }

        echo "<input type = 'HIDDEN' name = 'postkorb' value = '$postkorb'>";
        echo "<input type = 'HIDDEN' name = 'min_message_key' value = '$min_message_key'>";
        echo "<input type = 'HIDDEN' name = 'max_message_key' value = '$max_message_key'>";


        echo" </form>\n";
        frame_footer();


        /*
         * DB-Verbindung sauber beenden
         */
        mysqli_close($connection);
        ?>