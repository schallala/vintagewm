<?php
SESSION_start();

// Globale Variablen einbinden
include 'functions.php';

// Name des Skripts
$skript_name = "spieler.php";

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
    $Modus = 2;  // Spieler aendern
};
if (isset($_POST["MODUS_LOESCHEN"])) {
    $Modus = 3;  // Spieler loeschen
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

// Die Rolle wird noch oefter benoetigt ...
if (!empty($_SESSION["rolle"])) {
    $user_rolle = $_SESSION["rolle"];
} else {
    $user_rolle = "nix";
}

// feststellen, ob bereits ein Ergebnis eingetragen ist
// falls ja, darf der Europameistertipp nicht mehr geaendert werden
$query = "SELECT 1 FROM gruppenspiel WHERE status=1";
$result = mysqli_query($connection, $query) or die("Abfrage fehlgeschlagen!");
if (mysqli_num_rows($result) > 0) {
    $turnier_start = 1;
} else {
    $turnier_start = 0;
}

// Spieler-Key auslesen
if (isset($_POST["spieler_key"])) {
    $aktueller_spieler = $_POST["spieler_key"];
}

// falls irgendwie der Spieler uebergeben wurde, in den Anzeige-Modus wechseln
if ($Modus == 0 AND isset($_REQUEST["spieler"])) {
    $aktueller_spieler = $_REQUEST["spieler"];
    $Modus = 7;
}


//nur registrierte User duerfen hier arbeiten
if (empty($_SESSION["spieler_key"])) {
    $Titel = "Spielerdaten";
    $Meldung = "Dieser Bereich ist nur f&uuml;r registrierte Benutzer erreichbar! <br>Finger weg!";
    $Modus = 999;
}

// Falls Login geaendert werden soll, pruefen ob es schon existiert oder leer ist
if ($Modus == 4 OR $Modus == 5) {
    if (empty($_POST["login"])) {
        $Meldung = "Das Login-Feld darf nicht leer sein!<br>Es wurde nicht gespeichert!";
        $Modus = 999;
    };
    if ($Modus == 4) {
        $aktueller_spieler = -1;
    }; //
    $result = mysqli_query($connection, "SELECT 1 FROM spieler "
            . " WHERE spieler_key !=  " . $aktueller_spieler
            . "   AND login        = '" . $_POST["login"] . "'")
            or die("Abfrage fehlgeschlagen!");
    if (mysqli_num_rows($result) != 0) {
        $Meldung = "Das Login existiert bereits!<br>Es wurde nicht gespeichert!";
        $Modus = 999;
    }
}

// Pruefung bei Aendern / Loeschen / Einzelanzeige, ob ein Datensatz ausgewaehlt wurde
if (($Modus == 2 OR $Modus == 3 OR $Modus == 7) AND ( !isset($aktueller_spieler))) {
    $Meldung = "Es wurde kein Spieler ausgew&auml;hlt!";
    $Modus = 0;  // Auswahlliste erneut anzeigen
};

// alle Spieler aendern darf  nur der Admin
if ($Modus == 2 AND $aktueller_spieler != $_SESSION["spieler_key"]
        AND $user_rolle != ADMINROLLE) {
    $Meldung = "Es k&ouml;nnen nur die eigenen Daten modifiziert werden!";
    $aktueller_spieler = $_SESSION["spieler_key"];  // Spieler-Key austauschen
};


/*
 * Aktionen auf DB (SELECT, INSERT, UPDATE) vorbereiten und
 * anschlie&szlig;end ausfuehren
 */

// vielleicht kann man diese Statements nochmal verwenden ...
$mutter_aller_abfragen = "SELECT spieler_key, vorname, spielername, email, telefon, passwort, weltmeister, bezahlt"
        . "     , mannschaftsname, Newsletter, Freigabe, Rolle, login, icq_nummer "
        . "  FROM spieler "
        . "  LEFT JOIN mannschaft ON (spieler.weltmeister=mannschaft.mannschaft_key) ";
$order_klausel = " ORDER BY spielername, vorname";

// Im Insert- und Update-Modus m&ouml;gliche Leerfelder abfangen
IF ($Modus == 4 OR $Modus == 5) {
    if (empty($_POST["icq_nummer"])) {
        $_POST["icq_nummer"] = "NULL";
    };
    if (empty($_POST["weltmeister"])) {
        $_POST["weltmeister"] = "NULL";
    };
    if (empty($_POST["rolle"])) {
        $_POST["rolle"] = TIPPERROLLE;
    };
    if (empty($_POST["bezahlt"])) {
        $_POST["bezahlt"] = '0';
    };
    if (empty($_POST["freigabe"])) {
        $_POST["freigabe"] = '0';
    };
    if (empty($_POST["newsletter"])) {
        $_POST["newsletter"] = '0';
    };
};


// SQL-Statements zu jedem Fall erzeugen
switch ($Modus) {
    case 1 :
        $Titel = "Spieler neu eintragen";
        break;
    case 0:
        // Abfrage aller Datens&auml;tze
        $query = $mutter_aller_abfragen . $order_klausel;
        $Titel = "Spieler ausw&auml;hlen";
        break;
    case 6:
        // Abfrage aller Datens&auml;tze
        $query = $mutter_aller_abfragen . $order_klausel;
        $Titel = "&Uuml;bersicht der Spieler";
        break;
    case 7:
        // Abfrage eines Datensatzes
        $Titel = "Spieler anzeigen";
        $query = $mutter_aller_abfragen . " WHERE spieler_key=" . $aktueller_spieler . $order_klausel;
        break;
    case 2:
        // Seitentitel und Meldung setzen
        $Titel = "Spieler bearbeiten";
        // Abfrage eines Datensatzes ueber Primary Key
        $query = $mutter_aller_abfragen . " WHERE spieler_key=" . $aktueller_spieler . $order_klausel;
        break;
    case 3:
        // einen Datensatz loeschen
        $query = "DELETE FROM spieler WHERE spieler_key=" . $aktueller_spieler;
        // Seitentitel und Meldung setzen
        $Titel = "Spieler gel&ouml;scht";
        $Meldung = "Der Spieler wurde gel&ouml;scht!";
        // erneute Anzeige der Auswahlliste einleiten
        $Modus = 99;
        break;
    case 4:
        // einen neuen Datensatz in der DB speichern
        // ACHTUNG!!! Hier wird mit dem MySQL-Feature "auto-increment"
        //            ein INSERT gemacht!!!
        $query = "INSERT INTO spieler (spielername, vorname, login, telefon, "
                . "email, passwort, weltmeister, punkte, exakte_tips, richtige_tendenz, bezahlt, Freigabe, Newsletter, Rolle, ICQ_NUMMER) "
                . "VALUES('" . $_POST["spielername"]
                . "', '" . $_POST["vorname"]
                . "', '" . $_POST["login"]
                . "', '" . $_POST["telefon"]
                . "', '" . $_POST["email"]
                . "', '" . $_POST["passwort"]
                . "', " . $_POST["weltmeister"]
                . " , 0, 0, 0 " // Punke auf 0 setzen
                . " , " . $_POST["bezahlt"]
                . " , " . $_POST["freigabe"]
                . " , " . $_POST["newsletter"]
                . " , '" . $_POST["rolle"]
                . "', '" . $_POST["icq_nummer"]
                . "')";
        // Seitentitel und Meldung setzen
        $Titel = "Neuer Spieler gespeichert";
        $Meldung = "Der Spieler wurde neu eingetragen!";
        // erneute Anzeige der Auswahlliste einleiten
        $Modus = 99;
        break;
    case 5:
        // einen bestehenden  Datensatz aktualisieren
        // abhaengig von der Rolle die Query setzen
        if ($user_rolle == ADMINROLLE) {
            $query = "UPDATE spieler "
                    . "SET spielername = '" . $_POST["spielername"]
                    . "'  ,vorname     = '" . $_POST["vorname"]
                    . "'  ,login       = '" . $_POST["login"]
                    . "'  ,telefon     = '" . $_POST["telefon"]
                    . "'  ,email       = '" . $_POST["email"]
                    . "'  ,passwort    = '" . $_POST["passwort"]
                    . "'  ,weltmeister = " . $_POST["weltmeister"]
                    . "   ,bezahlt     = " . $_POST["bezahlt"]
                    . "   ,freigabe    = " . $_POST["freigabe"]
                    . "   ,newsletter  = " . $_POST["newsletter"]
                    . "   ,rolle       = '" . $_POST["rolle"]
                    . "'  ,icq_nummer  = '" . $_POST["icq_nummer"]
                    . "' ";
        } else {
            $query = "UPDATE spieler "
                    . "SET login       = '" . $_POST["login"]
                    . "'  ,icq_nummer  = '" . $_POST["icq_nummer"]
                    . "'  ,telefon     = '" . $_POST["telefon"]
                    . "'  ,email       = '" . $_POST["email"]
                    . "'  ,passwort    = '" . $_POST["passwort"]
                    . "'  ,freigabe    = " . $_POST["freigabe"]
                    . "   ,newsletter  = " . $_POST["newsletter"];
            // falls die EM noch nicht begonnen hat, kann der Europameistertipp noch geaendert werden
            if ($turnier_start == 0) {
                $query = $query . "  ,weltmeister = " . $_POST["weltmeister"];
            }
        }
        // Einschraenkung auf einen Spieler
        $query = $query . " WHERE spieler_key=" . $aktueller_spieler;
        // Seitentitel und Meldung setzen
        $Titel = "Spieler aktualisiert";
        $Meldung = "Die Daten des Spielers wurden aktualisiert.";
        // erneute Anzeige der Auswahlliste einleiten
        $Modus = 99;
        break;
}

// Statement nur bei Bedarf ausfuehren !!!
if (isset($query)) {
    //echo $query;
    $result = mysqli_query($connection, $query) or die("Abfrage fehlgeschlagen!bla");
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
                printf("<input type='HIDDEN' name='spieler_key' value='%s'>", $line[0]);
            };

            // einfache Textfelder
            echo "  <tr><td align=\"left\"> Login    </td> <td> <INPUT type='TEXT' name='login' size='30' ";
            if (isset($line[12])) {
                echo "value='$line[12]'";
            };
            echo "> </td> </tr>\n";

            // der Name darf nur vom Admin geaendert werden!
            echo "  <tr><td align=\"left\"> Vorname  </td> <td align=\"left\"> ";
            if ($user_rolle == ADMINROLLE) {
                echo "<INPUT name='vorname'";
                if (isset($line[1])) {
                    echo "value='$line[1]'";
                };
                echo " type='TEXT' size='30' >";
            } else {
                echo "<b>$line[1]</b>";
            }
            echo "</td> </tr>\n";
            echo "  <tr><td align=\"left\"> Nachname  </td> <td align=\"left\"> ";
            if ($user_rolle == ADMINROLLE) {
                echo "<INPUT name='spielername'";
                if (isset($line[2])) {
                    echo "value='$line[2]'";
                };
                echo " type='TEXT' size='30' >";
            } else {
                echo "<b>$line[2]</b>";
            }
            echo "</td> </tr>\n";

            echo "  <tr><td align=\"left\"> E-Mail   </td> <td> <INPUT type='TEXT' name='email' size='30' ";
            if (isset($line[3])) {
                echo "value='$line[3]'";
            };
            echo "> </td> </tr>\n";
            echo "  <tr><td align=\"left\"> Telefon  </td> <td> <INPUT type='TEXT' name='telefon' size='30' ";
            if (isset($line[4])) {
                echo "value='$line[4]'";
            };
            echo "> </td> </tr>\n";
            echo "  <tr><td align=\"left\"> passwort </td> <td> <INPUT type='TEXT' name='passwort' size='30' ";
            if (isset($line[5])) {
                echo "value='$line[5]'";
            };
            echo "> </td> </tr>\n";
            echo "  <tr><td align=\"left\"> ICQ-Nummer </td> <td> <INPUT type='TEXT' name='icq_nummer' size='20' ";
            if (isset($line[5])) {
                echo "value='$line[13]'";
            };
            echo "> </td> </tr>\n";

            // LOV fuer den Europameister-Tipp erstellen
            // aber nur, wenn noch kein Ergebnis feststeht!!!
            if ($turnier_start == 0 OR $user_rolle == ADMINROLLE) {
                $query_lov = "SELECT mannschaft_key, mannschaftsname FROM mannschaft";
                $result_lov = mysqli_query($connection, $query_lov) or die("Abfrage fehlgeschlagen!");

                // LOV-Anzeige aufbauen
                echo "  <tr><td align=\"left\"> Cupsieger </td> <td> <SELECT  name='weltmeister' size='1'> \n";
                //
                while ($line_lov = mysqli_fetch_array($result_lov)) {
                    echo "<option value='$line_lov[0]' ";
                    // Der eventuell bereits eingetragene Europameister wird vorselektiert
                    if (!empty($line[6]) AND $line_lov[0] == $line[6]) {
                        echo "selected";
                    }
                    echo ">$line_lov[1]</option>\n ";
                }

                echo "           </select></td></tr>\n";
            } else {
                // Europameister-Tipp nur anzeigen (nicht bei NEU!)
                echo "  <tr><td align=\"left\"> Cupsieger </td> <td>";
                if ($Modus != 1) {
                    echo $line[8];
                }
                echo "</td></tr>\n";
            }
            // Die Rolle des Benutzers festlegen
            echo "<tr> <td align=\"left\"> Benutzerrolle </td> <td> ";
            if ($user_rolle == ADMINROLLE) {
                echo "<select name='rolle' size='1'> \n";
                echo "<option value=" . TIPPERROLLE;
                if (!empty($line[11]) AND $line[11] == TIPPERROLLE) {
                    echo " selected";
                };
                echo ">Tipper</option> ";
                echo "<option value=" . HIWIROLLE;
                if (!empty($line[11]) AND $line[11] == HIWIROLLE) {
                    echo " selected";
                };
                echo ">Hilfsadmin</option> ";
                echo "<option value=" . ADMINROLLE;
                if (!empty($line[11]) AND $line[11] == ADMINROLLE) {
                    echo " selected";
                };
                echo ">Admin</option> ";
                echo "</select>\n";
            } else {
                echo "<b>$line[11]</b>";
            }
            echo "</td></tr>\n";

            // Jetzt noch ein paar Checkboxen
            // Bezahlung
            echo "  <tr><td align=\"left\"> Hat bezahlt?</td> <td> ";
            if ($user_rolle == ADMINROLLE) {
                echo "<input type='checkbox' name='bezahlt' value='1'";
                if (!empty($line[7]) AND $line[7] == 1) {
                    echo " checked";
                }
                echo "> </td></tr>\n";
            } else {
                if (!empty($line[7]) AND $line[7] == 1) {
                    echo " Ja!";
                } else {
                    echo "Nein";
                }
            }
            // Freigabe
            echo "  <tr><td align=\"left\"> Tipps freigeben??</td> <td> <input type='checkbox' name='freigabe' value='1'";
            if (!empty($line[10]) AND $line[10] == 1) {
                echo " checked";
            }
            echo "> </td></tr>\n";
            // Newsletter
            echo "  <tr><td align=\"left\"> Newsletter abonieren?</td> <td> <input type='checkbox' name='newsletter' value='1'";
            if (!empty($line[9]) AND $line[9] == 1) {
                echo " checked";
            }
            echo "> </td></tr>\n";
            echo "</table>\n";
        };  // Ende Modus 1




        /*
         *
         * Fall 6: Uebersicht aller Spieler
         *
         */
        if ($Modus == 6) {
            echo "<table> <tr> <th>Login</th> <th>Name</th> <th>Telefon</th>\n";
            echo "<th>Email</th> <th>ICQ-Nummer</th> <th>Cupsieger</th> <th>Rolle</th> ";
            echo "<th>bezahlt?</th> <th>Newsletter?</th> <th>Freigabe?</th> </tr>\n";
            while ($line = mysqli_fetch_array($result)) {
                echo "<tr> <td align='LEFT'>$line[12]</td> <td align='LEFT'><a href='spieler.php?spieler=$line[0]'>$line[1] $line[2]</a></td> <td align='LEFT'>$line[4]</td> ";
                echo "<td align='LEFT'>$line[3]</td> <td>$line[13] </td> <td>$line[8]</td> <td>$line[11]</td> ";
                if ($line[7] == 1) {
                    echo "<td>Ja!</td> ";
                } else {
                    echo "<td>Nein</td> ";
                }
                if ($line[9] == 1) {
                    echo "<td>Ja!</td> ";
                } else {
                    echo "<td>Nein</td> ";
                }
                if ($line[10] == 1) {
                    echo "<td>Ja!</td> ";
                } else {
                    echo "<td>Nein</td> </tr>\n";
                }
            }
            echo "</table>\n";
        }

        /*
         *
         * Fall 7: Anzeige eines Spielers
         *
         */
        if ($Modus == 7) {
            // Daten abholen
            $line = mysqli_fetch_array($result);
            if (!isset($_SESSION["rolle"]) OR $_SESSION["rolle"] != ADMINROLLE) {
                $line[5] = "*****"; //nur Goetter duerfen alle Passwoerter sehen
            }

            // einfache Textfelder in Tabellenform
            echo "<table>\n";
            echo "  <tr><td align=\"left\"> Login    </td> <td align=\"left\"> <b>$line[12]</b> </td> </tr>\n";
            echo "  <tr><td align=\"left\"> Vorname  </td> <td align=\"left\"> <b>$line[1]</b> </td> </tr>\n";
            echo "  <tr><td align=\"left\"> Nachname </td> <td align=\"left\"> <b>$line[2]</b> </td> </tr>\n";
            echo "  <tr><td align=\"left\"> E-Mail   </td> <td align=\"left\"> <b>$line[3]</b> </td> </tr>\n";
            echo "  <tr><td align=\"left\"> Telefon  </td> <td align=\"left\"> <b>$line[4]</b> </td> </tr>\n";
            echo "  <tr><td align=\"left\"> passwort </td> <td align=\"left\"> <b>$line[5]</b> </td> </tr>\n";
            echo "  <tr><td align=\"left\"> ICQ-Nummer </td> <td align=\"left\"> <b>$line[13]</b> </td> </tr>\n";
            echo "  <tr><td align=\"left\"> Benutzerrolle </td> <td align=\"left\"> <b>$line[11]</b> </td> </tr>\n";
            echo "  <tr><td align=\"left\"> Cupsieger </td> <td align=\"left\"> <b>$line[8]</b> </td> </tr>\n";
            echo "  <tr><td align=\"left\"> bezahlt? </td> <td align=\"left\"> <b>\n";
            if ($line[7] == 1) {
                echo "Ja! </b>\n";
            } else {
                echo "Nein </b>\n";
            };
            echo "  <tr><td align=\"left\"> Tipps freigegeben? </td> <td align=\"left\"> <b>\n";
            if ($line[10] == 1) {
                echo "Ja! </b>\n";
            } else {
                echo "Nein </b>\n";
            };
            echo "  <tr><td align=\"left\"> Newsletter aboniert? </td> <td align=\"left\"> <b>\n";
            if ($line[9] == 1) {
                echo "Ja! </b>\n";
            } else {
                echo "Nein </b>\n";
            };
            echo "</b> </td>";
            echo " </tr>\n";
            echo "</table>\n";

            // Anzeige der heutigen Aktivitaeten
            $query = "SELECT logging.spieler,  DATE_FORMAT(max(logging.datum), '%d.%m.%Y'), spieler.login "
                    . "  FROM logging, spieler "
                    . " WHERE logging.spieler=spieler.spieler_key "
                    . "   AND logging.spieler=" . $aktueller_spieler
                    . " GROUP  BY logging.spieler"
                    . " ORDER BY 2 desc";
            $result = mysqli_query($connection, $query) or die("Abfrage fehlgeschlagen!");
            $line_login = mysqli_fetch_array($result);
            echo "<i>Zuletzt aktiv am $line_login[1] unter dem Login <b>$line_login[2]</b>";
        }

        /*
         * MODUSWECHSEL
         *
         * nach bestimmten Aktionen soll gleich wieder die Auswahlbox erscheinen;
         * dazu muss eine erneute Abfrage der Daten stattfinden und der Modus auf 0 gesetzt werden
         */
        if ($Modus == 99) {
            $result = mysqli_query($connection, $mutter_aller_abfragen . $order_klausel) or die("Abfrage fehlgeschlagen!");
            $Modus = 0;
        };


        /*
         *
         * Fall 0: einfache Auswahliste aller Spieler
         *
         */
        if ($Modus == 0) {
            echo "<select size='10' name='spieler_key'>\n";
            while ($line = mysqli_fetch_array($result)) {
                echo "<option value=$line[0]>" . $line[1] . " " . $line[2] . "</option>\n";
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
                if (isset($_SESSION["rolle"]) AND $_SESSION["rolle"] == ADMINROLLE) { //nur Goetter duerfen loeschen
                    echo " <input type = 'SUBMIT' name = 'MODUS_LOESCHEN' value = 'L&ouml;schen'> \n";
                }
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

        echo" </form>\n";
        frame_footer();


        /*
         * DB-Verbindung sauber beenden
         */
        mysqli_close($connection);
        ?>