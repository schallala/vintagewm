<?php
SESSION_start();

// Globale Variablen einbinden
include 'functions.php';

// Name des Skripts
$skript_name = "index.php";

// Login pruefen
pruefe_login($skript_name);

$Titel = "Das " . TURNIERART_KURZ . "-Tippspiel-FAQ";

frame_header($skript_name);
?>

<H3> <?php echo $Titel ?> </H3>
<!-- in allen Modi ist der Inhalt ein Formular -->
<p><form action = '<?php echo $skript_name; ?>' method = 'POST'>

    <div align="LEFT">
        <font face="Simpson"><font color="BLUE">
        <ol>
            <li><b>Achtung!</b> Die Wertung der Tipps hat sich etwas ver&auml;ndert. F&uuml;r Kritik, weitere
                Anregungen und sonstige Kommentare diesbez&uuml;glich bin ich immer aufgeschlossen. Die Details
                k&ouml;nnt ihr den <a href=turnier_spielregeln.php>Turnier-Spielregeln</a> entnehmen...
            <li>
                <b><i> Hier ist alles so bunt und anders! Ich bin verwirrt ...</i></b><br>
                Das Tippspiel ist komplett neu geschrieben worden (in 2004), wobei die alten M&ouml;glichkeiten
                (gr&ouml;&szlig;tenteils) bestehen geblieben sind und neue Features hinzugef&uuml;gt
                wurden. Wenn man ein bisschen herumprobiert sollte man (fast) alles wiederfinden. Wer
                irgendetwas vermisst bzw. weitere Anregungen hat kann mir dieses gerne mitteilen.<br>
                Der grunds&auml;tzliche Aufbau soll hier kurz erkl&auml;rt werden:<br>
                <ul>
                    <li>Oben links kann man sich einloggen, damit man als registrierter User alle Bereiche
                        einsehen kann. Als nicht registrierter User ist der Zugriff sehr eigeschr&auml;nkt...<br>
                        Hat sich der Benutzer angemeldet befinden sich dort an Stelle der Authorisierungsaufforderung
                        einige Login-Infos zum User sowie der Button zum Log-Out. Dieser dient dazu, die gesetzten
                        Cookies zu l&ouml;schen und damit ein automatisches Einloggen beim n&auml;chsten Besuch
                        zu unterbinden.
                    <li>Ganz links befindet sich die Haupt-Men&uuml;leiste von der aus die wichtigsten
                        Funktionen direkt aufgerufen werden k&ouml;nnen. Die Leiste ist in zwei Bereiche
                        unterteilt: Der obere sammelt vereinigt Funktionen die dem normalsterblichen Benutzer zur Verf&uuml;gung
                        stehen; der untere bietet weitere Administrationsoptionen f&uuml;r die User mit
                        Admin-Rechten.
                    <li>Im Bereich rechts oben werden alle registrierten Benutzer aufgef&uuml;rt
                        die sich zuletzt am System angemeldet haben. Die Uhrzeit bezieht sich auf die
                        letzte registrierte Aktivit&auml;t des Benutzers. Durch Anklicken des User-Logins k&ouml;nnen
                        alle registrierten Benutzer erfahren wer sich hinter dem Login-Namen verbirgt.
                    <li>Die Leiste ganz rechts stellt im Bereich <i><?php echo TURNIERART_KURZ ?>-Ticker</i> die Ergebnisse der letzten
                        zwei Spiele sowie Infos zu den n&auml;chsten zwei Spielen dar. So ist gleich die Aktualit&auml;t
                        der eingegebenen Spiel-Ergebnisse zu erkennen. Der untere Bereich <i>News</i> f&uuml;hrt
                        die &Uuml;berschriften der zwei aktuellsten Beitr&auml;ge im News-Bereich auf.<br>
                        Neu ist der Info-Bereich f&uuml;r den Stammtisch, wo die j&uuml;ngsten Beitr&auml;ge im
                        G&auml;stebuch aufge&uuml;hrt sind.
                    <li>Ebenfalls relativ neu ist der <i>Fast-Live-Ticker</i>, der w&auml;hrend der laufenden Partien, die aktuellen
                        Spielst&auml;nde anzeigt. Dort ist auch eine Blitzrangliste abrufbar, die auf der Basis der aktuellen 
                        Zwischenergebnisse eine tempor&auml;re Rangliste berechnet. Wer N&auml;heres zum jeweiligen Spiel erfahren m&ouml;chte
                        kann den angezeigten Link anklicken und gelangt so zu einem "richtigen" Spielticker. Nach Spielende 
                        sollten (wenn alles klappt) die Ergebnisse automatisch aus dem Ticker direkt in das Tippspiel &uuml;bernommen und alle 
                        Auswertungen vorgenommen werden.
                </ul>
                <p>Erweiterungen k&ouml;nnten noch folgen ... ;-)
                <p>

            <li>
                <b><i> Es gibt einige Men&uuml;punkte und Optionen, die nicht funktionieren
                        und irgendwie sinnlos erscheinen!</i></b><br>
                Wie gesagt - das System ist noch nicht fertig (auch noch nicht in 2006). Im Moment arbeite ich noch an einigen
                Optionen, so dass immer noch Fehler oder auch tote Links auftreten k&ouml;nnen. Konkret
                geplant sind:
                <ul>
                    <li> Eine M&ouml;glichkeit zum Tippen der KO-Runden, bevor die Partien feststehen (z.B. wegen Urlaub).
                        Hiermit verbunden ist dann ein vollst&auml;ndiger Spielplan zum Turnier. <i>--> erledigt</i>
                    <li> Ein Ticker (Info-Bereich) mit Ergebnissen gerader laufender Partien. Geplant ist, diese
                        Ergebnisse f&uuml;r "Blitzranglisten" zu verwenden, d.h. aus den Live-Ergebnissen k&ouml;nnen sofort
                        die Auswirkungen auf die Tipperrangliste eingesehen werden. <i>--> erledigt</i>
                    <li> Ein Newsletter-System, damit man die aktuellen Ergebnisse, die Tipperrangliste sowie andere
                        Infos zum Turnier und Tippspiel gleich morgens (in Abels-Einheiten) im Email-Postkasten hat.
                    <li> ... und einige Funktionen, die mir die Administration des Tippspiels erleichtern.
                </ul>
                <p>
                    B&uuml;tte, b&uuml;tte - wenn Euch Fehler auffallen: Gleich eine Mail an mich
                    schicken undn nicht irgendwelchen Jokus veranstalten.
                <p>
            <li>
                <b><i> Ich will ohne gro&szlig;en Aufwand tippen - wie geht das am einfachsten?</i></b><br>
                Einfach eine Mail an mich schicken, damit ein passwort eingerichtet wird.
                Dann die pers&ouml;nkichen Daten unter dem Punkt <i>Spielerinfo</i> pr&uuml;fen und ggf. anpassen.
                Dann im </i>Tippzettel</i> Deine Tipps eintragen, den <?php echo TURNIER_SIEGER_ART ?> bestimmen und festlegen,
                ob andere die Tipps vor Start der <?php echo TURNIERART_KURZ ?> einsehen k&ouml;nnen.<br>
                Das Ganze muss vor dem Anpfiff des ersten <?php echo TURNIERART_KURZ ?>-Spiels geschehen. Nach Ende der Vorrunde
                wird jeweils immer die n&auml;chste Runde bis zum Finale getippt - und auch da gilt,
                dass die Tipps bis zum Anpfiff des ersten Spiel der jeweiligen Runde eingegeben werden
                m&uuml;ssen. Mit Anpfiff des ersten Spiels der Runde werden s&auml;mtliche Tipps aller
                Spieler zur Einsicht durch andere freigegeben. Der Cupsiegertipp ist nach Beginn der <?php echo TURNIERART_KURZ ?> nicht mehr zu &auml;ndern!<br>
                Die Rangliste (<i>Auswertung/Statistik</i>) kann jederzeit eingesehen werden und wird m&ouml;glichst schnell aktualisiert.
                Vergleichsm&ouml;glichkeiten zu einem Spiel sind ebenfalls vorhanden. Man kann bei <i>Tippvergleich</i>
                ein Spiel ausw&auml;hlen und sich dazu die Tipps aller Spieler in einer
                &Uuml;bersicht darstellen lassen. Die restlichen Features sind eigentlich nur Beiwerk ....;)
                Wer sich mit seinen Tipps noch nicht ganz sicher ist, kann sich wertvolle Hintergrund-Infos  direkt auf den Turnierseiten
                der beteiligten Mannschaften holen, indem er den Namen der interessierende Mannschaft anklickt (geht fast &uuml;berall).
                <p>

            <li>
                <b><i> Wie kann ich meinen <?php echo TURNIER_SIEGER_ART ?>-Tipp eingeben?</i></b><br>
                Dies kann entweder unter <i>Spielerdaten</i> geschehen oder auch beim Ausf&uuml;llen
                des <i>Tippzettels</i> erledigt werden. Nochmal: Sobald eine Runde angepfiffen wurde sind keine
                &Auml;nderungen an den Tipps mehr m&ouml;glich! (hoffe ich)
                <p>

            <li>
                <b><i> Wieso ist bei mir *MannschaftX* als <?php echo TURNIER_SIEGER_ART ?> eingetragen?</i></b><br>
                Beim Eintragen eines neuen Tippers wird der Cupsieger-Tipp initial gesetzt.
                Jeder Tipper sollte dies entsprechend &auml;ndern, sobald er Zugang zum System hat.
                Wenn Ihr Punkte f&uuml;r den richtigen
                Cupsiegertipp kassieren wollt (<b>satte <?php echo PUNKTE_RICHTIGER_MEISTER ?> Punkte</b>), dann m&uuml;sst
                Ihr Euren Tipp wie unter 3. beschrieben unbedingt anpassen.
                <p>

            <li>
                <b><i> Mein passwort / Login gef&auml;llt mir nicht mehr - wie kann ich es &auml;ndern?</i></b><br>
                <i>Spielerdaten</i> w&auml;hlen, den eigenen Namen aus der Liste selektieren
                und dann die Daten entsprechend &auml;ndern.
                <br>
                Falls jemand sein passwort vergessen hat, kann ich es ihm jederzeit zuschicken.
            <li>
                <b><i> Ich bin nach umfangreicher Recherche mit meinen Tipps unzufrieden - darf ich die noch &auml;ndern?</i></b><br>
                Bis zum Beginn der Spiele der jeweiligen Runde k&ouml;nnen die Tipps unter </i>Tippzettel</i>
                so oft wie Ihr wollt ge&auml;ndert werden. Erst mit Anpfiff wird der Zugang gesperrt.
                <p>


            <li>
                <b><i> Bei der Darstellung der Seiten treten merkw&uuml;rdige Effekte auf - was soll ich tun?</i></b><br>
                Mir Bescheid sagen oder eventuell auf einen Browser des neuen Jahrtausends umsteigen ;-)
                <p>

            <li>
                <b><i> Wieso machst Du so viele Rechtschreibfehler, bevorzugt bei Umlauten? K&ouml;nnen wir Dir bei Deinem L&auml;hga&szlig;teni-Problem helfen?</i></b><br>
                Nein Danke - das hat keine psychischen oder intellektuelle Ursachen! Ich schreibe diese d&auml;mlichen
                HTML-Seiten in einem normalen Editor ohne Rechtschreibpr&uuml;fung und habe dar&uuml;ber hinaus keine Lust,
                mir den ganzen Mist nochmal durchzulesen.
                <p>
<?php
if (!empty($_SESSION["spieler_key"])) {
    echo "<li><b><i> Ich habe so einen anstrengenden Beruf ... / Meine guten Klamotten sind leider alle in der W&auml;sche ... / Mein Hund hat meine Schuhe gefressen ... / ";
    echo "Ich befinde mich auf einem anderen Kontinent ...  und kann dir die Kohle leider nicht direkt &uuml;berbringen. Kann ich dir das Geld eventuell &uuml;berweisen?</i></b><br>";
    echo "Sch&ouml;n, dass du fragst! Bitte verwende folgende Bankverbindungsdaten:<b><br>Torsten Abels<br>netbank, BLZ 20090500<br>KtNr. 7509251</b><p>";
}
?>

            <li>
                <b>	.....................Fortsetzung folgt (eventuell).....................
                    <p>


                        </font></font>

                        </ol>
                        </div>
<?php
echo" </form>\n";
frame_footer();


/*
 * DB-Verbindung sauber beenden
 */
mysqli_close($connection);
?>