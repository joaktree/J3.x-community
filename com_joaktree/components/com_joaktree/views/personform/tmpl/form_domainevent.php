<?php
// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.modal');

// import component libraries
JLoader::import('helper.formhelper', JPATH_COMPONENT);

?>

<script type="text/javascript">
	function jtsubmitbutton(task)
	{
		if (task == 'cancel' || document.formvalidator.isValid(document.id('peventForm'))) {
			Joomla.submitform(task, document.getElementById('peventForm'));
		} else {
			jtrefnot(1);
			alert('<?php echo $this->escape(JText::_('JT_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>
<!--  set up counters and other necessities -->
<?php 
	$evtCnt = 0; 
	$this->form->setValue('living', 'person', $this->lists['indLiving']); 
	$display 	= FormHelper::checkDisplay('person', $this->lists['indLiving']); 
	$display_id = JoaktreeHelper::getDispId();
?>

<script type="text/javascript">
	<?php echo FormHelper::getNameEventRowScript('domainEvent', $this->form, $this->lists['appId']); ?>
	<?php echo FormHelper::getReferenceRowScript($this->form, $this->lists['appId']); ?>
	<?php echo FormHelper::getNoteRowScript(false, $this->form, $this->lists['appId']); ?>
	<?php echo FormHelper::getGeneralRowScript(); ?>
	<?php echo FormHelper::getDisplayScript(); ?>
</script>

<div id="jt-form"> 
<form action="<?php echo JRoute::_('index.php?option=com_joaktree'); ?>" method="post" name="peventForm" id="peventForm" class="form-validate">
<?php echo $this->form->getInput('type', null, 'domain'); ?>

<?php if (($this->lists['userAccess']) && (is_object($this->canDo)) && $this->canDo->get('core.edit')) { ?> 
<!-- user has access to information -->
<div class="fltlft">
	<div class="jt-content-th" >
		<div class="jt-h3-th">
			<?php echo JText::_( 'JT_EDIT_RECORD' ).':&nbsp;'.$this->item->firstName.'&nbsp;'.$this->item->familyName; ?>
			<span style="float: right;">
				<?php echo (($this->lists['indLiving']) ? JText::_( 'JT_LIVING' ) : JText::_( 'JT_NOTLIVING' )); ?>
			</span>
		</div>
	</div>

	<!-- Hidden fields -->
	<?php echo $this->form->getInput('lineEnd', null, $this->lists['lineEnd']); ?>
	<?php echo $this->form->getInput('id', 'person', ((is_object($this->item)) ? $this->item->id : null)); ?>		
	<?php echo $this->form->getInput('app_id', 'person', $this->lists['appId']); ?>
	<?php echo $this->form->getInput('living', 'person', $this->lists['indLiving']); ?>
	<?php echo $this->form->getInput('status', 'person', ((is_object($this->item)) ? 'loaded' : 'new')); ?>		
	<!-- End: Hidden fields -->		

	<fieldset class="joaktreeform">
		<legend><?php echo JText::_('JT_EDITEVENTS'); ?></legend>

		<?php if (is_object($this->item)) { ?>	
			<!-- Save + cancel buttons -->
			<?php $but = array( 'save' 		=> true 
							  , 'cancel'	=> true
							  , 'check'		=> (is_object($this->item) ? false : true )
							  , 'done'		=> false
							  , 'add'		=> false
							  );
			?>
			<?php echo FormHelper::getButtons(1, $but, false); ?>							
			<!-- End save + cancel buttons -->
		<?php } ?>		

		<?php if ($display) { ?>
			<!-- Events -->	
			<table style="width: 96%;">
				<!-- header for events -->
				<thead>
					<tr>
						<th class="jt-content-th">
							<?php echo JText::_( 'JT_EVENTS' ); ?>
						</th>
						<th class="jt-content-th">
							<?php echo JText::_( 'JT_ACTIONS' ); ?>
						</th>						
					</tr>
				</thead>
				<!-- End: header for events -->
				
				<!-- tabel body for events -->
				<?php $tabEvtId = 'ev_'; ?>
				<tbody id="<?php echo $tabEvtId; ?>">
					<!-- Add row for new event -->
					<tr class="jt-table-entry3" >
						<td style="padding: 2px 5px;">&nbsp;</td>
						<td style="padding: 2px 5px;">
							<div class="jt-edit">
								<a 	href="#"
									onclick="inject_namevtrow('<?php echo $tabEvtId; ?>', '<?php echo $this->lists['appId']; ?>'); return false;"
									title="<?php echo JText::_('JTADD_DESC'); ?>" 
								>
									<?php echo JText::_('JTADD'); ?>
								</a>
							</div>
						</td>
					</tr>			
					<!-- End: Add row for new event -->
					
					<!-- List of existing events -->
					<?php 
					if (is_object($this->item)) {
			  			$events = $this->item->getPersonEvents();			  			
			  			if (count($events)) {
							$k = 4;
							for ($i=0, $n=count($events); $i < $n; $i++)	{
								$event 	= &$events[$i];
								$evtCnt = ($event->orderNumber > $evtCnt) ? $event->orderNumber : $evtCnt;
								if (($event->domain) && ($event->display_id == $display_id)) {
					?>
									<!-- Row for one existing event -->
									<?php echo FormHelper::getNameEventRow(true, 'domainEvent', $this->form, $this->item, $event, $this->lists['appId'], null) ;?>							
									<!-- End: Row for one existing event -->
															
					<?php   
									$k = 7 - $k;
								}			
			  				}
			  			}
					}
					?>
					<!-- End: List of existing events -->
				</tbody>	
				<!-- End: tabel body for events -->
			</table>
			<!-- End: Events -->
		<?php } ?>
		
		<?php echo FormHelper::getButtons(2, $but, false); ?>							
	</fieldset>
		
	<!-- keep counter values in the form -->
	<input type="hidden" id="namevtcounter" name="namevtcounter" value="<?php echo $evtCnt; ?>"	/>
	<input type="hidden" id="refcounter" name="refcounter" 
		value="<?php echo ((is_object($this->item)) ? $this->item->getMaxReference() : 0); ?>" 
	/>
	<input type="hidden" id="notcounter" name="notcounter" 
		value="<?php echo ((is_object($this->item)) ? $this->item->getMaxNote() : 0); ?>" 
	/>

	<input type="hidden" name="treeId" value="<?php echo $this->lists['treeId']; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="personform" />
	<?php echo JHtml::_('form.token'); ?>

</div>

<div class="clr"></div>

<?php } else { ?>
<!-- user has NO access to information -->
	<div class="jt-content-th" >
		<div class="jt-noaccess"><?php echo JText::_( 'JT_NOACCESS' ); ?></div>
	</div>
<?php } ?>

<div class="jt-stamp">
	<?php echo $this->lists[ 'CR' ]; ?>
</div>

</form>
</div>