<?php
// no direct access
defined('_JEXEC') or die;

// are these needed
//JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers'.DS.'html');
JHTML::_('behavior.modal', 'a.jtmodal'); 
//JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'setting.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
			Joomla.submitform(task, document.getElementById('adminForm'));
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form 
	action="<?php echo JRoute::_('index.php?option=com_joaktree'); ?>" 
	method="post" 
	name="adminForm" 
	id="adminForm" 
	class="form-validate form-horizontal"
>

<div class="span10 form-horizontal">
<?php // print_r($this->item); stop(); ?>
	<fieldset>
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#details" data-toggle="tab">
			
				<?php echo ((!is_object($this->item)) || ((is_object($this->item)) && (!$this->item->id)))
						? JText::_('JTSETTING_TITLE_NEWNAME')
						: JText::sprintf('JTSETTING_TITLE_EDITNAME', JTEXT::_($this->item->code));
				?>
				</a>
			</li>
		</ul>

		<!-- content starts here -->
		<div class="tab-content">
			<div class="tab-pane active" id="details">
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('code'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('code'); ?>
						
						<?php if ((!is_object($this->item)) || ((is_object($this->item)) && (!$this->item->id))) { ?>
							<?php echo $this->form->getInput('level', null, $this->level); ?>
						<?php } else { ?>
							<?php echo $this->form->getInput('level'); ?>
						<?php } ?>
						
						<?php echo $this->form->getInput('id'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('explanation'); ?>
					</div>
					<div class="controls">
						<?php if (is_object($this->item) && isset($this->item->explanation)) { ?>
							<?php echo $this->item->explanation; ?>
						<?php } ?>
					</div>
				</div>				
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('published'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('published'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('access'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('access'); ?>
						<?php echo $this->form->getInput('access_level'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('accessLiving'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('accessLiving'); ?>
						<?php echo $this->form->getInput('access_level_living'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('altLiving'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('altLiving'); ?>
						<?php echo $this->form->getInput('access_level_alttext'); ?>
					</div>
				</div>
				<?php if ($this->level == 'person') { ?>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('domain'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('domain'); ?>
						</div>
					</div>
				<?php } ?>
			</div>
			
		</div>
	</fieldset>
</div>

<input type="hidden" name="task" value="" />
<input type="hidden" name="cid_" value="<?php echo (!empty($this->item->id) ? $this->item->id : null); ?>" />
<input type="hidden" name="controller" value="jt_settings" />
<input type="hidden" name="boxchecked" value="0" />
<?php echo JHtml::_('form.token'); ?>

</form>
