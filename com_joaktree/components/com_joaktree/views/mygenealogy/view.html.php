<?php
/**
 * Joomla! component Joaktree
 * file		view my genealogy - view.html.php
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

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Joaktree component
 */
class JoaktreeViewMygenealogy extends JViewLegacy {
	function display($tpl = null) {

//		// Load the parameters.
//		//$model			= $this->getModel();
//		
		// Initialiase variables.
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');	

		if (is_object($this->item)) {
			// existing user we know the app_id + tree_id 
			$this->params	= JoaktreeHelper::getJTParams();
		} else {
			$this->params	= JoaktreeHelper::getTheme(false, true);
			$this->title	= JFactory::getUser()->name;
		}

//
//		if ($this->params->get('siteedit', 1)) {	
//			$canDo		 	= JoaktreeHelper::getActions();	
//		} else {
//			$canDo		 	= null;
//		}
//		
//		// Find the value for tech
//		//$technology		= $this->get( 'technology' );
//			
		// set up style sheets and javascript files
		JHTML::stylesheet( JoaktreeHelper::joaktreecss() );
		JHTML::stylesheet( JoaktreeHelper::joaktreecss($this->params->get('theme')) );
		JHTML::stylesheet( 'components/com_joaktree/assets/css/uploadManager.css' );

//		//load the administrator language file
//		$lang 	= JFactory::getLanguage();
//		$lang->load('com_joaktree', JPATH_ADMINISTRATOR);		
		
//					
//		if ($technology != 'b') {
//			// javascript template - no ajax
//			// default template includes ajax
//			JHTML::stylesheet( JoaktreeHelper::briaskcss() );
//			$document->addScript( JoaktreeHelper::joaktreejs('mod_briaskISS.js'));
//			$document->addScript( JoaktreeHelper::joaktreejs('toggle.js'));
//		}
//		
//		if (($technology != 'b') and ($technology != 'j')) {
//			// default template includes ajax
//			$document->addScript( JoaktreeHelper::joaktreejs('jtajax.js'));
//		}		
//		
//		// Access
//		$lists['userAccess'] 	= $this->get( 'access' );
//		$lists['technology'] 	= $technology;
//		$edit					= $this->get( 'action' );
//		$lists['edit'] 			= ($edit == 'edit') ? true : false;
//		
//		// Person 
//		$this->person			= $this->get( 'person' );
//		$model->setCookie();

//
//		$Html[ 'lineage' ]	= $this->showLineage();			
//		$lists['showAncestors']   = (int) $this->params->get('ancestorchart', 0);
//		$lists['showDescendants'] = (int) $this->params->get('descendantchart', 0);
//		$lists['numberArticles']  = $this->person->getArticleCount();
//		
//		// Pictures
//		$Html[ 'pictures' ]		= $this->showPictures();
//		$lists[ 'nextDelay']	= round( ((int) $this->params->get('nextDelay',  0)) / 1000, 3);
//		$lists[ 'transDelay']	= round( ((int) $this->params->get('transDelay', 0)) / 1000, 3);
//		
//		// Static map
//		if ($this->person->map == 1) {
//			$Html[ 'staticmap' ] 	= $this->person->getStaticMap();
//			$lists['indStaticMap'] = ($Html[ 'staticmap' ]) ? true : false;
//		}
//		
//		// Interactive map
//		if ($this->person->map == 2) {
//			$Html[ 'interactivemap' ] 	= $this->person->getInteractiveMap();
//			$lists['indInteractiveMap'] = ($Html[ 'interactivemap' ]) ? true : false;
//			$lists[ 'pxHeightMap']	= (int) $this->params->get('pxHeight', 0);
//		}
//		
//		// last update
//		$lists[ 'showUpdate ']	= $this->params->get('show_update');
//		if ( $lists[ 'showUpdate '] != 'N' ) {
//			$lists[ 'lastUpdate' ]	= JoaktreeHelper::lastUpdateDateTimePerson($this->person->lastUpdateDate);
//			$lists[ 'showchange' ]	= (int) $this->params->get('indLogging', 0);
//		}
	
		// copyright
		$this->lists[ 'CR' ]			= JoaktreeHelper::getJoaktreeCR();
		
//		//return
//		$retObject				= new stdClass;
//		$retObject->object		= 'prsn';
//		$retObject->app_id		= $this->person->app_id;
//		$retObject->object_id	= $this->person->id;
//		$lists[ 'retId' ]		= base64_encode(json_encode($retObject));
//		
//		// tab behavior
//		if ((int) $this->params->get('indTabBehavior') == 1) {
//			$lists[ 'action' ]	= 'onClick';
//		} else {
//			$lists[ 'action' ]	= 'onMouseOver';
//		}
//	
//		$this->assignRef( 'Html', 	$Html );
//		$this->assignRef( 'canDo',	$canDo);		
//		$this->assignRef( 'lists',	$lists);
//		
//		if ($lists['userAccess']) {
//			// set title, meta title
//			$title = $this->person->firstName.' '.$this->person->familyName;
//			$document->setTitle($title);
//			$document->setMetadata('title', $title);
//			
//			// set additional meta tags
//			if ($this->params->get('menu-meta_description')) {
//				$document->setDescription($this->params->get('menu-meta_description'));
//			}
//
//			if ($this->params->get('menu-meta_keywords')) {
//				$document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
//			}
//
//			// robots
//			if ($this->person->robots > 0) {
//				$document->setMetadata('robots', JoaktreeHelper::stringRobots($this->person->robots));
//			} else if ($this->params->get('robots')) {
//				$document->setMetadata('robots', $this->params->get('robots'));
//			}
//		}

		
		parent::display($tpl);
	}	
}
?>