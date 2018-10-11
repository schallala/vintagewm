
-- Tabellenstruktur für Tabelle `benutzergruppe`
--

CREATE TABLE IF NOT EXISTS `benutzergruppe` (
  `gruppen_key` int(11) NOT NULL,
  `bezeichnung` varchar(50) NOT NULL,
  `position` int(11) NOT NULL,
  UNIQUE KEY `gruppen_key` (`gruppen_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `benutzergruppe`
--

INSERT INTO `benutzergruppe` (`gruppen_key`, `bezeichnung`, `position`) VALUES(1, 'Fußball-Experte', 1);
INSERT INTO `benutzergruppe` (`gruppen_key`, `bezeichnung`, `position`) VALUES(2, 'Fußball-Kenner', 2);
INSERT INTO `benutzergruppe` (`gruppen_key`, `bezeichnung`, `position`) VALUES(3, 'Fußball-Laien', 3);
INSERT INTO `benutzergruppe` (`gruppen_key`, `bezeichnung`, `position`) VALUES(4, 'Mädchen', 4);
INSERT INTO `benutzergruppe` (`gruppen_key`, `bezeichnung`, `position`) VALUES(0, 'Gesamtwertung', 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `blitz_ergebnis`
--

CREATE TABLE IF NOT EXISTS `blitz_ergebnis` (
  `key_spiel` int(11),
  `tore_ms1` int(11),
  `tore_ms2` int(11),
  `tendenz` int(11),
  `status` int(11)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `blitzrangliste`
--

CREATE TABLE IF NOT EXISTS `blitzrangliste` (
  `spieler_key` int(11) NOT NULL DEFAULT '0',
  `spielername` varchar(20),
  `vorname` varchar(20),
  `telefon` varchar(20),
  `email` varchar(50),
  `weltmeister` int(11),
  `bezahlt` varchar(5),
  `punkte` int(11),
  `exakte_tips` int(11),
  `richtige_Tendenz` int(11),
  `passwort` varchar(20),
  `Freigabe` tinyint(1) NOT NULL DEFAULT '0',
  `Newsletter` tinyint(1) NOT NULL DEFAULT '0',
  `Rolle` varchar(15) NOT NULL DEFAULT '0',
  `login` varchar(15) NOT NULL DEFAULT '',
  `ICQ_NUMMER` varchar(20)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `gruppe`
--

CREATE TABLE IF NOT EXISTS `gruppe` (
  `gruppe_key` smallint(6) NOT NULL DEFAULT '0',
  `Bezeichnung` varchar(30) NOT NULL DEFAULT '',
  `sortierung` smallint(6),
  PRIMARY KEY (`gruppe_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `gruppe`
--

INSERT INTO `gruppe` (`gruppe_key`, `Bezeichnung`, `sortierung`) VALUES(1, 'Gruppe A', 1);
INSERT INTO `gruppe` (`gruppe_key`, `Bezeichnung`, `sortierung`) VALUES(2, 'Gruppe B', 2);
INSERT INTO `gruppe` (`gruppe_key`, `Bezeichnung`, `sortierung`) VALUES(3, 'Gruppe C', 3);
INSERT INTO `gruppe` (`gruppe_key`, `Bezeichnung`, `sortierung`) VALUES(4, 'Gruppe D', 4);
INSERT INTO `gruppe` (`gruppe_key`, `Bezeichnung`, `sortierung`) VALUES(5, 'Gruppe E', 5);
INSERT INTO `gruppe` (`gruppe_key`, `Bezeichnung`, `sortierung`) VALUES(6, 'Gruppe F', 6);
INSERT INTO `gruppe` (`gruppe_key`, `Bezeichnung`, `sortierung`) VALUES(7, 'Gruppe G', 7);
INSERT INTO `gruppe` (`gruppe_key`, `Bezeichnung`, `sortierung`) VALUES(8, 'Gruppe H', 8);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `gruppenspiel`
--

CREATE TABLE IF NOT EXISTS `gruppenspiel` (
  `spiel_key` int(11) NOT NULL AUTO_INCREMENT,
  `spiel_art` smallint(6),
  `gruppe` smallint(6),
  `key_ms1` int(11),
  `tore_ms1` int(11),
  `key_ms2` int(11),
  `tore_ms2` int(11),
  `tendenz` int(11),
  `datum` date,
  `zeit` time,
  `Status` smallint(6) NOT NULL DEFAULT '0',
  `bezeichnung` varchar(100),
  `auto_ko_fk_ms1` int(11),
  `auto_ko_fk_ms2` int(11),
  `auto_gruppe_fk_ms1` int(11),
  `auto_gruppe_fk_ms2` int(11),
  `auto_platz_ms1` int(11),
  `auto_platz_ms2` int(11),
  `ticker_url` varchar(200),
  `ticker_spiel_id` varchar(100),
  `bezeichnung_detail` varchar(200),
  PRIMARY KEY (`spiel_key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=100;

--
-- Daten für Tabelle `gruppenspiel`
--

INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(1, 1, 1, 1, 5, 2, 0, 1, '2018-06-14', '17:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/7064', 'beg7064', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(2, 1, 1, 3, 0, 4, 1, 2, '2018-06-15', '14:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/7066', 'beg7066', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(3, 1, 1, 1, 3, 3, 1, 1, '2018-06-19', '20:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/7096', 'beg7096', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(4, 1, 1, 4, 1, 2, 0, 1, '2018-06-20', '17:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/7098', 'beg7098', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(5, 1, 1, 4, 3, 1, 0, 1, '2018-06-25', '16:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/7127#/7127/7128', 'beg7128', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(6, 1, 1, 2, 2, 3, 1, 1, '2018-06-25', '16:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/7127#/7127/7129', 'beg7129', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(7, 1, 2, 5, 3, 6, 3, 0, '2018-06-15', '20:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/7070', 'beg7070', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(8, 1, 2, 7, 0, 8, 1, 2, '2018-06-15', '17:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/7068', 'beg7068', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(9, 1, 2, 5, 1, 7, 0, 1, '2018-06-20', '14:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/7100', 'beg7100', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(10, 1, 2, 8, 0, 6, 1, 2, '2018-06-20', '20:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/7102', 'beg7102', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(11, 1, 2, 8, 1, 5, 1, 0, '2018-06-25', '20:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/7130#/7130/7132', 'beg7132', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(12, 1, 2, 6, 2, 7, 2, 0, '2018-06-25', '20:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/7130#/7130/7131', 'beg7131', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(13, 1, 3, 9, 2, 10, 1, 1, '2018-06-16', '12:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/7072', 'beg7072', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(14, 1, 3, 11, 0, 12, 1, 2, '2018-06-16', '18:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/7074', 'beg7074', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(15, 1, 3, 9, 1, 11, 0, 1, '2018-06-21', '17:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/7106', 'beg7106', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(16, 1, 3, 12, 1, 10, 1, 0, '2018-06-21', '14:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/7104', 'beg7104', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(17, 1, 3, 12, 0, 9, 0, 0, '2018-06-26', '16:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/7133#/7133/7134', 'beg7134', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(18, 1, 3, 10, 0, 11, 2, 2, '2018-06-26', '16:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/7133#/7133/7134', 'beg7135', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(19, 1, 4, 13, 1, 14, 1, 0, '2018-06-16', '15:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/7076', 'beg7076', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(20, 1, 4, 15, 2, 16, 0, 1, '2018-06-16', '21:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/7078', 'beg7078', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(21, 1, 4, 13, 0, 15, 3, 2, '2018-06-21', '20:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/7108', 'beg7108', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(22, 1, 4, 16, 2, 14, 0, 1, '2018-06-22', '17:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/7110', 'beg7110', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(23, 1, 4, 16, 1, 13, 2, 2, '2018-06-26', '20:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/7136#/7136/7138', 'beg7138', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(24, 1, 4, 14, 1, 15, 2, 2, '2018-06-26', '20:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/7136#/7136/7137', 'beg7137', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(25, 1, 5, 17, 1, 18, 1, 0, '2018-06-17', '20:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/7082', 'beg7082', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(26, 1, 5, 19, 0, 20, 1, 2, '2018-06-17', '14:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/7080', 'beg7080', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(27, 1, 5, 17, 2, 19, 0, 1, '2018-06-22', '14:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/7112', 'beg7112', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(28, 1, 5, 20, 1, 18, 2, 2, '2018-06-22', '20:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/7114', 'beg7114', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(29, 1, 5, 20, 0, 17, 2, 2, '2018-06-27', '20:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/7139#/7139/7140', 'beg7140', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(30, 1, 5, 18, 2, 19, 2, 0, '2018-06-27', '20:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/7139#/7139/7141', 'beg7141', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(31, 1, 6, 21, 0, 22, 1, 2, '2018-06-17', '17:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/7084', 'beg7084', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(32, 1, 6, 23, 1, 24, 0, 1, '2018-06-18', '14:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/7086', 'beg7086', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(33, 1, 6, 21, 2, 23, 1, 1, '2018-06-23', '20:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/7118', 'beg7118', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(34, 1, 6, 24, 1, 22, 2, 2, '2018-06-23', '17:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/7116', 'beg7116', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(35, 1, 6, 24, 2, 21, 0, 1, '2018-06-27', '16:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/5005#/5005/5006', 'beg5006', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(36, 1, 6, 22, 0, 23, 3, 2, '2018-06-27', '16:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/5005#/5005/5007', 'beg5007', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(37, 1, 7, 25, 3, 26, 0, 1, '2018-06-18', '17:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/7088', 'beg7088', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(38, 1, 7, 27, 1, 28, 2, 2, '2018-06-18', '20:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/7090', 'beg7090', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(39, 1, 7, 25, 5, 27, 2, 1, '2018-06-23', '14:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/7120', 'beg7120', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(40, 1, 7, 28, 6, 26, 1, 1, '2018-06-24', '14:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/7122', 'beg7122', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(41, 1, 7, 28, 0, 25, 1, 2, '2018-06-28', '20:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/7145#/7145/7146', 'beg5007', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(42, 1, 7, 26, 1, 27, 2, 2, '2018-06-28', '20:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/7145#/7145/7147', 'beg7147', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(43, 1, 8, 29, 1, 30, 2, 2, '2018-06-19', '17:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/7094', 'beg7094', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(44, 1, 8, 31, 1, 32, 2, 2, '2018-06-19', '14:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/7092', 'beg7092', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(45, 1, 8, 29, 0, 31, 3, 2, '2018-06-24', '20:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/7126', 'beg7126', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(46, 1, 8, 32, 2, 30, 2, 0, '2018-06-24', '17:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/7124', 'beg7124', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(47, 1, 8, 32, 0, 29, 1, 2, '2018-06-28', '16:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/7148#/7148/7150', 'beg7150', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(48, 1, 8, 30, 0, 31, 1, 2, '2018-06-28', '16:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www1.sportschau.de/ticker/html/7148#/7148/7149', 'beg7149', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(49, 2, NULL, 4, 2, 5, 1, 1, '2018-06-30', '20:00:00', 1, 'Achtelfinale 2', NULL, NULL, 1, 2, 1, 2, 'http://www1.sportschau.de/ticker/html/7179', 'beg7179', 'Zweiter Gruppe A - Zweiter Gruppe C');
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(50, 2, NULL, 6, 4, 1, 5, 2, '2018-07-01', '16:00:00', 1, 'Achtelfinale 3', NULL, NULL, 2, 1, 1, 2, 'http://www1.sportschau.de/ticker/html/7181', 'beg7181', 'Sieger Gruppe B - Dritter Gruppe A/C/D');
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(51, 2, NULL, 9, 4, 13, 3, 1, '2018-06-30', '16:00:00', 1, 'Achtelfinale 1', NULL, NULL, 3, 4, 1, 2, 'http://www1.sportschau.de/ticker/html/7177', 'beg7177', 'Sieger Gruppe D - Dritter Gruppe B/E/F');
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(52, 2, NULL, 15, 4, 12, 3, 1, '2018-07-01', '20:00:00', 1, 'Achtelfinale 4', NULL, NULL, 4, 3, 1, 2, 'http://www1.sportschau.de/ticker/html/7183', 'beg7183', 'Sieger Gruppe A - Dritter Gruppe C/D/E');
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(53, 2, NULL, 17, 2, 22, 0, 1, '2018-07-02', '16:00:00', 1, 'Achtelfinale 5', NULL, NULL, 5, 6, 1, 2, 'http://www1.sportschau.de/ticker/html/7185', 'beg7185', 'Sieger Gruppe C - Dritter Gruppe A/B/F');
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(54, 2, NULL, 23, 1, 18, 0, 1, '2018-07-03', '16:00:00', 1, 'Achtelfinale 7', NULL, NULL, 6, 5, 1, 2, 'http://www1.sportschau.de/ticker/html/7187', 'beg7187', 'Sieger Gruppe F - Zweiter Gruppe E');
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(55, 2, NULL, 25, 3, 32, 2, 1, '2018-07-02', '20:00:00', 1, 'Achtelfinale 6', NULL, NULL, 7, 8, 1, 2, 'http://www1.sportschau.de/ticker/html/7189', 'beg7189', 'Sieger Gruppe E - Zweiter Gruppe D');
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(56, 2, NULL, 31, 4, 28, 5, 2, '2018-07-03', '20:00:00', 1, 'Achtelfinale 8', NULL, NULL, 8, 7, 1, 2, 'http://www1.sportschau.de/ticker/html/7191', 'beg7191', 'Zweiter Gruppe B - Zweiter Gruppe F');
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(57, 3, NULL, 4, 0, 9, 2, 2, '2018-07-06', '16:00:00', 1, 'Viertelfinale 1', 51, 49, NULL, NULL, 1, 1, 'http://www1.sportschau.de/ticker/html/7193', 'beg7193', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(58, 3, NULL, 17, 1, 25, 2, 2, '2018-07-06', '20:00:00', 1, 'Viertelfinale 2', 53, 55, NULL, NULL, 1, 1, 'http://www1.sportschau.de/ticker/html/7195', 'beg7195', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(59, 3, NULL, 23, 0, 28, 2, 2, '2018-07-07', '16:00:00', 1, 'Viertelfinale 3', 54, 56, NULL, NULL, 1, 1, 'http://www1.sportschau.de/ticker/html/7197', 'beg7197', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(60, 3, NULL, 1, 5, 15, 6, 2, '2018-07-07', '20:00:00', 1, 'Viertelfinale 4', 50, 52, NULL, NULL, 1, 1, 'http://www1.sportschau.de/ticker/html/7199', 'beg7199', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(61, 4, NULL, 9, 1, 25, 0, 1, '2018-07-10', '20:00:00', 1, 'Halbfinale A', 57, 58, NULL, NULL, 1, 1, 'http://www1.sportschau.de/ticker/html/7227', 'beg7227', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(62, 4, NULL, 28, 1, 15, 2, 2, '2018-07-11', '20:00:00', 1, 'Halbfinale B', 59, 60, NULL, NULL, 1, 1, 'http://www1.sportschau.de/ticker/html/7226#/7226/7228', 'beg7228', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(64, 6, NULL, 9, 4, 15, 2, 1, '2018-07-15', '17:00:00', 1, 'Finale', 61, 62, NULL, NULL, 1, 1, 'http://www1.sportschau.de/ticker/html/7337', 'beg7337', NULL);
INSERT INTO `gruppenspiel` (`spiel_key`, `spiel_art`, `gruppe`, `key_ms1`, `tore_ms1`, `key_ms2`, `tore_ms2`, `tendenz`, `datum`, `zeit`, `Status`, `bezeichnung`, `auto_ko_fk_ms1`, `auto_ko_fk_ms2`, `auto_gruppe_fk_ms1`, `auto_gruppe_fk_ms2`, `auto_platz_ms1`, `auto_platz_ms2`, `ticker_url`, `ticker_spiel_id`, `bezeichnung_detail`) VALUES(63, 5, NULL, 25, 2, 28, 0, 1, '2018-07-14', '16:00:00', 1, 'Spiel um Platz 3', 61, 62, NULL, NULL, 2, 2, 'http://www1.sportschau.de/ticker/html/7335', 'beg7335', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `logging`
--

CREATE TABLE IF NOT EXISTS `logging` (
  `nummer` int(11) NOT NULL AUTO_INCREMENT,
  `spieler` smallint(6) NOT NULL DEFAULT '0',
  `datum` date NOT NULL DEFAULT '0000-00-00',
  `zeit` time NOT NULL DEFAULT '00:00:00',
  `source` varchar(30),
  `ip_adresse` varchar(20),
  `logtext` varchar(200),
  PRIMARY KEY (`nummer`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mannschaft`
--

CREATE TABLE IF NOT EXISTS `mannschaft` (
  `mannschaft_key` int(11) NOT NULL AUTO_INCREMENT,
  `mannschaftsname` varchar(20),
  `gruppe` varchar(5),
  `anz_spiele` int(11),
  `punkte` int(11),
  `plusTore` int(11),
  `minusTore` int(11),
  `tordifferenz` int(11),
  `info_link` varchar(200),
  `STATUS` int(11),
  `GEWINNER` int(1) NOT NULL DEFAULT '0',
  `flagge` varchar(200),
  `IS_NULL` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`mannschaft_key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=100 ;

--
-- Daten für Tabelle `mannschaft`
--

INSERT INTO `mannschaft` (`mannschaft_key`, `mannschaftsname`, `gruppe`, `anz_spiele`, `punkte`, `plusTore`, `minusTore`, `tordifferenz`, `info_link`, `STATUS`, `GEWINNER`, `flagge`, `IS_NULL`) VALUES(1, 'Russland', '1', 3, 6, 8, 4, 4, 'http://de.fifa.com/worldcup/stories/y=2017/m=11/news=russia-2921194.html', 0, 0, 'rus.png', 0);
INSERT INTO `mannschaft` (`mannschaft_key`, `mannschaftsname`, `gruppe`, `anz_spiele`, `punkte`, `plusTore`, `minusTore`, `tordifferenz`, `info_link`, `STATUS`, `GEWINNER`, `flagge`, `IS_NULL`) VALUES(2, 'Saudi Arabien', '1', 3, 3, 2, 7, -5, 'http://de.fifa.com/worldcup/stories/y=2017/m=11/news=saudi-arabia-2921208.html', 1, 0, 'ksa.png', 0);
INSERT INTO `mannschaft` (`mannschaft_key`, `mannschaftsname`, `gruppe`, `anz_spiele`, `punkte`, `plusTore`, `minusTore`, `tordifferenz`, `info_link`, `STATUS`, `GEWINNER`, `flagge`, `IS_NULL`) VALUES(3, 'Ägypten', '1', 3, 0, 2, 6, -4, 'http://de.fifa.com/worldcup/stories/y=2017/m=11/news=egypt-2921199.html', 1, 0, 'egy.png', 0);
INSERT INTO `mannschaft` (`mannschaft_key`, `mannschaftsname`, `gruppe`, `anz_spiele`, `punkte`, `plusTore`, `minusTore`, `tordifferenz`, `info_link`, `STATUS`, `GEWINNER`, `flagge`, `IS_NULL`) VALUES(4, 'Uruguay', '1', 3, 9, 5, 0, 5, 'http://de.fifa.com/worldcup/stories/y=2017/m=11/news=uruguay-2921215.html', 0, 0, 'uru.png', 0);
INSERT INTO `mannschaft` (`mannschaft_key`, `mannschaftsname`, `gruppe`, `anz_spiele`, `punkte`, `plusTore`, `minusTore`, `tordifferenz`, `info_link`, `STATUS`, `GEWINNER`, `flagge`, `IS_NULL`) VALUES(5, 'Portugal', '2', 3, 5, 5, 4, 1, 'http://de.fifa.com/worldcup/stories/y=2017/m=11/news=portugal-2921193.html', 0, 0, 'por.png', 0);
INSERT INTO `mannschaft` (`mannschaft_key`, `mannschaftsname`, `gruppe`, `anz_spiele`, `punkte`, `plusTore`, `minusTore`, `tordifferenz`, `info_link`, `STATUS`, `GEWINNER`, `flagge`, `IS_NULL`) VALUES(6, 'Spanien', '2', 3, 5, 6, 5, 1, 'http://de.fifa.com/worldcup/stories/y=2017/m=11/news=spain-2921196.html', 0, 0, 'esp.png', 0);
INSERT INTO `mannschaft` (`mannschaft_key`, `mannschaftsname`, `gruppe`, `anz_spiele`, `punkte`, `plusTore`, `minusTore`, `tordifferenz`, `info_link`, `STATUS`, `GEWINNER`, `flagge`, `IS_NULL`) VALUES(7, 'Marokko', '2', 3, 1, 2, 4, -2, 'http://de.fifa.com/worldcup/stories/y=2017/m=11/news=morocco-2921201.html', 1, 0, 'mar.png', 0);
INSERT INTO `mannschaft` (`mannschaft_key`, `mannschaftsname`, `gruppe`, `anz_spiele`, `punkte`, `plusTore`, `minusTore`, `tordifferenz`, `info_link`, `STATUS`, `GEWINNER`, `flagge`, `IS_NULL`) VALUES(8, 'Iran', '2', 3, 4, 2, 2, 0, 'http://de.fifa.com/worldcup/stories/y=2017/m=11/news=iran-2921205.html', 1, 0, 'irn.png', 0);
INSERT INTO `mannschaft` (`mannschaft_key`, `mannschaftsname`, `gruppe`, `anz_spiele`, `punkte`, `plusTore`, `minusTore`, `tordifferenz`, `info_link`, `STATUS`, `GEWINNER`, `flagge`, `IS_NULL`) VALUES(9, 'Frankreich', '3', 3, 7, 3, 1, 2, 'http://de.fifa.com/worldcup/stories/y=2017/m=11/news=france-2921189.html', 1, 1, 'fra.png', 0);
INSERT INTO `mannschaft` (`mannschaft_key`, `mannschaftsname`, `gruppe`, `anz_spiele`, `punkte`, `plusTore`, `minusTore`, `tordifferenz`, `info_link`, `STATUS`, `GEWINNER`, `flagge`, `IS_NULL`) VALUES(10, 'Australien', '3', 3, 1, 2, 5, -3, 'http://de.fifa.com/worldcup/stories/y=2017/m=11/news=australia-2921204.html', 1, 0, 'aus.png', 0);
INSERT INTO `mannschaft` (`mannschaft_key`, `mannschaftsname`, `gruppe`, `anz_spiele`, `punkte`, `plusTore`, `minusTore`, `tordifferenz`, `info_link`, `STATUS`, `GEWINNER`, `flagge`, `IS_NULL`) VALUES(11, 'Peru', '3', 3, 3, 2, 2, 0, 'http://de.fifa.com/worldcup/stories/y=2017/m=11/news=peru-2921214.html', 1, 0, 'per.png', 0);
INSERT INTO `mannschaft` (`mannschaft_key`, `mannschaftsname`, `gruppe`, `anz_spiele`, `punkte`, `plusTore`, `minusTore`, `tordifferenz`, `info_link`, `STATUS`, `GEWINNER`, `flagge`, `IS_NULL`) VALUES(12, 'Dänemark', '3', 3, 5, 2, 1, 1, 'http://de.fifa.com/worldcup/stories/y=2017/m=11/news=denmark-2921187.html', 0, 0, 'den.png', 0);
INSERT INTO `mannschaft` (`mannschaft_key`, `mannschaftsname`, `gruppe`, `anz_spiele`, `punkte`, `plusTore`, `minusTore`, `tordifferenz`, `info_link`, `STATUS`, `GEWINNER`, `flagge`, `IS_NULL`) VALUES(13, 'Argentinien', '4', 3, 4, 3, 5, -2, 'http://de.fifa.com/worldcup/stories/y=2017/m=11/news=argentina-2921174.html', 0, 0, 'arg.png', 0);
INSERT INTO `mannschaft` (`mannschaft_key`, `mannschaftsname`, `gruppe`, `anz_spiele`, `punkte`, `plusTore`, `minusTore`, `tordifferenz`, `info_link`, `STATUS`, `GEWINNER`, `flagge`, `IS_NULL`) VALUES(14, 'Island', '4', 3, 1, 2, 5, -3, 'http://de.fifa.com/worldcup/stories/y=2017/m=11/news=iceland-2921191.html', 1, 0, 'isl.png', 0);
INSERT INTO `mannschaft` (`mannschaft_key`, `mannschaftsname`, `gruppe`, `anz_spiele`, `punkte`, `plusTore`, `minusTore`, `tordifferenz`, `info_link`, `STATUS`, `GEWINNER`, `flagge`, `IS_NULL`) VALUES(15, 'Kroatien', '4', 3, 9, 7, 1, 6, 'http://de.fifa.com/worldcup/stories/y=2017/m=11/news=croatia-2921186.html', 0, 0, 'cro.png', 0);
INSERT INTO `mannschaft` (`mannschaft_key`, `mannschaftsname`, `gruppe`, `anz_spiele`, `punkte`, `plusTore`, `minusTore`, `tordifferenz`, `info_link`, `STATUS`, `GEWINNER`, `flagge`, `IS_NULL`) VALUES(16, 'Nigeria', '4', 3, 3, 3, 4, -1, 'http://de.fifa.com/worldcup/stories/y=2017/m=11/news=nigeria-2921200.html', 1, 0, 'nga.png', 0);
INSERT INTO `mannschaft` (`mannschaft_key`, `mannschaftsname`, `gruppe`, `anz_spiele`, `punkte`, `plusTore`, `minusTore`, `tordifferenz`, `info_link`, `STATUS`, `GEWINNER`, `flagge`, `IS_NULL`) VALUES(17, 'Brasilien', '5', 3, 7, 5, 1, 4, 'http://de.fifa.com/worldcup/stories/y=2017/m=11/news=brazil-2921212.html', 0, 0, 'bra.png', 0);
INSERT INTO `mannschaft` (`mannschaft_key`, `mannschaftsname`, `gruppe`, `anz_spiele`, `punkte`, `plusTore`, `minusTore`, `tordifferenz`, `info_link`, `STATUS`, `GEWINNER`, `flagge`, `IS_NULL`) VALUES(18, 'Schweiz', '5', 3, 5, 5, 4, 1, 'http://de.fifa.com/worldcup/stories/y=2017/m=11/news=switzerland-2921197.html', 0, 0, 'sui.png', 0);
INSERT INTO `mannschaft` (`mannschaft_key`, `mannschaftsname`, `gruppe`, `anz_spiele`, `punkte`, `plusTore`, `minusTore`, `tordifferenz`, `info_link`, `STATUS`, `GEWINNER`, `flagge`, `IS_NULL`) VALUES(19, 'Costa Rica', '5', 3, 1, 2, 5, -3, 'http://de.fifa.com/worldcup/stories/y=2017/m=11/news=costa-rica-2921209.html', 1, 0, 'crc.png', 0);
INSERT INTO `mannschaft` (`mannschaft_key`, `mannschaftsname`, `gruppe`, `anz_spiele`, `punkte`, `plusTore`, `minusTore`, `tordifferenz`, `info_link`, `STATUS`, `GEWINNER`, `flagge`, `IS_NULL`) VALUES(20, 'Serbien', '5', 3, 3, 2, 4, -2, 'http://de.fifa.com/worldcup/stories/y=2017/m=11/news=serbia-2921195.html', 1, 0, 'srb.png', 0);
INSERT INTO `mannschaft` (`mannschaft_key`, `mannschaftsname`, `gruppe`, `anz_spiele`, `punkte`, `plusTore`, `minusTore`, `tordifferenz`, `info_link`, `STATUS`, `GEWINNER`, `flagge`, `IS_NULL`) VALUES(21, 'Deutschland', '6', 3, 3, 2, 4, -2, 'http://de.fifa.com/worldcup/stories/y=2017/m=11/news=germany-2921190.html', 0, 0, 'ger.png', 0);
INSERT INTO `mannschaft` (`mannschaft_key`, `mannschaftsname`, `gruppe`, `anz_spiele`, `punkte`, `plusTore`, `minusTore`, `tordifferenz`, `info_link`, `STATUS`, `GEWINNER`, `flagge`, `IS_NULL`) VALUES(22, 'Mexiko', '6', 3, 6, 3, 4, -1, 'http://de.fifa.com/worldcup/stories/y=2017/m=11/news=mexico-2921210.html', 0, 0, 'mex.png', 0);
INSERT INTO `mannschaft` (`mannschaft_key`, `mannschaftsname`, `gruppe`, `anz_spiele`, `punkte`, `plusTore`, `minusTore`, `tordifferenz`, `info_link`, `STATUS`, `GEWINNER`, `flagge`, `IS_NULL`) VALUES(23, 'Schweden', '6', 3, 6, 5, 2, 3, 'http://de.fifa.com/worldcup/stories/y=2017/m=11/news=sweden-2921198.html', 0, 0, 'swe.png', 0);
INSERT INTO `mannschaft` (`mannschaft_key`, `mannschaftsname`, `gruppe`, `anz_spiele`, `punkte`, `plusTore`, `minusTore`, `tordifferenz`, `info_link`, `STATUS`, `GEWINNER`, `flagge`, `IS_NULL`) VALUES(24, 'Südkorea', '6', 3, 3, 3, 3, 0, 'http://de.fifa.com/worldcup/stories/y=2017/m=11/news=korea-republic-2921207.html', 1, 0, 'kor.png', 0);
INSERT INTO `mannschaft` (`mannschaft_key`, `mannschaftsname`, `gruppe`, `anz_spiele`, `punkte`, `plusTore`, `minusTore`, `tordifferenz`, `info_link`, `STATUS`, `GEWINNER`, `flagge`, `IS_NULL`) VALUES(25, 'Belgien', '7', 3, 9, 9, 2, 7, 'http://de.fifa.com/worldcup/stories/y=2017/m=11/news=belgium-2921180.html', 0, 0, 'bel.png', 0);
INSERT INTO `mannschaft` (`mannschaft_key`, `mannschaftsname`, `gruppe`, `anz_spiele`, `punkte`, `plusTore`, `minusTore`, `tordifferenz`, `info_link`, `STATUS`, `GEWINNER`, `flagge`, `IS_NULL`) VALUES(26, 'Panama', '7', 3, 0, 2, 11, -9, 'http://de.fifa.com/worldcup/stories/y=2017/m=11/news=panama-2921211.html', 1, 0, 'pan.png', 0);
INSERT INTO `mannschaft` (`mannschaft_key`, `mannschaftsname`, `gruppe`, `anz_spiele`, `punkte`, `plusTore`, `minusTore`, `tordifferenz`, `info_link`, `STATUS`, `GEWINNER`, `flagge`, `IS_NULL`) VALUES(27, 'Tunesien', '7', 3, 3, 5, 8, -3, 'http://de.fifa.com/worldcup/stories/y=2017/m=11/news=tunisia-2921203.html', 1, 0, 'tun.png', 0);
INSERT INTO `mannschaft` (`mannschaft_key`, `mannschaftsname`, `gruppe`, `anz_spiele`, `punkte`, `plusTore`, `minusTore`, `tordifferenz`, `info_link`, `STATUS`, `GEWINNER`, `flagge`, `IS_NULL`) VALUES(28, 'England', '7', 3, 6, 8, 3, 5, 'http://de.fifa.com/worldcup/stories/y=2017/m=11/news=england-2921188.html', 0, 0, 'eng.png', 0);
INSERT INTO `mannschaft` (`mannschaft_key`, `mannschaftsname`, `gruppe`, `anz_spiele`, `punkte`, `plusTore`, `minusTore`, `tordifferenz`, `info_link`, `STATUS`, `GEWINNER`, `flagge`, `IS_NULL`) VALUES(29, 'Polen', '8', 3, 3, 2, 5, -3, 'http://de.fifa.com/worldcup/stories/y=2017/m=11/news=poland-2921192.html', 1, 0, 'pol.png', 0);
INSERT INTO `mannschaft` (`mannschaft_key`, `mannschaftsname`, `gruppe`, `anz_spiele`, `punkte`, `plusTore`, `minusTore`, `tordifferenz`, `info_link`, `STATUS`, `GEWINNER`, `flagge`, `IS_NULL`) VALUES(30, 'Senegal', '8', 3, 4, 4, 4, 0, 'http://de.fifa.com/worldcup/stories/y=2017/m=11/news=senegal-2921202.html', 1, 0, 'sen.png', 0);
INSERT INTO `mannschaft` (`mannschaft_key`, `mannschaftsname`, `gruppe`, `anz_spiele`, `punkte`, `plusTore`, `minusTore`, `tordifferenz`, `info_link`, `STATUS`, `GEWINNER`, `flagge`, `IS_NULL`) VALUES(31, 'Kolumbien', '8', 3, 6, 5, 2, 3, 'http://de.fifa.com/worldcup/stories/y=2017/m=11/news=colombia-2921213.html', 0, 0, 'col.png', 0);
INSERT INTO `mannschaft` (`mannschaft_key`, `mannschaftsname`, `gruppe`, `anz_spiele`, `punkte`, `plusTore`, `minusTore`, `tordifferenz`, `info_link`, `STATUS`, `GEWINNER`, `flagge`, `IS_NULL`) VALUES(32, 'Japan', '8', 3, 4, 4, 4, 0, 'http://de.fifa.com/worldcup/stories/y=2017/m=11/news=japan-2921206.html', 0, 0, 'jpn.png', 0);
INSERT INTO `mannschaft` (`mannschaft_key`, `mannschaftsname`, `gruppe`, `anz_spiele`, `punkte`, `plusTore`, `minusTore`, `tordifferenz`, `info_link`, `STATUS`, `GEWINNER`, `flagge`, `IS_NULL`) VALUES(50, 'NULL-Mannschaft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, NULL, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `message`
--

CREATE TABLE IF NOT EXISTS `message` (
  `user_von` int(11) NOT NULL DEFAULT '0',
  `user_nach` int(11) NOT NULL DEFAULT '0',
  `titel` varchar(255) NOT NULL DEFAULT '',
  `text` mediumtext NOT NULL,
  `datum` date ,
  `zeit` time ,
  `gelesen` tinyint(1) NOT NULL DEFAULT '0',
  `message_key` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`message_key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `news`
--

CREATE TABLE IF NOT EXISTS `news` (
  `news_key` int(11) NOT NULL AUTO_INCREMENT,
  `titel` varchar(80),
  `text` mediumtext,
  `datum` date,
  `zeit` time,
  PRIMARY KEY (`news_key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `runde`
--

CREATE TABLE IF NOT EXISTS `runde` (
  `runde_key` smallint(6) NOT NULL DEFAULT '0',
  `Bezeichnung` varchar(30) NOT NULL DEFAULT '',
  `freigabe` tinyint(4),
  `Sortierung` smallint(6),
  PRIMARY KEY (`runde_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `runde`
--

INSERT INTO `runde` (`runde_key`, `Bezeichnung`, `freigabe`, `Sortierung`) VALUES(1, 'Vorrunde', 0, 1);
INSERT INTO `runde` (`runde_key`, `Bezeichnung`, `freigabe`, `Sortierung`) VALUES(2, 'Achtelfinale', 0, 2);
INSERT INTO `runde` (`runde_key`, `Bezeichnung`, `freigabe`, `Sortierung`) VALUES(3, 'Viertelfinale', 0, 3);
INSERT INTO `runde` (`runde_key`, `Bezeichnung`, `freigabe`, `Sortierung`) VALUES(4, 'Halbfinale', 0, 4);
INSERT INTO `runde` (`runde_key`, `Bezeichnung`, `freigabe`, `Sortierung`) VALUES(6, 'Finale', 0, 6);
INSERT INTO `runde` (`runde_key`, `Bezeichnung`, `freigabe`, `Sortierung`) VALUES(5, 'Kleines Finale', 0, 5);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `spieler`
--

CREATE TABLE IF NOT EXISTS `spieler` (
  `spieler_key` int(11) NOT NULL AUTO_INCREMENT,
  `spielername` varchar(20),
  `vorname` varchar(20),
  `telefon` varchar(20),
  `email` varchar(50),
  `weltmeister` int(11),
  `bezahlt` varchar(5),
  `punkte` int(11),
  `exakte_tips` int(11),
  `richtige_Tendenz` int(11),
  `passwort` varchar(20),
  `Freigabe` tinyint(1) NOT NULL DEFAULT '0',
  `Newsletter` tinyint(1) NOT NULL DEFAULT '0',
  `Rolle` varchar(15) NOT NULL DEFAULT '0',
  `login` varchar(15) NOT NULL DEFAULT '',
  `ICQ_NUMMER` varchar(20),
  PRIMARY KEY (`spieler_key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=100 ;

--
-- Daten für Tabelle `spieler`
--

INSERT INTO `spieler` (`spieler_key`, `spielername`, `vorname`, `telefon`, `email`, `weltmeister`, `bezahlt`, `punkte`, `exakte_tips`, `richtige_Tendenz`, `passwort`, `Freigabe`, `Newsletter`, `Rolle`, `login`, `ICQ_NUMMER`) VALUES(1, 'Doe', 'John', '0190-696969', 'admin@mydomain.com', 6, '0', 122, 6, 33, 'admin', 1, 0, 'ADMIN', 'admin', 'NULL');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `spieler_benutzergruppe`
--

CREATE TABLE IF NOT EXISTS `spieler_benutzergruppe` (
  `key_spieler` int(11) NOT NULL,
  `key_gruppe` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `st_badwords`
--

CREATE TABLE IF NOT EXISTS `st_badwords` (
  `badword` varchar(25) NOT NULL DEFAULT '',
  `id` smallint(5) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `st_entries`
--

CREATE TABLE IF NOT EXISTS `st_entries` (
  `comment` mediumtext NOT NULL,
  `date` varchar(10) NOT NULL DEFAULT '',
  `email` varchar(50) NOT NULL DEFAULT '',
  `homepage` varchar(150) NOT NULL DEFAULT '',
  `icq` int(9) NOT NULL DEFAULT '0',
  `id` mediumint(7) NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `name` varchar(27) NOT NULL DEFAULT '',
  `activation_code` varchar(32) NOT NULL DEFAULT '',
  `text` mediumtext NOT NULL,
  `time` varchar(5) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `st_ip_ban`
--

CREATE TABLE IF NOT EXISTS `st_ip_ban` (
  `id` mediumint(7) NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) NOT NULL DEFAULT '',
  `time` int(15) NOT NULL DEFAULT '0',
  `type` varchar(5) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1161 ;

--
-- Daten für Tabelle `st_ip_ban`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `st_properties`
--

CREATE TABLE IF NOT EXISTS `st_properties` (
  `admin_email` varchar(50) NOT NULL DEFAULT '',
  `antiflood_ban` smallint(5) NOT NULL DEFAULT '0',
  `bbcode` tinyint(1) NOT NULL DEFAULT '0',
  `captcha` tinyint(1) NOT NULL DEFAULT '0',
  `check_email` varchar(15) NOT NULL DEFAULT '',
  `check_homepage` tinyint(1) NOT NULL DEFAULT '0',
  `check_icq` tinyint(1) NOT NULL DEFAULT '0',
  `deactivate_html` varchar(15) NOT NULL DEFAULT '',
  `default_style` tinyint(3) NOT NULL DEFAULT '0',
  `default_template` tinyint(3) NOT NULL DEFAULT '0',
  `entries_per_site` tinyint(3) NOT NULL DEFAULT '0',
  `entry_length_limit` tinyint(1) NOT NULL DEFAULT '0',
  `entry_length_maximum` mediumint(7) NOT NULL DEFAULT '0',
  `entry_length_minimum` tinyint(3) NOT NULL DEFAULT '0',
  `guestbook_status` tinyint(1) NOT NULL DEFAULT '0',
  `guestbook_title` varchar(50) NOT NULL DEFAULT '',
  `images_in_entries` tinyint(1) NOT NULL DEFAULT '0',
  `language` varchar(10) NOT NULL DEFAULT '',
  `links_in_sitefunction` tinyint(3) NOT NULL DEFAULT '0',
  `max_word_length` tinyint(3) NOT NULL DEFAULT '0',
  `notification_entries` tinyint(1) NOT NULL DEFAULT '0',
  `password` varchar(32) NOT NULL DEFAULT '',
  `release_entries` tinyint(1) NOT NULL DEFAULT '0',
  `show_ip` tinyint(1) NOT NULL DEFAULT '0',
  `smilies` tinyint(1) NOT NULL DEFAULT '0',
  `thanks_email` tinyint(1) NOT NULL DEFAULT '0',
  `statistic` tinyint(1) NOT NULL DEFAULT '0',
  `statistic_ban` smallint(5) NOT NULL DEFAULT '0',
  `username` varchar(15) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `st_properties`
--

INSERT INTO `st_properties` (`admin_email`, `antiflood_ban`, `bbcode`, `captcha`, `check_email`, `check_homepage`, `check_icq`, `deactivate_html`, `default_style`, `default_template`, `entries_per_site`, `entry_length_limit`, `entry_length_maximum`, `entry_length_minimum`, `guestbook_status`, `guestbook_title`, `images_in_entries`, `language`, `links_in_sitefunction`, `max_word_length`, `notification_entries`, `password`, `release_entries`, `show_ip`, `smilies`, `thanks_email`, `statistic`, `statistic_ban`, `username`) VALUES('cheffe@bei-abels.de', 20, 1, 0, '', 0, 0, '1', 2, 1, 20, 0, 0, 0, 1, 'EM-Stammtisch', 1, 'German', 10, 100, 0, '38cb5df00ba0a25a453a8bb6a07a9da2', 0, 0, 1, 0, 1, 7200, 'abels');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `st_smilies`
--

CREATE TABLE IF NOT EXISTS `st_smilies` (
  `id` smallint(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(15) NOT NULL DEFAULT '',
  `bbcode` varchar(15) NOT NULL DEFAULT '',
  `filename` varchar(20) NOT NULL DEFAULT '',
  `width` tinyint(3) NOT NULL DEFAULT '0',
  `height` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

--
-- Daten für Tabelle `st_smilies`
--

INSERT INTO `st_smilies` (`id`, `name`, `bbcode`, `filename`, `width`, `height`) VALUES(1, 'smile', ':)', 'smile.gif', 15, 15);
INSERT INTO `st_smilies` (`id`, `name`, `bbcode`, `filename`, `width`, `height`) VALUES(2, 'sad', ':(', 'sad.gif', 15, 15);
INSERT INTO `st_smilies` (`id`, `name`, `bbcode`, `filename`, `width`, `height`) VALUES(3, 'angry', ':angry:', 'angry.gif', 15, 15);
INSERT INTO `st_smilies` (`id`, `name`, `bbcode`, `filename`, `width`, `height`) VALUES(4, 'big grin', ':D', 'biggrin.gif', 15, 15);
INSERT INTO `st_smilies` (`id`, `name`, `bbcode`, `filename`, `width`, `height`) VALUES(5, 'cool', ':cool:', 'cool.gif', 15, 15);
INSERT INTO `st_smilies` (`id`, `name`, `bbcode`, `filename`, `width`, `height`) VALUES(6, 'frown', ':frown:', 'frown.gif', 15, 15);
INSERT INTO `st_smilies` (`id`, `name`, `bbcode`, `filename`, `width`, `height`) VALUES(7, 'indifferent', ':-|', 'indifferent.gif', 15, 15);
INSERT INTO `st_smilies` (`id`, `name`, `bbcode`, `filename`, `width`, `height`) VALUES(8, 'oh', ':O', 'oh.gif', 15, 15);
INSERT INTO `st_smilies` (`id`, `name`, `bbcode`, `filename`, `width`, `height`) VALUES(9, 'oh well', ':-/', 'ohwell.gif', 15, 15);
INSERT INTO `st_smilies` (`id`, `name`, `bbcode`, `filename`, `width`, `height`) VALUES(10, 'tonque', ':P', 'tongue.gif', 15, 15);
INSERT INTO `st_smilies` (`id`, `name`, `bbcode`, `filename`, `width`, `height`) VALUES(11, 'wink', ';)', 'wink.gif', 15, 15);
INSERT INTO `st_smilies` (`id`, `name`, `bbcode`, `filename`, `width`, `height`) VALUES(12, 'glasses', '8)', 'glasses.gif', 15, 15);
INSERT INTO `st_smilies` (`id`, `name`, `bbcode`, `filename`, `width`, `height`) VALUES(13, 'love', ':love:', 'love.gif', 15, 15);
INSERT INTO `st_smilies` (`id`, `name`, `bbcode`, `filename`, `width`, `height`) VALUES(14, 'shoked', ':shoked:', 'shoked.gif', 15, 15);
INSERT INTO `st_smilies` (`id`, `name`, `bbcode`, `filename`, `width`, `height`) VALUES(15, 'devil', ':devil:', 'devil.gif', 15, 15);
INSERT INTO `st_smilies` (`id`, `name`, `bbcode`, `filename`, `width`, `height`) VALUES(16, 'kiss', ':kiss:', 'kiss.gif', 15, 15);
INSERT INTO `st_smilies` (`id`, `name`, `bbcode`, `filename`, `width`, `height`) VALUES(17, 'pirate', ':pirate:', 'pirate.gif', 15, 15);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `st_statistic`
--

CREATE TABLE IF NOT EXISTS `st_statistic` (
  `date` varchar(15) NOT NULL DEFAULT '',
  `hits` mediumint(7) NOT NULL DEFAULT '0',
  `id` smallint(5) NOT NULL AUTO_INCREMENT,
  `visits` mediumint(7) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=178 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `st_style`
--

CREATE TABLE IF NOT EXISTS `st_style` (
  `id` tinyint(3) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL DEFAULT '',
  `style` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Daten für Tabelle `st_style`
--

INSERT INTO `st_style` (`id`, `name`, `style`) VALUES(1, 'ygGS', 'body {\n            font-family: Tahoma,Verdana,Helvetica;\n            font-size: 11px;\n            color: #000000;\n            background-attachment: fixed;\n            background-repeat: no-repeat;\n            background-position: center center;\n            }\n\n            .guestbook_table {\n            font-family: Tahoma,Verdana,Helvetica;\n            font-size: 11px;\n            color: #000000;\n            background-color: #000000;\n            }\n\n            .guestbook_table2 {\n            font-family: Tahoma,Verdana,Helvetica;\n            font-size: 11px;\n            color: #000000;\n            }\n\n            A:link {\n            color:#000000;\n            text-decoration: none;\n            }\n\n            A:visited {\n            color: #000000;\n            text-decoration: none;\n            }\n\n            A:active {\n            color: #000000;\n            text-decoration: none;\n            }\n\n            A:hover {\n            color: #808080;\n            text-decoration: underline;\n            }\n\n            textarea {\n            background-color: #e3e3e3;\n            border: 1px solid #000000;\n            color: #000000;\n            font-size: 11px;\n            font-family: Tahoma,Verdana,Helvetica;\n            }\n\n            input {\n            background-color: #ffff00;\n            border: 1px solid #000000;\n            color: #000000;\n            font-size: 11px;\n            font-family: Tahoma,Verdana,Helvetica;\n            }\n\n            #captcha {\n            border: 1px solid #000000;\n            }');
INSERT INTO `st_style` (`id`, `name`, `style`) VALUES(2, 'myPHP Guestbook 2.0.1', 'body {\n            font-family: Tahom,Verdana,Helvetica;\n            font-size: 11px;\n            color: #000000;\n            background-attachment: fixed;\n            background-repeat: no-repeat;\n            background-position: center center;\n            }\n\n            .guestbook_table {\n            font-family: Tahoma,Verdana,Helvetica;\n            font-size: 11px;\n            color: #000000;\n            background-color: #808080;\n            }\n\n            .guestbook_table2 {\n            font-family: Tahoma,Verdana,Helvetica;\n            font-size: 11px;\n            color: #000000;\n            }\n\n            A:link {\n            color:#000000;\n            text-decoration: none;\n            }\n\n            A:visited {\n            color: #000000;\n            text-decoration: none;\n            }\n\n            A:active {\n            color: #000000;\n            text-decoration: none;\n            }\n\n            A:hover {\n            color: #808080;\n            text-decoration: underline;\n            }\n\n            textarea {\n            background-color: #f2f2f2;\n            border: 1px solid #808080;\n            color: #000000;\n            font-size: 11px;\n            font-family: Tahoma,Verdana,Helvetica;\n            }\n\n            input {\n            background-color: #f2f2f2;\n            border: 1px solid #808080;\n            color: #000000;\n            font-size: 11px;\n            font-family: Tahoma,Verdana,Helvetica;\n            }\n\n            #captcha {\n            border: 1px solid #808080;\n            }');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `st_template`
--

CREATE TABLE IF NOT EXISTS `st_template` (
  `bgcolor` varchar(15) NOT NULL DEFAULT '',
  `bgimage` varchar(100) NOT NULL DEFAULT '',
  `border` tinyint(2) NOT NULL DEFAULT '0',
  `cellpadding` tinyint(2) NOT NULL DEFAULT '0',
  `cellspacing` tinyint(2) NOT NULL DEFAULT '0',
  `html` mediumtext NOT NULL,
  `id` tinyint(3) NOT NULL AUTO_INCREMENT,
  `image_email` varchar(25) NOT NULL DEFAULT '',
  `image_homepage` varchar(25) NOT NULL DEFAULT '',
  `name` varchar(25) NOT NULL DEFAULT '',
  `tablealign` varchar(6) NOT NULL DEFAULT '',
  `tablewidth` smallint(4) NOT NULL DEFAULT '0',
  `tdcolor` varchar(15) NOT NULL DEFAULT '',
  `td2color` varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Daten für Tabelle `st_template`
--

INSERT INTO `st_template` (`bgcolor`, `bgimage`, `border`, `cellpadding`, `cellspacing`, `html`, `id`, `image_email`, `image_homepage`, `name`, `tablealign`, `tablewidth`, `tdcolor`, `td2color`) VALUES('#ffffff', '', 1, 2, 1, '<table style="table-layout: fixed" cellpadding="<$cellpadding$>" cellspacing="<$cellspacing$>" border="<$border$>" width="<$tablewidth$>" class="guestbook_table">\n <tr>\n  <td width="55%" bgcolor="<$tdcolor$>" align="left"><b><$name$></b></td>\n  <td width="15%" bgcolor="<$tdcolor$>" align="center"><$email_icon$> <$homepage_icon$> <$icq_icon$></td>\n  <td width="30%" bgcolor="<$tdcolor$>" align="center"><$date$> | <$time$></td>\n </tr>\n <tr>\n  <td colspan="3" bgcolor="<$td2color$>" align="left"><span style="font-size: 10px;"><$ip$></span>\n  <$text$><$comment$></td>\n </tr>\n</table>\n<br />', 1, 'emailxp.gif', 'homepagexp.gif', 'myPHP Guestbook 2.0.1', 'center', 700, '#f2f2f2', '#eeeeff');
INSERT INTO `st_template` (`bgcolor`, `bgimage`, `border`, `cellpadding`, `cellspacing`, `html`, `id`, `image_email`, `image_homepage`, `name`, `tablealign`, `tablewidth`, `tdcolor`, `td2color`) VALUES('#ffffff', '', 1, 2, 0, '<table border="<$border$>" cellspacing="<$cellspacing$>" cellpadding="<$cellpadding$>" style="table-layout: fixed" width="<$tablewidth$>" class="guestbook_table2">\n <tr>\n  <td width="85%" bgcolor="<$tdcolor$>" align="left" style="border-top: 1px solid #000000"><b><$name$></b>, schrieb am <$date$> um <$time$> Uhr</td>\n  <td width="15%" bgcolor="<$tdcolor$>" align="right" style="border-top: 1px solid #000000"><$email_icon$> <$homepage_icon$> <$icq_icon$></td>\n </tr>\n <tr>\n  <td colspan="2" bgcolor="<$td2color$>" align="left"><span style="font-size: 10px;"><$ip$></span><$text$><$comment$></td>\n </tr>\n</table>\n<br />', 2, 'emailmodern.png', 'homepagemodern.png', 'Light', 'center', 600, '#DCDCDC', '#ADD8E6');
INSERT INTO `st_template` (`bgcolor`, `bgimage`, `border`, `cellpadding`, `cellspacing`, `html`, `id`, `image_email`, `image_homepage`, `name`, `tablealign`, `tablewidth`, `tdcolor`, `td2color`) VALUES('#ffffff', '', 1, 3, 1, '<table cellpadding="<$cellpadding$>" cellspacing="<$cellspacing$>" border="<$border$>" width="<$tablewidth$>" class="guestbook_table">\n<tr>\n   <td width="22%" bgcolor="<$td2color$>" align="left" valign="top">\n     <b><$name$></b><br /><$date$> <$time$><br />\n      #<$id$><br />\n<$email_icon$> <$homepage_icon$> <$icq_icon$>\n     <br />\n     <$ip$>\n   </td>\n  <td width="78%" bgcolor="<$td2color$>" align="left" valign="top">\n    <$text$><$comment$>\n  </td>\n</tr>\n</table>', 3, 'emailmodern.png', 'homepagemodern.png', 'myPHP Guestbook 2.0.0', 'center', 600, '#dcdcdc', '#ADD8E6');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tips`
--

CREATE TABLE IF NOT EXISTS `tips` (
  `key_spieler` int(11) NOT NULL DEFAULT '0',
  `key_spiel` int(11) NOT NULL DEFAULT '0',
  `tore_ms1` int(11),
  `tore_ms2` int(11),
  `tendenz` int(11),
  `erreichte_punkte` int(11)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

