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

/*Funktionen laden*/
require_once 'functions.inc.php';

/*Variablen definieren*/
$table = TABLE_PREFIX."mod_mdLV".$page_id."-".$section_id;

if(!isset($jahr)) 
	if(isset($_GET['jahr'])) { $jahr = strip_tags($_GET['jahr']); }
	else $jahr = date("Y"); // Aktuelles Jahr wählen wenn nichts anderes definiert ist.
if(isset($_POST["pers"])) { $pers = $_POST['pers']; } else $pers = 1;
if(isset($_POST["vname"])) { $vname = $_POST["vname"]; } //Vorname
if(isset($_POST["nname"])) { $nname = $_POST["nname"]; } //Nachname
if(isset($_POST["bezahlt"])) { $bez = $_POST["bezahlt"]; } //Bezahltstatus
if(isset($_POST["bemerk"])) { $bemerk = $_POST["bemerk"]; } //Bemerkungen
if(isset($_POST["stufe"])) { $stufe = $_POST["stufe"]; } //Altersstufestufe

if(isset($_POST["Id"])) { $Id = $_POST["Id"]; } // ID des Datenbanksatzes, nur bei bestehenden einträgen
if(isset($_POST["aktion"])) { $aktion = $_POST["aktion"]; } // Auszufürende Aktion, z.B. Datensatz löschen oder Bezahltstatus ändern
if( isset($bez) && $bez == 'on'){$bez = true;}
	
mysql_set_charset('utf8'); 

/*Veranstaltungsdaten aus der DB holenn*/

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

	
// Neuen Datensatz in die datenbank schreiben und die Seite neu laden	
if(isset($vname)){
	if(isset($tage_erfassen)){
		$days = "";
		for ($i = 0; $i < $dauer; $i++){
			$j = $i+1;
			if(isset($_POST['tag_'.$i])) $days .= $j.",";
		}
	}
	$query = "INSERT INTO `$table` SET Name = '$nname', Vorname = '$vname', Stufe = '$stufe', Bezahlt =  '$bez', Personen='$pers', Tage = '$days', Bemerkung = '$bemerk', Jahr = '$jahr'";
	$database->query($query);
	header('Location: '.$_SERVER['PHP_SELF']);
}

echo "<div class=\"mdLV\">"; //Container

if(isset($Id)) // Nur wenn ein bereits bestehende Eintrag gewählt wurde
if(isset($aktion)){
	//Datensatz aus der Datenbank löschen und Seite neu laden
	if($aktion == "loeschen"){
	   	$query = "DELETE FROM `$table` WHERE ID = '$Id'";
	   	$database->query($query);
	   	header('Location: '.$_SERVER['PHP_SELF']);
	}
}
else{
// Bezahltstatus ändern und Seite neu laden
  	$query = "SELECT Bezahlt FROM `$table` WHERE ID = '$Id'";
  	$status = mysql_query($query);
	$status = mysql_fetch_array($status);
	if ($status['Bezahlt'] == true){
		$query = "UPDATE `$table` SET Bezahlt=false WHERE ID = '$Id' ";
	}else{
		$query = "UPDATE `$table` SET Bezahlt=true WHERE ID = '$Id' ";
	}	
	$database->query($query);
	header('Location: '.$_SERVER['PHP_SELF']);
}

/*Tabelle der Bisherigen Anmeldungen aufbauen*/
echo "<table  align='center' style='border: 0px solid #000000;'><tr><th align='left' style='border-bottom: 2px solid #000000;'> </th><th align='left' style='border-bottom: 2px solid #000000;'>Vorname</th><th align='left' style='border-bottom: 2px solid #000000;'>Nachname</th><th align='left' style='border-bottom: 2px solid #000000;'>Stufe</th>";
if(isset($pers_erfassen) && $pers_erfassen) echo"<th align='left' style='border-bottom: 2px solid #000000;'>Personen</th>";
if (isset($tage_erfassen) && $tage_erfassen){
	for ($i = 0; $i < $dauer; $i++){
		$date=new DateTime($start_date, new DateTimeZone('Europe/Berlin'));
		$date->modify('+'.$i.' day');
		echo "<th align='left' style='border-bottom: 2px solid #000000;'>".$date->format('D d.m.')."</th>";
	}
}

