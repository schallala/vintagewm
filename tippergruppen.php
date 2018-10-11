<?php
SESSION_start();

// Globale Variablen einbinden
include 'functions.php';

// Name des Skripts
$skript_name = "tippergruppen.php";

// Anzeige Begrenzung
$max_anzahl_spiele = 15;
// Login pruefen
pruefe_login($skript_name);


/*
 * Modus aus evtl. Parametern setzen
 */

$Modus = 0;  // keiner, Auswahlliste
if (isset($_POST["MODUS_AUSWAHL"])) {
    $Modus = 0;  // Darstellung eines Datensatzes
};
if (isset($_POST["MODUS_SPEICHERN"])) {
    $Modus = 11;  // Darstellung eines Datensatzes
};
if (isset($_POST["GRUPPE_NEU"])) {
    $Modus = 12;  // Darstellung eines Datensatzes
};
if (isset($_POST["GRUPPE_LOESCHEN"])) {
    $Modus = 13;  // Darstellung eines Datensatzes
};

/*
 * einige Infos zum User und angeforderten Spieler ermitteln
 */

// ein Spiel muss ausgewaehlt sein
$aktuelle_gruppe = -1;
if (isset($_REQUEST["benutzergruppe"])) {
    $aktuelle_gruppe = $_REQUEST["benutzergruppe"];
}

if (isset($_REQUEST["gruppenmitglieder"])) {
    $gruppenmitglieder = $_REQUEST["gruppenmitglieder"];
}

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

if ($user_rolle != ADMINROLLE) {
    $Meldung = "Hier hat nur der Admin Zugriff!";
    $Modus = 999;
}

$Titel = "Benutzergruppen mit Zuordnung";





/*
 * SELECTs vorbereiten und
 */

// vielleicht kann man diese Statements nochmal verwenden ...
$query_tipper = "SELECT spieler.spieler_key, spieler.vorname, spieler.spielername "
        . " FROM spieler "
        . " ORDER BY spieler.spielername, spieler.vorname ";

$query_benutzergruppen = " SELECT gruppen_key, bezeichnung, position "
        . " FROM benutzergruppe "
        . " ORDER BY position ";

$query_spieler_benutzergruppe = //key_spieler, key_gruppe
        "SELECT a.spieler_key, a.vorname, a.spielername "
        . " FROM spieler a, spieler_benutzergruppe b "
        . " WHERE a.spieler_key = b.key_spieler ";

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
  Aenderungen speichern (auch Spieler-Zuordnungen)
 */
if ($Modus == 11) {
    if ($aktuelle_gruppe >= 0) {
        $bez = "***keine Bezeichnung***";
        if (isset($_REQUEST["gruppe_bezeichnung"])) {
            $bez = $_REQUEST["gruppe_bezeichnung"];
        }
        $sort = "1";
        if (isset($_REQUEST["gruppe_sortierung"])) {
            $sort = $_REQUEST["gruppe_sortierung"];
        }

        $query = "UPDATE benutzergruppe SET bezeichnung = \"$bez\", position=$sort WHERE gruppen_key =$aktuelle_gruppe";
        $result = mysqli_query($connection, $query)
                or die("UPDATE Benutzergruppe  fehlgeschlagen! $query");

        $query = "DELETE FROM spieler_benutzergruppe WHERE key_gruppe =$aktuelle_gruppe";
        $result = mysqli_query($connection, $query)
                or die("DELETE Spieler-Benutzergruppe  fehlgeschlagen! $query");

        if (isset($gruppenmitglieder)) {
            foreach ($gruppenmitglieder as $mitglied) {
                $query = "INSERT INTO spieler_benutzergruppe(key_spieler, key_gruppe) VALUES ($mitglied, $aktuelle_gruppe)";
                $result = mysqli_query($connection, $query)
                        or die("INSERT Spieler-Benutzergruppe  fehlgeschlagen! $query");
            }
        }
    }
}

/**
  Neue Gruppe erstellen
 */
if ($Modus == 12) {
    $bez = "Neue Gruppe";
    if (isset($_REQUEST["gruppe_bezeichnung"])) {
        $bez = $_REQUEST["gruppe_bezeichnung"];
    }
    $sort = "1";
    if (isset($_REQUEST["gruppe_sortierung"])) {
        $sort = $_REQUEST["gruppe_sortierung"];
    }

    $query = "INSERT INTO benutzergruppe(bezeichnung, position) VALUES(\"$bez\", $sort)";
    $result = mysqli_query($connection, $query)
            or die("INSERT Benutzergruppe  fehlgeschlagen!");
    $aktuelle_gruppe = -1;
}

/**
  Gruppe loeschen
 */
