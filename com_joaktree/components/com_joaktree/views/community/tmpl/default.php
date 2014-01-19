<?php 
// no direct access
defined('_JEXEC') or die('Restricted access'); 
JHtml::_('behavior.formvalidation');
?>

<?php if (isset($this->msg)) : ?>
	<?php echo $this->msg; ?>
<?php else: ?>

	<div class="jt-h3">
		<?php echo (is_object($this->topic)) 
				? JHtml::_('kunenaforum.link', $this->topic->getUri ($this->category), JText::sprintf('JT_KUNENADISCUSS_DISCUSS', $this->person->fullName) )
				: JText::sprintf('JT_KUNENADISCUSS_DISCUSS', $this->person->fullName); 
		?>
	</div>
	
	<div id="system-message">		
		<?php foreach($this->applicationMessages as $appMessage) { ?>
			<div id="system-message-container">
				<?php $alert_color = ($appMessage['type'] == 'error') ? 'alert-error' : '';?>
				<div class="alert <?php echo $alert_color; ?> alert-message"><a data-dismiss="alert" class="close">x</a>
					<h4 class="alert-heading"><?php echo JText::_($appMessage['type']); ?></h4>
					<div>
						<?php echo $appMessage['message']; ?>
					</div>
				</div>
			</div>
		
		<?php } ?>
	
	</div>
	

	<div id="kdiscuss-quick-post<?php echo $this->person->app_id.'!'.$this->person->id; ?>" class="kdiscuss-form">	
		<form method="post" id="postform" name="postform" class="form-validate">
			<fieldset class="joaktreeform">
				<?php if ($this->lists['canpost']) { ?>	
					<ul class="joaktreeformlist">
						<li>
							<?php echo $this->form->getLabel('name'); ?>
							<?php echo $this->form->getInput('name'); ?>			
						</li>
						
						<?php if(!$this->user->exists() && $this->config->askemail) : ?>
							<li>
								<?php echo $this->form->getLabel('email'); ?>
								<?php echo $this->form->getInput('email'); ?>			
							
							</li>
						<?php endif; ?>
						
						<li>
							<?php echo $this->form->getLabel('subject'); ?>
							<?php echo $this->form->getInput('subject'); ?>			
						</li>

						<li>
							<?php echo $this->form->getLabel('message'); ?>
							<?php echo $this->form->getInput('message'); ?>			
						</li>
						
						<?php if ($this->lists['hasCaptcha']) : ?>
							<li>
								<?php echo $this->form->getLabel('captcha'); ?>
								<?php echo $this->form->getInput('captcha'); ?>		
							</li>				
						<?php endif; ?>
					</ul>
					
					<div class="jt-clearfix"></div>
					<div style="margin-left: 10px; padding-bottom: 40px;" class="jt-buttonbar">
						<a 	onclick="postMessage1();" 
							title="<?php echo JText::_('JSAVE'); ?>" 
							class="jt-button-closed jt-buttonlabel" 
							id="save1" 
							href="#"
						>
							<?php echo JText::_('JSAVE'); ?>
						</a>&nbsp;
					</div>
					<input type="hidden" name="kdiscussContentId" value="<?php echo $this->person->app_id.'!'.$this->person->id ?>" />		
					<?php echo JHTML::_( 'form.token' ); ?>
				<?php } ?>
				
				<?php if (isset($this->messages)) { ?>
					<div style="padding: 10px;">
						<?php echo $this->messages; ?>
					</div>
				<?php } else if ($this->lists['canpost']) { ?>
					<div style="padding: 10px;">
						<?php echo JText::sprintf('JT_KUNENADISCUSS_NOMESSAGES', $this->person->fullName); ?>
					</div>
				<?php } ?>
				
			</fieldset>
		</form>
	</div>
		
	<div class="kdiscuss-more">
		<?php echo (is_object($this->topic)) 
				? JHtml::_('kunenaforum.link', $this->topic->getUri ($this->category), JText::_('COM_KUNENA_READMORE') ) 
				: null; 
		?>
	</div>

<?php endif; ?>

