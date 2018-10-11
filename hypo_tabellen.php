<?php
SESSION_start();

// Globale Variablen einbinden
include 'functions.php';

// Name des Skripts
$skript_name = "hypo_tabellen.php";

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

// per GET oder POST uebergebene Spieler auswerten
if (!empty($_REQUEST["spieler"])) {
    $spieler = $_REQUEST["spieler"];
} else {
    $spieler = -1;
    $Meldung = "Der Spieler muss angegeben werden!";
    $Modus = 999;
}

if ($user_rolle == 'nix') {
    $Meldung = "Nicht angemeldete User haben hier keinen Zugriff!";
    $Modus = 999;
}

// feststellen, ob ob ueberhaupt eine Runde freigegeben ist
$query = "SELECT 1 FROM runde WHERE runde_key=1 AND freigabe=1";
$result = mysqli_query($connection, $query) or die("Abfrage fehlgeschlagen!");
if (mysqli_num_rows($result) > 0) {
    $runde_freigabe = 1;
} else {
    $runde_freigabe = 0;
}



$Titel = "Getippte Tabellen";

/*
 * eventuell noch eine Spielerauswahl-Box
 */

/*
 * pruefen, ob Tipps vorhanden sind
 */



frame_header($skript_name);
?>
<H3> <?php echo $Titel ?> </H3>
<H4> <?php echo $Meldung ?> </H4>

<!--.  in allen Modi ist der Inhalt ein Formular -->
<p><form action = '<?php echo $skript_name; ?>' method = 'POST'>

    <!--  Knoeppe, die immer erscheinen sollen werden oben angezeigt:      -->
    <!--  Neu, Auswahl, Uebersicht                                         -->
    <p>
<?php
$punkte_sieg = PUNKTE_SIEG;
$punkte_remis = PUNKTE_REMIS;

// Tipper-Auswahlbox
// Spieler-Auswahllbox anzeigen
$result = mysqli_query($connection, "SELECT spieler_key, vorname, spielername FROM spieler ORDER BY spielername, vorname")
        or die("Abfrage fehlgeschlagen!");

// LOV-Anzeige aufbauen
echo "<br><SELECT  name='spieler' > \n";
//
while ($line = mysqli_fetch_array($result)) {
    echo "<option value='$line[0]' ";
    // Der eventuell bereits eingetragene Spieler wird vorselektiert
    if ($line[0] == $spieler) {
        echo "selected";
    }
    echo ">$line[1] $line[2]</option>\n ";
}
echo "</select><input type = 'SUBMIT' name = 'MODUS_AUSWAHL' value = 'Auswahl'>";
echo "<br><br>";

$query = "SELECT vorname, spielername, freigabe FROM spieler WHERE spieler_key=$spieler";
//	echo $query;
$result = mysqli_query($connection, $query) or die("Ermittlung des Tippers fehlgeschlagen!");
$line = mysqli_fetch_array($result);
// falls keine Freigabe, dann Einsicht verhindern
if ($runde_freigabe == 1 AND $line[2] != 1 AND $user_key != $spieler AND $user_rolle != ADMINROLLE) {
    $Meldung = "Der ausgew&auml;hlte Spieler hat seine Tipps noch nicht feigegeben!";
    $Modus = 999;
}