if ($Modus == 13) {

    $bez = "***nix***";
    if (isset($_REQUEST["gruppe_bezeichnung"])) {
        $bez = $_REQUEST["gruppe_bezeichnung"];
    }

    if ($aktuelle_gruppe >= 0) {
        $query = "DELETE FROM benutzergruppe WHERE gruppen_key =$aktuelle_gruppe AND bezeichnung=\"$bez\"";
        $result = mysqli_query($connection, $query)
                or die("DELETE Benutzergruppe  fehlgeschlagen! $query");
        $aktuelle_gruppe = -1;
    }
}

if ($Modus != 999) {
    // falls nicht in der Druckansicht
    if ($Modus != 10) {
//			echo "<INPUT  type='SUBMIT' name='MODUS_DRUCK' value='Druckansicht'>\n"	;

        /*
         *
         *  Auswahlboxen Spiel und Sortierung
         *
         */
        // Auswahlbox Benutzergruppe
        $result = mysqli_query($connection, $query_benutzergruppen)
                or die("Abfrage Benutzergruppe (Auswahl fehlgeschlagen!");
        echo "<table>\n<tr>\n";
        echo "<td valign=top><b>Auswahl Benutzergruppe</b><br><SELECT name='benutzergruppe' >";
        while ($line = mysqli_fetch_array($result)) {
            echo "<OPTION value='$line[0]'";
            if ($aktuelle_gruppe == $line[0]) {
                echo " selected ";
                $gruppe_bezeichnung = $line[1];
                $gruppe_sortierung = $line[2];
            }
            echo ">$line[1]</OPTION>\n";
        }
        echo "</SELECT>";
        echo "\n<INPUT  type='SUBMIT' name='gruppe_anzeigen' value='Ausw&auml;hlen'>";

        // Basisdaten zur aktuellen Gruppe
        echo "<p>Gruppenname<br><input type='TEXT' NAME='gruppe_bezeichnung' SIZE='30' ";
        if ($aktuelle_gruppe >= 0) {
            echo "VALUE='$gruppe_bezeichnung'";
        } // vorbelegen, wenn Tipp vorhanden
        echo ">";
        echo "<p>SortierNr.<br><input type='TEXT' NAME='gruppe_sortierung' SIZE='3' ";
        if ($aktuelle_gruppe >= 0) {
            echo "VALUE='$gruppe_sortierung'";
        } // vorbelegen, wenn Tipp vorhanden
        echo ">\n<INPUT  align=right type='SUBMIT' name='GRUPPE_NEU' value='Neue Gruppe'>";
        // Loeschen nur zulassen, wenn Gruppe ausgewaehlt ist
        if ($aktuelle_gruppe >= 0) {
            echo "\n<INPUT  align=right type='SUBMIT' name='GRUPPE_LOESCHEN' value='Gruppe l&ouml;schen'>";
        }
        // zugeordnete Tipper auflisten
        if ($aktuelle_gruppe >= 0) {
            $result = mysqli_query($connection, $query_spieler_benutzergruppe . " AND b.key_gruppe=$aktuelle_gruppe "
                    . " ORDER BY a.spielername, a.vorname ")
                    or die("Abfrage Gruppenmitglieder fehlgeschlagen!");
            echo "<p>Zugeordnete Gruppenmitglieder: <ul> ";
            while ($line = mysqli_fetch_array($result)) {
                echo "<li>$line[1], $line[2]</li>";
            }
            echo "</ul></td>";
        }

        // Auswahlbox Zugeordnete Tipper 
        $result = mysqli_query($connection, $query_tipper)
                or die("Abfrage Tipper fehlgeschlagen!");

        echo "<td><b>Auswahl Tipper</b><br><SELECT name='gruppenmitglieder[]'  size=20 multiple>";
        $aktuelles_spiel = reset($aktuelle_spiele);
        while ($line = mysqli_fetch_array($result)) {
            echo "<OPTION value=$line[0]";
            if (isset($gruppenmitglieder)) {
                if (in_array($line[0], $gruppenmitglieder)) {
                    echo " selected ";
                }
            }
            echo ">$line[2], $line[1] "; // Spielbezeihnung
            //if (($gruppenmitglieder
            echo "</OPTION>\n";
        }
        echo "</SELECT></td></tr>\n";
        echo "</table>\n<INPUT  align=center type='SUBMIT' name='MODUS_SPEICHERN' value='&auml;nderungen speichern'>\n";
    }

    /*
     *
     *  Anzeige aller ausgewaehlten Spiele
     *
     *
      $tmp_key_liste = "";
      $endergebnis = "*";
      if (isset($aktuelle_spiele)){
      $sel_counter = 0;
      $query_selected = $query_spiel;
      $tmp_key_liste = "";
      foreach ($aktuelle_spiele as $spielkey) {
      $sel_counter++;
      if (strcmp($tmp_key_liste, "") != 0){
      $tmp_key_liste = $tmp_key_liste . ", ";
      }
      $tmp_key_liste = $tmp_key_liste . $spielkey;
      if ($sel_counter>=$max_anzahl_spiele) break;
      }
      if  (strcmp($tmp_key_liste, "") != 0){
      $query_selected = $query_selected . " AND spiel.spiel_key in(" . $tmp_key_liste . ") ";
      }
      $result = mysqli_query($connection, $query_selected  . $order_spiel );
      $sel_counter = 0;

      echo "<p><h4>Ausgew&auml;hlte Spiele:</h4><table border=3 rules=none >";
      $rowflag = 1;
      while ($line = mysqli_fetch_array($result)){
      $sel_counter = $sel_counter + 1;
      // Modifizierte Darstellung bei noch nicht feststehenden Gegnern
      if ($rowflag==1){
      echo "<tr bgcolor=lightblue>";
      $rowflag = 0;
      }else{
      echo "<tr bgcolor=lightgray>";
      $rowflag = 1;
      }

      echo "<td><b>($sel_counter)</b></td><td> $line[7] $line[15]: ";	// Spielbezeichnung
      if($line[22] == 0){	// entspricht der Mannschaftskey dem NULL-Wert?
      echo $line[2] ;
      }else{
      if($line[16] != NULL){	// KO-Spiel
      // Bezeichnung des referenzierten Spiels ermitteln
      if($line[20] == 1)	// 1. PLatz=Sieger, sonst Verlierer
      echo "Sieger Spiel " . ermittle_spiel_bezeichnung($line[16]);
      else
      echo "Verlierer Spiel " . ermittle_spiel_bezeichnung($line[16]);
      }else{	// KO-Spiel nach Gruppenphase
      // Bezeichnung der referenzierten Gruppe ermitteln
      echo "$line[20]. " . ermittle_gruppen_bezeichnung($line[18]);
      }
      }
      echo " - ";
      if($line[23] == 0){	// entspricht der Mannschaftskey dem NULL-Wert?
      echo " $line[5] ";
      }else{
      if($line[17] != NULL){	// KO-Spiel
      // Bezeichnung des referenzierten Spiels ermitteln
      if($line[20] == 1)	// 1. PLatz=Sieger, sonst Verlierer
      echo " Sieger " . ermittle_spiel_bezeichnung($line[17]);
      else
      echo " Verlierer " . ermittle_spiel_bezeichnung($line[17]);
      }else{	// KO-Spiel nach Gruppenphase
      // Bezeichnung der referenzierten Gruppe ermitteln
      echo " $line[21]. " . ermittle_gruppen_bezeichnung($line[19]);
      }
      }
      echo "</td>";
      if ($line[11]==1){
      echo "<td>$line[3] - $line[6]</td>";
      } else {
      echo "<td>$line[9], $line[10] Uhr</td>";
      }
      echo "</tr>";
      }
      echo "</table><p>";

      /*
     *
     *  Anzeige der Tippuebersicht
     *
     *
      $endergebnis = "*";

      // Anzeige der (freigegebenen) Tipps aller Spiele
      $result = mysqli_query($connection, $query_tipps . " AND tips.key_spiel in(" . $tmp_key_liste . ") " . $order_tipps) or die ("Abfrage fehlgeschlagen!");
      // noch ein paar statistische Daten zaehlen
      $sieg_ms1 = 0;
      $sieg_ms2 = 0;
      $remis = 0;
      $punkter = 0;
      $hoechstpunkter = 0;
      $rowflag = 1;
      echo "<table border=3 rules=none >";
      echo "<tr  bgcolor=lightgreen> <th>Tipper</th>";
      for ($i=1; $i<= $sel_counter; $i++){
      echo "<th>Spiel<br>($i)</th>";
      }
      echo "</tr>";
      $letzter_spieler = -1;
      while ($line_tipps = mysqli_fetch_array($result)){
      if ($letzter_spieler != $line_tipps[0]){
      if ($letzter_spieler > 0){
      echo "</tr>";
      }
      if ($rowflag==1){
      echo "<tr bgcolor=lightblue>";
      $rowflag = 0;
      }else{
      echo "<tr bgcolor=lightgray>";
      $rowflag = 1;
      }
      echo "<td align='LEFT'><b><a href='tippzettel.php?spieler=$line_tipps[0]'>$line_tipps[1] $line_tipps[2]</a> </td> ";
      }
      if($line_tipps[3]==1 OR $line[11]==1 OR $line[14]==0 OR $user_rolle==ADMINROLLE){	// Freigabe oder bereits ausgetragen oder Runde ist sowieso gesperrt
      if ($line_tipps[5] > -1){
      echo "<td  align='CENTER'>$line_tipps[5]:$line_tipps[6]</td> ";
      }
      } else {
      echo "<td  align='CENTER'> *** </td>";
      }
      $letzter_spieler = $line_tipps[0];
      }
      echo "</tr></table>";
      }
     */
}
echo" </form>\n";

frame_footer();


/*
 * DB-Verbindung sauber beenden
 */
mysqli_close($connection);
?>