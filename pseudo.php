<?php

session_start();

// Globale Variablen einbinden
include 'functions.php';

// Name des Skripts
$skript_name = "pseudo.php";

// Login pruefen
pruefe_login($skript_name);

$Titel = "Das Punkte-Experiment";

frame_header($skript_name);

// Modus ermitteln
$Modus = 0;  // keiner, Auswahlliste
if (isset($_POST["MODUS_AUSWAHL"])) {
    $Modus = 0;  // Auswahlmodus
};
if (isset($_POST["MODUS_BERECHNEN"])) {
    $Modus = 1;  // berechnen mit uebergebenen Werten
};

$pseudorangliste = "PSEUDORANGLISTE" . $_SESSION["spieler_key"];
$query_insert = "CREATE TEMPORARY TABLE $pseudorangliste AS SELECT * FROM spieler";
$result_insert = mysqli_query($connection, $query_insert) or die("Erstellen PSEUDORANGLISTE fehlgeschlagen! - " . $query_insert);

echo "<h2>$Titel</h2>";

// Parametereingabe
if ($Modus == 0) {
    echo "<p>Hier k&ouml;nnt ihr mit verschiedenen Punktesystemen experimentieren und die daraus resultierenden Ergebnisse studieren. Voreingestellt sind willk&uuml;rliche Werte von mir. (Alpha-Version!). <br>Viel Spa&szlig;!</p>";
    echo "<p><form action = '$skript_name' method = 'POST'> ";
    echo "<h3>Bitte Punkeverteilung vornehmen:</h3>";
    echo "<table>";
    echo "<tr><td>Punkte f&uuml;r richtige Tendenz: </td><td> <INPUT type='TEXT' name='punkte_tendenz' size='2' value=4> </td></tr>";
    echo "<tr><td>Punkte f&uuml;r richtige Tordifferenz: </td><td> <INPUT type='TEXT' name='punkte_differenz' size='2' value=2> </td></tr>";
    echo "<tr><td>Punkte f&uuml;r richtige Anzahl Tore: </td><td> <INPUT type='TEXT' name='punkte_exakte_tore' size='2' value=1> </td></tr>";
    echo "<tr><td>Bonuspunkte f&uuml;r exaktes Ergebnis: </td><td> <INPUT type='TEXT' name='bonus_exakter_tipp' size='2'  value=1> </td></tr>";
    echo "</table><input type = 'SUBMIT' name = 'MODUS_BERECHNEN' value = 'Verteilung berechnen'> \n";
} else {
    $punkte_tendenz = $_POST["punkte_tendenz"];
    $punkte_differenz = $_POST["punkte_differenz"];
    $punkte_exakte_tore = $_POST["punkte_exakte_tore"];
    $bonus_exakter_tipp = $_POST["bonus_exakter_tipp"];
    $nur_punkte_bei_tendenz = 0; // = nein

    echo "<h3>Punkteverteilung:</h3> ";
    echo "<ul><li>Punkte f&uuml;r richtige Tendenz: $punkte_tendenz ";
    echo "<li>Punkte f&uuml;r richtige Tordifferenz: $punkte_differenz ";
    echo "<li>Punkte f&uuml;r richtige Anzahl Tore: $punkte_exakte_tore ";
    echo "<li>Bonuspunkte f&uuml;r exaktes Ergebnis: $bonus_exakter_tipp </ul>";
    $query_spieler = "SELECT spieler_key, vorname, spielername, punkte "
            . "  FROM spieler "
            . " ORDER BY punkte desc";
    $result_spieler = mysqli_query($connection, $query_spieler) or die("Abfrage fehlgeschlagen!");

    while ($line_spieler = mysqli_fetch_array($result_spieler)) {
        echo "<br>\n <b> $line_spieler[1] $line_spieler[2] </b>  -  $line_spieler[3] Punkte<br>";
        $punkte_spieler = 0;
        $tendenz = 0;
        $exakt = 0;
        $query_tipps = " SELECT tips.key_spiel, gruppenspiel.key_ms1, man1.mannschaftsname " // 0, 1, 2
                . "			 , gruppenspiel.key_ms2, man2.mannschaftsname, tips.tore_ms1, tips.tore_ms2 " // 3, 4, 5, 6
                . "			 , tips.tendenz, gruppenspiel.tore_ms1, gruppenspiel.tore_ms2, gruppenspiel.tendenz " //7, 8, 9, 10
                . "		FROM tips, gruppenspiel, mannschaft AS man1, mannschaft AS man2 "
                . "	 WHERE tips.key_spiel = gruppenspiel.spiel_key  "
                . "		 AND gruppenspiel.key_ms1 = man1.mannschaft_key  "
                . "		 AND gruppenspiel.key_ms2 = man2.mannschaft_key  "
                . "		 AND gruppenspiel.status = 1 "
                . "		 AND tips.key_spieler =$line_spieler[0]	 "
                . "	 ORDER BY gruppenspiel.datum,	gruppenspiel.zeit ";

        $result_tipps = mysqli_query($connection, $query_tipps) or die("Abfrage Tipps fehlgeschlagen!");

        echo "<table>";
        echo "<th> <tr><td> Begegnung </td> <td> Endergebnis </td>  <td> Getippt </td> <td> Punkte </td></tr></th>\n";
        while ($line_tipps = mysqli_fetch_array($result_tipps)) {
            $punkte_spiel = 0;
            // Tendenz pruefen
            if ($line_tipps[7] == $line_tipps[10]) {
                $punkte_spiel = $punkte_spiel + $punkte_tendenz;
                $tendenz = $tendenz + 1;
            }
            // Differenz pruefen
            if ($line_tipps[5] - $line_tipps[6] == $line_tipps[8] - $line_tipps[9]) {
                $punkte_spiel = $punkte_spiel + $punkte_differenz;
            }
            // Pruefung Tore MS1
            if ($line_tipps[5] == $line_tipps[8]) {
                $punkte_spiel = $punkte_spiel + $punkte_exakte_tore;
            }
            // Pruefung Tore MS2
            if ($line_tipps[6] == $line_tipps[9]) {
                $punkte_spiel = $punkte_spiel + $punkte_exakte_tore;
            }
            // Bonus exakter Tipp
            if ($line_tipps[5] == $line_tipps[8] AND $line_tipps[6] == $line_tipps[9]) {
                $punkte_spiel = $punkte_spiel + $bonus_exakter_tipp;
                $exakt = $exakt + 1;
            }
            $punkte_spieler = $punkte_spieler + $punkte_spiel;
            echo "<tr> <td> $line_tipps[2] gegen $line_tipps[4] </td> <td> $line_tipps[8] - $line_tipps[9] </td>  <td> $line_tipps[5] - $line_tipps[6] </td> <td> = $punkte_spiel </td></tr>\n";
            // temporaere Rangliste
            $query_update = "UPDATE " . $pseudorangliste . " SET punkte = " . $punkte_spieler . ", exakte_tips = " . $exakt
                    . ", richtige_Tendenz = " . $tendenz . " WHERE spieler_key = " . $line_spieler[0];
            $result_update = mysqli_query($connection, $query_update) or die("UPDATE Spielerpunkte fehlgeschlagen!");
        }
        echo "</table>";
        echo "Punkte gesamt: $punkte_spieler <br>";
    }

    // alternative Rangliste anzeigen
    $query = "SELECT a.vorname, a.spielername, a.punkte, a.exakte_tips, a.richtige_Tendenz, b.punkte "
            . " FROM $pseudorangliste AS a, spieler AS b WHERE a.spieler_key=b.spieler_key "
            . "ORDER BY a.punkte DESC, a.spielername DESC, a.vorname DESC";
    $result = mysqli_query($connection, $query) or die("Abfrage alternative Rangliste fehlgeschlagen");
    echo "<hr><center><h3>Alternative Rangliste</h3><br>(in Klammern reale Punktzahl)\n";
    echo "<table><tr><th> Rang </th> <th> Spielername </th> <th> Tendenzen </th> <th> exakte Tipps </th> <th> Punkte </th> <th>  </th></tr>";
    $rang = 1;
    $alte_punkte = -1;
    while ($line = mysqli_fetch_array($result)) {
        echo "<tr>";
        if ($line[2] != $alte_punkte) {
            echo "<td> $rang </td>";
            $alte_punkte = $line[2];
        } else {
            echo "<td>  </td>";
        }
        echo "<td> $line[0] $line[1] </td> <td align=center> $line[4] </td> <td align=center> $line[3] </td> <td align=center> $line[2] </td> <td align=center> ($line[5]) </td> </tr>";
        $rang = $rang + 1;
    }

    echo "</table><p><form action = '$skript_name' method = 'POST'> ";
    echo " <input type = 'SUBMIT' name = 'MODUS_AUSWAHL' value = 'Neue Berechnung'> \n";
}
$query_drop = "DROP TABLE $pseudorangliste ";
$result_drop = mysqli_query($connection, $query_drop) or die("Loeschen den PSEUDORANGLISTE fehlgeschlagen! - " . $query_drop);


frame_footer();

/*
 * DB-Verbindung sauber beenden
 */
mysqli_close($connection);
?>
