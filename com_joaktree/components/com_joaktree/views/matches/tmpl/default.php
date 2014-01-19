<?php 
// no direct access
defined('_JEXEC') or die('Restricted access'); 
?>

<?php if (count($this->personlist) > 0) { ?>
	<table>
		<tbody>
		<?php foreach ($this->personlist as $row) { ?>
			<tr>
				<td>
					<?php $link = JRoute::_('index.php?option=com_joaktree&view=joaktree'
										   .'&tech='.$this->lists['technology']
										   .'&treeId='.$this->lists['tree_id']
										   .'&personId='.$row->app_id.'!'.$row->id
										   );
					?>
					<a 	href="<?php echo $link; ?>" 
						target="_community_"
						rel="noindex, nofollow"
					><?php echo $row->name; ?></a>
					<br />
					<?php if (!empty($row->birthDate) || !empty($row->deathDate)) { ?>
						(
						<?php echo (empty($row->deathDate)) ? JText::_('BIRT').':&nbsp;' : ''; ?>
						<?php echo (empty($row->birthDate)) ? JText::_('DEAT').':&nbsp;' : $row->birthDate; ?> 
						<?php echo (!empty($row->birthDate) && !empty($row->deathDate)) ? '&nbsp;-&nbsp;' : ''; ?> 
						<?php echo $row->deathDate; ?>
						)
					<?php } ?>
				</td>
				<td>
					<?php if ((!$row->indLinked) && (!$row->indUsed)) { ?>
						<a 	href="#"
							title="<?php echo JText::sprintf('JT_DESC_LINK', $row->name); ?>"
							onclick="if (confirm('<?php echo JText::sprintf('JT_CONFIRM_LINK', $row->name); ?>')){
										$('cid').value='<?php echo $row->app_id.'!'.$row->id; ?>';
										$('mygencid').value='<?php echo $row->MyGenPersonId; ?>'
										jtsubmitbutton('save');}"
							class="jt-btn-plus"
							style="text-decoration: none;"
							rel="noindex, nofollow"
						>&nbsp;</a>
						
					<?php } else { ?>
						&nbsp;
					<?php } ?>
				</td>
			</tr>
		
		<?php } ?>
		</tbody>
	</table>
<?php } else { ?>
	<?php echo JText::_('JT_NOMATCHES'); ?>
<?php } ?>


