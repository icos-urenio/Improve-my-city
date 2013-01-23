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
 * View to edit
 */
class ImprovemycityViewIssue extends JView
{
	protected $state;
	protected $item;
	protected $form;
	
	protected $language = '';
	protected $region = '';
	protected $lat = '';
	protected $lon = '';
	protected $searchterm = '';
	protected $issuer;
	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');

		$lang = $this->state->params->get('maplanguage');
		$region = $this->state->params->get('mapregion');
		$lat = $this->state->params->get('latitude');
		$lon = $this->state->params->get('longitude');
		$term = $this->state->params->get('searchterm');
		
		$this->language = (empty($lang) ? "en" : $lang);
		$this->region = (empty($region) ? "GB" : $region);
		$this->lat = (empty($lat) ? 40.54629751976399 : $lat);
		$this->lon = (empty($lon) ? 23.01861169311519 : $lon);
		$this->searchterm = (empty($term) ? "" : $term);

		$this->issuer = &JFactory::getUser($this->item->userid);
		

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

			JToolBarHelper::apply('issue.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('issue.save', 'JTOOLBAR_SAVE');
		}
		if (!$checkedOut && ($canDo->get('core.create'))){
			JToolBarHelper::custom('issue.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		}
		// If an existing item, can save to a copy.
		/*
		if (!$isNew && $canDo->get('core.create')) {
			JToolBarHelper::custom('issue.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}
		*/
		if (empty($this->item->id)) {
			JToolBarHelper::cancel('issue.cancel', 'JTOOLBAR_CANCEL');
		}
		else {
			JToolBarHelper::cancel('issue.cancel', 'JTOOLBAR_CLOSE');
		}

	}
	
	protected function setDocument() 
	{
		$isNew = $this->item->id == 0;
		$document = JFactory::getDocument();
		$document->setTitle($isNew ? JText::_('COM_IMPROVEMYCITY_IMPROVEMYCITY_CREATING') : JText::_('COM_IMPROVEMYCITY_IMPROVEMYCITY_EDITING'));
		
		$document->addScript("https://maps.google.com/maps/api/js?sensor=false&language=".$this->language."&region=" . $this->region);

		$LAT = $this->form->getValue('latitude');
		$LON = $this->form->getValue('longitude');
		if($isNew || $LAT == '' || $LON == ''){
			$LAT = $this->lat;
			$LON = $this->lon;
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
				var address = document.getElementById('address').value + ' ".$this->searchterm."';
				geocoder.geocode( { 'address': address, 'language': '".$this->language."'}, function(results, status) {
				  if (status == google.maps.GeocoderStatus.OK) {
					map.setCenter(results[0].geometry.location);
					marker.setPosition(results[0].geometry.location);
					
					document.getElementById('jform_latitude').value = results[0].geometry.location.lat();
					document.getElementById('jform_longitude').value = results[0].geometry.location.lng();					
					
					updateMarkerAddress(results[0].formatted_address);			

				  } else {
					alert('".JText::_('COM_IMPROVEMYCITY_ADDRESS_NOT_FOUND')."');
				  }
				});		
			}
			
			
			function geocodePosition(pos) {
			  geocoder.geocode({
				latLng: pos,
				language: '".$this->language."'
			  }, function(responses) {
				if (responses && responses.length > 0) {
				  updateMarkerAddress(responses[0].formatted_address);
				} else {
				  updateMarkerAddress('".JText::_('COM_IMPROVEMYCITY_ADDRESS_NOT_FOUND')."');
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
				title: '".JText::_('COM_IMPROVEMYCITY_REPORT_LOCATION')."',
				map: map,
				draggable: true
			  });
			  
			  
			  var infoString = '".JText::_('COM_IMPROVEMYCITY_DRAG_MARKER')."';
				
			  var infowindow = new google.maps.InfoWindow({
				content: infoString
			  });
			  
			  
			  // Update current position info.
			  updateMarkerPosition(latLng);
			  geocodePosition(latLng);
			  
			  // Add dragging event listeners.
			  google.maps.event.addListener(marker, 'dragstart', function() {
				infowindow.close();
				updateMarkerAddress('".JText::_('COM_IMPROVEMYCITY_MOVING')."');
			  });
			  
			  google.maps.event.addListener(marker, 'drag', function() {
				updateMarkerPosition(marker.getPosition());
			  });
			  
			  google.maps.event.addListener(marker, 'dragend', function() {
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
	
	}	
	
}
