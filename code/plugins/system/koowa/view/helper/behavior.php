<?php
/**
 * @version		$Id: behavior.php 2106 2010-05-26 19:30:56Z johanjanssens $
 * @category	Koowa
 * @package		Koowa_View
 * @subpackage	Html
 * @copyright	Copyright (C) 2007 - 2010 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.koowa.org
 */

/**
 * Behavior View Helper Class
 *
 * @author		Mathias Verraes <mathias@joomlatools.org>
 * @category	Koowa
 * @package		Koowa_View
 * @subpackage	Html
 */
class KViewHelperBehavior
{
	/**
	 * Method to load the mootools framework into the document head
	 *
	 * - If debugging mode is on an uncompressed version of mootools is included for easier debugging.
	 *
	 * @param	boolean	$debug	Is debugging mode on? [optional]
	 */
	public static function mootools($debug = null)
	{
		static $loaded;

		// Only load once
		if ($loaded) {
			return;
		}

		// If no debugging value is set, use the configuration setting
		if ($debug === null) {
			$config = KFactory::get('lib.joomla.config');
			$debug = $config->getValue('config.debug');
		}

		if ($debug) {
			KViewHelper::script('mootools-uncompressed.js', 'media/system/js/', false);
		} else {
			KViewHelper::script('mootools.js', 'media/system/js/', false);
		}
		$loaded = true;
		return;
	}
	
	public static function caption() {
		KViewHelper::script('caption.js', 'media/system/js/');
	}

	public static function formvalidation() {
		KViewHelper::script('validate.js', 'media/system/js/' );
	}

	public static function switcher() {
		KViewHelper::script('switcher.js', 'media/system/js/' );
	}

	public static function combobox() {
		KViewHelper::script('combobox.js', 'media/system/js/' );
	}

	public static function tooltip($selector='.hasTip', $params = array())
	{
		// For now, delegate to JHTML, because loading the tooltip stuff twice causes problems.
		return JHTML::_('behavior.tooltip', $selector, $params );
	}

