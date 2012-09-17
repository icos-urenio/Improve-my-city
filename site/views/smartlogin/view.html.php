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

jimport('joomla.application.component.view');

/**
 * HTML View class for the Improvemycity component
 */
class ImprovemycityViewSmartlogin extends JView
{
	
	function display($tpl = null)
	{
		//$app		= JFactory::getApplication();
		//$this->params		= $app->getParams();
	
        parent::display($tpl);
		// Set the document
		//$this->setDocument();
	}
}
