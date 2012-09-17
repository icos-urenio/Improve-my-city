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

class ImprovemycityController extends JController
{
	/**
	 * Method to display a view.
	 *
	 * @param	boolean			$cachable	If true, the view output will be cached
	 * @param	array			$urlparams	An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 * @since	1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		require_once JPATH_COMPONENT.'/helpers/improvemycity.php';

		// Load the submenu.
		ImprovemycityHelper::addSubmenu(JRequest::getCmd('view', 'issues'));

		$view = JRequest::getCmd('view', 'issues');
        
		JRequest::setVar('view', $view);
		
		parent::display();

		return $this;
	}
}
