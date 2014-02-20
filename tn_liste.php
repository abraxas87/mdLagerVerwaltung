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

/* Script für die PDF-Erzeugung einbinden */
require_once 'fpdf/fpdf.php';

/*Funktionen laden*/
require_once '../../config.php';
require_once 'functions.inc.php';

/*Variablen definieren*/
if (isset($_GET['page_id'])) $page_id = strip_tags($_GET['page_id']);
if (isset($_GET['section_id'])) $section_id = strip_tags($_GET['section_id']);
$table = TABLE_PREFIX."mod_mdLV".$page_id."-".$section_id;
$mod_url = WB_URL."/modules/mdLagerVerwaltung/";
mysql_set_charset('utf8');

//Prüfen ob vor dem Script ein Jahr ausgewählt wurde, ansonstzen das aktuelle Jahr verwenden
if(isset($_GET['jahr'])) { $jahr = $_GET['jahr']; } else { $jahr = date("Y"); }


/* Erster Teil des Scripts.
 * Formular für die Eingabe der Veranstaltungsdaten
 * Nur aufrufen wenn noch kein Formular dieses Scripts abgeschickt wurde */
if(!isset($_POST['send']) && !isset($_POST['send2'])){
	$query = "SELECT * FROM `".TABLE_PREFIX."mod_mdLagerVerwaltung` WHERE `page_id` = '$page_id' && `section_id` = '$section_id'";
	$erg = mysql_query($query);
	$data = mysql_fetch_array($erg);
	?>
	<form method=post action="<?php $_SERVER['PHP_SELF'] ?>">
		<input type="hidden" name="send" value="true"></input>
		<h2 align="center">Veranstaltungs-Daten</h2>
		<table align="center">
			<tr>
				<td>Veranstaltung:</td><td><?php echo $data['Titel']; ?></td>
			</tr>
			<tr>
				<td>Von:</td><td><?php echo $data['begin']; ?></td>
			</tr>
			<tr>
				<td>Bis:</td><td><?php echo $data['end']; ?></td>
			</tr>
			<tr>
				<td>Veranstaltungsort:</td><td><?php echo $data['Ort']; ?></td>
			</tr>
			<tr>
				<td>Gäste auf Teilnahmeliste:</td><td><input type="checkbox" name="guest" /></td>
			</tr>
			<tr>
				<td colspan="2">Die Veranstaltungsdaten können im Administrations-Bereich der Seite angepasst werden.</td>
			</tr>
			<tr>
				<td colspan="2" align="right"><input type="submit" value="Weiter"></input></td>
			</tr>
		</table>
	</form>
<?php 
}

/**********************************************************************************************************/

/* Zweiter Teil des Scripts
 * Teilnehmer aus der Datenbank laden und Formular für ergänzende Angaben
 * Nur aufrufen wenn das erste Formular abgeschickt wurde */
