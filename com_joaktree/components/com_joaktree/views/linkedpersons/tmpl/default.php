<?php defined('_JEXEC') or die('Restricted access'); 
JHtml::_('behavior.formvalidation');

?>

<script type="text/javascript">
	function findMatches() {
		var jtEl = $('jt-content').getElement('div.jt-ajax');
		if ((jtEl) && (jtEl.hasClass('jt-ajax'))) {
			jtEl.removeClass('jt-ajax');
			
			var myRequest = new Request({
			    url: 'index.php?option=com_joaktree&format=raw&tmpl=component&view=matches&personId='+jtEl.id,
			    method: 'get',
				onFailure: function(xhr) {
					alert('Error occured for url: index.php?option=com_joaktree&format=raw&tmpl=component&view=matches&personId='+jtEl.id);
				},
				onComplete: function(response) {
			    		HandleResponse(jtEl.id, response);	    		
				}
			}).send();
		}
		
	}

	function HandleResponse(id, response) {
		var jtEl = $(id);
		jtEl.set('html', response);
		findMatches()
	}
	
	window.addEvent('domready', function() {
		findMatches();
	});

	function jtsubmitbutton(task)
	{
		if (task == 'cancel' || document.formvalidator.isValid(document.id('wizardForm'))) {
			Joomla.submitform(task, $('wizardForm'));
		} else {
			alert('<?php echo $this->escape(JText::_('JT_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>


<form action="<?php echo JRoute::_( 'index.php?option=com_joaktree&view=linkedpersons&treeId='.$this->lists['tree_id'].'&appId='.$this->lists['app_id'] ); ?>" method="post" id="wizardForm" name="wizardForm">
<?php echo JHTML::_( 'form.token' ); ?>

<?php if ($this->lists['userAccess']) { ?> 
<!-- user has access to information -->

	<div id="jt-content">
		<table>
			<!-- header -->
			<thead>
				<tr>
					<th class="jt-content-th" colspan="5" align="left">
						<input 
							type="text" 
							name="search1" 
							id="search1" 
							value="<?php echo $this->lists['search1'];?>" 
							style="width: 300px;" 
							onchange="document.adminForm.submit();" 
						/>
						<input type="submit" onclick="this.form.submit();" name="Go" class="button" value="<?php echo JText::_( 'JT_SEARCH' ); ?>" title="<?php echo JText::_( 'JT_TO_SEARCH' ); ?>" />
						<input type="submit" onclick="document.getElementById('search1').value='';this.form.submit();" name="Reset" class="button" value="<?php echo JText::_( 'JT_RESET' ); ?>" title="<?php echo JText::_( 'JT_TO_RESET' ); ?>" />                               
					
					</th>
				</tr>
				
				<tr>
					<th class="jt-content-th" width="5" rowspan="2" align="center">
						<?php echo JText::_( 'JT_NUM' ); ?>
					</th>
					<th class="jt-content-th">
						<div class="jt-h3-list jt-content-tha">
							<?php echo JText::_('JT_MYGEN_TREE'); ?>
						</div>
					</th>
					<th class="jt-content-th" colspan="2">
						<div class="jt-h3-list jt-content-tha">
							<?php echo JText::_('JT_LINKED_TREE'); ?>
						</div>
					</th>
					<th class="jt-content-th">
						<div class="jt-h3-list jt-content-tha">
							<?php echo JText::_('JT_POSSIBLE_MATCHES'); ?>
						</div>
					</th>
					
				</tr>
			</thead>
			
			<!-- footer -->
			<?php if ($this->pagination->pagesTotal > 1) { ?>
				<tfoot>
					<tr align="center">
						<th colspan="5" >
							<?php echo $this->pagination->getListFooter(); ?>
						</th>
					</tr>
				</tfoot>
			<?php } ?>
			
			<!-- table body -->
			<tbody>
			<?php
			$k = 2;
			for ($i=0, $n=count( $this->personlist ); $i < $n; $i++)	{
				$row = &$this->personlist[$i];
				$link = JRoute::_('index.php?option=com_joaktree&view=joaktree'
						 .'&tech='.$this->lists['technology']
						 .'&treeId='.$this->lists['tree_id']
						 .'&personId='.$row->app_id.'!'.$row->id
						 );
				
				$rowclass = 'jt-table-entry' . $k;
			?>
				<tr class="<?php echo $rowclass; ?>" >
					<td align="center"><?php echo $this->pagination->getRowOffset( $i ); ?></td>
					<td>
						<a 	href="<?php echo $link; ?>"
							target="_mygenealogy_" 
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
					<td><!-- Delete the link -->
						<?php if (!empty($row->name_c)) { ?>
							<a 	href="#"
								title="<?php echo JText::sprintf('JT_DESC_UNLINK', $row->name); ?>"
								onclick="if (confirm('<?php echo JText::sprintf('JT_CONFIRM_UNLINK', $row->name); ?>')){$('cid').value='<?php echo $row->app_id.'!'.$row->id; ?>';jtsubmitbutton('delete');}"
								class="jt-btn-cross"
								style="text-decoration: none;"
								rel="noindex, nofollow"
							>&nbsp;</a>
						<?php } else { ?>&nbsp;<?php } ?>
					</td>
					<td><!-- Linked person -->
						<?php if (!empty($row->name_c)) { ?>
							<?php echo $row->name_c; ?>&nbsp;[<?php echo $row->id_c; ?>]
							<br />
							<?php if (!empty($row->birthDate_c) || !empty($row->deathDate_c)) { ?>
								(
								<?php echo (empty($row->deathDate_c)) ? JText::_('BIRT').':&nbsp;' : ''; ?>
								<?php echo (empty($row->birthDate_c)) ? JText::_('DEAT').':&nbsp;' : $row->birthDate_c; ?> 
								<?php echo (!empty($row->birthDate_c) && !empty($row->deathDate_c)) ? '&nbsp;-&nbsp;' : ''; ?> 
								<?php echo $row->deathDate_c; ?>
								)
							<?php } ?>
						<?php } else { ?>&nbsp;<?php } ?>
					</td>
					<td><div id="<?php echo $row->app_id.'!'.$row->id; ?>" class="jt-ajax">&nbsp;</div></td>
				</tr>
				<?php
			       $k = 3 - $k;
			}
			?>
			</tbody>
		</table>
	</div> <!-- jt-content -->
	
<?php } else { ?>
<!-- user has NO access to information -->
	<div class="jt-content-th" >
		<div class="jt-noaccess"><?php echo JText::_( 'JT_NOACCESS' ); ?></div>
	</div>
<?php } ?>

<div class="jt-stamp">
	<?php echo $this->lists[ 'CR' ]; ?>
</div>

<!-- input type="hidden" name="option" value="com_joaktree" / -->
<!-- input type="hidden" name="view" value="linkedpersons" / -->
<input type="hidden" name="task" value="" />
<input type="hidden" id="cid" name="cid" value="" />
<input type="hidden" id="mygencid" name="mygencid" value="" />
<input type="hidden" name="controller" value="linkedpersons" />
<input type="hidden" name="treeId" value="<?php echo $this->lists['tree_id']; ?>" />
<input type="hidden" name="appId"  value="<?php echo $this->lists['app_id']; ?>" />

</form>

<script type="text/javascript">


</script>