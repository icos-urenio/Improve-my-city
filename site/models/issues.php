<?php
/**
 * @version     2.5.x
 * @package     com_improvemycity
 * @copyright   Copyright (C) 2011 - 2012 URENIO Research Unit. All rights reserved.
 * @license     GNU Affero General Public License version 3 or later; see LICENSE.txt
 * @author      Ioannis Tsampoulatidis for the URENIO Research Unit
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
jimport('joomla.application.component.helper');
jimport('joomla.application.categories');


JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_improvemycity/tables');

/**
 * Model
 */
class ImprovemycityModelIssues extends JModelList
{
	//protected $_item;
	private $_categories = null;
	private $_parent = null;
	private $_params = null;
	//public $_context = 'com_improvemycity.issues';	

	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
				'state', 'a.state',
				'ordering', 'a.ordering',
				'hits', 'a.hits',
				'votes', 'a.votes',
				'reported', 'a.reported',
				'currentstatus', 'a.currentstatus'
			);
		}

		parent::__construct($config);
	}	

 	//protected function populateState($ordering = 'ordering', $direction = 'ASC')
 	protected function populateState()
	{
		$app = JFactory::getApplication();
		
		//set filter status in state
		$value = $app->getUserStateFromRequest($this->context.'.filter_status', 'status', array()); 
		$this->setState('filter_status', $value);
		//set filter category in state
		$value = $app->getUserStateFromRequest($this->context.'.filter_category', 'cat', array()); 
		$this->setState('filter_category', $value);
		
		
 		// List state information
		// $value = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
		$value = $app->getUserStateFromRequest($this->context.'.list.limit', 'limit', 10); //set 10 as default do not use admin configuration...
		$this->setState('list.limit', $value);
		
		$value = $app->getUserStateFromRequest($this->context.'.limitstart', 'limitstart', 0);
		//$value = JRequest::getUInt('limitstart', 0);
		$this->setState('list.start', $value);

		//$orderCol	= JRequest::getCmd('filter_order', 'a.ordering'); 		//set default to reported ?? actually is the same as ordering ...
		$orderCol = $app->getUserStateFromRequest($this->context.'.filter_order', 'filter_order', 'a.ordering');
		if (!in_array($orderCol, $this->filter_fields)) {
			$orderCol = 'a.ordering';
		}
		$this->setState('list.ordering', $orderCol);

		//$listOrder	=  JRequest::getCmd('filter_order_Dir', 'DESC');			//set default DESC 
		$listOrder = $app->getUserStateFromRequest($this->context.'.filter_order_Dir', 'filter_order_Dir', 'DESC');		
		if (!in_array(strtoupper($listOrder), array('ASC', 'DESC', ''))) {
			$listOrder = 'DESC';
		}
		$this->setState('list.direction', $listOrder);

		$params = $app->getParams();
		$this->setState('params', $params);
		//TODO: If sometimes need multiple layouts I could use the layout state...
		//$this->setState('layout', JRequest::getCmd('layout'));
	}	
	
	function getCategories($recursive = false)
	{
        $_categories = JCategories::getInstance('Improvemycity');
        $this->_parent = $_categories->get();
        if(is_object($this->_parent))
        {
            $this->_items = $this->_parent->getChildren($recursive);
        }
        else
        {
            $this->_items = false;
        }
        return $this->loadCats($this->_items);
	}
		
	protected function loadCats($cats = array())
    {
        if(is_array($cats))
        {
            $i = 0;
            $return = array();
            foreach($cats as $JCatNode)
            {
                $return[$i]->title = $JCatNode->title;
                $return[$i]->parentid = $JCatNode->parent_id;
                $return[$i]->path = $JCatNode->get('path');
                $return[$i]->id = $JCatNode->id;
				$params = new JRegistry();
				$params->loadJSON($JCatNode->params);
				$return[$i]->image = $params->get('image');

				if($JCatNode->hasChildren())
                    $return[$i]->children = $this->loadCats($JCatNode->getChildren());
                else
                    $return[$i]->children = false;

                $i++;
            }
            return $return;
        }
        return false;
    }
	
	function getItems()
	{
		
		// Invoke the parent getItems method to get the main list
		$items = parent::getItems();
		
		//$this->_total = count($items);

		//I need the discussions model to get discussions for every item...
		//so I get model (discussions) from within another model (issues) ...
		
		//JModel::addIncludePath(JPATH_SITE.'/components/com_improvemycity/models', 'Discussions'); //don't need this we are already inside a model ;)
		
		
		// Convert the params field into an object, saving original in _params
		$model_discussions = JModel::getInstance('Discussions', 'ImprovemycityModel');
		for ($i = 0, $n = count($items); $i < $n; $i++) {
			$item = &$items[$i];
			
			//calculate relative dates here
			$item->reported_rel = ImprovemycityHelper::getRelativeTime($item->reported);
			$item->acknowledged_rel = ImprovemycityHelper::getRelativeTime($item->acknowledged);
			$item->closed_rel = ImprovemycityHelper::getRelativeTime($item->closed);
			
			//TODO: Important: Get this outside for loop and set it to main query. It causes lots of queries (overhead)
			$item->discussion = $model_discussions->getItems($item->id);
			
			if (!isset($this->_params)) {
				$params = new JRegistry();
				$params->loadJSON($item->params);
				$item->params = $params;
			}
		}
		
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
				'a.*, #__categories.title as category, catid, #__categories.path, #__categories.parent_id'
			)
		);
		$query->from('`#__improvemycity` AS a');
		$query->leftJoin('#__categories on catid=#__categories.id');		
		$query->where('a.state = 1');
		
		// Join on user table.
		$query->select('u.name AS fullname');
		$query->join('LEFT', '#__users AS u on u.id = a.userid');
				
		//consider filtering...
		$filter_status = $this->getState('filter_status');
		if(!empty($filter_status)){
			$filter_status = implode(',', $filter_status);
			$query->where('a.currentstatus IN ('.$filter_status.')');
		}
		
		$filter_category = $this->getState('filter_category');
		if(!empty($filter_category)){
			$filter_category = implode(',', $filter_category);
			$query->where('a.catid IN ('.$filter_category.')');
		}

		
		// Add the list ordering clause.
		$query->order($this->getState('list.ordering', 'a.ordering').' '.$this->getState('list.direction', 'ASC'));
		//$query->group('a.id');		
		
		return $query;
	}	
 
 	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.extension');
		$id	.= ':'.$this->getState('filter.published');
		$id	.= ':'.$this->getState('filter.access');
		$id	.= ':'.$this->getState('filter.parentId');

		return parent::getStoreId($id);
	}

	function getTimestamp()
	{
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		
		$query->select('a.*');
		$query->from('`#__improvemycity_timestamp` AS a');
		$query->where('a.id = 1');

		$db->setQuery($query);
		$result = $db->loadRow();		
		
		return $result;		
	}
	
	function getCategoryTimestamp()
	{
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
	
		$query->select('a.*');
		$query->from('`#__improvemycity_timestamp` AS a');
		$query->where('a.id = 2');
	
		$db->setQuery($query);
		$result = $db->loadRow();
	
		return $result;
	}	
	
	function getItemsInBoundaries($x0up = 0, $x0down = 0, $y0up = 0, $y0down = 0, $limit = 0)
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
				
		$query->select(
				$this->getState(
						'list.select',
						'a.*, #__categories.title as category, catid, #__categories.path, #__categories.parent_id'
				)
		);
		$query->from('`#__improvemycity` AS a');
		$query->leftJoin('#__categories on catid=#__categories.id');
		$query->where('a.state = 1');
		$query->where('a.latitude >= '.$y0down);
		$query->where('a.latitude <= '.$y0up);
		$query->where('a.longitude >= '.$x0down);
		$query->where('a.longitude <= '.$x0up);
		
		// Join on user table.
		$query->select('u.name AS fullname');
		$query->join('LEFT', '#__users AS u on u.id = a.userid');
		
		//consider filtering...
		$filter_status = $this->getState('filter_status');
		if(!empty($filter_status)){
			$filter_status = implode(',', $filter_status);
			$query->where('a.currentstatus IN ('.$filter_status.')');
		}
		
		$filter_category = $this->getState('filter_category');
		if(!empty($filter_category)){
			$filter_category = implode(',', $filter_category);
			$query->where('a.catid IN ('.$filter_category.')');
		}
		
		$query->order('id DESC');

		$db->setQuery($query, 0, $limit);
		$result = $db->loadRowList();
		
		return $result;
	}	
	
	function getSimpleCategories()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		
		$query->select('c.id, c.title, c.level, c.parent_id, c.params');
		$query->from('`#__categories` AS c');
		$query->where('c.extension = "com_improvemycity"');
		$query->where('c.published = 1');

		$db->setQuery($query);
		$result = $db->loadRowList();
		
		return $result;
	}
}




