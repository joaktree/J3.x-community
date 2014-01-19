<?php
/**
 * Joomla! component Joaktree
 * file		jt_settings
 *
 * @version	1.5.0
 * @author	Niels van Dantzig
 * @package	Joomla
 * @subpackage	Joaktree
 * @license	GNU/GPL
 *
 * Component for genealogy in Joomla!
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class JoaktreeControllerJt_settings extends JoaktreeController {
	function __construct() {
		// first check token
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// create an input object
		$this->input = JFactory::getApplication()->input;

		//Get View
		if($this->input->get('view') == '') {
			$this->input->set('view', 'jt_settings');
		}
		
		parent::__construct();
		
		$this->registerTask( 'add_personname'    , 'edit' );		
		$this->registerTask( 'add_personevent'   , 'edit' );		
		$this->registerTask( 'add_relationevent' , 'edit' );		
		$this->registerTask( 'edit_personname'   , 'edit' );		
		$this->registerTask( 'edit_personevent'  , 'edit' );		
		$this->registerTask( 'edit_relationevent', 'edit' );		
		
		$this->registerTask( 'setting.cancel'  , 'cancel' );		
		$this->registerTask( 'setting.apply'   , 'save_item' );		
		$this->registerTask( 'setting.save'    , 'save_item' );	
			
		$this->registerTask( 'domain.add' 	   , 'edit_domain' );		
		$this->registerTask( 'domain.edit' 	   , 'edit_domain' );		
		$this->registerTask( 'domain.apply'    , 'save_domain' );		
		$this->registerTask( 'domain.save'     , 'save_domain' );		
		$this->registerTask( 'domain.delete'   , 'delete_domain' );	
		$this->registerTask( 'domain.cancel'   , 'cancel_domain' );	
		$this->registerTask( 'domain.close'    , 'close_domain' );	
		
		$this->registerTask( 'unpublish'       , 'publish' );		
	}
	
	
	// Functions for adding, editing and removing gedcom tags
	public function edit() {
		switch ($this->task) {
			case 'add_personname'	:	// continue
			case 'edit_personname'	:	$level = 'name';
										break;
			case 'add_relationevent':	// continue
			case 'edit_relationevent':	$level = 'relation';
										break;
			case 'add_personevent'	:	// continue
			case 'edit_personevent'	:	// continue
			default					:	$level = 'person';
										break;
		}
		$this->input->set( 'level', $level );

		$cids	= $this->input->get( 'cid', null, 'array' );
		if (is_array($cids)) {
			$this->input->set( 'id', (int) array_shift($cids)  );
		}
		
		$this->input->set( 'view', 'jt_setting' );
		$this->input->set( 'layout', 'form'  );
				
		parent::display();
	}
	
	public function remove() {
		$cids	= $this->input->get( 'cid', null, 'array' );
		$model = $this->getModel('jt_setting');
		
		$layout	= $model->delete($cids);
		
		$link = 'index.php?option=com_joaktree&view=jt_settings&layout='.$layout;
		$this->setRedirect($link, $msg);
	}
	
	public function cancel() {
		$form   = $this->input->get( 'jform', null, 'array' );
		
		switch ($form['level']) {
			case 'name'		: $layout = 'personname';
						 	  break;
			case 'person'	: $layout = 'personevent';
						 	  break;
			case 'relation'	: $layout = 'relationevent';
						 	  break;
			default			: $layout = 'personevent';
						 	  break;
		}
		
		JFactory::getApplication()->setUserState('com_joaktree.edit.setting.data', '');		
		$link = 'index.php?option=com_joaktree&view=jt_settings&layout='.$layout;
		$this->setRedirect($link);
	}
	
	public function setDefault() {
		$layout = $this->input->get('layout');
		$model	= $this->getModel('jt_settings');
		
		$msg	= $model->setDefault();
		
		$link	= 'index.php?option=com_joaktree&view=jt_settings&layout='.$layout;
		$this->setRedirect($link);
	}
	
	public function publish() {
		$layout = $this->input->get('layout');
		$model = $this->getModel('jt_settings');
		
		$msg = $model->publish();
		
		$link = 'index.php?option=com_joaktree&view=jt_settings&layout='.$layout;
		$this->setRedirect($link);
	}
	
	public function save_item() {
		$form   = $this->input->get( 'jform', null, 'array' );
		
		$model 	= $this->getModel('jt_setting');
		$layout	= $model->save($form);
		
		if ($this->task == 'setting.apply') {
			JFactory::getApplication()->setUserState('com_joaktree.edit.setting.data', $form);
		} else {
			JFactory::getApplication()->setUserState('com_joaktree.edit.setting.data', '');
		}
		
		
		if ($layout && ($this->task == 'setting.save')) {
			$link 	= 'index.php?option=com_joaktree&view=jt_settings&layout='.$layout;
			$msg	= '';
		} else if ($layout && ($this->task == 'setting.apply')) {			
			$link 	= 'index.php?option=com_joaktree&view=jt_setting&layout=form';
			$msg	= '';
		} else { 
			$link 	= 'index.php?option=com_joaktree&view=jt_settings&layout=personevent';
			$msg	= JText::_('JTAPPS_MESSAGE_NOSAVE'); 
		}
			
		$this->setRedirect($link, $msg);
	}
	
	
	// Functions for viewing, adding, editing and removing domain values
	public function view_domains() {
		$cids	= JFactory::getApplication()->input->get('cid', array(), 'array');
		$link 	= 'index.php?option=com_joaktree&view=jt_domainvalues&display='.(int) array_shift($cids);
		$this->setRedirect($link);
	}
	
	public function edit_domain() {	
		$cids	= $this->input->get( 'cid', null, 'array' );
		$display= $this->input->get( 'display_id', null, 'int' );
		$link 	= 'index.php?option=com_joaktree&view=jt_domainvalue&layout=form&id='.(int) array_shift($cids).'&display='.(int)$display;
		$this->setRedirect($link);
	}
	
	public function save_domain() {
		$form   = $this->input->get( 'jform', null, 'array' );
		$id		= $form['display_id'];
		
		$model 	= $this->getModel('jt_domainvalue');
		$msg	= ($model->save($form)) ? '' : JText::_('JTAPPS_MESSAGE_NOSAVE');

		$link 	= 'index.php?option=com_joaktree&view=jt_domainvalues&display='.(int)$id;
		$this->setRedirect($link, $msg);
	}
	
	public function delete_domain() {
		$cids	= $this->input->get( 'cid', null, 'array' );
		$id		= $this->input->get( 'display_id', null, 'int' );
		
		$model	= $this->getModel('jt_domainvalue');
		$msg	= ($model->delete($cids)) ? '' : JText::_('JTAPPS_MESSAGE_NOSAVE'); 
		
		$link 	= 'index.php?option=com_joaktree&view=jt_domainvalues&display='.(int)$id;
		$this->setRedirect($link, $msg);
	}
	
	public function cancel_domain() {
		$form   = $this->input->get( 'jform', null, 'array' );
		$id		= $form['display_id'];
		$link 	= 'index.php?option=com_joaktree&view=jt_domainvalues&display='.(int)$id;
		$this->setRedirect($link);
	}
	
	public function close_domain() {
		$level   = $this->input->get( 'level', null, 'string' );
		
		switch ($level) {
			case 'name'		: $layout = 'personname';
						 	  break;
			case 'person'	: $layout = 'personevent';
						 	  break;
			case 'relation'	: $layout = 'relationevent';
						 	  break;
			default			: $layout = 'personevent';
						 	  break;
		}
		
		$link = 'index.php?option=com_joaktree&view=jt_settings&layout='.$layout;
		$this->setRedirect($link);
	}
	
	public function save() {
		$layout = $this->input->get('layout');
		$model 	= $this->getModel('jt_settings');
		
		$msg 	= $model->save($layout);
		
		$link 	= 'index.php?option=com_joaktree&view=jt_settings&layout='.$layout;
		$this->setRedirect($link);
		
		
//		//$post	= $this->input->get('post', null, 'array' );
//		$model	= $this->getModel('jt_settings');
//		$retmsg = '';
//				
//		// retrieve id
//		$cids	= $this->input->get( 'cid', null, 'array' );
//		
//		// We are only doing it when we have items to be updated
//		if ((count( $cids ) > 0) and ($cids[0] > 0)) {			
//			for ($i=0, $n=count( $cids ); $i < $n; $i++) {
//				$post['id'] 	        = intval( $cids[$i] );
//				
//				$tmp = $this->input->get( 'access'.$post['id'], null, 'string' );
//				$tmp = (int) substr($tmp, 0, 3);
//				$post['access']	= $tmp;
//				
//				$tmp = $this->input->get( 'accessLiving'.$post['id'], null, 'string' );
//				$tmp = (int) substr($tmp, 0, 3);
//				$post['accessLiving']	= $tmp;
//				
//				$tmp = $this->input->get( 'altLiving'.$post['id'], null, 'string' );
//				$tmp = (int) substr($tmp, 0, 3);
//				$post['altLiving']	= $tmp;
//							
//				$code = $this->input->get( 'code'.$post['id'], null, 'string' );		
//							
//				$retmsg .= $model->store($post, $code).';&nbsp;';
//			}
//		}
//		
//		if (strlen($retmsg) > 0) {
//			$msg = JText::_( 'JTSETTINGS_SAVE_ACCESSLEVEL' ).':&nbsp;'.$retmsg;
//		} else {
//			$msg = JText::_( 'JTSETTINGS_NO_SAVE_ACCESSLEVEL' );
//		}
//				
//		$link = 'index.php?option=com_joaktree&view=jt_settings&layout='.$layout;
//		$this->setRedirect($link, $msg);
		
	}
	
//	public function setOrder() {	
//		$layout = $this->input->get('layout');
//		$model = $this->getModel('jt_settings');
//		
//		$msg = $model->setOrder($layout);
//		
//		$link = 'index.php?option=com_joaktree&view=jt_settings&layout='.$layout;
//		$this->setRedirect($link);
//	}

	
	
}
?>