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

?>


<?php echo 'Total: ' . count($this->items);?>
<table class="adminlist">
  <thead>
    <tr>
      <th>#</th>
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

  </tbody>
</table> 