if ($Modus == 0) {
    // Tipper ermitteln
    echo "<b><i>So w&uuml;rden die Vorrundentabellen aussehen wenn die Tipps von $line[0] "
    . "$line[1] in Erf&uuml;llung gehen w&uuml;den:</i></b>";
    /**
     * Hypotabelle fuer den User initial erzeugen
     */
    $query = "CREATE TEMPORARY TABLE hypomtab" . $spieler . "  SELECT * FROM mannschaft";
    $result = mysqli_query($connection, $query) or die("Erstellen HYPOMTAB fehlgeschlagen!");
    // initialisieren
    $query = "UPDATE hypomtab" . $spieler . " SET anz_spiele=3, punkte=0, plusTore=0, minusTore=0, tordifferenz=0";
    $result = mysqli_query($connection, $query) or die("Update HYPOMTAB fehlgeschlagen!");

    /**
     * Ergebnisse des Spielers eintragen
     */
    // zunaechst beteiligte Mannschaften und Tipps ermitteln
    $query = "SELECT gruppenspiel.key_ms1, tips.tore_ms1, gruppenspiel.key_ms2, tips.tore_ms2, tips.tendenz"
            . "  FROM gruppenspiel, tips "
            . " WHERE gruppenspiel.spiel_key = tips.key_spiel "
            . "   AND gruppenspiel.spiel_art = 1 " // nur Vorrundenspiele
            . "   AND tips.key_spieler = $spieler";
    $result = mysqli_query($connection, $query) or die("Abfrage fehlgeschlagen!");
    while ($line = mysqli_fetch_array($result)) {
        // abhaengig vom Ergebnis die Punkte und Tore verteilen
        switch ($line[4]) {
            // Unentschieden
            case 0 : $sql = "UPDATE hypomtab" . $spieler . " SET "
                        . "       punkte    = punkte + $punkte_remis "
                        . ",      plusTore  = plusTore + $line[1] "
                        . ",      minusTore = minusTore + $line[1] "
                        . " WHERE mannschaft_key = $line[0] "
                        . "    OR mannschaft_key = $line[2] ";
                $result_sql = mysqli_query($connection, $sql) or die("UPDATE Unentschieden fehlgeschlagen!");
                break;
            // Sieg Mannschaft 1
            case 1 : $sql = "UPDATE hypomtab" . $spieler . " SET "  // Update Mannschaft 1
                        . "       punkte    = punkte + $punkte_sieg "
                        . ",      plusTore  = plusTore + $line[1] "
                        . ",      minusTore = minusTore + $line[3] "
                        . ",      tordifferenz = tordifferenz + ($line[1] - $line[3]) "
                        . " WHERE mannschaft_key = $line[0] ";
                $result_sql = mysqli_query($connection, $sql) or die("UPDATE Mannschaft1 fehlgeschlagen!");
                $sql = "UPDATE hypomtab" . $spieler . " SET " // Update Mannschaft 2
                        . "       plusTore  = plusTore + $line[3] "
                        . ",      minusTore = minusTore + $line[1] "
                        . ",      tordifferenz = tordifferenz + ($line[3] - $line[1]) "
                        . " WHERE mannschaft_key = $line[2] ";
                $result_sql = mysqli_query($connection, $sql) or die("UPDATE Mannschaft2 fehlgeschlagen!");
                break;
            // Sieg Mannschaft 2
            case 2 : $sql = "UPDATE hypomtab" . $spieler . " SET "  // Update Mannschaft 2
                        . "       punkte    = punkte + $punkte_sieg "
                        . ",      plusTore  = plusTore + $line[3] "
                        . ",      minusTore = minusTore + $line[1] "
                        . ",      tordifferenz = tordifferenz + ($line[3] - $line[1]) "
                        . " WHERE mannschaft_key = $line[2] ";
                $result_sql = mysqli_query($connection, $sql) or die("UPDATE Mannschaft2 fehlgeschlagen!");
                $sql = "UPDATE hypomtab" . $spieler . " SET " // Update Mannschaft 1
                        . "       plusTore  = plusTore + $line[1] "
                        . ",      minusTore = minusTore + $line[3] "
                        . ",      tordifferenz = tordifferenz + ($line[1] - $line[3]) "
                        . " WHERE mannschaft_key = $line[0] ";
                $result_sql = mysqli_query($connection, $sql) or die("UPDATE Mannschaft1 fehlgeschlagen!");
                break;
        }
    }
    /*
     * Anzeige der hyptetischen Tabellen
     */

    // temporaere Tabelle fuer Folgepartien erstellen
    $query = "CREATE TEMPORARY TABLE hypospieltab$spieler AS "
            . " SELECT  * FROM  gruppenspiel "
            . "  WHERE auto_gruppe_fk_ms1 IS  NOT  NULL "
            . "    AND auto_gruppe_fk_ms1 != 0 ";
    $result = mysqli_query($connection, $query) or die("CREATE hypospieltab$spieler fehlgeschlagen!");

    // Haupt-Query
    $query = "SELECT gruppe.bezeichnung, mannschaft.mannschaft_key, mannschaft.mannschaftsname, mannschaft.punkte" // 0, 1, 2, 3
            . "      ,mannschaft.anz_spiele ,mannschaft.plusTore ,mannschaft.minusTore ,mannschaft.tordifferenz" // 4, 5, 6, 7
            . "      ,mannschaft.info_link, mannschaft.flagge, gruppe.gruppe_key" // 8, 9,10
            . "  FROM hypomtab" . $spieler . " AS mannschaft, gruppe "
            . " WHERE mannschaft.gruppe=gruppe.gruppe_key ";

    $order_klausel = " ORDER BY gruppe.sortierung, mannschaft.punkte DESC, mannschaft.tordifferenz DESC, mannschaft.plusTore DESC";



    //    echo $query;
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
            $platzierung = 0;
            $wechsel = 1;
        }
        $platzierung = $platzierung + 1;
        if ($wechsel == 1) {
            echo "<table><tr> <th> </th> <th> Mannschaft </th> <th> Spiele </th> <th> Punkte </th> <th>Tore</th> <th>Differenz</th></tr>";
            $wechsel = 2;
        }
        echo "<tr> <td>$platzierung. </td><td align='LEFT'><img border=\"1\" src=" . FLAGS_PATH . $line[9] . "> <a href='$line[8]'>$line[2]</a></td> <td align='CENTER'> $line[4]</td> ";
        echo "<td align='CENTER'>$line[3]</td> <td align='RIGHT'>$line[5] - $line[6]</td> <td align='CENTER'> $line[7]</td> </td> </tr>";
        // jetzt die KO-Partien anpassen
        $query_update = "UPDATE hypospieltab$spieler SET key_ms1 = $line[1] WHERE auto_gruppe_fk_ms1=$line[10] AND auto_platz_ms1=$platzierung";
        $result_update = mysqli_query($connection, $query_update) or die("UPDATE KO-Partien fehlgeschlagen!:$query_update");
        $query_update = "UPDATE hypospieltab$spieler SET key_ms2 = $line[1] WHERE auto_gruppe_fk_ms2=$line[10] AND auto_platz_ms2=$platzierung";
        $result_update = mysqli_query($connection, $query_update) or die("UPDATE KO-Partien fehlgeschlagen!:$query_update");
    }
    if ($wechsel == 2) {
        echo "</table>";
    }

    // Jetzt noch die KO-Partien nach der Gruppenphase ermitteln und ausgeben
    $query = "SELECT mann1.mannschaft_key, mann1.mannschaftsname, gruppenspiel.tore_ms1" // 0, 1, 2
            . "      ,mann2.mannschaft_key, mann2.mannschaftsname, gruppenspiel.tore_ms2" // 3, 4, 5
            . "      ,runde.bezeichnung, gruppe.bezeichnung, DATE_FORMAT(gruppenspiel.datum, '%d.%m.')" // 6, 7, 8
            . "      ,mann1.flagge, mann2.flagge, gruppenspiel.bezeichnung, TIME_FORMAT(gruppenspiel.zeit, '%H:%i')" // 9, 10, 11, 12
            . "  FROM hypospieltab$spieler AS gruppenspiel JOIN runde JOIN mannschaft mann1 JOIN mannschaft mann2 "
            . "  LEFT JOIN gruppe ON (gruppenspiel.gruppe=gruppe.gruppe_key) "
            . " WHERE runde.runde_key=gruppenspiel.spiel_art "
            . "   AND mann1.mannschaft_key=gruppenspiel.key_ms1 "
            . "   AND mann2.mannschaft_key=gruppenspiel.key_ms2 ";

    $result = mysqli_query($connection, $query) or die("Abfrage KO-Tabellen fehlgeschlagen!");
    echo "<h3><br><p><p>Damit kommt es in der n&auml;chsten Runde zu folgenden Partien:</h3>";
    // Bezeichnung - Name1 - Flagge1 - - - Flagge2 - Mannschaft2
    echo "<table> <tr><th></th> <th></th> <th></th> <th></th> <th></th> <th></th> <th></th></tr> ";
    while ($line = mysqli_fetch_array($result)) {
        echo "<tr> <td> $line[11]: </td> <td><b> $line[1] </b></td> <td> <img border=\"1\" src=" . FLAGS_PATH . $line[9]
        . "> </td> <td> - </td> <td> <img border=\"1\" src=" . FLAGS_PATH . $line[10]
        . "> </td> <td><b> $line[4] </b></td> <td> am $line[8] um $line[12] Uhr</td> </tr>\n";
    }
    echo "</table>";
    // am Ende die temporaeren Tabellen wieder loeschen
    $query = "DROP TABLE hypomtab" . $spieler;
    $result = mysqli_query($connection, $query) or die("DROP hypomtab fehlgeschlagen!");
    $query = "DROP TABLE hypospieltab$spieler";
    $result = mysqli_query($connection, $query) or die("DROP hypospieltab$spieler fehlgeschlagen!");
}



echo" </form>\n";
frame_footer();


/*
 * DB-Verbindung sauber beenden
 */
mysqli_close($connection);
?>