echo "<th align='left' style='border-bottom: 2px solid #000000;'>Bezahlt</th><th align='left' style='border-bottom: 2px solid #000000;'>Bemerkung</th><th align='left' style='border-bottom: 2px solid #000000;'>Löschen</th>"; //Tabelle aufbauen
mysql_set_charset('utf8'); 

/*Anmeldungen aus der DB laden und in die Tabelle einfügen*/
$query = "SELECT * FROM `$table` WHERE Jahr = $jahr ORDER BY Stufe, Name, Vorname";
if($ergebnis = mysql_query($query)){  //Prüfen ob zugriff auf die Datenabk besteht, Anmeldungen aus der Datenbank laden
	$gesamttage = array();
	while ($zeile = mysql_fetch_array($ergebnis)) {      //Für jede Anmeldung ausführen
		$id = $zeile['ID']; //ID auslesen
		$vorname = $zeile['Vorname'];   //Vorname auslesen
		$nachname = $zeile['Name'];  //Nachname auslesen
		$stufe = $zeile['Stufe'];  //Stufe auslesen
		$pers = $zeile['Personen'];
		$tage = explode(',', $zeile['Tage']);
		$bezahlt = $zeile['Bezahlt'];  //Bezahlt Status auslesen
		if($bezahlt == 1){$bezahlt = 'checked'; $button="Bezahlt"; $checkimg = 'yes.png';}
		else {$button = "Nicht Bezahlt"; $checkimg = 'no.png';}
		if( $zeile['Bemerkung'] != NULL ){
			$bemerkung = $zeile['Bemerkung']; //
		}else{$bemerkung = "&nbsp;";}
		list($r, $g, $b) = stufenfarbe($stufe);
		echo "<tr><td style='border-bottom: 1px solid #000000;'><a href=\"".WB_URL."/modules/mdLagerVerwaltung/edit.php?id=$id&jahr=$jahr&page_id=$page_id&section_id=$section_id\" class=\"lytebox\" data-lyte-options=\"refreshPage:true height:800 width:600\"><img src=\"".WB_URL."/modules/mdLagerVerwaltung/images/edit.png\" alt=\"edit\"></img></a></td><td style='border-bottom: 1px solid #000000; font-weight: bold;'>" .$vorname."</td><td style='border-bottom: 1px solid #000000; font-weight: bold;'>".$nachname."</td><td style='border-bottom: 1px solid #000000; font-weight: bold; color: rgb(".$r.",".$g.",".$b.")'>".$stufe."</td>";
		if(isset($pers_erfassen) && $pers_erfassen) echo "<td style='border-bottom: 1px solid #000000; font-weight: bold;'>".$pers."</td>";
		if(isset($tage_erfassen) && $tage_erfassen) {
			for ($i = 1; $i <= $dauer; $i++){
				echo "<td style='border-bottom: 1px solid #000000; text-align: center;'>";
				if(in_array($i, $tage)) {echo "<img src=\"".WB_URL."/modules/mdLagerVerwaltung/images/y.png\" alt=\"ja\"/>"; if(isset($gesamttage[$i])) $gesamttage[$i] = $gesamttage[$i]+1; else $gesamttage[$i] = 1; }
				else echo "<img src=\"".WB_URL."/modules/mdLagerVerwaltung/images/x.png\" alt=\"nein\"/>";
				echo "</td>";	
			}
		}
		echo "<td style='border-bottom: 1px solid #000000; font-weight: bold; text-align: center;'><form method=post action=".$_SERVER['PHP_SELF']."><input type='hidden' name='Id' value='".$id."'></input><input type=\"image\" src=\"".WB_URL."/modules/mdLagerVerwaltung/images/$checkimg\" alt=\"$button\"></form></td><td style='border-bottom: 1px solid #000000; font-weight: bold;'>".$bemerkung."</td><td style='border-bottom: 1px solid #000000; font-weight: bold; text-align: center;'><form method=post action=".$_SERVER['PHP_SELF']."><input type='hidden' name='Id' value='".$id."'><input type='hidden' name='aktion' value='loeschen'></input><input type=\"image\" src=\"".WB_URL."/modules/mdLagerVerwaltung/images/delete.png\" alt=\"Löschen\"></form></td></tr>"; //Tabelle Zeilen bauen
	}
	
	//Teilnehmerzahl berechnen und in die Tabelle einfügen
	if($pers_erfassen) $query = "Select SUM(personen) as GESAMT FROM `$table` WHERE Jahr = $jahr";
	else $query = "Select COUNT(*) as GESAMT FROM `$table` WHERE Jahr = $jahr"; // Gesamtzahl der Teilnehmer
	if($ergebnis = mysql_query($query)){
		$summe = mysql_fetch_row($ergebnis);
	} else $summe = ('0');
	if ($tage_erfassen) {
		echo "<tr><td style='border-bottom: 1px solid #000000; font-weight: bold; text-align: right' colspan='4'>Gesamt:</td>";
		for ($j = 1; $j <= $dauer; $j++){
			if (!isset($gesamttage[$j])) $gesamttage[$j] = '0';
			echo "<td style='border-bottom: 1px solid #000000; font-weight: bold; text-align: center'>".$gesamttage[$j]."</td>";
		}
		echo "</tr>";
	} else{ echo "<tr><td style='border-bottom: 1px solid #000000; font-weight: bold; text-align: right' colspan='4'>Gesamt:</td><td style='border-bottom: 1px solid #000000; font-weight: bold; text-align: center' colspan='2'>".$summe[0]."</td></tr>"; $j=1; }
	echo "<tr><td colspan=\""; echo $j+6; echo"\" align=\"center\"><div style=\"background: none repeat scroll 0% 0% rgb(236, 236, 236); border: 1px solid gray; width: 150px;\"><a href=\"".WB_URL."/modules/mdLagerVerwaltung/tn_liste.php?jahr=$jahr&page_id=$page_id&section_id=$section_id\" class=\"lytebox btn_link\" data-lyte-options=\"refreshPage:false height:800 width:1024\">Teilnahmeliste Erzeugen</a></div></td></tr>";
} 
else { //Fehler beim DB Zugriff melden
		echo "<tr><td colspan=3>Dantebankzugriff gescheitert!<br>Bitte versuchen Sie es erneut.<br>Falls der Fehler dauerhaft auftritt<br>informieren Sie bitte den Administrator</td></tr>";
}
	
