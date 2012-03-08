<?php
/**
 * @version     1.0
 * @package     com_improvemycity
 * @copyright   Copyright (C) 2011 - 2012 URENIO Research Unit. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      URENIO Research Unit
 */
 
// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

?>



<div id="system">	
	<div id="wrapper-improvemycity">
		<article class="improvemycity <?php echo $this->pageclass_sfx; ?>">
			<header>
				<?php if ($this->params->get('show_page_heading', 1)) : ?>			
				<h1 class="title">
					<?php if ($this->escape($this->params->get('page_heading'))) :?>
						<?php echo $this->escape($this->params->get('page_heading')); ?>
					<?php else : ?>
						<?php echo $this->escape($this->params->get('page_title')); ?>
					<?php endif; ?>				
				</h1>
				<?php endif; ?>
			</header>
			
			<h2 class="issue-title"><?php echo JText::_('NEW_ISSUE');?></h2>
			
			<div id="mapCanvasNew"><?php echo JText::_('COM_IMPROVEMYCITY');?></div>
			<div id="issue-form">
				<div class="issue-new">
					
					
					
					<form class="box form-validate" action="<?php echo JRoute::_('index.php?option=com_improvemycity&view=issues'); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">					
						<fieldset>
						
						<?php foreach($this->form->getFieldset('details') as $field): ?>
							<div>
								<?php 
								echo $field->label;
								echo $field->input;
								if($field->id == 'jform_address'){
									echo '<br /><a href="javascript:void(0);" onclick="codeAddress();">'.JText::_('FIND_ADDRESS_ON_MAP').'</a>';
								}
								?>
								
							</div>
						<?php endforeach; ?>
						
						<?php if(!$this->guest) {?>
							<div class="formelm-buttons">
								<button type="button" class="cancel" onclick="Joomla.submitbutton('issue.cancel')">
									<?php echo JText::_('JCANCEL') ?>
								</button>
								<button type="button" class="post" onclick="Joomla.submitbutton('issue.save')">
									<?php echo JText::_('POST_ISSUE') ?>
								</button>
							</div>
						<?php } else { //NOT LOGGED USER?>
							<div class="formelm-buttons">
								<button type="button" class="cancel" onclick="Joomla.submitbutton('issue.cancel')">
									<?php echo JText::_('JCANCEL'); ?>
								</button>
								<button type="button" class="post" onclick="alert('<?php echo JText::_('NOT_LOGGED_IN');?>');">
									<?php echo JText::_('POST_ISSUE'); ?>
								</button>
								<p class="box-info">
								<small>
								<?php echo JText::_('NOT_LOGGED_IN');?>
								<?php $return = base64_encode(JRoute::_('index.php?option=com_improvemycity&controller=improvemycity&task=addIssue&Itemid='.JRequest::getVar( 'Itemid' ))); ?>
								<a class="colorbox" href="index.php?option=com_users&view=login&tmpl=component&return=<?php echo $return; ?>"><?php echo JText::_('PLEASE_LOG');?></a>
								</small>
								</p>
							</div>
						<?php }?>
						
						
						
						<input type="hidden" name="return" value="<?php echo $this->return_page;?>" />
						<input type="hidden" name="task" value="" />
						<?php echo JHtml::_('form.token'); ?>
						</fieldset>

					</form>



			
				</div>
			</div>	
			
		</article>
	</div>
</div>			