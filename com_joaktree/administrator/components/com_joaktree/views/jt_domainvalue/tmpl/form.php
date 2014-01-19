<?php
// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'domain.cancel' || document.formvalidator.isValid(document.id('domain-form'))) {
			Joomla.submitform(task, document.getElementById('domain-form'));
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form 
	action="<?php echo JRoute::_('index.php?option=com_joaktree'); ?>" 
	method="post" 
	name="adminForm" 
	id="domain-form" 
	class="form-validate form-horizontal"
>

<div class="span10 form-horizontal">
<?php // print_r($this->item); stop(); ?>
	<fieldset>
		<ul class="nav nav-tabs">
			<li class="active">			
				<?php echo (empty($this->item->id)) 
							? JText::sprintf('JTSETTINGS_DOMAIN_CREATE', JTEXT::_($this->item->code))
							: JText::sprintf('JTSETTINGS_DOMAIN_EDIT'  , JTEXT::_($this->item->code))
							; 
				?>
			</li>
		</ul>
		
		<!-- content starts here -->
		<div class="tab-content">
			<div class="tab-pane active">
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
						<?php echo $this->form->getInput('display_id'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('value'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('value'); ?>
					</div>
				</div>
			</div>
		</div>
	</fieldset>
</div>

<input type="hidden" name="task" value="" />
<input type="hidden" name="cid[]" value="<?php echo (!empty($this->item->id) ? $this->item->id : null); ?>" />
<input type="hidden" name="controller" value="jt_settings" />
<?php echo JHtml::_('form.token'); ?>

</form>
