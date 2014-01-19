<?php
/**
 * Joomla! component Joaktree
 * file		view joaktree linked persons - view.html.php
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
class JoaktreeViewLinkedpersons extends JViewLegacy {
	function display($tpl = null) {
		
		// Load the parameters.
		$app 			= JFactory::getApplication('site');
		$params			= JoaktreeHelper::getJTParams();
		$document = &JFactory::getDocument();
		
		// set up style sheets and javascript files
		JHTML::stylesheet( JoaktreeHelper::joaktreecss() );
		JHTML::stylesheet( JoaktreeHelper::joaktreecss($params->get('theme')) );
				
		// get user info
		$userId			= $this->get( 'userId' );
		if(!$userId || $userId == 0) {
			$document->addScript( JoaktreeHelper::joaktreejs('jtform.js'));
		}
		
		// Get data from the model
		$this->personlist	= $this->get( 'personlist' );  
		$this->pagination	= $this->get( 'Pagination' );
		
		// Id's and settings
		$this->lists['app_id']	= $this->get( 'applicationId' );;
		$this->lists['tree_id']	= $this->get( 'treeId' );;
		$this->lists['userAccess']	= $this->get( 'userAccess' );
		$this->lists['technology'] 	= $this->get( 'technology' );

		//Filter
		$context			= 'com_joaktree.linkedpersons.list.';		
		$this->lists['search1']	= JString::strtolower($app->getUserStateFromRequest( $context.'search1',	'search1',	'',	'string' ));
		
		// copyright
		$this->lists[ 'CR' ]	= JoaktreeHelper::getJoaktreeCR();
		
		if ($this->lists['userAccess']) {
			// set title, meta title
			if ($params->get('treeName')) {
				$title = $params->get('treeName');
				$document->setTitle($title);
				$document->setMetadata('title', $title);
			}
			
			// set additional meta tags
			if ($params->get('menu-meta_description')) {
				$document->setDescription($params->get('menu-meta_description'));
			}

			if ($params->get('menu-meta_keywords')) {
				$document->setMetadata('keywords', $params->get('menu-meta_keywords'));
			}

			// robots
			if ($params->get('treeRobots') > 0) {
				$document->setMetadata('robots', JoaktreeHelper::stringRobots($params->get('treeRobots')));
			} else if ($params->get('robots')) {
				$document->setMetadata('robots', $params->get('robots'));
			}				
		}
					
	
		parent::display($tpl);
	}
}
?>