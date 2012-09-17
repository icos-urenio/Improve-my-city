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

/**
 * Model
 */
class ImprovemycityModelKeys extends JModel
{
	
	function getSecretKey()
	{
		
		// Get a database object
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->select('skey');
		$query->from('#__improvemycity_keys');
		$query->where('id=1');
		
		$db->setQuery($query);
		$result = $db->loadResult();
		return $result;
	}
}
