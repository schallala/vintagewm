
DROP TABLE IF EXISTS `st_style`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `st_style` (
  `id` tinyint(3) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL DEFAULT '',
  `style` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `st_style`
--

LOCK TABLES `st_style` WRITE;
/*!40000 ALTER TABLE `st_style` DISABLE KEYS */;
INSERT INTO `st_style` VALUES (1,'ygGS','body {\n            font-family: Tahoma,Verdana,Helvetica;\n            font-size: 11px;\n            color: #000000;\n            background-attachment: fixed;\n            background-repeat: no-repeat;\n            background-position: center center;\n            }\n\n            .guestbook_table {\n            font-family: Tahoma,Verdana,Helvetica;\n            font-size: 11px;\n            color: #000000;\n            background-color: #000000;\n            }\n\n            .guestbook_table2 {\n            font-family: Tahoma,Verdana,Helvetica;\n            font-size: 11px;\n            color: #000000;\n            }\n\n            A:link {\n            color:#000000;\n            text-decoration: none;\n            }\n\n            A:visited {\n            color: #000000;\n            text-decoration: none;\n            }\n\n            A:active {\n            color: #000000;\n            text-decoration: none;\n            }\n\n            A:hover {\n            color: #808080;\n            text-decoration: underline;\n            }\n\n            textarea {\n            background-color: #e3e3e3;\n            border: 1px solid #000000;\n            color: #000000;\n            font-size: 11px;\n            font-family: Tahoma,Verdana,Helvetica;\n            }\n\n            input {\n            background-color: #ffff00;\n            border: 1px solid #000000;\n            color: #000000;\n            font-size: 11px;\n            font-family: Tahoma,Verdana,Helvetica;\n            }\n\n            #captcha {\n            border: 1px solid #000000;\n            }'),(2,'myPHP Guestbook 2.0.1','body {\n            font-family: Tahom,Verdana,Helvetica;\n            font-size: 11px;\n            color: #000000;\n            background-attachment: fixed;\n            background-repeat: no-repeat;\n            background-position: center center;\n            }\n\n            .guestbook_table {\n            font-family: Tahoma,Verdana,Helvetica;\n            font-size: 11px;\n            color: #000000;\n            background-color: #808080;\n            }\n\n            .guestbook_table2 {\n            font-family: Tahoma,Verdana,Helvetica;\n            font-size: 11px;\n            color: #000000;\n            }\n\n            A:link {\n            color:#000000;\n            text-decoration: none;\n            }\n\n            A:visited {\n            color: #000000;\n            text-decoration: none;\n            }\n\n            A:active {\n            color: #000000;\n            text-decoration: none;\n            }\n\n            A:hover {\n            color: #808080;\n            text-decoration: underline;\n            }\n\n            textarea {\n            background-color: #f2f2f2;\n            border: 1px solid #808080;\n            color: #000000;\n            font-size: 11px;\n            font-family: Tahoma,Verdana,Helvetica;\n            }\n\n            input {\n            background-color: #f2f2f2;\n            border: 1px solid #808080;\n            color: #000000;\n            font-size: 11px;\n            font-family: Tahoma,Verdana,Helvetica;\n            }\n\n            #captcha {\n            border: 1px solid #808080;\n            }');
/*!40000 ALTER TABLE `st_style` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `st_template`
--

DROP TABLE IF EXISTS `st_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `st_template` (
  `bgcolor` varchar(15) NOT NULL DEFAULT '',
  `bgimage` varchar(100) NOT NULL DEFAULT '',
  `border` tinyint(2) NOT NULL DEFAULT '0',
  `cellpadding` tinyint(2) NOT NULL DEFAULT '0',
  `cellspacing` tinyint(2) NOT NULL DEFAULT '0',
  `html` text NOT NULL,
  `id` tinyint(3) NOT NULL AUTO_INCREMENT,
  `image_email` varchar(25) NOT NULL DEFAULT '',
  `image_homepage` varchar(25) NOT NULL DEFAULT '',
  `name` varchar(25) NOT NULL DEFAULT '',
  `tablealign` varchar(6) NOT NULL DEFAULT '',
  `tablewidth` smallint(4) NOT NULL DEFAULT '0',
  `tdcolor` varchar(15) NOT NULL DEFAULT '',
  `td2color` varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `st_template`
--

LOCK TABLES `st_template` WRITE;
/*!40000 ALTER TABLE `st_template` DISABLE KEYS */;
INSERT INTO `st_template` VALUES ('#ffffff','',1,2,1,'<table style=\"table-layout: fixed\" cellpadding=\"<$cellpadding$>\" cellspacing=\"<$cellspacing$>\" border=\"<$border$>\" width=\"<$tablewidth$>\" class=\"guestbook_table\">\n <tr>\n  <td width=\"55%\" bgcolor=\"<$tdcolor$>\" align=\"left\"><b><$name$></b></td>\n  <td width=\"15%\" bgcolor=\"<$tdcolor$>\" align=\"center\"><$email_icon$> <$homepage_icon$> <$icq_icon$></td>\n  <td width=\"30%\" bgcolor=\"<$tdcolor$>\" align=\"center\"><$date$> | <$time$></td>\n </tr>\n <tr>\n  <td colspan=\"3\" bgcolor=\"<$td2color$>\" align=\"left\"><span style=\"font-size: 10px;\"><$ip$></span>\n  <$text$><$comment$></td>\n </tr>\n</table>\n<br />',1,'emailxp.gif','homepagexp.gif','myPHP Guestbook 2.0.1','center',700,'#f2f2f2','#eeeeff'),('#ffffff','',1,2,0,'<table border=\"<$border$>\" cellspacing=\"<$cellspacing$>\" cellpadding=\"<$cellpadding$>\" style=\"table-layout: fixed\" width=\"<$tablewidth$>\" class=\"guestbook_table2\">\n <tr>\n  <td width=\"85%\" bgcolor=\"<$tdcolor$>\" align=\"left\" style=\"border-top: 1px solid #000000\"><b><$name$></b>, schrieb am <$date$> um <$time$> Uhr</td>\n  <td width=\"15%\" bgcolor=\"<$tdcolor$>\" align=\"right\" style=\"border-top: 1px solid #000000\"><$email_icon$> <$homepage_icon$> <$icq_icon$></td>\n </tr>\n <tr>\n  <td colspan=\"2\" bgcolor=\"<$td2color$>\" align=\"left\"><span style=\"font-size: 10px;\"><$ip$></span><$text$><$comment$></td>\n </tr>\n</table>\n<br />',2,'emailmodern.png','homepagemodern.png','Light','center',600,'#DCDCDC','#ADD8E6'),('#ffffff','',1,3,1,'<table cellpadding=\"<$cellpadding$>\" cellspacing=\"<$cellspacing$>\" border=\"<$border$>\" width=\"<$tablewidth$>\" class=\"guestbook_table\">\n<tr>\n   <td width=\"22%\" bgcolor=\"<$td2color$>\" align=\"left\" valign=\"top\">\n     <b><$name$></b><br /><$date$> <$time$><br />\n      #<$id$><br />\n<$email_icon$> <$homepage_icon$> <$icq_icon$>\n     <br />\n     <$ip$>\n   </td>\n  <td width=\"78%\" bgcolor=\"<$td2color$>\" align=\"left\" valign=\"top\">\n    <$text$><$comment$>\n  </td>\n</tr>\n</table>',3,'emailmodern.png','homepagemodern.png','myPHP Guestbook 2.0.0','center',600,'#dcdcdc','#ADD8E6');
/*!40000 ALTER TABLE `st_template` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tips`
--

DROP TABLE IF EXISTS `tips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tips` (
  `key_spieler` int(11) NOT NULL DEFAULT '0',
  `key_spiel` int(11) NOT NULL DEFAULT '0',
  `tore_ms1` int(11) DEFAULT NULL,
  `tore_ms2` int(11) DEFAULT NULL,
  `tendenz` int(11) DEFAULT NULL,
  `erreichte_punkte` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tips`
--

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-06-04 17:24:49
