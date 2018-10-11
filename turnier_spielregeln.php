<?php
SESSION_start();

// Globale Variablen einbinden
include 'functions.php';

// Name des Skripts
$skript_name = "index.php";

// Login pruefen
pruefe_login($skript_name);

$Titel = "Die Tippspiel-Regeln";

frame_header($skript_name);
?>
<H3> <?php echo $Titel ?> </H3>
<!-- in allen Modi ist der Inhalt ein Formular -->
<p><form action = '<?php echo $skript_name; ?>' method = 'POST'>

    <div align="LEFT">
        <ol>
            <li>
                <font face="Simpson"><font color="BLUE">
                Auch in diesem Jahr ist es m&ouml;glich, die Tipps direkt &uuml;ber
                einen Webbrowser einzugeben. Dazu m&uuml;sst Ihr mir nur eine Mail
                schicken, damit ich ein passwort f&uuml;r Euch einrichten und
                dann per Mail zur&uuml;ckschicken kann. Login und passwort k&ouml;nnen von Euch
                im Bereich <i>Spielerinfo </i> modifiziert
                werden. Bei der Eingabe der Tipps k&ouml;nnen diese optional schon vor
                Beginn der <?php echo TURNIERART_KURZ ?> f&uuml;r andere zur Einsicht freigegeben werden (Standard-m&auml;&szlig;ig
                ist die Freigabe gesetzt). Mit dem ersten Anpfiff
                der jeweiligen <?php echo TURNIERART_KURZ ?>-Runde werden alle Tipps freigegeben und k&ouml;nnen somit eingesehen werden.
                Danach sind &Auml;nderungen nat&uuml;rlich nicht mehr m&ouml;glich ;-)
                <br>Die Tippeingabe &uuml;ber das Web erleichtert mir die Arbeit deutlich,
                doch f&uuml;r die Leute, die dazu keine M&ouml;glichkeiten besitzen, kann
                wie bei den vorigen Tippspiel verfahren werden:<br>
                Tipps werden mir (<a href=mailto:<?php echo WEBMASTER_EMAIL ?>><?php echo WEBMASTER ?></a>) sp&auml;testens
                eine Stunde vor Anpiff der aktuellen <?php echo TURNIERART_KURZ ?>-Runde per Mail zugeschickt oder
                per Telefon (<?php echo WEBMASTER_TELEFON ?>) &uuml;bermittelt. F&uuml;r die Vorrunde w&auml;re
                dies also der 10. Juni bis 20 Uhr.</font></font></li><p>

            <li>
                <font face="Simpson"><font color="BLUE"><b>Die Punkte f&uuml;r die Tipps werden folgenderma&szlig;en verteilt:</b><br>
<?php
// Hier werden die Spielregeln auf Basis der Parameter in globals.php dynamisch erstellt
if (PUNKTE_EXAKTER_TIPP > 0) {
    echo " - f&uuml;r einen exakten Tipp gibt es " . sage_mir(PUNKTE_EXAKTER_TIPP, "Punkt", "Punkte", "en") . "<br>";
}
if (PUNKTE_RICHTIGE_TENDENZ > 0) {
    echo " - f&uuml;r die richtige Tendenz gibt es " . sage_mir(PUNKTE_RICHTIGE_TENDENZ, "Punkt", "Punkte", "en") . "<br>";
}
if (PUNKTE_RICHTIGE_DIFFERENZ > 0) {
    echo " - f&uuml;r die richtig getippte Tordifferenz gibt es " . sage_mir(PUNKTE_RICHTIGE_DIFFERENZ, "Punkt", "Punkte", "en") . "<br>";
}
if (PUNKTE_RICHTIGE_TORANZAHL > 0) {
    echo " - f&uuml;r jede richtig vorausgesagte Anzahl der Tore pro Mannschaft gibt es " . sage_mir(PUNKTE_RICHTIGE_TORANZAHL, "Punkt", "Punkte", "en") . "<br>";
}
echo "Zu beachten ist hierbei, dass sich die Punkte pro Spiel addieren. Somit kann ein Tipper die
			H&ouml;chstpunktzahl von " . sage_mir(PUNKTE_KOMPLETT, "Punkt", "Punkte", "em") . " pro Spiel erreichen, wenn er exakt das richtige
			Ergebnis vorausgesagt hat. ";
