/* DOKUWIKI:include_once script/jscalendar-1.0/calendar.js */
/* DOKUWIKI:include_once script/jscalendar-1.0/calendar-setup.js */
/* DOKUWIKI:include_once script/jscalendar-1.0/lang/calendar-en.js */
/* DOKUWIKI:include_once script/jscalendar-1.0/lang/calendar-de.js */
/**
 * DokuWiki Plugin datepicker (JavaScript Component) 
 *
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author  lisps    
 */
var EMPTYSTRINGWEEK;
var EMPTYSTRINGDATE;
function datepickerInit(id,emptyString) 
{
	Calendar.setup({
			ifFormat       :    "%Y-%m-%d",     // format of the input field (even if hidden, this format will be honored)
			displayArea    :    "datepicker__show__"+id,       // ID of the span where the date is to be shown
			daFormat       :    "%Y-%m-%d",// format of the displayed date
			button         :    "datepicker__button__"+id,  // trigger button (well, IMG in our case)
			align          :    "Br",           // alignment (defaults to "Bl")
			singleClick    :    true,
			onUpdate	   :    datepickeronUpdate,
			electric       :    false,
			firstDay       :    1
		});
	EMPTYSTRINGDATE = emptyString;
}

function weekpickerInit(id,emptyString) 
{
	Calendar.setup({
			ifFormat       :    "%W/%y",     // format of the input field (even if hidden, this format will be honored)
			displayArea    :    "weekpicker__show__"+id,       // ID of the span where the date is to be shown
			daFormat       :    "%W/%y",// format of the displayed date
			button         :    "weekpicker__button__"+id,  // trigger button (well, IMG in our case)
			align          :    "Br",           // alignment (defaults to "Bl")
			singleClick    :    true,
			onUpdate	   :    weekpickeronUpdate,
			firstDay	   :    1,
			electric       :    false
		});
	EMPTYSTRINGWEEK = emptyString;
}

function datepickeronUpdate(calendar) 
{
	mode = 'datepicker';
	par = calendar.params;
	
	if(par.displayArea.innerHTML == ''){
		par.displayArea.innerHTML = EMPTYSTRINGDATE;
		datestr = '';
	}
	else {
		datestr = calendar.date.print(par.daFormat);
	}
	var idx = null;
	if(jQuery("#"+par.displayArea.id).parents('div.sortable').length != 0) {
		idx = jQuery("#"+par.displayArea.id).data("plugin-datepicker-idx");
	} else {
		idx = ajaxedit_getIdxByIdClass(par.displayArea.id, //DOM-id
		'datepicker');		//DOM-class
	}
	ajaxedit_send(
		'datepicker',		//pluginname
		idx,
		datepickerdone,		//success-function
		{
			datestr:datestr,
			mode:mode,
			//id,
		}
	);	
}

function weekpickeronUpdate(calendar) 
{
	mode = 'weekpicker';
	par = calendar.params;
	if(par.displayArea.innerHTML == ''){
		par.displayArea.innerHTML = EMPTYSTRINGWEEK;
		datestr = '';
	}
	else {
		datestr = calendar.date.print(par.daFormat);
	}	

	ajaxedit_send(
		'datepicker',		//pluginname
		ajaxedit_getIdxByIdClass(
			par.displayArea.id, //DOM-id
			'weekpicker'),		//DOM-class
		datepickerdone,		//success-function
		{
			datestr:datestr,
			mode:mode,
			//id,
		}
	);
}
   
function datepickerdone(data)
{
	ret = ajaxedit_parse(data);
	ajaxedit_checkResponse(ret);	
}
