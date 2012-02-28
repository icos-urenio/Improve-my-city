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

//JHtml::_('behavior.modal');

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
				<br />
				<div id="loading"><img src="<?php echo JURI::base().'components/com_improvemycity/images/ajax-loader.gif';?>" /></div>
				
			</header>
			<div id="issues-list">
				<div class="issue-item-details">
					<?php 
						$status = '';
						$steps = '';
						$arrow_gray = '<img src="' . JURI::root(true).'/components/com_improvemycity/images/arrow_gray.png' . '" />';
						$arrow_open = '<img src="' . JURI::root(true).'/components/com_improvemycity/images/arrow_open.png' . '" />';
						$arrow_ack = '<img src="' . JURI::root(true).'/components/com_improvemycity/images/arrow_ack.png' . '" />';
						$arrow_closed = '<img src="' . JURI::root(true).'/components/com_improvemycity/images/arrow_closed.png' . '" />';
						$steps_ok = '<img src="' . JURI::root(true).'/components/com_improvemycity/images/steps_ok.png' . '" />';
						
						switch($this->item->currentstatus){
							case 1:
							$status = 'OPEN';
							$steps .= $arrow_open . '<span class="status-OPEN">' . JText::_('OPEN') . '</span>' . $arrow_gray . '<span class="status-GRAY">' . JText::_('ACK') . '</span>' . $arrow_gray . '<span class="status-GRAY">' . JText::_('CLOSED') . '<span>';
							break;
							case 2:
							$status = 'ACK';
							$steps .= $arrow_open . '<span class="status-OPEN">' . JText::_('OPEN') . '</span>' . $arrow_ack . '<span class="status-ACK">' . JText::_('ACK') . '</span>' . $arrow_gray . '<span class="status-GRAY">' . JText::_('CLOSED') . '<span>';							
							break;
							case 3:
							$status = 'CLOSED';
							$steps .= $arrow_open . '<span class="status-OPEN">' . JText::_('OPEN') . '</span>' . $arrow_ack . '<span class="status-ACK">' . JText::_('ACK') . '</span>' . $arrow_closed . '<span class="status-CLOSED">' . JText::_('CLOSED') . '<span>' ;
							break;
						}
					?>					
					
					<div id="steps">
					<?php echo $steps; ?>
					</div>
					
					<h2 class="issue-title">#<?php echo $this->item->id . ' ' . $this->item->title;?></h2>
					<div id="issue-general-info">
						
						<span class="strong"><?php echo JText::_('ADDRESS');?></span><?php echo $this->item->address;?><br />
						<span class="strong"><?php echo JText::_('REPORTED_BY');?></span><?php echo $this->item->username . ' ' . $this->item->reported_rel;?><br />
						<span class="strong"><?php echo JText::_('VIEWED');?></span><?php echo $this->item->hits;?><br />
						

						<span class="strong"><?php echo JText::_('ISSUE_STATUS');?></span><span class="status-<?php echo $status;?>"><?php echo JText::_($status);?></span>						
						<p>
						<?php 
							if($this->item->closed_rel != ''){
								echo ' ' . JText::_('SET_CLOSED') . ' ' . $this->item->closed_rel;
							}
							else if($this->item->acknowledged_rel != ''){
								echo ' ' . JText::_('SET_ACKNOWLEDGED') . ' ' . $this->item->acknowledged_rel;
							}						
						?>
						</p>
						<span class="votes"><?php echo JText::_('ISSUE_VOTES');?><span class="votes-counter"><?php echo $this->item->votes;?></span></span>						
						
						<?php if($this->item->currentstatus != 3) { ?>
							
							<?php /*
							<!--
								<span class="new-vote"><a href="<?php echo JRoute::_('index.php?option=com_improvemycity&controller=improvemycity&task=addVote&issue_id='.$this->item->id);?>"><?php echo JText::_('NEW_VOTE');?></a></span>
								<div id="addVote"><?php echo JText::_('NEW_VOTE');?></div>
							-->
							*/ ?>
							
							<?php if(!$this->guest) {?>
								<?php if(!$this->hasVoted) {?>
									<span class="new-vote"><a href="javascript:vote(<?php echo $this->item->id; ?>, '<?php echo JUtility::getToken(); ?>');"><?php echo JText::_('NEW_VOTE');?></a></span>							
								<?php } else { //already voted ?>
									<span class="new-vote"><a href="javascript:alert('<?php echo JText::_('ALREADY_VOTED'); ?>');"><?php echo JText::_('NEW_VOTE');?></a></span>							
								<?php }?>
							<?php } else { //not logged?>
								
								<span class="new-vote"><a href="javascript:alert('<?php echo JText::_('ONLY_LOGGED_VOTE');?>');"><?php echo JText::_('NEW_VOTE');?></a></span>
								
								<p class="box-hint vote-note"><small>
								<?php echo JText::_('ONLY_LOGGED_VOTE');?>
								<?php $return = base64_encode(JRoute::_('index.php?option=com_improvemycity&view=issue&issue_id='.$this->item->id)); ?>
								<a class="colorbox" href="index.php?option=com_users&view=login&tmpl=component&return=<?php echo $return; ?>"><?php echo JText::_('PLEASE_LOG');?></a>
								</small></p>
							<?php }?>
							
						<?php } else { echo '<div class="box-info">' . JText::_('CANNOT_VOTE_ON_CLOSED') . '</div>';}?>
					</div>
					<div style="clear: both;"></div>
					<h3><?php echo JText::_('DESCRIPTION'); ?></h3>
					<div id="issue-description">
						<?php if($this->item->photo != '') : ?>
							<div class="img-wrp"><img src="<?php echo preg_replace('/thumbs\//', '', $this->item->photo, 1);?>" /></div>
						<?php endif; ?>
						<?php echo $this->item->description;?>
					</div>
					<div style="clear: both;"></div>

					<h3><?php echo JText::_('COMMENTS'); ?></h3>
					<div id="comments-wrapper">
					<?php if(!empty($this->discussion)):?>
						<?php foreach ($this->discussion as $item) : ?>
							<div class="chat">
								<span class="chat-info"><?php echo JText::_('COMMENT_REPORTED') . ' ' . $item->progressdate_rel . ' ' .JText::_('BY') .' ' . $item->username; ?></span>
								<span class="chat-desc"><?php echo $item->description;?></span>
							</div>
						<?php endforeach;?>
					<?php endif;?>
					</div>
					
					<?php if($this->item->currentstatus != 3) { ?>
					<div id="new_comment_wrapper">
						<textarea id="comment_area" style="max-height: 200px; min-height: 65px; max-width: 100%; min-width: 100%; width: 100%;"></textarea>
						
						<?php if(!$this->guest) {?>
							<div id="add-comment"><a href="javascript:comment(<?php echo $this->item->id; ?>, '<?php echo JUtility::getToken(); ?>');"><?php echo JText::_('ADD_COMMENT');?></a></div>
						<?php } else { //not logged?>
							<div id="add-comment"><a href="javascript:alert('<?php echo JText::_('ONLY_LOGGED_COMMENT');?>');"><?php echo JText::_('ADD_COMMENT');?></a></div>

						<?php }?>
						
					</div>
					<?php } else { 
						echo '<div class="box-warning">' . JText::_('CANNOT_COMMENT_ON_CLOSED') . '</div>';}
					?>
						
					</div>
				</div>
			</div>
			<div id="details-sidebar">			
				<div id="mapCanvas"><?php echo JText::_('COM_IMPROVEMYCITY');?></div>
			</div>
		</article>
	</div>
</div>			