echo "</table><br /><br />"; //Tabelle abschließen

/*Formular für neuen Eintrag aufbauen*/
echo "<h2 align='center'>Neuer Eintrag</h2>";  
echo "<form method=post action=".$_SERVER['PHP_SELF'].">
<table  align='center' style='border: 0px solid #000000;'>
	<tr>
		<td>Vorname: </td><td><input type='text' name='vname' length='20'></td>
	</tr>
	<tr>
		<td>Nachname: </td><td><input type='text' name='nname' length='20'></td>
	</tr>
	<tr>
		<td>Stufe</td>
		<td><select size=\"5\" multiple name=\"stufe\">";
			$sql = "SHOW COLUMNS FROM `$table` LIKE 'Stufe'"; //stufennamen aus der Datenbank holen
			mysql_set_charset('utf8'); 
    		$temp = mysql_query($sql);
    		if(mysql_num_rows($temp)>0){
        		$row=mysql_fetch_row($temp);
   	    		$werte =  explode("','",preg_replace("/(enum|set)\('(.+?)'\)/","\\2",$row[1])); // Stufennamen aus DB Set auslesen
   			}
			foreach($werte AS $wert){
				echo "<option value=\"".$wert."\">".$wert."</option>";
			}
			echo "</select>
		</td>
	</tr>
	<tr><td>Bezahlt: </td><td><input type='checkbox' name='bezahlt'></td>
	</tr>";
	if(isset($tage_erfassen) && $tage_erfassen) {
		for ($i = 0; $i < $dauer; $i++){
			$date=new DateTime($start_date, new DateTimeZone('Europe/Berlin'));
			$date->modify('+'.$i.' day');
			echo "<tr><td>".$date->format('D d.m.').":</td><td><input type='checkbox' name='tag_$i'></td>";
		}
	}
	if(isset($pers_erfassen) && $pers_erfassen)	echo "<tr><td>Personen</td><td><input type='text' name='pers' length='20'></td></tr>";
	echo "<tr>
		<td>Bemerkung: </td><td><textarea name='bemerk' clos='20' rows='10'></textarea></td>
	</tr><tr>
		<td colspan='2' align='center'><input type='reset'><input type='submit'></td>
	</tr>
</table>
</form>";
echo "</div>";
?>