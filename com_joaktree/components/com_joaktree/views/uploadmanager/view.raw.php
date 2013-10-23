<?php
/**
 * Joomla! component Joaktree
 * file		view uploadmanager - view.html.php
 *
 * @version	1.5.0
 * @author	Niels van Dantzig
 * @package	Joomla
 * @subpackage	Joaktree
 * @license	GNU/GPL
 *
 * Component for genealogy in Joomla!
 *
 * This component file was created using the Joomla Component Creator by Not Web Design
 * http://www.notwebdesign.com/joomla_component_creator/
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// import component libraries
jimport( 'joomla.application.component.view');
JLoader::import('helper.uploadmanager', JPATH_COMPONENT);

if(!defined('JT_TMP_PATH'))	define('JT_TMP_PATH', JPATH_SITE.'/tmp/joaktree');


/**
 * HTML View class for the Joaktree component
 */
class JoaktreeViewUploadmanager extends JViewLegacy {
	function display($tpl = null) {

		//upload max size in bytes
		if(!defined('UPLOAD_MAX_SIZE'))
//			define('UPLOAD_MAX_SIZE', 0); //2621440 2.5M
			define('UPLOAD_MAX_SIZE', 100); //2621440 2.5M 
		
		if (!function_exists('apache_request_headers'))  {
			function apache_request_headers() {
			   foreach ($_SERVER as $name => $value) 
				   if (substr($name, 0, 5) == 'HTTP_') 
					   $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
			   return $headers;
			}
		}
		
		$this->headers = apache_request_headers();

		//file are not supposed to stay here for a long period, they should be moved after being uploaded
		$t = time();
		$max_age = 3600 * 24;
	   
		if($handle = opendir(JT_TMP_PATH)) {
			while (false !== ($file = readdir($handle))) {
				if($file == '.' || $file == '..'|| $file == 'index.html')
					continue;
	
				if($t - filemtime(JT_TMP_PATH.DS.$file) > $max_age)
					unlink(JT_TMP_PATH.DS.$file);
			}
			closedir($handle);
		}
   
		parent::display($tpl);
	}	
}
?>