<?php
/**
 * @version     2.0
 * @package     com_improvemycity
 * @copyright   Copyright (C) 2011 - 2012 URENIO Research Unit. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      URENIO Research Unit
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Issue controller class.
 */
class ImprovemycityControllerComment extends JControllerForm
{

    function __construct() {
        $this->view_list = 'comments';
        parent::__construct();
		
    }

}