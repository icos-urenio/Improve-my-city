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

JHtml::_('behavior.tooltip');

$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$saveOrder	= $listOrder == 'a.ordering';
?>


<form action="<?php echo JRoute::_('index.php?option=com_improvemycity&view=reports'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('Search'); ?>" />
		    <div style="display:inline;"><span style="float:left;padding: 7px 5px 0 5px;">FROM:</span><?php echo JHtml::calendar($this->state->get('filter.from'), 'filter_from', 'filter_from');?></div>
            <div style="display:inline;"><span style="float:left;padding: 7px 5px 0 5px;">TO:</span><?php echo JHtml::calendar($this->state->get('filter.to'), 'filter_to', 'filter_to');?></div>
			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="filter-select fltrt">
                        
            <select name="filter_currentstatus" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('COM_IMPROVEMYCITY_SELECT_CURRENTSTATUS');?></option>
                <option <?php echo ($this->escape($this->state->get('filter.currentstatus')) == 1 ?'selected="selected"':'');?> value="1"><?php echo JText::_('JOPTION_SELECT_STATUS_OPEN');?></option>
                <option <?php echo ($this->escape($this->state->get('filter.currentstatus')) == 2 ?'selected="selected"':'');?> value="2"><?php echo JText::_('JOPTION_SELECT_STATUS_ACK');?></option>
                <option <?php echo ($this->escape($this->state->get('filter.currentstatus')) == 3 ?'selected="selected"':'');?> value="3"><?php echo JText::_('JOPTION_SELECT_STATUS_CLOSED');?></option>
			</select>
                     
			<select name="filter_category_id" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_improvemycity'), 'value', 'text', $this->state->get('filter.category_id'));?>
			</select>			

		</div>
		<div class="clr"> </div>
		<div class="statistics">
			<p style="padding-top: 20px; font-weight: bold;">
			<?php echo JText::_('JOPTION_SELECT_STATUS_OPEN');?>: <?php echo $this->statistics['open'] ;?><br />
			<?php echo JText::_('JOPTION_SELECT_STATUS_ACK');?>: <?php echo $this->statistics['ack'] ;?><br />
			<?php echo JText::_('JOPTION_SELECT_STATUS_CLOSED');?>: <?php echo $this->statistics['closed'] ;?><br />
			------------------------------<br />
			<?php echo JText::_('COM_IMPROVEMYCITY_TITLE_ITEMS'); ?>: <?php echo $this->statistics['closed']+$this->statistics['ack']+$this->statistics['open'] ;?><br />			
			</p>
		</div>		
	</fieldset>
	<div class="clr"> </div>
	<?php if(empty($this->items)) {echo '<strong>'.JText::_('COM_IMPROVEMYCITY_NO_ISSUES_YET').'</strong>'; }?>
<table class="adminlist">
  <thead>
    <tr>
      <th><?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?></th>
      <th><?php echo JText::_('COM_IMPROVEMYCITY_IMPROVEMYCITY_FIELD_TITLE_LABEL');?></th>
      <th><?php echo JText::_('COM_IMPROVEMYCITY_IMPROVEMYCITY_FIELD_CATID_LABEL');?></th>
      <th><?php echo JText::_('COM_IMPROVEMYCITY_IMPROVEMYCITY_FIELD_LATITUDE_LABEL');?></th>
      <th><?php echo JText::_('COM_IMPROVEMYCITY_IMPROVEMYCITY_FIELD_LONGITUDE_LABEL');?></th>
      <th><?php echo JText::_('COM_IMPROVEMYCITY_IMPROVEMYCITY_FIELD_ADDRESS_LABEL');?></th>
      <th><?php echo JText::_('COM_IMPROVEMYCITY_IMPROVEMYCITY_FIELD_CURRENTSTATUS_LABEL');?></th>
      
      <th><?php echo JText::_('JOPTION_SELECT_STATUS_OPEN');?></th>
      <th><?php echo JText::_('JOPTION_SELECT_STATUS_ACK');?></th>
      <th><?php echo JText::_('JOPTION_SELECT_STATUS_CLOSED');?></th>
      <th><?php echo JText::_('JGLOBAL_USERNAME');?></th>

    </tr>
  </thead>
  <tbody>
	<?php 
	$i=-1;
	foreach($this->items as $item){
		$i++;$a = $i%2;
		echo '<tr class="row'.$a.'">';
		echo '<td>'.$item->id . '</td>' . "\n";
		echo '<td>'.$item->title . '</td>' . "\n";
		echo '<td>'.$item->category . '</td>' . "\n";
		echo '<td>'.$item->latitude . '</td>' . "\n";
		echo '<td>'.$item->longitude . '</td>' . "\n";
		echo '<td>'.$item->address . '</td>' . "\n";
		switch ($item->currentstatus){
			case 1: 
				echo '<td>'.JText::_('JOPTION_SELECT_STATUS_OPEN').'</td>' . "\n";
			break;
			case 2: 
				echo '<td>'.JText::_('JOPTION_SELECT_STATUS_ACK').'</td>' . "\n";
			break;
			case 3:
				echo '<td>'.JText::_('JOPTION_SELECT_STATUS_CLOSED').'</td>' . "\n";
			break;
		}
		echo '<td>'.$item->reported . '</td>' . "\n";
		echo '<td>'.$item->acknowledged . '</td>' . "\n";
		echo '<td>'.$item->closed . '</td>' . "\n";
		echo '<td>'.$item->username . '</td>' . "\n";
		
		echo '</tr>'; 
		
	}
	?>
        <tr>
                <td colspan="11"><?php echo $this->pagination->getListFooter(); ?></td>
        </tr>
  </tbody>
</table> 

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>        
</form>