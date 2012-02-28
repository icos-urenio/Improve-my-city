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

jimport('joomla.application.component.view');

/**
 * View to edit
 */
class ImprovemycityViewItem extends JView
{
	protected $state;
	protected $item;
	protected $form;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		parent::display($tpl);
		
		// Set the document
		$this->setDocument();		
	}

	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		JRequest::setVar('hidemainmenu', true);

		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
        if (isset($this->item->checked_out)) {
		    $checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
        } else {
            $checkedOut = false;
        }
		$canDo		= ImprovemycityHelper::getActions();

		JToolBarHelper::title(JText::_('COM_IMPROVEMYCITY_TITLE_ITEM'), 'item.png');

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit')||($canDo->get('core.create'))))
		{

			JToolBarHelper::apply('item.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('item.save', 'JTOOLBAR_SAVE');
		}
		if (!$checkedOut && ($canDo->get('core.create'))){
			JToolBarHelper::custom('item.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		}
		// If an existing item, can save to a copy.
		/*
		if (!$isNew && $canDo->get('core.create')) {
			JToolBarHelper::custom('item.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}
		*/
		if (empty($this->item->id)) {
			JToolBarHelper::cancel('item.cancel', 'JTOOLBAR_CANCEL');
		}
		else {
			JToolBarHelper::cancel('item.cancel', 'JTOOLBAR_CLOSE');
		}

	}
	
	protected function setDocument() 
	{
		$isNew = $this->item->id == 0;
		$document = JFactory::getDocument();
		$document->setTitle($isNew ? JText::_('COM_IMPROVEMYCITY_IMPROVEMYCITY_CREATING') : JText::_('COM_IMPROVEMYCITY_IMPROVEMYCITY_EDITING'));
		
		
		$document->addScript("http://maps.google.com/maps/api/js?sensor=false&language=el&region=GR");

		$LAT = $this->form->getValue('latitude');
		$LON = $this->form->getValue('longitude');
		if($isNew || $LAT == '' || $LON == ''){
			$LAT = '40.54629751976399';
			$LON = '23.01861169311519';
		}
		
		$googleMapInit = "
			var geocoder = new google.maps.Geocoder();
			var map;
			var marker;
			
			function zoomIn() {
				map.setCenter(marker.getPosition());
				map.setZoom(map.getZoom()+1);
			}

			function zoomOut() {
				map.setCenter(marker.getPosition());
				map.setZoom(map.getZoom()-1);
			}
			
			
			function codeAddress() {
				var address = document.getElementById('address').value + ' Θέρμη 57001';
				geocoder.geocode( { 'address': address, 'language': 'el'}, function(results, status) {
				  if (status == google.maps.GeocoderStatus.OK) {
					map.setCenter(results[0].geometry.location);
					marker.setPosition(results[0].geometry.location);
					
					document.getElementById('jform_latitude').value = results[0].geometry.location.lat();
					document.getElementById('jform_longitude').value = results[0].geometry.location.lng();					
					
					updateMarkerAddress(results[0].formatted_address);			

				  } else {
					alert('Η διεύθυνση δε μπορεί να βρεθεί. Status: ' + status);
				  }
				});		
			}
			
			
			function geocodePosition(pos) {
			  geocoder.geocode({
				latLng: pos,
				language: 'el'
			  }, function(responses) {
				if (responses && responses.length > 0) {
				  updateMarkerAddress(responses[0].formatted_address);
				} else {
				  updateMarkerAddress('Δεν μπορεί να εντοπιστεί η διεύθυνση.');
				}
			  });
			}

			//function updateMarkerStatus(str) {
			//  document.getElementById('markerStatus').innerHTML = str;
			//}

			function updateMarkerPosition(latLng) {
			  document.getElementById('info').innerHTML = [
				latLng.lat(),
				latLng.lng()
			  ].join(', ');
			  //update fields
			  document.getElementById('jform_latitude').value = latLng.lat();
			  document.getElementById('jform_longitude').value = latLng.lng();
			}

			function updateMarkerAddress(str) {
			  document.getElementById('near_address').innerHTML = str;
			  document.getElementById('jform_address').value = str;
			}

			
			function initialize() {
			  var LAT = ".$LAT.";
			  var LON = ".$LON.";

			  var latLng = new google.maps.LatLng(LAT, LON);
			  map = new google.maps.Map(document.getElementById('mapCanvas'), {
				zoom: 17,
				center: latLng,
				panControl: false,
				streetViewControl: false,
				zoomControlOptions: {
					style: google.maps.ZoomControlStyle.SMALL
				},
				mapTypeId: google.maps.MapTypeId.ROADMAP
			  });
			  
			  marker = new google.maps.Marker({
				position: latLng,
				title: 'Το σημείο για το οποίο γίνεται η αναφορά',
				map: map,
				draggable: true
			  });
			  
			  var infoString = 'Σύρετε τον κόκκινο δείκτη για να<br />βελτιώσετε τη γεωγραφική θέση';
			  
				
			  var infowindow = new google.maps.InfoWindow({
				content: infoString
			  });
			  
			  
			  // Update current position info.
			  updateMarkerPosition(latLng);
			  geocodePosition(latLng);
			  
			  // Add dragging event listeners.
			  google.maps.event.addListener(marker, 'dragstart', function() {
				infowindow.close();
				updateMarkerAddress('Μετακίνηση...');
			  });
			  
			  google.maps.event.addListener(marker, 'drag', function() {
				//updateMarkerStatus('Μετακίνηση...');
				updateMarkerPosition(marker.getPosition());
			  });
			  
			  google.maps.event.addListener(marker, 'dragend', function() {
				//updateMarkerStatus('Drag ended');
				infowindow.open(map, marker);
				geocodePosition(marker.getPosition());
			  });
			  
		  
			  
			  infowindow.open(map, marker);
			}

			// Onload handler to fire off the app.
			google.maps.event.addDomListener(window, 'load', initialize);
		";
		
		//add the javascript to the head of the html document
		$document->addScriptDeclaration($googleMapInit);
	
		JText::script('COM_IMPROVEMYCITY_IMPROVEMYCITY_ERROR_UNACCEPTABLE');
	}	
	
}
