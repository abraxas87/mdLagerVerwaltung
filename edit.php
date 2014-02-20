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

/*Funktionen laden*/
require_once '../../config.php';

/*Variablen definieren*/
if (isset($_GET['page_id'])) $page_id = strip_tags($_GET['page_id']);
if (isset($_GET['section_id'])) $section_id = strip_tags($_GET['section_id']);
if(isset($_GET["id"])) { $id = $_GET['id']; }
if(isset($_GET["jahr"])) { $jahr = $_GET['jahr']; }
if (isset($_POST['page_id'])) $page_id = strip_tags($_POST['page_id']);
if (isset($_POST['section_id'])) $section_id = strip_tags($_POST['section_id']);
$table = TABLE_PREFIX."mod_mdLV".$page_id."-".$section_id;

if(isset($_POST["id"])) { $id = $_POST['id']; }
if(isset($_POST["jahr"])) { $jahr = $_POST['jahr']; }
if(isset($_POST["pers"])) { $pers = $_POST['pers']; } else $pers = 1;
if(isset($_POST["vname"])) { $vname = $_POST["vname"]; }
if(isset($_POST["nname"])) { $nname = $_POST["nname"]; }
if(isset($_POST["bezahlt"])) { $bez = $_POST["bezahlt"]; }
if(isset($_POST["bemerk"])) { $bemerk = $_POST["bemerk"]; }
if(isset($_POST["stufe"])) { $stufe = $_POST["stufe"]; }

mysql_set_charset('utf8');
	
if(isset($_GET['id']) || isset($id)){
if(isset($_GET['id'])) $id = $_GET['id'];
echo "<h2>Eintrag bearbeiten</h2>";

$query = "SELECT `personen_erfassen`, `tage_erfassen`, `begin`, `end` FROM `".TABLE_PREFIX."mod_mdLagerVerwaltung` WHERE `page_id` = $page_id && `section_id` = $section_id";
$ergebnis = mysql_query($query);
$ergebnis = mysql_fetch_row($ergebnis);
$pers_erfassen = $ergebnis[0];
$tage_erfassen = $ergebnis[1];
$start_date = $ergebnis[2];
$end_date = $ergebnis[3];

if((isset($start_date) && ($start_date != "")) && ((isset($end_date) && ($end_date != "") )) ){
	list($sd,$sm,$sy) = split("\\.",$start_date);
	list($ed,$em,$ey) = split("\\.",$end_date);
	$date1 = mktime(0,0,0,$sm,$sd,$sy);
	$date2 = mktime(0,0,0,$em,$ed,$ey);
	$DIFF = $date2 - $date1;
	$dauer = floor($DIFF/86400)+1;
}
else $dauer = "";

if(isset($vname)){
	if(isset($bez) && $bez == 'on'){$bez = true;}
	else $bez = false;
	if(isset($tage_erfassen)){
		$days = "";
		for ($i = 0; $i < $dauer; $i++){
			$j = $i+1;
			if(isset($_POST['tag_'.$i])) $days .= $j.",";
		}
	}
	$query = "UPDATE `".$table."` SET Name = '$nname', Vorname = '$vname', Stufe = '$stufe', Bezahlt =  '$bez', Personen='$pers', Tage = '$days', Bemerkung = '$bemerk', Jahr = '$jahr' WHERE Id = $id";
	$result = mysql_query($query);
	if (!$result)  die('Ungültige Anfrage: ' . mysql_error());
	else echo "<img src=\"../../media/icons/yes.png\" alt=\"OK\"></img><strong> Datensatz wurde aktualisiert.</strong>";
}

$query = "SELECT * FROM `".$table."` WHERE ID = $id";
$daten = mysql_query($query);
$datensatz = mysql_fetch_array($daten);

$tage = explode(',', $datensatz['Tage']);

echo "<form method=post action=".$_SERVER['PHP_SELF'].">
<table  align='center' style='border: 0px solid #000000;'>
<tr>
<td>Vorname: </td><td><input type='text' name='vname' length='20' value='".$datensatz['Vorname']."'></td>
</tr>
<tr>
<td>Nachname: </td><td><input type='text' name='nname' length='20' value='".$datensatz['Name']."'></td>
</tr>
<tr>
<td>Stufe</td>
<td><select size=\"5\" name=\"stufe\">";
	$sql = "SHOW COLUMNS FROM `".$table."` LIKE 'Stufe'";
	mysql_set_charset('utf8'); 
    $temp = mysql_query($sql);
    if(mysql_num_rows($temp)>0){
        $row=mysql_fetch_row($temp);
        $werte =  explode("','",preg_replace("/(enum|set)\('(.+?)'\)/","\\2",$row[1]));
    }
	foreach($werte AS $wert){
		echo "<option value=\"".$wert."\""; if($wert == $datensatz['Stufe']) echo "selected"; echo ">".$wert."</option>";
	}echo "</select>
</td>
</tr>
<tr><td>Bezahlt: </td><td><input type='checkbox' name='bezahlt'"; if ($datensatz['Bezahlt'] == true) echo "checked";
echo "></td>
</tr>";
if(isset($tage_erfassen) && $tage_erfassen) {
	for ($i = 0; $i < $dauer; $i++){
		$j = $i+1;
		$date=new DateTime($start_date, new DateTimeZone('Europe/Berlin'));
		$date->modify('+'.$i.' day');
		echo "<tr><td>".$date->format('D d.m.').":</td><td><input type='checkbox' name='tag_$i'"; if(in_array($j, $tage)) { echo "checked"; } echo"></td>";
	}
}
if(isset($pers_erfassen) && $pers_erfassen) echo "<tr><td>Personen: </td><td><input type='text' name='pers' length='20'value='".$datensatz['Personen']."'></td></tr>";
echo "<tr>
<td>Bemerkung: </td><td><textarea name='bemerk' clos='20' rows='10'>".$datensatz['Bemerkung']."</textarea></td>
</tr><tr>
<td colspan='2' align='center'><input type='hidden' name='id' value='".$id."'></input><input type='hidden' name='jahr' value='".$jahr."'></input><input type='hidden' name='page_id' value='".$page_id."'></input><input type='hidden' name='section_id' value='".$section_id."'></input><input type='reset'><input type='submit'></td>
</tr>
</table>
</form>";
}
?>