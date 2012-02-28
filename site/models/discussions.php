<?php
/**
 * @version     1.0
 * @package     com_improvemycity
 * @copyright   Copyright (C) 2011 - 2012 URENIO Research Unit. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      URENIO Research Unit
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
jimport('joomla.application.component.helper');
jimport('joomla.application.categories');
jimport('joomla.html.pagination');

JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_improvemycity/tables');

/**
 * Model
 */
class ImprovemycityModelDiscussions extends JModelList
{
	
	protected $items;
	private $issue_id = null;

	
	
	function getItems($issue_id = '')	//usually we don't use arguments but it's really useful for the issues view and model
	{
		
		$this->issue_id = $issue_id;
		
		if($this->issue_id == null || $this->issue_id == ''){
			$this->issue_id = JRequest::getVar('issue_id');
		}
		
		// Invoke the parent getItems method to get the main list
		$items = &parent::getItems();
		//$this->_total = count($items);
		
		
		// Convert the params field into an object, saving original in _params
		for ($i = 0, $n = count($items); $i < $n; $i++) {
			$item = &$items[$i];
			
			//calculate relative dates here
			$item->progressdate_rel = ImprovemycityHelper::getRelativeTime($item->created);
		}
		
		$this->items = $items;
		return $items;	
	}

	protected function getListQuery()
	{
	
		//$user	= JFactory::getUser();
		//$groups	= implode(',', $user->getAuthorisedViewLevels());
		

		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		$query->select(
			$this->getState(
				'list.select',
				'a.*'
			)
		);
		

		$query->from('`#__improvemycity_comments` AS a');
		if($this->issue_id != null)
			$query->where('a.state = 1 AND a.improvemycityid='.$this->issue_id);
		else
			$query->where('a.state = 1');

		// Join on user table.
		$query->select('u.name AS username');
		$query->join('LEFT', '#__users AS u on u.id = a.userid');			

		$query->order('a.created desc');
		
		return $query;
	}	

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	
	protected function populateState($ordering = null, $direction = null)
	{
		
		// Initialise variables.
		$app	= JFactory::getApplication();
		//$params	= JComponentHelper::getParams('com_improvemycity');

 		// List state information
		// $value = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
		$value = $app->getUserStateFromRequest($this->context.'.list.limit', 'limit', 15); //set 15 as default do not use admin configuration...
		$this->setState('list.limit', $value);

		
		$value = $app->getUserStateFromRequest($this->context.'.limitstart', 'limitstart', 0);
		$this->setState('list.start', $value);
	}	
	
	
	public function comment($pk = 0, $userid = 0, $description = '')
	{
		
		$pk = (!empty($pk)) ? $pk : (int) $id = $this->getState('improvemycity.id');
		$db = $this->getDbo();


		$db->setQuery(
				'INSERT INTO #__improvemycity_comments ( improvemycityid, userid, description)' .
				' VALUES ( '.(int) $pk.', '. (int) $userid.', "'.$description.'")'
		);

		if (!$db->query()) {
				$this->setError($db->getErrorMsg());
				return false;
		}		

		
		//return the latest comment so as to be displayed with ajax in the frontend
		$query	= $db->getQuery(true);
		$query->select(
			'a.*'
			);
		$query->from('#__improvemycity_comments as a');
		$query->where('a.improvemycityid = ' . (int) $pk);
		$query->where('a.state = 1');

		// Join on user table.
		$query->select('u.name AS username');
		$query->join('LEFT', '#__users AS u on u.id = a.userid');	
		$query->order('created DESC');
		$db->setQuery((string) $query);
				

		if (!$db->query()) {
				$this->setError($db->getErrorMsg());
				return false;
		}

		//$comments = $db->loadResult();		//return first field of first row
		//$comments = $db->loadAssocList();		//return all rows
		$comments = $db->loadAssoc();			//return first row
		$comments['textual_descr'] = JText::_('COMMENT_REPORTED') . ' ' . ImprovemycityHelper::getRelativeTime($comments['created']) . ' ' . JText::_('BY') . ' ' . $comments['username'];
		return $comments;
	}		
	
}