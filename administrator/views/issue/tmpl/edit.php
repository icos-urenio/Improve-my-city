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
JHtml::_('behavior.formvalidation');
$params = $this->form->getFieldsets('params');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'issue.cancel' || document.formvalidator.isValid(document.id('item-form'))) {
			Joomla.submitform(task, document.getElementById('item-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_improvemycity&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_IMPROVEMYCITY_LEGEND_ITEM'); ?></legend>
			<ul class="adminformlist">
					<?php foreach($this->form->getFieldset('details') as $field): ?>
						
						<li>
							<?php 
							echo $field->label;
							
							if ($field->type == 'Editor'){
								echo '<div style="float:left;">'.$field->input . '</div>';
							}
							else if ($field->type == 'Media'){
								echo $field->input;
								echo '<img style="clear: both;padding-left: 140px;" src="'.JURI::root().$this->form->getValue('photo') . '" height="80" alt="'.JText::_('COM_IMPROVEMYCITY_PHOTO_PREVIEW').'" />';
							}							
							else{
								echo $field->input;
							}
							
							?>
						</li>
					<?php endforeach; ?>
					<li><?php echo $this->issuer->username; ?></li>
					<li><?php echo $this->issuer->name; ?></li>
					<li><?php echo $this->issuer->email; ?></li>
					
					
            </ul>
		</fieldset>
	</div>

	<div class="width-40 fltrt">
		<?php echo JHtml::_('sliders.start', 'improvemycity-slider2'); ?>
		<?php echo JHtml::_('sliders.panel', JText::_('COM_IMPROVEMYCITY_IMPROVEMYCITY_MAP'), 'map');?>
			<div style="width: 100%;height: 400px;" id="mapCanvas"><?php echo JText::_('COM_IMPROVEMYCITY_IMPROVEMYCITY_MAP');?></div>				
			<div id="infoPanel" style="margin: 15px;">
			<b><?php echo JText::_('COM_IMPROVEMYCITY_IMPROVEMYCITY_GEOLOCATION');?></b>
			<div id="info"></div>
			<b><?php echo JText::_('COM_IMPROVEMYCITY_IMPROVEMYCITY_CLOSEST_ADDRESS');?></b>
			<div id="near_address"></div>
			<div id="geolocation">
				<input id="address" type="text" size="75" value="">
				<input style="background-color: #ccc;cursor:pointer;" type="button" value="<?php echo JText::_('COM_IMPROVEMYCITY_IMPROVEMYCITY_LOCATE');?>" onclick="codeAddress()">
			</div>	
			</div>	
				
		<?php foreach ($params as $name => $fieldset): ?>
				<?php echo JHtml::_('sliders.panel', JText::_($fieldset->label), $name.'-params');?>
			<?php if (isset($fieldset->description) && trim($fieldset->description)): ?>
				<p class="tip"><?php echo $this->escape(JText::_($fieldset->description));?></p>
			<?php endif;?>
				<fieldset class="panelform" >
					<ul class="adminformlist">
			<?php foreach ($this->form->getFieldset($name) as $field) : ?>
						<li><?php echo $field->label; ?><?php echo $field->input; ?></li>
			<?php endforeach; ?>
					</ul>
				</fieldset>
		<?php endforeach; ?>

	
		<?php echo JHtml::_('sliders.end'); ?>
	</div>
	<div class="clr"></div>	

	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
	<div class="clr"></div>
</form>
