<?php
/**
 * DokuWiki Plugin datepicker (AJAX Component) 
 *
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author  lisps    
 */
if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../../').'/');
require_once(DOKU_INC.'inc/init.php');

#Variables  
$datecount = intval($_POST["id"]);
$datestr   = trim($_POST["datestr"]);
$mode 	   = trim($_POST['mode']);

$Hajax = plugin_load('helper', 'ajaxedit');

if($mode !== "datepicker" && $mode !== "weekpicker"){
	$Hajax->error('unknown mode');
	return;
}

$data=$Hajax->getWikiPage();



$range_delemiters = array();
//remove pagemod area - no changes here
$ranges  = preg_split('$<pagemod[\w\W]*?</pagemod>$',$data);
$count = preg_match_all('$<pagemod[\w\W]*?</pagemod>$',$data,$range_delemiters);

if($count) {
	$range_delemiters = $range_delemiters[0];
} else {
	$range_delemiters = array();
}

//will be set in loop to detect if change has already happened
$found_flag = false;

//will count the <multiselect - need for calculation
$found_counter = 0;

foreach($ranges as $range_index=>&$range_part){
	//find "our" datepicker
	$found=explode('<'.$mode,$range_part);

	//selectcount for the specific range
	$datecount_range = $datecount-$found_counter;
	
	//overall found counter
	$found_counter += count($found)-1;

	if (!$found_flag && $datecount < $found_counter) {
		$found_flag = true;

		$olddatestr = "none";
		$option= '';
		if($found[$datecount_range+1][0] === '\\'){
			$option = '\\';
			$found[$datecount_range+1]=substr($found[$datecount_range+1],1);
		}
		else if($found[$datecount_range+1][0] === '#'){
			$option = '#';
			$found[$datecount_range+1]=substr($found[$datecount_range+1],1);
		}
		$found[$datecount_range+1] = ltrim($found[$datecount_range+1]);
		$stop=strpos($found[$datecount_range+1],">");
		if ($stop === FALSE) {
			$Hajax->error('Cannot find object, please contact your admin!');
		}
		else if ($stop > 0) {
			$olddatestr=substr($found[$datecount_range+1],0,$stop);
			$found[$datecount_range+1]=str_replace($olddatestr,$option." ".$datestr." ",$found[$datecount_range+1]);
		} 
		else if ($stop == 0) {
			$found[$datecount_range+1]= $option." ".$datestr . $found[$datecount_range+1];
		}
		//create new pagesource        
		$range_part=implode('<'.$mode,$found). (isset($range_delemiters[$range_index])?$range_delemiters[$range_index]:'');
		

	} else {
		$range_part .= isset($range_delemiters[$range_index])?$range_delemiters[$range_index]:'';
	}
}
	$data = implode($ranges);	
	
	$param = array(
		'mode'	=> $mode,
		'index'	=> $datecount
	);
	$summary= $mode.' '.$datecount." changed from ".$olddatestr." to ".$datestr;
	$Hajax->saveWikiPage($data,$summary,true,$param);


?>