if(isset($_POST['send']) && !isset($_POST['send2'])){

if(isset($_POST['guest'])) $guest = $_POST['guest']; else $guest = false;
if($guest == "on") $guest = true;

if ($guest) $query = "SELECT * FROM `$table` WHERE Jahr = $jahr ORDER BY Stufe, Name, Vorname";
else $query = "SELECT * FROM `$table` WHERE Jahr = $jahr && Stufe != 'Gast' ORDER BY Stufe, Name, Vorname";

if($data = mysql_query ($query)){?>
	<form method=post action="<?php $_SERVER['PHP_SELF'] ?>">
		<input type="hidden" name="send2" value="true"></input>
		<h2 align="center">Teilnahmeliste</h2>
		<table>
			<tr>
				<th>Lfd. Nr.</th><th>Name</th><th>Vorname</th><th>Wohnort</th><th>Straße</th><th>Geburtsdatum</th><th>ml.</th><th>wbl.</th><th>PM</th><th>Julei-Card Nr.</th>
			</tr>
			<?php 
			$i = 1;
			while ($zeile = mysql_fetch_array($data)) {	
				$id = $zeile['ID'];
				if (isset($zeile['Name'])){ $nname = $zeile['Name']; } else { $nname = ""; }
				if (isset($zeile['Vorname'])){ $vname = $zeile['Vorname']; } else { $vname = ""; }
				if (isset($zeile['Ort'])){ $ort = $zeile['Ort']; } else { $ort = NULL; }
				if (isset($zeile['Strasse'])){ $str = $zeile['Strasse']; } else { $str = ""; }
				if ($zeile['Geburtsdatum'] != 0){ $geburtsdatum = $zeile['Geburtsdatum']; } else { $geburtsdatum = "01.01.1970"; }
				if ($zeile['male'] == true){ $male = true; } else { $male = false; }
				if ($zeile['female'] == true){ $female = true; } else { $female = false; }
				if ($zeile['PM'] == true){ $pm = true; } else { $pm = false; }
				if (isset($zeile['Juleica'])){ $juleica = $zeile['Juleica']; } else { $juleica = ""; }
				if (isset($zeile['Stufe'])){ $stufe = $zeile['Stufe']; } else $stufe = 'keine';
				
				list($r, $g, $b) = stufenfarbe($stufe, array (255, 255, 255));
				
				echo "<tr>
						<td style=\"background-color:rgb(".$r.",".$g.",".$b.")\">".$i."</td>
						<input type=\"hidden\" value=\"".$id."\" name=\"id".$i."\" />
					  	<td><input type=\"text\" value=\"".$nname."\" name=\"nname".$id."\" /></td>
					  	<td><input type=\"text\" value=\"".$vname."\" name=\"vname".$id."\" /></td>
						<td><input type=\"text\" value=\"".$ort."\" name=\"ort".$id."\" /></td>
						<td><input type=\"text\" value=\"".$str."\" name=\"str".$id."\" /></td>
						<td><input type=\"text\" value=\"".$geburtsdatum."\" name=\"geburtsdatum".$id."\" /></td>
						<td><input type=\"checkbox\""; if($male == true) echo "checked"; echo" name=\"male".$id."\" /></td>
						<td><input type=\"checkbox\""; if($female == true) echo "checked"; echo" name=\"female".$id."\" /></td>
						<td><input type=\"checkbox\""; if($pm == true) echo "checked"; echo" name=\"pm".$id."\" /></td>
						<td><input type=\"text\" value=\"".$juleica."\" name=\"juleica".$id."\" /></td>
					</tr>";
				$i++;
			} ?>
			<tr>
				<td colspan="10" align="right">
					<input type="hidden" name="titel" value="<?php echo $titel; ?>">	
					<input type="hidden" name="start_date" value="<?php echo $start_date; ?>">
					<input type="hidden" name="end_date" value="<?php echo $end_date; ?>">
					<input type="hidden" name="location" value="<?php echo $location; ?>">
					<input type="hidden" name="count" value="<?php echo $i-1; ?>">
					<?php if (isset($guest)) echo "<input type=\"hidden\" name=\"guest\" value=\"".$guest."\">"; ?>
					<input type="submit" value="Weiter"></input>
				</td>
			</tr>
		</table>
	</form>
<?php
	} else echo "Keine Verbindungzur Datenbank. Bitte den Administrator informieren.";
}

/**********************************************************************************************************/

/* Dritter Teil des Scripts
 * Daten aus dem zweiten Formular in die Datenbank schreiben
 * Teilnehmerdaten aus der Datenbank laden
 * PDF Datei erzeugen und mit Versanstaltungs- und Teilnehmerdaten füllen 
 * Nur aufrufen wenn das zweite Formular abgeschickt wurde*/
