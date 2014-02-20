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
	// Create table
	$mod_mdLV = "CREATE TABLE IF NOT EXISTS `".TABLE_PREFIX."mod_mdLagerVerwaltung` (
		`page_id` INT NOT NULL,
		`section_id` INT NOT NULL,
		`Titel` VARCHAR(20) NOT NULL DEFAULT '',
		`begin` VARCHAR(10) NOT NULL DEFAULT '',
		`end` VARCHAR(10) NOT NULL DEFAULT '',
		`Ort` VARCHAR(40) NOT NULL DEFAULT '',
		`personen_erfassen` TINYINT(1) NOT NULL DEFAULT '0',
		`tage_erfassen` TINYINT(1) NOT NULL DEFAULT '0',
		PRIMARY KEY (page_id, section_id))
		ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci";
	$database->query($mod_mdLV);
}
?>