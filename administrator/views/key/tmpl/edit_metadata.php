<?php
/**
 * @version     2.5.x
 * @package     com_improvemycity
 * @copyright   Copyright (C) 2011 - 2012 URENIO Research Unit. All rights reserved.
 * @license     GNU Affero General Public License version 3 or later; see LICENSE.txt
 * @author      Ioannis Tsampoulatidis for the URENIO Research Unit
 */
 
defined('_JEXEC') or die;
?>
<ul class="adminformlist">
	<li><?php echo $this->form->getLabel('metadesc'); ?>
	<?php echo $this->form->getInput('metadesc'); ?></li>

	<li><?php echo $this->form->getLabel('metakey'); ?>
	<?php echo $this->form->getInput('metakey'); ?></li>

	<?php foreach($this->form->getGroup('metadata') as $field): ?>
		<?php if ($field->hidden): ?>
			<li><?php echo $field->input; ?></li>
		<?php else: ?>
			<li><?php echo $field->label; ?>
			<?php echo $field->input; ?></li>
		<?php endif; ?>
	<?php endforeach; ?>
</ul>
