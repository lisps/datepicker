<?php
/**
 * DokuWiki Plugin datepicker (Syntax Component) 
 *
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author  lisps    
 */

if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');

/*
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_datepicker extends DokuWiki_Syntax_Plugin
{
    var $idwcount = 0;
	var $iddcount = 0;

    /*
     * What kind of syntax are we?
     */
    function getType() {return 'substition';}

    /*
     * Where to sort in?
     */
    function getSort() {return 155;}

    /*
     * Paragraph Type
     */
    function getPType() {return 'normal';}

    /*
     * Connect pattern to lexer
     */
    function connectTo($mode){
		$this->Lexer->addSpecialPattern("<datepicker[^>]*>",$mode,'plugin_datepicker');
		$this->Lexer->addSpecialPattern("<weekpicker[^>]*>",$mode,'plugin_datepicker');
	}

    /*
     * Handle the matches
     */
    function handle($match, $state, $pos, Doku_Handler $handler) {
		$mode=trim(substr($match,1,10));
		$option = trim(substr($match,11,1));
		//echo $break;
		$match=trim(substr($match,12,strlen($match)-12-1));
		if($option === '\\' || $option === '#')
			$opts["option"] = $option;
		else
			$opts["option"] = false;
			
		if($mode === "datepicker"){
			$opts["id"]=$this->idwcount++;
		}
		else if($mode === "weekpicker"){
			$opts["id"]=$this->iddcount++;
		}
		
		$opts["mode"]=$mode;
		$opts["date"]=$match;
		return ($opts);
    }
        
    function iswriter(){
		global $ID;
		global $INFO;

		return(auth_quickaclcheck($ID) > AUTH_READ);
	}
    
    /*
     * Create output
     */
    function render($mode, Doku_Renderer $renderer, $opt) {
		global $INFO;
		
		if($mode == 'metadata') return false;
		if($mode == 'xhtml') {
			$renderer->nocache();
			$Hajax = plugin_load('helper', 'ajaxedit');
			if(!$Hajax){
				msg('Plugin ajaxedit is missing');
			}
			
			if($opt["mode"] == 'weekpicker'){
				$mode ='week'; 
				$empty = hsc($this->getConf('emptyStringWeek'));
			}
			else if ($opt["mode"] == 'datepicker'){
				$mode ='date'; 
				$empty = hsc($this->getConf('emptyStringDate'));
			}
			
			if($opt['date'] === ""){
				$opt['date']=$empty;
			}
			
			//insert selector if writable
			if ($this->iswriter()==TRUE && $Hajax) {
				$id = $opt["id"];
				$renderer->cdata("\n");
				$image = DOKU_URL."lib/plugins/datepicker/".$mode.".gif";
				switch($opt['option']){
					case '#':
						$renderer->doc .="<a class='".$mode."picker' style='cursor:pointer;' id='".$mode."picker__button__".$id."'>";
						$renderer->doc .="<img  src='$image' alt='Kalender' onload='".$mode."pickerInit(".$id.",\"".$empty."\")' style='display:none;' />";
						$renderer->doc .="<span class='".$mode."picker' id='".$mode."picker__show__".$id."' >";
						$renderer->doc .= hsc($opt['date']);
						$renderer->doc .= "</span></a>";
						break;
					case '\\':
						$renderer->doc .="<span class='".$mode."picker' id='".$mode."picker__show__".$id."'>";
						$renderer->doc .= hsc($opt['date'])."</span><br>";
						$renderer->doc .="<img class='".$mode."picker' src='$image' alt='Kalender' style='cursor:pointer;' id='".$mode."picker__button__".$id."' onload='".$mode."pickerInit(".$id.",\"".$empty."\")' />";
						break;
					case false:
						$renderer->doc .="<span class='".$mode."picker' id='".$mode."picker__show__".$id."'>";
						$renderer->doc .= hsc($opt['date'])."</span>";
						$renderer->doc .="<img class='".$mode."picker' src='$image' alt='Kalender' style='cursor:pointer;' id='".$mode."picker__button__".$id."' onload='".$mode."pickerInit(".$id.",\"".$empty."\")' />";
						break;
						
				}	
			} else {
				$renderer->doc .= hsc($opt['date']);
			}
		}
		else {
			$renderer->doc .= hsc($opt['date']);
		}
		return true;
	}

}

//Setup VIM: ex: et ts=4 enc=utf-8 :