if (isset($_POST['send2'])){
	
	$pdf=new FPDF('L','pt','A4'); //PDF, Querformat, Angaben in Pixel, DinA4
	$pdf->AddPage();
	$pdf->SetFont('Arial','',10); //Schriftart und Größe deffinieren
	$pdf->Image($mod_url.'images/tn_liste1.png', 0, 0, 850); //Teilnehmerliste als Hintergrundbild laden
	
	$y = 149;	
		
	for($j=1; $j<=$_POST['count']; $j++){
		$id = $_POST['id'.$j];
		$nname = $_POST['nname'.$id];
		$vname = $_POST['vname'.$id];
		$ort = $_POST['ort'.$id];
		$str = $_POST['str'.$id];
		$geburtsdatum = $_POST['geburtsdatum'.$id];
		if(isset($_POST['male'.$id])) $male = $_POST['male'.$id]; else $male = false;
		if($male == "on") $male = true;
		if(isset($_POST['female'.$id])) $female = $_POST['female'.$id]; else $female = false;
		if($female == "on") $female = true;
		if(isset($_POST['pm'.$id])) $pm = $_POST['pm'.$id]; else  $pm = false;
		if($pm == "on") $pm = true;
		$juleica = $_POST['juleica'.$id];
		$query = "UPDATE `$table` SET Name = '$nname', Vorname = '$vname', Ort = '$ort', Strasse = '$str', Geburtsdatum = '$geburtsdatum', male = '$male', female = '$female', PM = '$pm', Juleica = '$juleica'  WHERE Id = $id";
		$result = mysql_query($query);
	}
	
	$query = "SELECT * FROM `".TABLE_PREFIX."mod_mdLagerVerwaltung` WHERE `page_id` = '$page_id' && `section_id` = '$section_id'";
	$erg = mysql_query($query);
	$data = mysql_fetch_array($erg);
	$titel = $data['Titel'];
	$start_date = $data['begin'];
	$end_date = $data['end'];
	$location = $data['Ort'];

if(isset($_POST['guest'])) $guest = $_POST['guest']; else $guest = false;
if ($guest) $query = "SELECT * FROM `$table` WHERE Jahr = $jahr ORDER BY Stufe, Name, Vorname";
else $query = "SELECT * FROM `$table` WHERE Jahr = $jahr && Stufe != 'Gast' ORDER BY Stufe, Name, Vorname";
	
if($data = mysql_query ($query)){

if( (isset($start_date) && ($start_date != "")) && ((isset($end_date) && ($end_date != "") )) ){
	list($sd,$sm,$sy) = split("\\.",$start_date);
	list($ed,$em,$ey) = split("\\.",$end_date);
	$date1 = mktime(0,0,0,$sm,$sd,$sy);
	$date2 = mktime(0,0,0,$em,$ed,$ey);
	$DIFF = $date2 - $date1;
	$dauer = floor($DIFF/86400)+1;
}
else $dauer = ""; 
	
$pdf->SetXY(120, 87);
$pdf->Cell(340,15,utf8_decode($titel), 0);  //Veranstaltungstitel
$pdf->SetXY(500, 87);
$pdf->Cell(90,15,utf8_decode($start_date), 0);  //Anfangs-Datum
$pdf->SetXY(615, 87);
$pdf->Cell(90,15,utf8_decode($end_date), 0);  //End-Datum
$pdf->SetXY(730, 87);
$pdf->Cell(42,15,utf8_decode($dauer), 0, 0, 'R');  //Dauer
$pdf->SetXY(630, 105);
$pdf->Cell(199,15,utf8_decode($location), 0);  //Veranstaltungsort

$i = 1;
$count_male = 0;
$count_female = 0;
$count_pm = 0;
//foreach ($data AS $zeile){
while ($zeile = mysql_fetch_array($data)) {
	
	if (isset($zeile['Name'])){ $nname = $zeile['Name']; } else { $nname = ""; }
	if (isset($zeile['Vorname'])){ $vname = $zeile['Vorname']; } else { $vname = ""; }
	if (isset($zeile['Ort'])){ $ort = $zeile['Ort']; } else { $ort = NULL; }
	if (isset($zeile['Strasse'])){ $str = $zeile['Strasse']; } else { $str = ""; }
	if (isset($zeile['Stufe'])){ $stufe = $zeile['Stufe']; } else { $stufe = ""; }
	if (isset($zeile['Geburtsdatum']) && $zeile['Geburtsdatum'] != ''){ $geburtsdatum = $zeile['Geburtsdatum']; } else { $geburtsdatum = "01.01.1970"; }
	if ($zeile['male'] == true){ $male = "X"; $count_male++; } else { $male = ""; }
	if ($zeile['female'] == true){ $female = "X"; $count_female++; } else { $female = ""; }
	if ($zeile['PM'] == true){ $pm = "X"; $count_pm++; } else { $pm = ""; }
	if (isset($zeile['Juleica'])){ $juleica = $zeile['Juleica']; } else { $juleica = ""; }
	
	if($geburtsdatum != "01.01.1970"){
		if($end_date == "") { $ed=date("d"); $em=date("n"); $ey=date("Y"); }
		list($bd,$bm,$by) = split("\\.",$geburtsdatum);
		$alter = ageCalculator($bd, $bm, $by, $ed, $em, $ey);
	} else $alter = "";
	
	list($r, $g, $b) = stufenfarbe($stufe);
	
	$pdf->SetTextColor($r, $g, $b);
	$pdf->SetXY(40, $y);
	$pdf->Cell(24,18,$i, 0, 0, 'C'); //lfd.-Nr.
	$pdf->SetTextColor(0, 0, 0);
	$pdf->Cell(114,18,utf8_decode($nname), 0);  //Nachname
	$pdf->Cell(100,18,utf8_decode($vname), 0);  //Vorname
	$pdf->Cell(129,18,utf8_decode($ort), 0);  //Wohnort
	$pdf->Cell(129,18,utf8_decode($str), 0);  //Straße
	$pdf->Cell(29,18,utf8_decode($alter), 0, 0, 'C');  //Alter
	$pdf->Cell(28,18,utf8_decode($male), 0, 0, 'C');  //Maennlich
	$pdf->Cell(28,18,utf8_decode($female), 0, 0, 'C');  //Weiblich
	$pdf->Cell(29,18,utf8_decode($pm), 0, 0, 'C');  //PM
	$pdf->Cell(71,18,utf8_decode($juleica), 0);  //Juleica-Nr.
	if($i%4==0)	$y += 20;
	else $y += 19;

	if ((($i < 20) && ($i % 16 == 0)) || (($i > 20) && (($i-16) % 20 == 0))){
		if (($i < 20) && ($i % 16 == 0)) $pdf->SetXY(565, 480);
		else $pdf->SetXY(565, 495);
		$pdf->Cell(28,18,utf8_decode($count_male), 0, 0, 'C');  //Übertrag Männlich
		$pdf->Cell(28,18,utf8_decode($count_female), 0, 0, 'C');  //Übertrag Weiblich
		$pdf->Cell(28,18,utf8_decode($count_pm), 0, 0, 'C');  //Übertrag PM
		$pdf->AddPage();
		$pdf->Image('../../media/icons/tn_liste2.png', 0, 0, 850);
		$pdf->SetXY(565, 45);
		$pdf->Cell(28,18,utf8_decode($count_male), 0, 0, 'C');  //Übertrag Männlich
		$pdf->Cell(28,18,utf8_decode($count_female), 0, 0, 'C');  //Übertrag Weiblich
		$pdf->Cell(28,18,utf8_decode($count_pm), 0, 0, 'C');  //Übertrag PM
		$y = 86;
	}
	
	$i++;
}
if($i < 17) $pdf->SetXY(565, 480);
else $pdf->SetXY(565, 495);
$pdf->Cell(28,18,utf8_decode($count_male), 0, 0, 'C');  //Übertrag Männlich
$pdf->Cell(28,18,utf8_decode($count_female), 0, 0, 'C');  //Übertrag Weiblich
$pdf->Cell(28,18,utf8_decode($count_pm), 0, 0, 'C');  //Übertrag PM
$pdf->Output('teilnehmerliste.pdf', 'I');
}



}
?>