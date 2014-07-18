<?php
/**
 * @version     2.5.x
 * @package     com_improvemycity
 * @copyright   Copyright (C) 2011 - 2012 URENIO Research Unit. All rights reserved.
 * @license     GNU Affero General Public License version 3 or later; see LICENSE.txt
 * @author      Ioannis Tsampoulatidis for the URENIO Research Unit
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Improvemycity records.
 */
class ImprovemycityModelReports extends JModelList
{

    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
				'id', 'a.id',
				'description', 'a.description',      
				'state', 'a.state',
				'improvemycityid', 'a.improvemycityid',
				'catid', 'a.catid',
                                'currentstatus', 'a.currentstatus'
                );
        }

        parent::__construct($config);
    }


	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		//no need since query selects only of status=1
                //$published = $app->getUserStateFromRequest($this->context.'.filter.state', 'filter_published', '', 'string');
		//$this->setState('filter.state', $published);
                
                $currentstatus = $app->getUserStateFromRequest($this->context.'.filter.currentstatus', 'filter_currentstatus');
                $this->setState('filter.currentstatus', $currentstatus);
                
		$categoryId = $this->getUserStateFromRequest($this->context.'.filter.category_id', 'filter_category_id');
		$this->setState('filter.category_id', $categoryId);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_improvemycity');
		$this->setState('params', $params);
		// List state information.
		parent::populateState('a.id', 'desc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 * @return	string		A store id.
	 * @since	1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id.= ':' . $this->getState('filter.search');
		$id.= ':' . $this->getState('filter.currentstatus');
		$id.= ':'.$this->getState('filter.category_id');
		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.*, #__categories.title AS category,catid, #__users.name AS username'
			)
		);
		
		$query->from('#__improvemycity AS a');
		$query->leftJoin('#__categories on a.catid=#__categories.id');
		$query->leftJoin('#__users on a.userid=#__users.id');
		$query->where('a.state = 1');
	

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->getEscaped($search, true).'%');
                $query->where('(a.description LIKE '.$search.' OR a.title LIKE '.$search.')');
			}
		}

                
		// Filter by category
		$categoryId = $this->getState('filter.category_id');
		if (is_numeric($categoryId)) {
			$query->where('a.catid IN ('.$categoryId.')');
		}
		
		// Filter by currentstatus
		$currentstatus = $this->getState('filter.currentstatus');
		if (is_numeric($currentstatus)) {
			$query->where('a.currentstatus = '.$currentstatus);
		}                
                
		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction', 'desc');
                if ($orderCol && $orderDirn) {
                            $query->order($db->getEscaped($orderCol.' '.$orderDirn));
                }

		return $query;
	}
}
