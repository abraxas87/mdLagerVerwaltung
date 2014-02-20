<?php
/**
 *
 * @category        modules
 * @package         mdLagerVerwaltung
 * @author          Markus Dittrich
 * @copyright       2014, Markus Dittrich
 * @license         MIT License (MIT)
 * @platform        WebsiteBaker 2.8
 * @requirements    PHP 5.2.2 and higher
 *
 */
// Must include code to stop this file being access directly
/* -------------------------------------------------------- */
if(defined('WB_PATH') == false)
{
	// Stop this file being access directly
	die('<head><title>Access denied</title></head><body><h2 style="color:red;margin:3em auto;text-align:center;">Cannot access this file directly</h2></body></html>');
}
/* -------------------------------------------------------- */

if(defined('WB_URL'))
{
	// Tabelle für die Veranstaltung erstellen
	mysql_set_charset('utf8');
	$database->query("DROP TABLE IF EXISTS `".TABLE_PREFIX."mod_mdLV".$page_id."-".$section_id."`");
	$mod_mdLV = "CREATE TABLE IF NOT EXISTS `".TABLE_PREFIX."mod_mdLV".$page_id."-".$section_id."` (
		`ID` INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		`Name` VARCHAR(20) NOT NULL DEFAULT '',
		`Vorname` VARCHAR(20) NOT NULL DEFAULT '',
		`Stufe` SET('Wölfling','Jungpfadfinder','Pfadfinder','Rover','Leiter','Gast') NOT NULL DEFAULT 'Gast',
		`Personen` INT(10) NOT NULL,
		`Bezahlt` TINYINT(1) NOT NULL,
		`Bemerkung` TEXT NOT NULL,
		`Jahr` INT(4) NOT NULL,
		`Ort` VARCHAR(40) NOT NULL DEFAULT '',
		`Strasse` VARCHAR(40) NOT NULL DEFAULT '',
		`Geburtsdatum` VARCHAR(10) NOT NULL DEFAULT '',
		`male` TINYINT(1) NOT NULL,
		`female` TINYINT(1) NOT NULL,
		`PM` TINYINT(1) NOT NULL,
		`Juleica` VARCHAR(40) NOT NULL,
		`Tage` VARCHAR(100) NOT NULL )
		ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci";
	$database->query($mod_mdLV);
	
	// Daten der Veranstaltung in die Modul-Tabelle schreiben
	$database->query("INSERT INTO `".TABLE_PREFIX."mod_mdLagerVerwaltung` SET `page_id` = '$page_id', `section_id` = '$section_id'");
}