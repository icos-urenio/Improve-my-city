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

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
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
				
	
				<a class="new-issue" style="float: right;" href="<?php echo JRoute::_('index.php?option=com_improvemycity&controller=improvemycity&task=addIssue');?>"><?php echo JText::_('REPORT_AN_ISSUE');?></a><br />
				
				<div id="loading"><img src="<?php echo JURI::base().'components/com_improvemycity/images/ajax-loader.gif';?>" /></div>
				
			</header>
			
			<div id="issues-list">
				<?php if(empty($this->items)) : ?>
					<p class="box-warning width75">
					<?php echo JText::_('COM_IMPROVEMYCITY_FILTER_REVISION'); ?>
					</p>
				<?php endif; ?>
				<?php foreach($this->items as $item){ ?>
					<div class="issue-item" id="issueid-<?php echo $item->id;?>">
						<div class="issue-votes"><span class="title"><?php echo JText::_('VOTES');?></span><span class="num"><?php echo $item->votes;?></span></div>
						<div class="issue-review">
							<h2 class="issue-title"><a onmouseover="markerhover(<?php echo $item->id; ?>);" href="<?php echo JRoute::_('index.php?option=com_improvemycity&view=issue&issue_id='.$item->id);?>"><?php echo $item->title;?></a>
								<?php $status = '';
									switch($item->currentstatus){
										case 1:
										$status = 'OPEN';
										break;
										case 2:
										$status = 'ACK';
										break;
										case 3:
										$status = 'CLOSED';
										break;
									}
								?>
								<span class="status-<?php echo $status;?>">
									<?php echo JText::_($status);?>
								</span>
							</h2>
							<div class="issue-address"><?php echo $item->address;?></div>
							<div class="issue-posted">
								<?php 
									echo JText::_('ISSUE_REPORTED') . ' ' . $item->reported_rel;
									if($item->closed_rel != ''){
										echo ' ' . JText::_('AND_CLOSED') . ' ' . $item->closed_rel;
									}
									else if($item->acknowledged_rel != ''){
										echo ' ' . JText::_('AND_ACKNOWLEDGED') . ' ' . $item->acknowledged_rel;
									}
								?>
							</div>
							
						</div>
						<?php if ($item->photo != '') : ?>
							<img src="<?php echo JURI::root().$item->photo;?>" height="60" alt="thumbnail photo">
						<?php endif; ?>	
						
						
						<?php /* UNCOMMENT IF YOU NEED DISCUSSION IN FRONT LIST...
							<?php $i=0; foreach($item->discussion as $item_d){ $i++;?>
							<div class="chat">
								<span class="chat-info">(<?php echo $item_d->userid . ') ' . JText::_('COMMENT_REPORTED') . ' ' . $item_d->progressdate_rel;?></span>
								<span class="chat-desc"><?php echo $item_d->description;?></span>
							</div>
							<?php 
								if($i == 3){echo '<a href="'.JRoute::_('index.php?option=com_improvemycity&view=issue&issue_id='.$item->id).'">See all comments...</a>';break;}
							}?>
							<!--
							<div class="issue-join"><a href="<?php echo JRoute::_('index.php?option=com_improvemycity&view=issue&issue_id='.$item->id);?>"><?php echo JText::_('JOIN_DISCUSSION');?></a></div>-->
							*/
						?>	
						
					</div>
				<?php }	?>	
				<?php echo $this->pagination->getPagesLinks(); ?>
			</div>
			<div id="details-sidebar">
				<div id="mapCanvas"><?php echo JText::_('COM_IMPROVEMYCITY');?></div>
				<div id="improvemycity-filters">
					<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" class="box form-validate" method="post" name="adminForm" id="adminForm">
						<h2><?php echo JText::_('COM_IMPROVEMYCITY_ORDERING')?></h2>
						<div id="ordering-content">
							<?php  echo JHtml::_('grid.sort', JText::_('COM_IMPROVEMYCITY_BY_DATE'), 'a.ordering', $listDirn, $listOrder) ; ?><br />
							<?php  echo JHtml::_('grid.sort', JText::_('COM_IMPROVEMYCITY_BY_VOTES'), 'a.votes', $listDirn, $listOrder) ; ?><br />	
							<?php  echo JHtml::_('grid.sort', JText::_('COM_IMPROVEMYCITY_BY_STATUS'), 'a.currentstatus', $listDirn, $listOrder) ; ?><br />
							<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
							<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
							<input type="hidden" name="status[0]" value="0" />
							<input type="hidden" name="cat[0]" value="0" />
							<input type="hidden" name="limitstart" value="" />
							<input type="hidden" name="task" value="" />

							<div class="display-limit">
								<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>&#160;
								<?php echo $this->getLimitBox; ?>
								
							</div>							
						</div>
						<h2><?php echo JText::_('COM_IMPROVEMYCITY_FILTERS')?></h2>
						<div id="content-filters">
							<h3><?php echo JText::_('COM_IMPROVEMYCITY_ISSUE_STATUS')?></h3>
							<?php echo $this->statusFilters;?>
							<h3><?php echo JText::_('COM_IMPROVEMYCITY_CATEGORIES')?></h3>
							<?php echo $this->filters;?>
						</div>
						<br />
						<div class="formelm-buttons">
							<button type="submit" class="post" name="Submit" value="<?php echo JText::_('COM_IMPROVEMYCITY_APPLY_FILTERS')?>"><?php echo JText::_('COM_IMPROVEMYCITY_APPLY_FILTERS')?></button>
						</div>
						<div style="clear: both; padding: 5px 0;"></div>
						
					</form>					
				</div>			
			</div>	
		</article>
	</div>
</div>			