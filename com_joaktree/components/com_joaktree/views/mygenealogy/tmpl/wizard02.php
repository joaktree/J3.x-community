<?php
// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
//JHtml::_('behavior.formvalidation');

// import component libraries
JLoader::import('helper.formhelper', JPATH_COMPONENT);

// import gedcom
$document		= &JFactory::getDocument();
$document->addScript( JURI::base().'administrator/components/com_joaktree/assets/js/joaktree_admin.js');

?>

<script type="text/javascript">
	function importGedcom() {
		var myRequest = new Request({
		    url: 'index.php?option=com_joaktree&view=gedcomimport&format=raw&tmpl=component',
		    method: 'get',
			onFailure: function(xhr) {
				alert('Error occured for url: index.php?option=com_joaktree&view=gedcomimport&format=raw&tmpl=component');
			},
			onComplete: function(response) {
		    		HandleResponseGedcom('import', response);	    		
			}
		}).send();
	}

	window.addEvent('domready', function() {
		importGedcom();
	});
</script>


<div id="jt-form"> 
<form action="<?php echo JRoute::_('index.php?option=com_joaktree'); ?>" method="post" name="wizardForm" id="wizardForm" class="form-validate">

<?php if (true) { ?> 
<!-- user has access to information -->

<div class="fltlft">

	<div id="j-main-container">
		<div id="cpanel" >
			<div id="head_process" style="display: inline;">
				<div style="float: left; height: 114px;">
					<h1><?php echo JText::_('JTPROCGEDCOM_PROC'); ?></h1>
					<?php echo JText::_('JTPROCGEDCOM_PROC_TXT'); ?>
				</div>
				<div style="float: right">
					<div class="jt-icon">
						<img src="<?php echo JURI::base().'administrator/components/com_joaktree/assets/images/ajax-loader.gif'; ?>" />
						<br />
						<span><?php echo JText::_('JT_LOADING'); ?></span>
					</div>
				</div>
			</div>
			<div id="head_finished" style="display: none;">
				<div style="float: left">
					<h1><?php echo JText::_('JTPROCGEDCOM_HFINISHED'); ?></h1>
				</div>
				<div style="float: right">
					<div class="jt-icon">
						<a href="index.php?option=com_joaktree&view=mygenealogy">
							<span><?php echo JText::_('JT_MYGENEALOGY'); ?></span>
						</a>
					</div>
				</div>
			</div>
			<div id="head_error" style="display: none;">
				<div style="float: left">
					<h1><?php echo JText::_('JTPROCGEDCOM_HERROR'); ?></h1>
				</div>
				<div style="float: right">
					<div class="jt-icon">
						<a href="index.php?option=com_joaktree&view=mygenealogy">
							<span><?php echo JText::_('JT_MYGENEALOGY'); ?></span>
						</a>
					</div>
				</div>
	
			</div>
		</div>
	</div>
	<div style="clear: both;"></div>
		
		
	<fieldset class="joaktreeform">
		<legend><?php echo $this->item->title; ?></legend>
		
		<div style="float: right; width: 40%;">
			<fieldset>
				<h2><?php echo JText::_('JTPROCESS_MSG'); ?></h2>
				<div id="procmsg"></div>		
			</fieldset>
		</div>
		
		
		<div class="form-horizontal" style="width: 40%;">
			<fieldset>
				
				<div class="tab-content">
					<div class="tab-pane active">
						<div class="control-group">
							<div class="control-label">
								<?php echo JText::_('JT_HEADING_ID'); ?>
							</div>
							<div class="controls">
								<input 
									type="text" 
									id="id_<?php echo $this->item->app_id; ?>" 
									class="readonly"
									value="<?php echo $this->item->app_id; ?>"
									readonly="readonly"
								/>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo JText::_('JTPROCESS_START'); ?>
							</div>
							<div class="controls">
								<input 
									type="text" 
									id="start_<?php echo $this->item->app_id; ?>" 
									class="readonly" 
									value=""
									readonly="readonly"
								/>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo JText::_('JTPROCESS_CURRENT'); ?>
							</div>
							<div class="controls">
								<input 
									type="text" 
									id="current_<?php echo $this->item->app_id; ?>" 
									class="readonly" 
									value=""
									readonly="readonly"
								/>
							</div>
						</div>
						<div class="control-group" id="l_persons_<?php echo $this->item->app_id; ?>" style="display: none;">
							<div class="control-label">
								<?php echo JText::sprintf('JTGEDCOM_MESSAGE_PERSONS', null); ?>
							</div>
							<div class="controls">
								<input 
									type="text" 
									id="persons_<?php echo $this->item->app_id; ?>" 
									class="readonly" 
									value="0"
									readonly="readonly"
								/>
							</div>
						</div>
						<div class="control-group" id="l_families_<?php echo $this->item->app_id; ?>"style="display: none;">
							<div class="control-label">
								<?php echo JText::sprintf('JTGEDCOM_MESSAGE_FAMILIES', null); ?>
							</div>
							<div class="controls">
								<input 
									type="text" 
									id="families_<?php echo $this->item->app_id; ?>" 
									class="readonly" 
									value="0"
									readonly="readonly"
								/>
							</div>
						</div>
						<div class="control-group" id="l_sources_<?php echo $this->item->app_id; ?>"style="display: none;">
							<div class="control-label">
								<?php echo JText::sprintf('JTGEDCOM_MESSAGE_SOURCES', null); ?>
							</div>
							<div class="controls">
								<input 
									type="text" 
									id="sources_<?php echo $this->item->app_id; ?>" 
									class="readonly" 
									value="0"
									readonly="readonly"
								/>
							</div>
						</div>
						<div class="control-group" id="l_repos_<?php echo $this->item->app_id; ?>"style="display: none;">
							<div class="control-label">
								<?php echo JText::sprintf('JTGEDCOM_MESSAGE_REPOS', null); ?>
							</div>
							<div class="controls">
								<input 
									type="text" 
									id="repos_<?php echo $this->item->app_id; ?>" 
									class="readonly" 
									value="0"
									readonly="readonly"
								/>
							</div>
						</div>
						<div class="control-group" id="l_notes_<?php echo $this->item->app_id; ?>"style="display: none;">
							<div class="control-label">
								<?php echo JText::sprintf('JTGEDCOM_MESSAGE_NOTES', null); ?>
							</div>
							<div class="controls">
								<input 
									type="text" 
									id="notes_<?php echo $this->item->app_id; ?>" 
									class="readonly" 
									value="0"
									readonly="readonly"
								/>
							</div>
						</div>
						<div class="control-group" id="l_unknown_<?php echo $this->item->app_id; ?>"style="display: none;">
							<div class="control-label">
								<?php echo JText::sprintf('JTGEDCOM_MESSAGE_UNKNOWN', null); ?>
							</div>
							<div class="controls">
								<input 
									type="text" 
									id="unknown_<?php echo $this->item->app_id; ?>" 
									class="readonly" 
									value="0"
									readonly="readonly"
								/>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo JText::_('JTPROCESS_END'); ?>
							</div>
							<div class="controls">
								<input 
									type="text" 
									id="end_<?php echo $this->item->app_id; ?>" 
									class="readonly" 
									value=""
									readonly="readonly"
								/>
							</div>
						</div>
		
					</div>
				</div>
				
				<div class="clr"> </div>
			</fieldset>	
		</div>
	</fieldset>
	
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="mygenealogy" />
	<input type="hidden" id="jform_wizard" name="jform[wizard]" value="2" />
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