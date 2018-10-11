<?php

/*
 * Globale Variablen, die in verschiedenen Skripten
 * benoetigt werden
 */



// Verbindungsdaten definieren
$db_host = "localhost";
$db_user = "xxx"; // User unter dem sich das System anmeldet
$db_password = "xxx"; // passwort fuer das DB-Schema
$database = "wm2018";

/*
 * Datenbankverbindung herstellen
 */

// Verbindung zur Datenbank herstellen
$connection = mysqli_connect($db_host, $db_user, $db_password, $database)
        or die("Verbindungsaufbau gescheitert!");
//  mysqli_select_db($connection, $database)
//                or die ("Datenbank konnte nicht gefunden werden!");
// abhaengig vom Server-Standort die Zeit setzen
// mySQL
// $result = mysqli_query($connection, "SET SESSION time_zone = '+02:00'") or die ("Zeit setzen fehlgeschlagen!");
// PHP
date_default_timezone_set('Europe/Berlin');
// Relikte aus alter Zeit
$Titel = "";
$Meldung = "";

/**
 * 	KONSTANTEN
 */
// Verhalten festlegen
define('AUTO_KO_BERECHNEN', 0);  // sollen KO-Partien automatisch eingetragen werden (ja/nein)?
define('AUTO_RUNDE_FREIGEBEN', 0); // soll naechste Runde automatisch freigegeben werden (ja/nein)?
define('AUTO_SIEGER_BERECHNEN', 0); // soll der Turniersieger automatisch eingetragen werden (ja/nein)?
define('AUTO_ERGEBNISSE_SUCHEN', 0); // sollen Ergebnisse bei kicker.de gesucht werden (ja/nein)?
// Der Ort des Tippspiels
define('SERVER_HOME', "http://192.168.178.50/");
define('TIPPSPIEL_HOME', SERVER_HOME . "wm2018/");
//  define('STAMMTISCH_HOME', SERVER_HOME . "stammtisch/");
define('STAMMTISCH_HOME', TIPPSPIEL_HOME . "stammtisch/");

// Namen der Benutzerrollen
// standardmae&szlig;ig sind drei ROllen eingerichtet
define('ADMINROLLE', "ADMIN"); // kann alles, darf alles
define('TIPPERROLLE', "TIPPER"); // kann Ergebnisse eingeben (Urlaubsvertretung)
define('HIWIROLLE', "HIWI-ADMIN"); // kann eigene Daten bearbeiten
// Turnierart
define('TURNIERART_KURZ', "WM"); // alle (viele) Abkuerzungen koennen hier zentral modifiziert werden
define('TURNIERART_LANG', "Weltmeisterschaft"); // kommt nicht so oft vor ..
define('TURNIER_SIEGER_ART', "Weltmeister"); // kommt nicht so oft vor ..
define('TURNIER_JAHR', "2018"); // eher nebensaechlich
// weiter wichtige Textkonstanten
define('TITEL_TIPPSPIEL', "Abels WM-Tippspiel 2018");
define('WEBMASTER', "Torsten Abels");
define('WEBMASTER_EMAIL', "torsten.abels@googlemail.com");
define('WEBMASTER_TELEFON', "xxx/xxx");
define('VERSIONSNUMMER', "0.2.0.1.8");

// Konstanten fuer die Punktevergabe	(Punkte addieren sich gegenseitig)
// durch Anpassung ist hier jederzeit ein eigenes Punktesystem konfigurierbar
define('PUNKTE_RICHTIGER_MEISTER', 10); // richtiger Tipp des Turniersiegers
define('PUNKTE_EXAKTER_TIPP', 1);   // Sonderbonus bei exaktem Tipp
define('PUNKTE_RICHTIGE_TENDENZ', 2);  // Punkte fuer richtige Vorhersage der Tendenz
define('PUNKTE_RICHTIGE_DIFFERENZ', 1); // Punkte fuer richtige Tordifferenz
define('PUNKTE_RICHTIGE_TORANZAHL', 1); // Punkte fuer richtige Anzahl geschossener Tore
define('FLAG_GUMMI_PUNKT', 1);   // soll trotz falscher Tendenz ein Trostpunkt bei richtiger
// Toranzahl vergeben werden (1=ja, 0=nein)
// in diesem System gibt es folglich  sechs Punkte bei einem exaktem Tipp
define('PUNKTE_KOMPLETT', PUNKTE_EXAKTER_TIPP + PUNKTE_RICHTIGE_TENDENZ + PUNKTE_RICHTIGE_DIFFERENZ + (PUNKTE_RICHTIGE_TORANZAHL * 2));

// Punkte-Vergabe fuer Mannschaften
// entsprechend dem Turniermodus
define('PUNKTE_SIEG', 3);   // Sieg = 3 Punkte
define('PUNKTE_REMIS', 1);  // Remis = 1 Punkt

define('COOKIE_KEY_LOGIN', "atippl");  // Cookie-Key fuer das Login
// Cookie-Settings (bei identischen Tippspiel-Servern sollten sich die Keys unterscheiden!)
define('COOKIE_KEY_PASSWORD', "atippp"); // Cookie-Key fuer das passwort
// Datei-Pfade definieren
define('CSS_DATEI', "format.css"); // sehr wichtig: die Style-Angaben

define('IMAGE_PATH', TIPPSPIEL_HOME . "images"); // Pfad zu den Grafiken
define('SMILEY_PATH', TIPPSPIEL_HOME . "smilies"); // Pfad zu den Smilies
define('FLAGS_PATH', TIPPSPIEL_HOME . "flags/"); // Pfad zu den Mannschaftsflaggen
// Spezialgrafiken (werden in Rangliste verwendet)
define('GRAFIK_AUSGESCHIEDEN', "images/totenk4.gif");
define('GRAFIK_RICHTIGE_TENDENZ', "smilies/005.gif");
define('GRAFIK_EXAKTER_TIPP', "smilies/dance.gif");
define('GRAFIK_NULL_PUNKTE', "smilies/wut.gif");
define('GRAFIK_GUMMI_PUNKT', "smilies/nein.gif");
define('IMAGE_BACKGROUND', "soc_back.jpg");

// Ueber diese Parameter lassen sich einzelne Menuepunkte deaktivieren
define('LINK_STAMMTISCH', STAMMTISCH_HOME . "index.php");
define('LINK_FORUM', "");
define('LINK_CHAT', "");
//  define('LINK_CHAT', TIPPSPIEL_HOME . "chat_info.php");
define('LINK_RSS', TIPPSPIEL_HOME . "tux_tippspiel_news.php");
?>