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
	// Drop Table
	$database->query("DROP TABLE IF EXISTS `".TABLE_PREFIX."mod_mdLV".$page_id."-".$section_id."`");
	$database->query("DELETE FROM `".TABLE_PREFIX."mod_mdLagerVerwaltung` WHERE `page_id` = ".$page_id." && `section_id` = ".$section_id);
}
?>