if (FLAG_GUMMI_PUNKT > 0) {
    echo "Umgekehrt erh&ouml;ht sich selbst das Konto eines Tipper, der nicht die Tendenz, daf&uuml;r aber die
			Anzahl der Tore einer Mannschaft (mehr ist nicht m&ouml;glich ;-) richtig bestimmt hat, immerhin noch um " . sage_mir(PUNKTE_RICHTIGE_TORANZAHL, "Punkt", "Punkte", "en") . ".<br>";
}
if (PUNKTE_RICHTIGER_MEISTER > 0) {
    echo "Am Ende des Turniers gibt es zus&auml;tzlich noch einmal " . sage_mir(PUNKTE_RICHTIGER_MEISTER, "Punkt", "Punkte", "en") .
    ", wenn der " . TURNIERART_KURZ . "-Sieger VOR(!) dem Turnierstart korrekt vorausgesagt wurde.
			Also unbedingt daran denken VOR dem Anpfiff des ersten Spiels den Tipp entsprechend zu platzieren.";
}
?>

                </font></font></li><p>

            <li>
                <font face="Simpson"><font color="BLUE">Der Einsatz
                betr&auml;gt (bisher) 5 Euro. Wer das Geld nicht pers&ouml;nlich bei mir abgeben
                kann, sollte mir eine Mail schicken und das Geld per Bank&uuml;berweisung abdr&uuml;cken.
                Die drei Erstplatzierten teilen sich den Gewinn nach folgendem Schl&uuml;ssel:</font></font></li>

            <br><font face="Simpson"><font color="BLUE">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            1. Platz ... ca. 50%<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 2.
            Platz ... ca. 30%<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 3. Platz ... ca. 20%</font></font>
            <br><font face="Simpson"><font color="#000099">Die konkreten Betr&auml;ge
            sind nat&uuml;rlich von der Teilnehmerzahl abh&auml;ngig.
            </font></font></li><p>
            <li>
                <font face="Simpson"><font color="BLUE"><b>M&ouml;glichkeiten zum &Uuml;bermitteln des Wetteinsatzes</b><p> 
                    In diesem Jahr gibt es folgende Optionen euren Beitrag f&uuml; meinen Vorruhestand zu &uuml;bermitteln:
                <ul>
                    <li>
                        <img title='Oldskool forever!' src='<?php echo IMAGE_PATH . "/doctor-fred.png" ?>' align=center width="40px" height="40px">
                        <i>Reallife</i> - Antreffen (nach Anmeldung) k&ouml;nnt ihr mich in meinem B&uuml;ro in Oldenburg im Alten Postweg 11, allerdings nur bis zum Beginn der EM :-)
                        Die Kollegen im Escherweg d&uuml;rfen auch in meiner Zweigstelle (Filialleiter Herr H&uuml;sing, Escherweg 3, Raum 1.27?) bezahlen. Nachteil ist, dass ihr euch mit ihm unterhalten m&uuml;&szlig;t, da er  zu viel Zeit hat. ;-)
                    <li>
                        <img title="I'm a Bankster!" src='<?php echo IMAGE_PATH . "/banksters2.jpg" ?>' align=center width="40px" height="40px">
                        <i>Bank&uuml;berweisung</i> - Leute, denen der Weg zu weit ist oder an einer Kontaktallergie leiden, d&uuml;rfen gerne auf mein Konto &uuml;berweisen
                        <br>ING-DiBa
                        <br>IBAN: DE66500105175423367171
                        <br>BIC: INGDDEFFXXX
                        <br>Kontoinhaber/-in: Torsten Abels 
                    <li>
                        <a href="https://paypal.me/tabels/5" TARGET = '_blank'><img  title='Raketentechniker' src='<?php echo IMAGE_PATH . "/Paypal_2012.png" ?>' align=center width="40px" height="40px"></a>
                        <i>Paypal.me</i> 
                        - Mit einem Klick  k&ouml;nnen crazy fortschrittliche Inhaber eines Paypal-Kontos mir das Geld zukommen lassen. Daf&uuml;r werden <i>nur</i> dann <s><i>leider</i></s> <s>auch</s> <s>keine</s> Geb&uuml;hren erhoben, <i>wenn ihr euer Paypal-Konto mit einer Kreditkarte verbunden habt</i>.
                </ul>

            <li><font face="Simpson"><font color="BLUE">Die Ranglisten und die ganzen Statistiken
                werden sobald wie m&ouml;glich nach jeweiligem Spielende aktualisiert. Manchmal habe ich auch was zu tun -
                also nicht gleich nach f&uuml;nf Minuten tonnenweise Beschwerdemails verschicken ;-)
                </font></font></li>

        </ol>
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