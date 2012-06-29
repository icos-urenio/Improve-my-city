<?php
/**
 * @version     2.0
 * @package     com_improvemycity
 * @copyright   Copyright (C) 2011 - 2012 URENIO Research Unit. All rights reserved.
 * @license     GNU Affero General Public License version 3 or later; see LICENSE.txt
 * @author      URENIO Research Unit
 */

// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');

echo 'Total: ' . count($this->items);
echo '<ul>';
foreach($this->items as $item){
	echo '<li>';
	echo $item->id . ' - ' . $item->title;
	echo '</li>'; 
}
echo '</ul>';
?>

