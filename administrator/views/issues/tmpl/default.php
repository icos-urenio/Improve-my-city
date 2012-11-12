<?php
/**
 * @version     2.5.x
 * @package     com_improvemycity
 * @copyright   Copyright (C) 2011 - 2012 URENIO Research Unit. All rights reserved.
 * @license     GNU Affero General Public License version 3 or later; see LICENSE.txt
 * @author      URENIO Research Unit
 */


// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHTML::_('script','system/multiselect.js',false,true);
$user	= JFactory::getUser();
$userId	= $user->get('id');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$canOrder	= $user->authorise('core.edit.state', 'com_improvemycity');
$saveOrder	= $listOrder == 'a.ordering';
?>

<form action="<?php echo JRoute::_('index.php?option=com_improvemycity&view=issues'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('Search'); ?>" />
			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="filter-select fltrt">
			<select name="filter_published" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true);?>
			</select>
			<select name="filter_category_id" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_improvemycity'), 'value', 'text', $this->state->get('filter.category_id'));?>
			</select>			

		</div>
	</fieldset>
	<div class="clr"> </div>
	<?php if(empty($this->items)) {echo '<strong>'.JText::_('COM_IMPROVEMYCITY_NO_ISSUES_YET').'</strong>'; }?>

	<table class="adminlist">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
				</th>

                <?php if (isset($this->items[0]->ordering)) { ?>
				<th width="10%">
					<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ORDERING', 'a.ordering', $listDirn, $listOrder); ?>
					<?php if ($canOrder && $saveOrder) :?>
						<?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'issues.saveorder'); ?>
					<?php endif; ?>
				</th>
                <?php } ?>
                <?php if (isset($this->items[0]->id)) { ?>
                <th width="1%" class="nowrap">
                    <?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                </th>
                <?php } ?>
                <?php if (isset($this->items[0]->title)) { ?>
                <th width="54%" class="nowrap">
                    <?php echo JHtml::_('grid.sort',  'COM_IMPROVEMYCITY_IMPROVEMYCITY_HEADING_TITLE', 'a.title', $listDirn, $listOrder); ?>
                </th>
                <?php } ?>
                <?php if (isset($this->items[0]->state)) { ?>
				<th width="5%">
					<?php echo JHtml::_('grid.sort',  'JPUBLISHED', 'a.state', $listDirn, $listOrder); ?>
				</th>
                <?php } ?>
                <?php if (isset($this->items[0]->catid)) { ?>
                <th width="30%" class="nowrap">
                    <?php echo JHtml::_('grid.sort',  'COM_IMPROVEMYCITY_IMPROVEMYCITY_HEADING_CATEGORY', 'a.catid', $listDirn, $listOrder); ?>
                </th>
                <?php } ?>					
                <?php if (isset($this->items[0]->currentstatus)) { ?>
                <th width="30%" class="nowrap">
                    <?php echo JHtml::_('grid.sort',  'COM_IMPROVEMYCITY_IMPROVEMYCITY_HEADING_CURRENTSTATUS', 'a.currentstatus', $listDirn, $listOrder); ?>
                </th>
                <?php } ?>					
				
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="10">
					<?php echo $this->pagination->getListFooter(); ?>
					<?php echo '<br /><br />'.$this->state->get('params')->get('version'); ?> 
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) :
			$ordering	= ($listOrder == 'a.ordering');
			$canCreate	= $user->authorise('core.create',		'com_improvemycity');
			$canEdit	= $user->authorise('core.edit',			'com_improvemycity');
			$canCheckin	= $user->authorise('core.manage',		'com_improvemycity');
			$canChange	= $user->authorise('core.edit.state',	'com_improvemycity');
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>



                <?php if (isset($this->items[0]->ordering)) { ?>
				    <td class="order">
					    <?php if ($canChange) : ?>
						    <?php if ($saveOrder) :?>
							    <?php if ($listDirn == 'asc') : ?>
								    <span><?php echo $this->pagination->orderUpIcon($i, true, 'issues.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								    <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'items.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							    <?php elseif ($listDirn == 'desc') : ?>
								    <span><?php echo $this->pagination->orderUpIcon($i, true, 'issues.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								    <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'issues.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							    <?php endif; ?>
						    <?php endif; ?>
						    <?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
						    <input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
					    <?php else : ?>
						    <?php echo $item->ordering; ?>
					    <?php endif; ?>
				    </td>
                <?php } ?>
                <?php if (isset($this->items[0]->id)) { ?>
				<td class="center">
					<?php echo (int) $item->id; ?>
				</td>
                <?php } ?>

               <?php if (isset($this->items[0]->title)) { ?>
				<td>
					<a href="<?php echo JRoute::_('index.php?option=com_improvemycity&task=issue.edit&id=' . $item->id); ?>">
						<?php echo $item->title; ?>
					</a>		
					<br />
				</td>
                <?php } ?>				
                <?php if (isset($this->items[0]->state)) { ?>
				    <td class="center">
					    <?php echo JHtml::_('jgrid.published', $item->state, $i, 'issues.', $canChange, 'cb'); ?>
				    </td>
                <?php } ?>		
                <?php if (isset($this->items[0]->catid)) { ?>
				<td>
					<?php echo '<strong>'.$item->category.'</strong>'; 
						if($item->path == '') echo ' -'; else echo ' ('.$item->path.')';
					?>
					
				</td>
                <?php } ?>		
                <?php if (isset($this->items[0]->currentstatus)) { ?>
				    <td class="center">
					    <?php 
							switch ((int) $item->currentstatus){
								case 1:
									echo JText::_('JOPTION_SELECT_STATUS_OPEN');
								break;
								case 2:
									echo JText::_('JOPTION_SELECT_STATUS_ACK');
								break;
								case 3:
									echo JText::_('JOPTION_SELECT_STATUS_CLOSED');
								break;
							}
						?>
				    </td>
                <?php } ?>					
			</tr>
			<?php endforeach; ?>
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
