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

$table = TABLE_PREFIX."mod_mdLagerVerwaltung";

$query = "SELECT * FROM `$table` WHERE `page_id` = '$page_id' && `section_id` = '$section_id'";
$erg = mysql_query($query);
$data = mysql_fetch_array($erg);

?>
<div class="mdLVBackend">
<form method=post action="<?php echo WB_URL; ?>/modules/mdLagerVerwaltung/save.php">
	<input type="hidden" name="send" value="true"></input>
	<h2>Veranstaltungs-Daten</h2>
	<table>
		<tr>
			<td>Veranstaltung:</td><td><input type="text" name="titel" size="30" value="<?php echo $data['Titel']; ?>" title="Der Veranstaltungstitel darf aus Buchstaben, Zahlen, Bindestrich, Unterstrich, Punkt und Leerzeichen bestehen und muss mindestens 3 Zeichen lang sein." placeholder="z.B. Stammeswochenende" pattern="[a-zA-ZäöüÄÜÖ0-9-_\. ]{3,}" required /></td>
		</tr>
		<tr>
			<td>Veranstaltungsort:</td><td><input type="text" name="location" size="30" value="<?php echo $data['Ort']; ?>" placeholder="z.B. Jugendherberge Musterstadt" title="Der Veranstaltungsort darf aus Buchstaben, Zahlen, Bindestrich, Unterstrich, Punkt und Leerzeichen bestehen und muss mindestens 3 Zeichen lang sein." pattern="[a-zA-ZäöüÄÜÖ0-9-_\. ]{3,}" required /></td>
		</tr>
		<tr>
			<td>Von:</td><td><input type="text" name="start_date" value="<?php echo $data['begin']; ?>" placeholder="TT.MM.JJJJ" title="Datum in deutschem Format TT.MM.JJJJ z.B. 28.02.2014" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01]).(0[1-9]|1[0-2]).(?:20)[0-9]{2}" required /></td>
		</tr>
		<tr>
			<td>Bis:</td><td><input type="text" name="end_date" value="<?php echo $data['end']; ?>" placeholder="TT.MM.JJJJ" title="Datum in deutschem Format TT.MM.JJJJ z.B. 28.02.2014" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01]).(0[1-9]|1[0-2]).(?:20)[0-9]{2}" required /></td>
		</tr>
		<tr>
			<td>Personenzahl erfassen:</td><td><input type="checkbox" name="pers_erfassen" <?php if ($data['personen_erfassen']) echo "checked"?> /></td>
		</tr>
		<tr>
			<td>Tage einzeln erfassen:</td><td><input type="checkbox" name="tage_erfassen" <?php if ($data['tage_erfassen']) echo "checked"?> /></td>
		</tr>
		<tr>
			<td>
				<input type="hidden" name="page_id" value="<?php echo $page_id; ?>" />
				<input type="hidden" name="section_id" value="<?php echo $section_id; ?>" /></td>
			<td><input type="submit" value="<?php echo $TEXT['SAVE']; ?>" style="width: 100px; margin-top: 5px;" /> <input type="button" value="<?php echo $TEXT['CANCEL']; ?>" onclick="javascript: window.location = 'index.php';" style="width: 100px; margin-top: 5px;" /></td>
		</tr>
	</table>
</form>
</div>