	public static function modal($selector='a.modal', $params = array())
	{
		static $modals;
		static $included;

		$document = KFactory::get('lib.joomla.document');

		// Load the necessary files if they haven't yet been loaded
		if (!isset($included)) {

			// Load the javascript and css
			KViewHelper::script('modal.js', 'media/system/js/');
			KViewHelper::stylesheet('modal.css', 'media/system/css/');

			$included = true;
		}

		if (!isset($modals)) {
			$modals = array();
		}

		$sig = md5(serialize(array($selector,$params)));
		if (isset($modals[$sig]) && ($modals[$sig])) {
			return;
		}

		// Setup options object
		$opt['ajaxOptions']	= (isset($params['ajaxOptions']) && (is_array($params['ajaxOptions']))) ? $params['ajaxOptions'] : null;
		$opt['size']		= (isset($params['size']) && (is_array($params['size']))) ? $params['size'] : null;
		$opt['onOpen']		= (isset($params['onOpen'])) ? $params['onOpen'] : null;
		$opt['onClose']		= (isset($params['onClose'])) ? $params['onClose'] : null;
		$opt['onUpdate']	= (isset($params['onUpdate'])) ? $params['onUpdate'] : null;
		$opt['onResize']	= (isset($params['onResize'])) ? $params['onResize'] : null;
		$opt['onMove']		= (isset($params['onMove'])) ? $params['onMove'] : null;
		$opt['onShow']		= (isset($params['onShow'])) ? $params['onShow'] : null;
		$opt['onHide']		= (isset($params['onHide'])) ? $params['onHide'] : null;

		$options = KViewHelperBehavior::_getJSObject($opt);

		// Attach modal behavior to document
		$document->addScriptDeclaration("
		window.addEvent('domready', function() {

			SqueezeBox.initialize(".$options.");

			$$('".$selector."').each(function(el) {
				el.addEvent('click', function(e) {
					new Event(e).stop();
					SqueezeBox.fromElement(el);
				});
			});
		});");

		// Set static array
		$modals[$sig] = true;
		return;
	}

	public static function uploader($id='file-upload', $params = array())
	{
		KViewHelper::script('swf.js', 'media/system/js/' );
		KViewHelper::script('uploader.js' );

		static $uploaders;

		if (!isset($uploaders)) {
			$uploaders = array();
		}

		if (isset($uploaders[$id]) && ($uploaders[$id])) {
			return;
		}

		// Setup options object
		$opt['url']					= (isset($params['targetURL'])) ? $params['targetURL'] : null ;
		$opt['swf']					= (isset($params['swf'])) ? $params['swf'] : JURI::root(true).'/media/system/swf/uploader.swf';
		$opt['multiple']			= (isset($params['multiple']) && !($params['multiple'])) ? '\\false' : '\\true';
		$opt['queued']				= (isset($params['queued']) && !($params['queued'])) ? '\\false' : '\\true';
		$opt['queueList']			= (isset($params['queueList'])) ? $params['queueList'] : 'upload-queue';
		$opt['instantStart']		= (isset($params['instantStart']) && ($params['instantStart'])) ? '\\true' : '\\false';
		$opt['allowDuplicates']		= (isset($params['allowDuplicates']) && !($params['allowDuplicates'])) ? '\\false' : '\\true';
		$opt['limitSize']			= (isset($params['limitSize']) && ($params['limitSize'])) ? (int)$params['limitSize'] : null;
		$opt['limitFiles']			= (isset($params['limitFiles']) && ($params['limitFiles'])) ? (int)$params['limitFiles'] : null;
		$opt['optionFxDuration']	= (isset($params['optionFxDuration'])) ? (int)$params['optionFxDuration'] : null;
		$opt['container']			= (isset($params['container'])) ? '\\$('.$params['container'].')' : '\\$(\''.$id.'\').getParent()';
		$opt['types']				= (isset($params['types'])) ?'\\'.$params['types'] : '\\{\'All Files (*.*)\': \'*.*\'}';


		// Optional functions
		$opt['createReplacement']	= (isset($params['createReplacement'])) ? '\\'.$params['createReplacement'] : null;
		$opt['onComplete']			= (isset($params['onComplete'])) ? '\\'.$params['onComplete'] : null;
		$opt['onAllComplete']		= (isset($params['onAllComplete'])) ? '\\'.$params['onAllComplete'] : null;

		//types: Object with (description: extension) pairs, default: Images (*.jpg; *.jpeg; *.gif; *.png)
		$options = KViewHelperBehavior::_getJSObject($opt);

		// Attach tooltips to document
		$document = KFactory::get('lib.joomla.document');
		$uploaderInit = 'sBrowseCaption=\''.JText::_('Browse Files', true).'\';
				sRemoveToolTip=\''.JText::_('Remove from queue', true).'\';
				window.addEvent(\'load\', function(){
				var Uploader = new FancyUpload($(\''.$id.'\'), '.$options.');
				$(\'upload-clear\').adopt(new Element(\'input\', { type: \'button\', events: { click: Uploader.clearList.bind(Uploader, [false])}, value: \''.JText::_('Clear Completed').'\' }));				});';
		$document->addScriptDeclaration($uploaderInit);

		// Set static array
		$uploaders[$id] = true;
		return;
	}

	public static function tree($id, $params = array(), $root = array())
	{
		static $trees;

		if (!isset($trees)) {
			$trees = array();
		}

		// Include mootools framework
		KViewHelperBehavior::mootools();
		KViewHelper::script('mootree.js', 'media/system/js/');
		KViewHelper::stylesheet('mootree.css', 'media/system/css');

		if (isset($trees[$id]) && ($trees[$id])) {
			return;
		}

		// Setup options object
		$opt['div']		= (array_key_exists('div', $params)) ? $params['div'] : $id.'_tree';
		$opt['mode']	= (array_key_exists('mode', $params)) ? $params['mode'] : 'folders';
		$opt['grid']	= (array_key_exists('grid', $params)) ? '\\'.$params['grid'] : '\\true';
		$opt['theme']	= (array_key_exists('theme', $params)) ? $params['theme'] : JURI::root(true).'/media/system/images/mootree.gif';

		// Event handlers
		$opt['onExpand']	= (array_key_exists('onExpand', $params)) ? '\\'.$params['onExpand'] : null;
		$opt['onSelect']	= (array_key_exists('onSelect', $params)) ? '\\'.$params['onSelect'] : null;
		$opt['onClick']		= (array_key_exists('onClick', $params)) ? '\\'.$params['onClick'] : '\\function(node){  window.open(node.data.url, $chk(node.data.target) ? node.data.target : \'_self\'); }';
		$options = KViewHelperBehavior::_getJSObject($opt);

		// Setup root node
		$rt['text']		= (array_key_exists('text', $root)) ? $root['text'] : 'Root';
		$rt['id']		= (array_key_exists('id', $root)) ? $root['id'] : null;
		$rt['color']	= (array_key_exists('color', $root)) ? $root['color'] : null;
		$rt['open']		= (array_key_exists('open', $root)) ? '\\'.$root['open'] : '\\true';
		$rt['icon']		= (array_key_exists('icon', $root)) ? $root['icon'] : null;
		$rt['openicon']	= (array_key_exists('openicon', $root)) ? $root['openicon'] : null;
		$rt['data']		= (array_key_exists('data', $root)) ? $root['data'] : null;
		$rootNode = KViewHelperBehavior::_getJSObject($rt);

		$treeName		= (array_key_exists('treeName', $params)) ? $params['treeName'] : '';

		$js = '		window.addEvent(\'domready\', function(){
			tree'.$treeName.' = new MooTreeControl('.$options.','.$rootNode.');
			tree'.$treeName.'.adopt(\''.$id.'\');})';

		// Attach tooltips to document
		$document = KFactory::get('lib.joomla.document');
		$document->addScriptDeclaration($js);

		// Set static array
		$trees[$id] = true;
		return;
	}

	public static function calendar()
	{
		$document = KFactory::get('lib.joomla.document');
		
		KViewHelper::stylesheet('calendar-jos.css', 'media/system/css/', array(' title' => JText::_( 'green' ) ,' media' => 'all' ));
		KViewHelper::script( 'calendar.js', 'media/system/js/' );
		KViewHelper::script( 'calendar-setup.js', 'media/system/js/' );

		$translation = KViewHelperBehavior::_calendartranslation();
		if($translation) {
			$document->addScriptDeclaration($translation);
		}
	}

	/**
	 * Keep session alive, for example, while editing or creating an article.
	 */
	public static function keepalive()
	{
		// Include mootools framework
		KViewHelperBehavior::mootools();

		$config 	 = KFactory::get('lib.joomla.config');
		$lifetime 	 = ( $config->getValue('lifetime') * 60000 );
		$refreshTime =  ( $lifetime <= 60000 ) ? 30000 : $lifetime - 60000;
		//refresh time is 1 minute less than the liftime assined in the configuration.php file

		$document = KFactory::get('lib.joomla.document');
		
		$script  = '';
		$script .= 'function keepAlive( ) {';
		$script .=  '	var myAjax = new Ajax( "index.php", { method: "get" } ).request();';
		$script .=  '}';
		$script .= 	' window.addEvent("domready", function()';
		$script .= 	'{ keepAlive.periodical('.$refreshTime.' ); }';
		$script .=  ');';

		$document->addScriptDeclaration($script);

		return;
	}

	/**
	 * Internal method to get a JavaScript object notation string from an array
	 *
	 * @param	array	$array	The array to convert to JavaScript object notation
	 * @return	string	JavaScript object notation representation of the array
	 */
	protected static function _getJSObject($array=array())
	{
		// Initialize variables
		$object = '{';

		// Iterate over array to build objects
		foreach ((array)$array as $k => $v)
		{
			if (is_null($v)) {
				continue;
			}
			if (!is_array($v) && !is_object($v)) {
				$object .= ' '.$k.': ';
				$object .= (is_numeric($v) || strpos($v, '\\') === 0) ? (is_numeric($v)) ? $v : substr($v, 1) : "'".$v."'";
				$object .= ',';
			} else {
				$object .= ' '.$k.': '.KViewHelperBehavior::_getJSObject($v).',';
			}
		}
		if (substr($object, -1) == ',') {
			$object = substr($object, 0, -1);
		}
		$object .= '}';

		return $object;
	}

	/**
	 * Internal method to translate the JavaScript Calendar
	 *
	 * @return	string	JavaScript that translates the object
	 */
	protected static function _calendartranslation()
	{
		static $jsscript = 0;

		if($jsscript == 0)
		{
			$return = 'Calendar._DN = new Array ("'.JText::_('Sunday').'", "'.JText::_('Monday').'", "'.JText::_('Tuesday').'", "'.JText::_('Wednesday').'", "'.JText::_('Thursday').'", "'.JText::_('Friday').'", "'.JText::_('Saturday').'", "'.JText::_('Sunday').'");Calendar._SDN = new Array ("'.JText::_('Sun').'", "'.JText::_('Mon').'", "'.JText::_('Tue').'", "'.JText::_('Wed').'", "'.JText::_('Thu').'", "'.JText::_('Fri').'", "'.JText::_('Sat').'", "'.JText::_('Sun').'"); Calendar._FD = 0;	Calendar._MN = new Array ("'.JText::_('January').'", "'.JText::_('February').'", "'.JText::_('March').'", "'.JText::_('April').'", "'.JText::_('May').'", "'.JText::_('June').'", "'.JText::_('July').'", "'.JText::_('August').'", "'.JText::_('September').'", "'.JText::_('October').'", "'.JText::_('November').'", "'.JText::_('December').'");	Calendar._SMN = new Array ("'.JText::_('January_short').'", "'.JText::_('February_short').'", "'.JText::_('March_short').'", "'.JText::_('April_short').'", "'.JText::_('May_short').'", "'.JText::_('June_short').'", "'.JText::_('July_short').'", "'.JText::_('August_short').'", "'.JText::_('September_short').'", "'.JText::_('October_short').'", "'.JText::_('November_short').'", "'.JText::_('December_short').'");Calendar._TT = {};Calendar._TT["INFO"] = "'.JText::_('About the calendar').'";
 		Calendar._TT["ABOUT"] =
 "DHTML Date/Time Selector\n" +
 "(c) dynarch.com 2002-2005 / Author: Mihai Bazon\n" +
"For latest version visit: http://www.dynarch.com/projects/calendar/\n" +
"Distributed under GNU LGPL.  See http://gnu.org/licenses/lgpl.html for details." +
"\n\n" +
"Date selection:\n" +
"- Use the \xab, \xbb buttons to select year\n" +
"- Use the " + String.fromCharCode(0x2039) + ", " + String.fromCharCode(0x203a) + " buttons to select month\n" +
"- Hold mouse button on any of the above buttons for faster selection.";
Calendar._TT["ABOUT_TIME"] = "\n\n" +
"Time selection:\n" +
"- Click on any of the time parts to increase it\n" +
"- or Shift-click to decrease it\n" +
"- or click and drag for faster selection.";

		Calendar._TT["PREV_YEAR"] = "'.JText::_('Prev. year (hold for menu)').'";Calendar._TT["PREV_MONTH"] = "'.JText::_('Prev. month (hold for menu)').'";	Calendar._TT["GO_TODAY"] = "'.JText::_('Go Today').'";Calendar._TT["NEXT_MONTH"] = "'.JText::_('Next month (hold for menu)').'";Calendar._TT["NEXT_YEAR"] = "'.JText::_('Next year (hold for menu)').'";Calendar._TT["SEL_DATE"] = "'.JText::_('Select date').'";Calendar._TT["DRAG_TO_MOVE"] = "'.JText::_('Drag to move').'";Calendar._TT["PART_TODAY"] = "'.JText::_('(Today)').'";Calendar._TT["DAY_FIRST"] = "'.JText::_('Display %s first').'";Calendar._TT["WEEKEND"] = "0,6";Calendar._TT["CLOSE"] = "'.JText::_('Close').'";Calendar._TT["TODAY"] = "'.JText::_('Today').'";Calendar._TT["TIME_PART"] = "'.JText::_('(Shift-)Click or drag to change value').'";Calendar._TT["DEF_DATE_FORMAT"] = "'.JText::_('%Y-%m-%d').'"; Calendar._TT["TT_DATE_FORMAT"] = "'.JText::_('%a, %b %e').'";Calendar._TT["WK"] = "wk";Calendar._TT["TIME"] = "'.JText::_('Time:').'";';
			$jsscript = 1;
			return $return;
		} else {
			return false;
		}
	}
}

