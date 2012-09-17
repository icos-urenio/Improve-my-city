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

jimport('joomla.application.component.modelitem');
jimport('joomla.application.component.helper');
jimport('joomla.application.categories');

/**
 * Model
 */
class ImprovemycityModelIssue extends JModelItem
{
	/**
	 * Model context string.
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_context = 'com_improvemycity.issue';

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication();
		$params	= $app->getParams();

		// Load the object state.
		$id	= JRequest::getInt('issue_id');
		$this->setState('improvemycity.id', $id);

		// Load the parameters.
		$this->setState('params', $params);
	}
	

	function &getItem($id = null)
	{
		if (!isset($this->_item))
		{

			if ($this->_item === null) {
				if (empty($id)) {
					$id = $this->getState('improvemycity.id');
				}				

				$db		= $this->getDbo();
				$query	= $db->getQuery(true);
				$query->select(
					'a.*'
					);
				$query->from('#__improvemycity as a');
				$query->where('a.id = ' . (int) $id);

				// Join on user table.
				$query->select('u.name AS fullname');
				$query->join('LEFT', '#__users AS u on u.id = a.userid');	

				// Join on catid table.
				$query->select('c.title AS catname');
				$query->join('LEFT', '#__categories AS c on c.id = a.catid');	

				
				$db->setQuery((string) $query);

				if (!$db->query()) {
					JError::raiseError(500, $db->getErrorMsg());
				}

				$this->_item = $db->loadObject();
				
			}
		}
		if ($this->_item != null){
			//also get the discussion for that record as well
			$model_discussions = JModel::getInstance('Discussions', 'ImprovemycityModel');
			$this->_item->discussion = $model_discussions->getItems($this->_item->id);
				
			$this->_item->reported_rel = ImprovemycityHelper::getRelativeTime($this->_item->reported);
			$this->_item->acknowledged_rel = ImprovemycityHelper::getRelativeTime($this->_item->acknowledged);
			$this->_item->closed_rel = ImprovemycityHelper::getRelativeTime($this->_item->closed);
		}
		return $this->_item;
	}	
	
	
	
	public function hit($pk = 0)
	{
		$pk = (!empty($pk)) ? $pk : (int) $id = $this->getState('improvemycity.id');
		$db = $this->getDbo();

		$db->setQuery(
				'UPDATE #__improvemycity' .
				' SET hits = hits + 1' .
				' WHERE id = '.(int) $pk
		);

		if (!$db->query()) {
				$this->setError($db->getErrorMsg());
				return false;
		}
        
		return true;
	}	
	
	public function vote($pk = 0, $userid = null)
	{
		
		$pk = (!empty($pk)) ? $pk : (int) $id = $this->getState('improvemycity.id');
		$db = $this->getDbo();

		$db->setQuery(
				'UPDATE #__improvemycity' .
				' SET votes = votes + 1' .
				' WHERE id = '.(int) $pk
		);

		if (!$db->query()) {
				$this->setError($db->getErrorMsg());
				return -1;
		}
        
		if($userid == null){
			$user =& JFactory::getUser();
			$userid = (int) $user->id;
		} 
		
		$db->setQuery(
				'INSERT INTO #__improvemycity_votes ( improvemycityid, userid)' .
				' VALUES ( '.(int) $pk.', '. (int) $userid.')'
		);

		if (!$db->query()) {
				$this->setError($db->getErrorMsg());
				return -1;
		}		
		
		//return new vote counter
		$query = 'SELECT votes FROM #__improvemycity WHERE id = ' . (int) $pk;
		$db->setQuery( $query );
		$votes = $db->loadResult();		
		
		return $votes;
	}	

	public function getHasVoted($pk = 0, $userid = null)
	{
		
		$pk = (!empty($pk)) ? $pk : (int) $id = $this->getState('improvemycity.id');
		$db = $this->getDbo();	
		
		if($userid == null){
			$user =& JFactory::getUser();
			$userid = (int) $user->id;
		}
				
		$query	= $db->getQuery(true);
		$query->select('COUNT(*)');
		$query->from('`#__improvemycity_votes` AS a');		
		$query->where('a.userid = '.(int) $userid.' AND a.improvemycityid='.(int) $pk);
		$db->setQuery( $query );
		$results = $db->loadResult();
	
		return $results;
	}
	
	public function getCategoryIcon($pk = 0)
	{
		$pk = (!empty($pk)) ? $pk : (int) $id = $this->getState('improvemycity.id');
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		$query->select('a.catid');
		$query->from('#__improvemycity as a');
		$query->where('a.id = ' . (int) $id);
		// Join on catid table.
		$query->select('c.params AS params');
		$query->join('LEFT', '#__categories AS c on c.id = a.catid');	
		
		$db->setQuery($query);
		//$result = $db->loadResult();
		$row = $db->loadAssoc();

		$parameters = new JRegistry();
		$parameters->loadJSON($row['params']);
		$image = $parameters->get('image');		

		return $image;
	}
}

