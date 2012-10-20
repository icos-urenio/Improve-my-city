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
?>

<div id="imc-wrapper">
	<div id="imc-print-header"><a href="javascript:window.print()"><i class="icon-print"></i> <?php echo JText::_('COM_IMPROVEMYCITY_CLICK_TO_PRINT');?></a></div>
	
	<h1><?php echo JText::_('COM_IMPROVEMYCITY'); ?></h1>
	<h2>#<?php echo $this->item->id . ' ' . $this->item->title;?></h2>
	
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
	
	<div id="imc-steps">
		<?php echo $steps; ?>
	</div>	

	<div id="imc-issue-general-info">
		<span class="strong"><?php echo JText::_('CATEGORY');?></span><span class="desc"><?php echo $this->item->catname;?></span><br />
		<span class="strong"><?php echo JText::_('ADDRESS');?></span><span class="desc"><?php echo $this->item->address;?></span><br />
		<span class="strong"><?php echo JText::_('REPORTED_BY');?></span><span class="desc"><?php echo $this->item->fullname . ' ' . $this->item->reported_rel;?></span><br />
		<span class="strong"><?php echo JText::_('VIEWED');?></span><span class="desc"><?php echo $this->item->hits;?></span><br />
		<span class="strong"><?php echo JText::_('ISSUE_STATUS');?></span><span class="status-<?php echo $status;?>"><?php echo JText::_($status);?></span><br />						
		<span class="strong"><?php echo JText::_('ISSUE_VOTES_DESC');?>: </span><span class="desc"><?php echo $this->item->votes;?></span>		
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
	</div>	
	
	<div id="imc-content">	
		<div id="imc-main-panel">
			
			<h3><?php echo JText::_('DESCRIPTION'); ?></h3>
			<div class="desc"><?php echo $this->item->description;?></div>	
			<?php if($this->item->photo != '') : ?>
				<div class="img-wrp"><img src="<?php echo preg_replace('/thumbs\//', '', $this->item->photo, 1);?>" /></div>
			<?php endif; ?>
			
		</div>
		<div id="imc-details-sidebar">			
			<div id="mapCanvas"><?php echo JText::_('COM_IMPROVEMYCITY');?></div>	
		</div>
		
		<?php if(!empty($this->discussion)):?>
		<div style="clear: both;"></div>
		<div id="imc-comments-wrapper">
			<h3><?php echo JText::_('COMMENTS'); ?></h3>
			<?php foreach ($this->discussion as $item) : ?>
				<div class="imc-chat">
					<span class="imc-chat-info"><?php echo JText::_('COMMENT_REPORTED') . ' ' . $item->progressdate_rel . ' ' .JText::_('BY') .' ' . $item->username; ?></span>
					<span class="imc-chat-desc"><?php echo $item->description;?></span>
				</div>
			<?php endforeach;?>
		</div>			
		<?php endif;?>			
	</div>
	
</div>	
