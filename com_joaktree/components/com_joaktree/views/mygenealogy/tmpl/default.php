<?php 
// no direct access
defined('_JEXEC') or die('Restricted access'); 

// Set up modal behavior
JHtml::_('behavior.formvalidation');
?>

<script type="text/javascript">
	function jtsubmitbutton(task, formid)
	{
		if (task == 'cancel' || document.formvalidator.isValid(document.id(formid))) {
			Joomla.submitform(task, $(formid));
		} else {
			alert('<?php echo $this->escape(JText::_('JT_VALIDATION_FORM_FAILED'));?>');
		}
	}

	function jtshowinfo(val) {
		if(val==1) {
			$('personinfo').setStyle('display', 'block');
			$('treeinfo').setStyle('display', 'none');
		} else if(val==2) {
			$('personinfo').setStyle('display', 'none');
			$('treeinfo').setStyle('display', 'block');
		} else {
			$('personinfo').setStyle('display', 'none');
			$('treeinfo').setStyle('display', 'none');
		}
	}
</script>

<div id="jt-content">
	<?php if ($this->lists['access']) { ?>
		<div class="jt-h1">
			<a onclick="jtshowinfo(1);" href="#"><?php echo ucfirst(JText::_('JT_ME')); ?></a>
			&amp;
			<?php if ($this->lists['community'] > 0) { ?><a onclick="jtshowinfo(2);" href="#"><?php } ?>
				<?php echo strtolower(JText::_('JT_MYFAMILYTREE')); ?>
			<?php if ($this->lists['community'] > 0) { ?></a><?php } ?>
		</div>
		
		<?php if (!is_object($this->item)) { ?>
			<!-- No information about user -->
			<form action="<?php echo JRoute::_('index.php?option=com_joaktree'); ?>" method="post" name="wizardForm11" id="wizardForm11" class="form-validate">
				<div class="jt-buttonbar">
					<?php if ($this->lists['community'] == 0) { ?>
						<a	onclick="jtsubmitbutton('edit_tree', 'wizardForm11');" 
							title="<?php echo JText::_('JT_SETUP_DESC_FAMILYTREE'); ?>" 
							class="jt-button-closed jt-buttonlabel"  
							href="#"
						>
							<?php echo JText::_('JT_SETUP_FAMILYTREE'); ?>
						</a>
						&nbsp;
					<?php } else { ?>
						<a 	onclick="jtsubmitbutton('edit_me', 'wizardForm11');" 
							title="<?php echo JText::_('JT_SETUP_DESC_ME'); ?>" 
							class="jt-button-closed jt-buttonlabel" 
							href="#"
						>
							<?php echo JText::_('JT_SETUP_ME'); ?>
						</a>
						&nbsp;
					<?php } ?>
				</div>
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="controller" value="mygenealogy" />
				<input type="hidden" id="jform_wizard" name="jform[wizard]" value="11" />
				<input type="hidden" id="jform_tech" name="jform[tech]" value="<?php echo $this->lists['technology']; ?>" />
				<?php echo JHtml::_('form.token'); ?>
			</form>
			
		<?php } else { ?>
			<!-- Person info -->
			<?php if (isset($this->person) && is_object($this->person)) { ?>
				<div id="personinfo" style="display: block;">
					<hr />
					<div class="jt-h3"><?php echo JText::sprintf('JT_YOUARE', $this->user->name); ?></div>					
					<div class="jt-h2"><?php echo $this->person->fullName; ?></div>
					
					<div class="jt-clearfix"></div>
					<?php 	$names 	= $this->person->getTreeAndAppNames();
							$name	= (strcasecmp(trim($names['tree_name']), trim($names['app_name']))== 0) 
										? $names['tree_name']
										: $names['tree_name'].' ['.$names['app_name'].']';  
					?>					
					<span class="jt-high-row jt-label">
						<?php echo JText::_('JTFAMTREE_LABEL'); ?>
					</span>
					<span class="jt-high-row jt-iconlabel">
						<span class="jt-empty-icon">&nbsp;</span>
					</span>
					<span class="jt-high-row jt-valuelabel">
						<?php echo $name; ?>							
					</span>
					<div class="jt-clearfix"></div>
					
					<?php $events = $this->person->getPersonEvents(); ?>
					<?php foreach ($events as $event) { ?>
						<div class="jt-clearfix"></div>
						<?php if (!empty($event->eventDate) || !empty($event->location) || !empty($event->value)) { ?>
							<span class="jt-high-row jt-label">
								<?php echo JText::_($event->code).(($event->type) ? ' - '.JText::_(str_replace(' ', '+', $event->type)) : ''); ?>
							</span>
							<span class="jt-high-row jt-iconlabel">
								<span class="jt-empty-icon">&nbsp;</span>
							</span>
							<span class="jt-high-row jt-valuelabel">
								<?php echo (!empty($event->value)) ? $event->value.'&nbsp;' : ''; ?>
								
								<?php echo (!empty($event->value) && (!empty($event->eventDate) || !empty($event->location))) ? '(&nbsp;' : ''; ?>
								<?php echo JoaktreeHelper::displayDate( $event->eventDate ).'&nbsp;'; ?>
								<?php echo (!empty($event->location)) ? JText::_('JT_IN') . '&nbsp;' . $event->location . '&nbsp;' : ''; ?>
								<?php echo (!empty($event->value) && (!empty($event->eventDate) || !empty($event->location))) ? ')&nbsp;' : ''; ?>
							
							</span>
						<?php } ?>
					
					<?php } ?>
					<div class="jt-clearfix"></div>
					<hr />
					
					<form action="<?php echo JRoute::_('index.php?option=com_joaktree'); ?>" method="post" name="wizardForm12" id="wizardForm12" class="form-validate">
						<div class="jt-buttonbar">
							<a	onclick="jtsubmitbutton('edit_me', 'wizardForm12');" 
								title="<?php echo JText::_('JT_EDIT'); ?>" 
								class="jt-button-closed jt-buttonlabel"  
								href="#"
							>
								<?php echo JText::_('JT_EDIT'); ?>
							</a>
							&nbsp;
							<a 	onclick="jtsubmitbutton('navigate', 'wizardForm12');" 
								title="<?php echo $this->person->fullName; ?>" 
								class="jt-button-closed jt-buttonlabel" 
								href="#"
							>
								<?php echo JText::_('JT_GOTO_MYPAGE'); ?>
							</a>
							&nbsp;
						</div>
						<input type="hidden" name="task" value="" />
						<input type="hidden" name="controller" value="mygenealogy" />
						<input type="hidden" id="jform_wizard" name="jform[wizard]" value="12" />
						<input type="hidden" id="jform_person_id" name="jform[person_id]" value="<?php echo $this->person->app_id.'!'.$this->person->id; ?>" />
						<input type="hidden" id="jform_tree_id" name="jform[tree_id]" value="<?php echo $this->person->tree_id; ?>" />
						<input type="hidden" id="jform_tech" name="jform[tech]" value="<?php echo $this->lists['technology']; ?>" />
						<?php echo JHtml::_('form.token'); ?>
					</form>
				</div>
			<?php } ?>

			<!-- Tree info -->
			<?php if ($this->lists['community'] > 0) { ?>
				<div id="treeinfo" style="display: none;">
					<?php if (is_object($this->item) && !empty($this->item->tree_id)) { ?>
						<div>
							<?php print_r($this->item); ?>

						<!-- TIJDELIJK -->
						<?php $link = '&view=joaktreestart&treeId='.(int)$this->item->tree_id; ?>
						<a href="<?php echo JRoute::_($link); ?>"><?php echo JText::_('JT_INDEX'); ?></a>

						<?php $link = '&view=joaktreelist&treeId='.(int)$this->item->tree_id; ?>
						<a href="<?php echo JRoute::_($link); ?>"><?php echo JText::_('JT_SEARCHLIST'); ?></a>

						<?php if ($this->lists['community'] == 2) { ?>
							<?php $link = '&view=linkedpersons&treeId='.(int)$this->item->tree_id.'&appId='.(int)$this->item->app_id; ?>
							<a href="<?php echo JRoute::_($link); ?>"><?php echo JText::_('JT_LINKEDPERSONS'); ?></a>
						<?php } ?>

						<form 	action="<?php echo JRoute::_('index.php?option=com_joaktree'); ?>" 
								method="post" 
								name="wizardForm" 
								id="wizardForm"
								style="margin: 0;" 
						>
							<a 	href="#" 
								title="<?php echo JText::_('JACTION_DELETE'); ?>"
								onclick="if (confirm('<?php echo JText::_('JT_CONFIRMDELETE'); ?>')){jtsubmitbutton('delete');}"
							>
								<?php echo JText::_('JACTION_DELETE'); ?>
							</a>
	
							<input type="hidden" name="task" value="" />
							<input type="hidden" name="controller" value="mygenealogy" />
							<input type="hidden" id="jform_wizard" name="jform[wizard]" value="D" />
							<?php echo JHtml::_('form.token'); ?>
						
						</form>
						<!-- EIND -->
						</div>
					<?php } else if ($this->canDo->get('core.create')) { ?>					
						<form action="<?php echo JRoute::_('index.php?option=com_joaktree'); ?>" method="post" name="wizardForm15" id="wizardForm15" class="form-validate">
							<div class="jt-buttonbar">
								<a	onclick="jtsubmitbutton('edit_tree', 'wizardForm15');" 
									title="<?php echo JText::_('JT_SETUP_DESC_FAMILYTREE'); ?>" 
									class="jt-button-closed jt-buttonlabel"  
									href="#"
								>
									<?php echo JText::_('JT_SETUP_FAMILYTREE'); ?>
								</a>
							</div>
							<input type="hidden" name="task" value="" />
							<input type="hidden" name="controller" value="mygenealogy" />
							<input type="hidden" id="jform_wizard" name="jform[wizard]" value="15" />
							<input type="hidden" id="jform_tech" name="jform[tech]" value="<?php echo $this->lists['technology']; ?>" />
							<?php echo JHtml::_('form.token'); ?>
						</form>
					<?php } else { ?>
						<div class="jt-content-th" >
							<div class="jt-noaccess"><?php echo JText::_( 'JT_NOACCESS' ); ?></div>
						</div>
					<?php } ?>
				</div>
			<?php } ?>
		<?php } ?>
	
	
	<?php } else { ?>	
		<!-- user has NO access to information -->
		<div class="jt-content-th" >
			<div class="jt-noaccess"><?php echo JText::_( 'JT_NOACCESS' ); ?></div>
		</div>
	<?php } ?>

	<div class="jt-clearfix jt-update">
	</div>
	<div class="jt-stamp">
		<?php echo $this->lists[ 'CR' ]; ?>
	</div>

</div><!-- jt-content -->

