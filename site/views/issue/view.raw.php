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
class ImprovemycityViewIssue extends JView
{

	protected $item;
	
	function display($tpl = null)
	{
	
		// Get some data from the models
		$this->item	= $this->get('Item');
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		
        //parent::display($tpl);
		$this->getMarkerAsXML();
		
	}

	protected function getMarkerAsXML()
	{
		JRequest::checkToken('get') or jexit('Invalid Token');
		
		$item = $this->item;

		$dom = new DOMDocument('1.0', 'UTF-8');
		$node = $dom->createElement("markers");
		$parnode = $dom->appendChild($node); 		
		
		$document = &JFactory::getDocument();
		$document->setMimeEncoding('text/xml');
		
		$node = $dom->createElement("marker");  
		$newnode = $parnode->appendChild($node);   
		$newnode->setAttribute("name",$item->title);
		$newnode->setAttribute("description", $item->description);  
		$newnode->setAttribute("lat", $item->latitude);  
		$newnode->setAttribute("lng", $item->longitude);  
		$newnode->setAttribute("catid", $item->catid);
		$newnode->setAttribute("id", $item->id);
		//$newnode->setAttribute("photos", $item->photos);
		
		echo $dom->saveXML();
		
	}	
	
	
}
