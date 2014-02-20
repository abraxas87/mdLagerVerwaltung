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
	//die('<head><title>Access denied</title></head><body><h2 style="color:red;margin:3em auto;text-align:center;">Cannot access this file directly</h2></body></html>');
}
/* -------------------------------------------------------- */

require('../../config.php');

// Include WB admin wrapper script
require(WB_PATH.'/modules/admin.php');

// Tells script to update when this page was last updated
$update_when_modified = true;

if (isset($_POST['page_id'])) $page_id = strip_tags($_POST['page_id']);
if (isset($_POST['section_id'])) $section_id = strip_tags($_POST['section_id']);

//Daten aus der POST Variablen holen
if(isset($_POST['titel'])) $titel = strip_tags($_POST['titel']);  else $titel = "";
if(isset($_POST['location'])) $location = strip_tags($_POST['location']); else $location = "";
if(isset($_POST['start_date'])) $start_date = strip_tags($_POST['start_date']);
if(isset($_POST['end_date'])) $end_date = strip_tags($_POST['end_date']);
if(isset($_POST['pers_erfassen'])) $pers_erfassen = strip_tags($_POST['pers_erfassen']); else $pers_erfassen = false;
if(isset($_POST['tage_erfassen'])) $tage_erfassen = strip_tags($_POST['tage_erfassen']); else $tage_erfassen = false;

if ($pers_erfassen == 'on') $pers_erfassen = true;
if ($tage_erfassen == 'on') $tage_erfassen = true;

$table = TABLE_PREFIX."mod_mdLagerVerwaltung";

$query = "UPDATE `$table` SET `Titel` = '$titel', `begin` = '$start_date', `end` = '$end_date', `Ort` = '$location', `personen_erfassen` = '$pers_erfassen', `tage_erfassen` = '$tage_erfassen' WHERE `page_id` = '$page_id' && `section_id` = '$section_id'";
$database->query($query);

$edit_page = ADMIN_URL.'/pages/modify.php?page_id='.$page_id;

// Check if there is a database error, otherwise say successful
if($database->is_error()) {
	$admin->print_error($database->get_error(), $js_back);
} else {
	$admin->print_success($MESSAGE['PAGES_SAVED'], $edit_page );
}

// Print admin footer
$admin->print_footer();

?>