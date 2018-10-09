<?php
/**
 * DokuWiki Plugin datepicker (Action Component) 
 *
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author lisps
 * @author peterfromearth
 */

if (!defined('DOKU_INC')) die();
class action_plugin_datepicker extends DokuWiki_Action_Plugin {
	/**
	 * Register the eventhandlers
	 */
	public function register(Doku_Event_Handler $controller) {
		$controller->register_hook('TOOLBAR_DEFINE', 'AFTER', $this, 'insert_button', array ());
		$controller->register_hook('AJAX_CALL_UNKNOWN', 'BEFORE',  $this, '_ajax_call');
	}
	
	/**
	 * Inserts the toolbar button
	 */
	public function insert_button(Doku_Event $event, $param) {
		$event->data[] = array(	
			'type'   => 'picker',
			'list' =>array('<datepicker>','<datepicker\>','<datepicker#>','<weekpicker>','<weekpicker\>','<weekpicker#>'),
			'title' => 'Datepicker',
			'icon'   => '../../plugins/datepicker/images/date.gif',
			'sample' => '',
			//'open' => '<datepicker>',
			'close'=>'',
			'insert'=>'',
		);
	}
	
	public function _ajax_call(Doku_Event $event, $param) {
	    if ($event->data !== 'plugin_datepicker') {
	        return;
	    }
	    //no other ajax call handlers needed
	    $event->stopPropagation();
	    $event->preventDefault();
	    
	    /* @var $INPUT \Input */
	    global $INPUT;
	    
	    #Variables
	    $datecount = $INPUT->int('id');
	    $datestr   = $INPUT->str('datestr');
	    $mode 	   = $INPUT->str('mode');
	    
	    /* @var $Hajax \helper_plugin_ajaxedit */
	    $Hajax = $this->loadHelper('ajaxedit');
	    
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
	    
	    $param['msg'] = sprintf($Hajax->getLang('changed_from_to'),hsc($mode),hsc($olddatestr),hsc($datestr));
	    
	    $Hajax->saveWikiPage($data,$summary,true,$param);
	}
}
