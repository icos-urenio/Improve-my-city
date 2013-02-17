<?php
/**
 * @version     2.5.x
 * @package     com_improvemycity
 * @copyright   Copyright (C) 2011 - 2012 URENIO Research Unit. All rights reserved.
 * @license     GNU Affero General Public License version 3 or later; see LICENSE.txt
 * @author      Ioannis Tsampoulatidis for the URENIO Research Unit
 */
 
// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
if($this->popupmodal == 1)
	JHTML::_('behavior.modal', 'a.modal', array('handler' => 'ajax')); /* fix */
?>
<style type="text/css">  @import url("<?php echo JURI::root(true).'/components/com_improvemycity/css/improvemycity_nohtml5.css'; ?>"); </style>

<div id="imc-wrapper" class="imc <?php echo $this->pageclass_sfx; ?>">
	<?php if ($this->params->get('show_page_heading', 0)) : ?>			
	<h1 class="title">
		<?php if ($this->escape($this->params->get('page_heading'))) :?>
			<?php echo $this->escape($this->params->get('page_heading')); ?>
		<?php else : ?>
			<?php echo $this->escape($this->params->get('page_title')); ?>
		<?php endif; ?>				
	</h1>
	<?php endif; ?>	
	
	<?php /* <h2 class="issue-title"><?php echo JText::_('NEW_ISSUE');?></h2> */ ?>
	
	<div id="imc-header">
		<div id="imc-menu">
			<h2 class="imc-title"><?php echo JText::_('NEW_ISSUE');?></h2>
			<!-- bootstrap buttons -->	
			
			<!-- Return to issues -->
			<div class="btn-group imc-right">
				<a class="btn" href="<?php echo ImprovemycityHelper::generateRouteLink('index.php?option=com_improvemycity');?>"><i class="icon-arrow-left"></i> <?php echo JText::_('RETURN_TO_ISSUES');?></a>
			</div>
		
		</div>
	</div>	
	<div id="imc-content">
		<div id="imc-main-panel">
			<div id="mapCanvasNew"><?php echo JText::_('COM_IMPROVEMYCITY');?></div>
		</div>
		<div id="imc-details-sidebar">
			<div id="imc-new-issue">
			
				<?php if($this->guest) :?>			
					<div class="alert alert-error">
					<?php echo JText::_('NOT_LOGGED_IN');?>
					<?php echo JText::_('PLEASE_LOG');?>
					<?php /* UNCOMMENT IF YOU WANT login link 
						<?php $return = base64_encode(ImprovemycityHelper::generateRouteLink('index.php?option=com_improvemycity&controller=improvemycity&task=addIssue')); ?>
						<a class="modal strong-link" rel="{size: {x: 320, y: 350}}" href="index.php?option=com_users&view=login&tmpl=component&return=<?php echo $return; ?>"><span class="strong-link"><?php echo JText::_('PLEASE_LOG');?></span></a>
					*/?>
					</div>				
				<?php endif;?>
				
				
				
				<?php if($this->guest) :?>
				<div id="imc-lock-wrapper">
				<div id="imc-lock"></div>
				<div id="imc-form-wrapper">
				<?php endif;?>
				<form class="form-validate" action="<?php echo ('index.php?option=com_improvemycity&view=issues'); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">					
					<fieldset>
						<?php foreach($this->form->getFieldset('details') as $field): ?>
							<p>
								<?php 
								echo $field->label;
								echo $field->input;
								if($field->id == 'jform_address'){
									echo '<br /><a href="javascript:void(0);" onclick="codeAddress();"><span class="label label-inverse">'.JText::_('FIND_ADDRESS_ON_MAP').'</span></a>';
								}
								?>
							</p>
						<?php endforeach; ?>
						
						<?php if(!$this->guest) {?>
							<button class="btn btn-success imc-right" onclick="Joomla.submitbutton('issue.save');return(false);"><i class="icon-ok icon-white"></i> <?php echo JText::_('POST_ISSUE') ?></button>
						<?php } else { //NOT LOGGED USER?>
							<button class="btn btn-success disabled imc-right" disabled="disabled"><i class="icon-ok icon-white"></i> <?php echo JText::_('POST_ISSUE');?></button>
						<?php }?>
						
						<input type="hidden" name="return" value="<?php echo $this->return_page;?>" />
						<input type="hidden" name="task" value="" />
						<?php echo JHtml::_('form.token'); ?>
					</fieldset>
				</form>
				<?php if($this->guest) :?>
				</div>
				</div>
				<?php endif;?>
				
			</div>
		</div>	
	</div>
</div>
