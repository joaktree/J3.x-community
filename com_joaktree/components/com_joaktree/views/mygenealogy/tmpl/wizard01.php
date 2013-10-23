<?php
// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

// import component libraries
JLoader::import('helper.formhelper', JPATH_COMPONENT);

// uploadManager
$document		= &JFactory::getDocument();
$document->addScript( JoaktreeHelper::joaktreejs('uploadmanager.js'));
$document->addScript( JoaktreeHelper::joaktreejs('uploadmanager_progressbar.js'));

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

<div id="jt-form"> 
<form action="<?php echo JRoute::_('index.php?option=com_joaktree'); ?>" method="post" name="wizardForm" id="wizardForm" class="form-validate">

<?php if (true) { ?> 
<!-- user has access to information -->
<div class="fltlft">

<?php print_r($this->item); ?>

	<fieldset class="joaktreeform">
		<legend><?php echo JText::_('JTTREE_TITLE_NEWNAME'); ?></legend>

		<!-- Save + cancel buttons -->
		<?php echo FormHelper::getButtons(1) ;?>							
		<!-- End save + cancel buttons -->		

		<ul class="joaktreeformlist">
			<li>
				<?php echo $this->form->getLabel('name'); ?>
				<?php echo $this->form->getInput('name'); ?>			
			</li>

			<li>
				<?php echo $this->form->getLabel('theme_id'); ?>
				<?php echo $this->form->getInput('theme_id'); ?>			
			</li>
			
			<li>
				<?php echo $this->form->getLabel('indGedCom'); ?>
				<?php echo $this->form->getInput('indGedCom'); ?>			
			</li>
			
		</ul>

		<div class="jt-clearfix"></div>
		<div id="gedcom" style="display: none;">
			<div>
				<ul class="joaktreeformlist">
					<?php foreach($this->form->getFieldset('gedcom') as $field): ?>
						<li>
							<?php if (!$field->hidden): echo $field->label; endif; ?>
							<?php echo $field->input; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<div class="jt-clearfix"></div>
		
			<div>
				<ul class="joaktreeformlist">
					<li style="margin-top: 10px;">
						<a href="#" id="upload-button" class="jt-button-closed jt-buttonlabel">
							<?php echo JText::_('JTAPPLICATION_DESC'); ?>
						</a>
						
						<div id="upload"></div> 
						<input 
							type="hidden" 
							id="jform_params_upload" 
							name="jform[params][upload]"
						>
					</li>			
							
					<li>&nbsp;</li>
	
				</ul>
			</div>
		</div>


		<div class="jt-clearfix"></div>
		<!-- Save + cancel buttons -->
		<?php echo FormHelper::getButtons(2) ;?>							
		<!-- End save + cancel buttons -->		
	</fieldset>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="mygenealogy" />
	<input type="hidden" id="jform_wizard" name="jform[wizard]" value="1" />
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

<!-- uploadManager -->
<script type="text/javascript">
	var options = {	
			//id of the upload container
			container: 'upload',

			// limits number of files to 1
			limit: 1,

			// max file size
			maxsize: 2621440,
			//maxsize: 100,
			
			//where to send the upload request
			//base: '../community/index.php?option=com_joaktree&view=uploadmanager',
			base: '..<?php echo JRoute::_('index.php?format=raw&tmpl=component&option=com_joaktree&view=uploadmanager'); ?>',   

			//filter file types
			filetype: 'ged',
			
			//form field name
			name: 'names[]',
			
			multiple: false, //enable multiple selection in file dialog
			progressbar: {

				width: 140, //fix the progressbar width, optional
				color: '#000', 
				fillColor: '#fff',
				text: 'Pending...',
				onChange: function (value, progressbar) {
				
					//console.log(arguments)
					progressbar.setText('completed: ' + (100 * value).format() + '%')
				}
			}
		};
	
	//enable file drag drop on $('upload')
	//uploadManager.attachDragEvents('upload', options);
	
	//click to add a new file upload
	document.getElementById('upload-button').addEvent('click', function(e) {
	
		e.stop();		
		uploadManager.upload(options)
	})
</script>
