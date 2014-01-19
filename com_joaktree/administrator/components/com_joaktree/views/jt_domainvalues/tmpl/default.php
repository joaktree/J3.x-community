<?php
defined('_JEXEC') or die('Restricted access');

//JHTML::_('behavior.tooltip');
//JHtml::_('formbehavior.chosen', 'select');
?>

<form 
	action="<?php echo JRoute::_('index.php?option=com_joaktree'); ?>" 
	method="post" 
	id="adminForm" 
	name="adminForm"
>

	<div id="j-main-container" >
		<?php if ($this->indDomain) { ?>
	
			<!--  table -->
			<table class="table table-striped" id="articleList">
				<thead>
					<tr>
						<th width="1%" class="nowrap center"><?php echo JText::_('JT_HEADING_NUMBER'); ?></th>
						<th width="1%" class="nowrap center">&nbsp;</th>
						<th class="nowrap"><?php  echo JText::_('JTSETTINGS_DOMAINVALUE'); ?></th>
					</tr>
				</thead>
				<tbody>			
					<?php foreach ($this->items as $i => $row) {
							if (is_object($row)) {
					?>
								<tr class="row<?php echo ($i % 2); ?>">
									
									<td class="center">
										<?php echo $row->id; ?>
									</td>
									<td class="center"><?php echo JHTML::_('grid.id',   $i, $row->id ); ?></td>
	
									<td>
										<?php echo $row->value; ?>
									</td>
								</tr>							
					<?php 
							} 
						  } 
					?>
				</tbody>
			</table>
		<?php } else { ?>
			<?php echo JText::sprintf('JTSETTINGS_DOMAIN_ACTIVATED', JText::_($this->code)); ?>
		<?php } ?>
		
		<input type="hidden" name="option" value="com_joaktree" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="controller" value="jt_settings" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="display_id" value="<?php echo $this->display_id; ?>" />
		<input type="hidden" name="level" value="<?php echo $this->level; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>


