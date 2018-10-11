<?php
session_start();

// Globale Variablen einbinden
include 'functions.php';

// Name des Skriptshttp://updates.netbeans.org/netbeans/updates/8.2/uc/final/distribution/catalog.xml.gz
$skript_name = "index.php";

// Login pruefen
pruefe_login($skript_name);

$Titel = TURNIERART_KURZ . "-Tippspiel Start";
frame_header($skript_name);
//  echo "<h2><blink>ACHTUNG! Serverausfall - bitte NEWS-ansehen!</blink></h2>!";
?>
<?php //	echo "<a href=\"" . LINK_RSS . "\"><img src=\"". IMAGE_PATH ."/rss.jpg\" width=30 height=30/ align=left> </a>";
?>


<H2> 
    <?php echo $Titel ?> 
</H3> <!-- in allen Modi ist der Inhalt ein Formular --> 
<p><form
    action = '<?php echo $skript_name; ?>' method = 'POST'>

    <div align="LEFT">
        <p ><b>Moin! Es ist mal wieder soweit: Das <?php echo TURNIERART_KURZ ?>-Tippspiel <?php echo TURNIER_JAHR ?> hat begonnen. Alle wichtigen Informationen hierzu k&ouml;nnt Ihr im
                Bereich <a href='turnier_spielregeln.php'>Spielregeln</a> finden. Neuerungen gegen&uuml;ber den vorhergehenden Tippspielen sind
                im Bereich <a href='turnier_news.php'>News</a> nachzulesen.
        </p>Viel Spa&szlig;!</b>
        <hr>
        <!--center>
        <h2><blink><font color="#00FF80">E I L M E L D U N G</font></blink></h2>
        <img src="images/hurricane16.jpg" align=right>
        <h3>Schutzevakuierung</h3><p>
        Lieber Besucher,<p>
        wir unterbrechen in Abstimmung mit der Gefahrenabwehrbeh&ouml;rde f&uuml;r einige Stunden den Veranstaltungsbetrieb, auf Grund eines schweren Unwetters. 
        Dies ist eine <i>vorr&uuml;bergehende</i> Unterbrechung
        und keine Absage des Tippspiels.<p> Bitte verlasst das Veranstaltungsgel&auml;nde zu euren Fahrzeugen und nehmt andere Tipper in euren Fahrzeugen auf.<br>
        Bleibt ruhig und achtet auf eure Mittipper. Bitte befolgt die Anweisungen des Sicherheitspersonals.<p>
        Wir werden die ganze Nacht versuchen mit dem THW und der Feuerwehr das Veranstaltungsgel&auml;nde wieder spielbereit zu bekommen.
        <p><p>Vielen Dank f&uuml;r euer Verst&auml;ndnis,<br>die Tippspielleitung
        
        <hr-->

        <center>
            <h4>Bezahlm&ouml;glichkeiten f&uuml;r den Wetteinsatz: </h4>
            <table width="50%">
                <tr>
                    <td><a href="https://paypal.me/tabels/5" TARGET = '_blank'><img  title='Raketentechniker' src='<?php echo IMAGE_PATH . "/Paypal_2012.png" ?>' align=center ></a></td>
                    <td><a href="turnier_spielregeln.php"><img title="I'm a Bankster!" src='<?php echo IMAGE_PATH . "/banksters2.png" ?>' align=center ></a></td>
                    <td><a href="turnier_spielregeln.php"><img title='Oldskool forever!' src='<?php echo IMAGE_PATH . "/doctor-fred.png" ?>' align=center ></a></td>
                </tr>
            </table>


        </center>
        <hr>
        <h4>
            <ul>
                <p><li> <font size=+2>I</font>nfos und News zum Tippspiel<br>
                    Alle wissenswerten Informationen rund um das <?php echo TURNIERART_KURZ ?>-Tippspiel
                    <ul>
                        <p><li>	Das <a href='turnier_faq.php'><font size=+1><?php echo TURNIERART_KURZ ?></font>-Tipp-FAQ</a> gibt Antwort auf (fast) alle Fragen des Lebens... (Achtung Beta-Version!)<br>
                            Hier sollten Einsteiger starten, denn hier werden oft auftretende Fragen zum Tippspiel beantwortet.
                            <p><li> <a href='turnier_spielregeln.php'><font size=+1>D</font>ie Spielregeln</a> zum <?php echo TURNIERART_KURZ ?>-Tippspiel
                            Alle Informationen bez&uuml;glich des Ablaufs des Tippspiels.
                            <p><li> <a href='turnier_news.php'><font size=+1>A</font>ktuelle Neuigkeiten</a> rund um das Tippspiel

                    </ul>

                    <p><li> <font size=+2>P</font>layers Corner<br>
                    In dieser Sektion k&ouml;nnen die Tipps und die per&ouml;nlichen Daten durch den Spieler eingegeben werden. Weiterhin
                    befindet sich hier der ber&uuml;chtigte <?php echo TURNIERART_KURZ ?>-Stammtisch und der private Chat zur Weltmeisterschaft.
                    <ul>
                        <p><li> <a href='spieler.php'><font size=+1>S</font>pielerdaten &auml;ndern</a><br>
                            Eingabe / &Auml;ndern der pers&ouml;nlichen Daten wie Name, E-Mail-Adresse, Telefon. Au&szlig;erdem kann hier das Passwort ge&auml;ndert werden.
                            <p><li> <a href='tippzettel.php'><font size=+1>T</font>ipps eingeben</a> <br>
                            Eingabe / &Auml;nderung der <?php echo TURNIERART_KURZ ?>-Tipps. Wurden vom Spieler noch keine Tipps eingegeben kann dies nun hier nachgeholt werden.
                            Es m&uuml;ssen nicht alle Tipps in einem Durchgang eingetragen werden, stattdessen wird f&uuml;r alle nicht
                            eingetragenen Ergebnisse ein 0-0 in die Datenbank geschrieben.
                            <p><li>
                            <?php
                            if (isset($_SESSION["spieler_key"]) AND LINK_STAMMTISCH != "") {
                                echo "<a href='" . LINK_STAMMTISCH . "'><font size=+1>D</font>er legend&auml;re <?php echo TURNIERART_KURZ?>-Stammtisch</a>";
                                echo "<br>Im Stammtisch kann jeder seine Meinung &uuml;ber das " . TURNIERART_KURZ . "-Turnier oder das Leben allgemein kundtun.";
                            }
                            ?>

                    </ul>
                    <p><li> <font size=+2>A</font>uswertung / Statistik<br>
                    Alle wichtigen Daten bez&uuml;glich des Tippspiel sind diesem Bereich zu entnehmen. Es sind hier die Punktest&auml;nde
                    der Spieler, alle bisherigen <?php echo TURNIERART_KURZ ?>-Ergebnisse, die Tippzettel aller Spieler usw. zu finden.
                    <ul>
                        <p><li>
                            <?php
                            if (isset($_SESSION["spieler_key"])) {
                                echo "   <a href='rangliste.php'><font size=+1>A</font>ktuelle Spielerrangliste</a><br>\n";
                            } else {
                                echo "   <font size=+1>A</font>ktuelle Spielerrangliste<br>\n";
                            }
                            ?>
                            Dies ist wohl der wichtigste Punkt: Wer steht wo und wieviele Punkte hat er aktuell beim Tippspiel erreicht?
                            Nur das Warum? kann hier nicht erkl&auml;rt werden, aber daf&uuml;r gibt es ja den Stammtisch ... ;-)


                            <p><li>
                            <?php
                            if (isset($_SESSION["spieler_key"])) {
                                echo "   <a href='tippzettel.php'><font size=+1>E</font>inzelne Tippzettel</a> abrufen<br>\n";
                            } else {
                                echo "   <font size=+1>E</font>inzelne Tippzettel abrufen<br>\n";
                            }
                            ?>
                            Unter diesem Punkt k&ouml;nnen die Tippzettel aller Spiele eingesehen werden, sofern sie von der betreffenden
                            Person auch freigeschaltet wurden. Mit Anpfiff des ersten Spiels der jeweiligen <?php echo TURNIERART_KURZ ?>-Runde werden die Tipps aller
                            Spieler automatisch zur Einsicht durch dritte freigegeben.


                            <p><li>
                            <?php
                            if (isset($_SESSION["spieler_key"])) {
                                echo "   <a href='tippvergleich.php'><font size=+1>T</font>ips aller Spieler</a> zu einem Spiel vergleichen<br>\n";
                            } else {
                                echo "   <font size=+1>T</font>ips aller Spieler zu einem Spiel vergleichen<br>\n";
                            }
                            ?>
                            Um zu einem bestimmten Spiel alle Tipps in einer &Uuml;bersicht vergleichen zu k&ouml;nnen sollte man
                            unbedingt hier reinschauen.

                            <P><LI> <a href='turnier_tabellen.php'><font size=+1>D</font>ie Vorrundentabellen</a><br>
                            Die aktuellen St&auml;nde in den einzelnen <?php echo TURNIERART_KURZ ?>-Gruppen.
                            <P><LI> <a href='turnier_ergebnisse.php'><font size=+1>A</font>lle <?php echo TURNIERART_KURZ ?>-Begegnungen</a><br>
                            Alle Termine der kommenden Spiele sowie alle Ergebnisse der bereits ausgetragenen Begegnungen
                            (bzw. in die DB eingetragen wurden ;) k&ouml;nnen hier eingesehen werden.
                    </ul>
                    <p><li> <font size=+2>A</font>dministration<br>
                    Dieser Bereich geht eigentlich nur dem allm&auml;chtigen Administrator etwas an. Also gleich die Finger
                    davon lassen ...
            </ul>
            <p>
                </div>

                <?php
                echo" </form>\n";
                frame_footer();


                /*
                 * DB-Verbindung sauber beenden
                 */
                mysqli_close($connection);
                ?>