<?php 
// no direct access
defined('_JEXEC') or die('Restricted access'); 

// Set up modal behavior
//JHtml::_('behavior.modal', 'a.modal');
JHtml::_('behavior.formvalidation');

$linkbase = 'index.php?option=com_joaktree';

// Load mooTools
//JHtml::_('behavior.framework', true);

// import component libraries
//JLoader::import('helper.formhelper', JPATH_COMPONENT);
?>

<script type="text/javascript">
	function jtsubmitbutton(task)
	{
		if (task == 'cancel' || document.formvalidator.isValid(document.id('wizardForm'))) {
			Joomla.submitform(task, document.getElementById('wizardForm'));
		} else {
			alert('<?php echo $this->escape(JText::_('JT_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<div id="jt-content">

	<table>
		<thead>
			<tr>
				<th colspan="3" class="jt-content-th">
					<?php echo (is_object($this->item)) ? $this->item->title : $this->title; ?>
				</th>
			</tr>
		</thead>
		
		<tfoot>
			<tr>
				<th colspan="3" class="jt-content-th">&nbsp;</th>
			</tr>
		</tfoot>
			
		<tbody>
		<?php if (is_object($this->item)) { ?>
			<tr class="jt-index-entry1">
				<td class="jt-small">
					<?php $link = '&view=joaktreestart&treeId='.(int)$this->item->tree_id; ?>
					<a href="<?php echo JRoute::_($linkbase.$link); ?>"><?php echo JText::_('JT_INDEX'); ?></a>
				</td>
				<td class="jt-small">
					<?php $link = '&view=joaktreelist&treeId='.(int)$this->item->tree_id; ?>
					<a href="<?php echo JRoute::_($linkbase.$link); ?>"><?php echo JText::_('JT_SEARCHLIST'); ?></a>
				</td>
				<td class="jt-small">
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
				</td>
			</tr>
		<?php } else { ?>
			<tr class="jt-index-entry1">
				<td class="jt-small">
					<?php $link = '&view=mygenealogy&layout=wizard01'; ?>
					<a href="<?php echo JRoute::_($linkbase.$link); ?>"><?php echo JText::_('JT_SETUP'); ?></a>
				</td>
				<td class="jt-small">&nbsp;</td>
				<td class="jt-small">&nbsp;</td>
			</tr>
		<?php } ?>	
		</tbody>
	
	</table>
	

	<div class="jt-clearfix jt-update">
	</div>
	<div class="jt-stamp">
		<?php echo $this->lists[ 'CR' ]; ?>
	</div>


</div><!-- jt-content -->

