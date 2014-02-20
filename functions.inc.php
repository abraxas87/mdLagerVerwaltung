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


/*  Alter aus zwei gegeben Daten berechen
	day, month, year für das Geburtsdatum
	cur_day, _cur_month, cur_year für das Vergleichsdatum */
function ageCalculator( $day, $month, $year, $cur_day, $cur_month, $cur_year ) {

	if ( !checkdate($month, $day, $year) )
		return false;

	$calc_year = $cur_year - $year;

	if( $month > $cur_month )
		return $calc_year - 1;
	elseif ( $month == $cur_month && $day > $cur_day )
	return $calc_year - 1;
	else
		return $calc_year;

}

function stufenfarbe($stufe='keine', $default=array(0,0,0)){
	switch ($stufe){
		case 'Wölfling': $r = 255; $g = 165; $b = 0;
		break;
		case 'Jungpfadfinder': $r = 0; $g = 0; $b = 255;
		break;
		case 'Pfadfinder': $r = 42; $g = 128; $b = 0;
		break;
		case 'Rover': $r = 255; $g = 0; $b = 0;
		break;
		case 'Leiter': $r = 218; $g = 165; $b = 32;
		break;
		default: $r = $default[0]; $g = $default[1]; $b = $default[2];
	}
	return array ($r, $g, $b);
}
?>