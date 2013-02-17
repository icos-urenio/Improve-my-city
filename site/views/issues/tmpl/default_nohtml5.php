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

//JHtml::_('behavior.tooltip');
//JHtml::_('behavior.formvalidation');
//load mootools for the ordering
JHtml::_('behavior.framework', true);

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
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

	<div id="imc-header">
		<div id="imc-menu" class="issueslist">
			<!-- Filters -->
			<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm">
				<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
				<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
				<input type="hidden" name="status[0]" value="0" />
				<input type="hidden" name="cat[0]" value="0" />
				<input type="hidden" name="limitstart" value="" />
				<input type="hidden" name="limit" value="<?php echo  $this->state->get('list.limit');?>" />
				<input type="hidden" name="task" value="" />
				
				<!-- Mega Menu -->
				<ul id="mega-menu">
					<li id="drop-1"><a id="btn-1" href="javascript:void(0);" class="btn"><i class="icon-list-alt"></i> <?php echo JText::_('COM_IMPROVEMYCITY_FILTER_SELECTION')?></a>
						<div class="megadrop dropdown_6columns">
							<div class="col_6">
								<h2><?php echo JText::_('COM_IMPROVEMYCITY_CATEGORIES')?></h2>
							</div>
							
							<?php foreach($this->arCat as $c){?>		
								<div class="col_2">
									<?php echo $c; ?>
								</div>					
							<?php }?>

							<div class="col_6">
								<h2><?php echo JText::_('COM_IMPROVEMYCITY_ISSUE_STATUS')?></h2>
							</div>
							
							<div class="col_6">
								<ul>
								<?php echo $this->statusFilters; ?>
								</ul>
							</div>

							<div class="col_6" style="text-align: center;">
								<button type="submit" class="btn btn-success" name="Submit" value="<?php echo JText::_('COM_IMPROVEMYCITY_APPLY_FILTERS')?>"><i class="icon-ok icon-white"></i> <?php echo JText::_('COM_IMPROVEMYCITY_APPLY_FILTERS')?></button>
							</div>
						</div>
					</li>
					<li id="drop-2"><a id="btn-2" href="javascript:void(0);" class="btn"><i class="icon-signal"></i> <?php echo JText::_('COM_IMPROVEMYCITY_ORDERING')?></a>
						<div class="megadrop dropdown_2columns">
							<div class="col_2">						
								<ul>
									<!-- dropdown menu links -->
									<li><?php  echo JHtml::_('grid.sort', JText::_('COM_IMPROVEMYCITY_BY_DATE'), 'a.ordering', $listDirn, $listOrder);?></li>
									<li><?php  echo JHtml::_('grid.sort', JText::_('COM_IMPROVEMYCITY_BY_VOTES'), 'a.votes', $listDirn, $listOrder);?></li>
									<li><?php  echo JHtml::_('grid.sort', JText::_('COM_IMPROVEMYCITY_BY_STATUS'), 'a.currentstatus', $listDirn, $listOrder);?></li>
								</ul>						
							</div>
						</div>
					</li>
					<li id="drop-3"><a id="btn-3" href="javascript:void(0);" class="btn"><i class="icon-check"></i> <?php echo JText::_('JGLOBAL_DISPLAY_NUM')?></a>
						<div class="megadrop dropdown_1column">
							<div class="col_1">						
								<ul>
									<!-- dropdown menu links -->
									<?php echo $this->getLimitBox; ?>
								</ul>						
							</div>
						</div>
					</li>					
				</ul>
			</form>	
	
			<!-- New Issue -->
			<div class="btn-group imc-right">
				<a class="btn btn-large btn-primary" href="<?php echo ImprovemycityHelper::generateRouteLink('index.php?option=com_improvemycity&task=addIssue');?>"><i class="icon-plus icon-white"></i> <?php echo JText::_('REPORT_AN_ISSUE');?></a>
			</div>
				
			
		</div>
	</div>
	
	<div id="loading"><img src="<?php echo JURI::base().'components/com_improvemycity/images/ajax-loader.gif';?>" /></div>
	
	<div id="imc-content">
		<div id="imc-main-panel-fifty">
			<?php if(empty($this->items)) : ?>
				<div class="alert alert-error width75">
				<?php echo JText::_('COM_IMPROVEMYCITY_FILTER_REVISION'); ?>
				</div>
			<?php endif; ?>
			<?php foreach($this->items as $item){ ?>
				<div class="imc-issue-item" id="issueid-<?php echo $item->id;?>" onclick="location.href='<?php echo ImprovemycityHelper::generateRouteLink('index.php?option=com_improvemycity&view=issue&issue_id='.$item->id);?>';void(0);" >
					<div class="imc-issue-content">
						<div class="imc-issue-review">
							<h2 class="imc-issue-title">
								<?php echo '#' . $item->id . '. ' .$item->title;?>
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
							<div class="imc-issue-address"><?php echo $item->address;?></div>
							<div class="imc-issue-posted">
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
							<div><span class="label"><?php echo $item->votes;?> <?php echo JText::_('VOTES');?></span></div>
						</div>
					</div>
					<?php if ($item->photo != '') : ?>
					<div class="imc-issue-photo">
						<img src="<?php echo JURI::root().$item->photo;?>" alt="thumbnail photo">
					</div>
					<?php endif; ?>	
					
					
					<?php /* UNCOMMENT IF YOU NEED DISCUSSION IN FRONT LIST...
						<?php $i=0; foreach($item->discussion as $item_d){ $i++;?>
						<div class="chat">
							<span class="chat-info">(<?php echo $item_d->userid . ') ' . JText::_('COMMENT_REPORTED') . ' ' . $item_d->progressdate_rel;?></span>
							<span class="chat-desc"><?php echo $item_d->description;?></span>
						</div>
						<?php 
							if($i == 3){echo '<a href="'.ImprovemycityHelper::generateRouteLink('index.php?option=com_improvemycity&view=issue&issue_id='.$item->id).'">See all comments...</a>';break;}
						}?>
						<!--
						<div class="issue-join"><a href="<?php echo JRoute::_('index.php?option=com_improvemycity&view=issue&issue_id='.$item->id);?>"><?php echo JText::_('JOIN_DISCUSSION');?></a></div>-->
						*/
					?>	
					
				</div>
			<?php }	?>	
			<div id="system">
			<?php echo $this->pagination->getPagesLinks(); ?>
			</div>
			
		</div>
		<div id="imc-details-sidebar-fifty">
			<div id="mapCanvas"><?php echo JText::_('COM_IMPROVEMYCITY');?></div>
			<?php if($this->credits == 1) : ?>
				<div style="margin-top: 30px;" class="alert alert-info"><?php echo JText::_('COM_IMPROVEMYCITY_INFOALERT');?></div>
			<?php endif; ?>
		</div>	
	</div>
</div>

