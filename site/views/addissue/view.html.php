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
 * HTML View class for the Improvemycity component
 */
class ImprovemycityViewAddissue extends JView
{
	protected $state;
	protected $item;
	protected $form;
	protected $params;
	protected $return_page;
	protected $pageclass_sfx;
	protected $guest;

	
	function display($tpl = null)
	{
		$app		= JFactory::getApplication();
		$this->params		= $app->getParams();
		
		//remove || from title
		$strip_title = $this->params->get('page_title');
		$strip_title = str_replace('||', '', $strip_title);
		$this->params->set('page_title', $strip_title);
		
		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

		//check if user is logged
		$user =& JFactory::getUser();
		$this->guest = $user->guest;
		
		
		// Get some data from the models
		$this->state = $this->get('State');
		$this->form	= $this->get('Form');
		$this->return_page = $this->get('ReturnPage');
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		
        parent::display($tpl);
		
		// Set the document
		$this->setDocument();
		
		
		
	}
	
	protected function setDocument() 
	{
		
		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::root(true).'/components/com_improvemycity/js/colorbox/css/colorbox.css');
		$document->addStyleSheet(JURI::root(true).'/components/com_improvemycity/css/improvemycity.css');	

		//add jquery
		$document->addScript(JURI::root(true).'/components/com_improvemycity/js/jquery-1.5.2.min.js');
		$document->addScript(JURI::root(true).'/components/com_improvemycity/js/jquery-ui.min.js');
		
		
		
		$document->addScript(JURI::root(true) . "/components/com_improvemycity/js/colorbox/jquery.colorbox-min.js");
		$document->addScript(JURI::root(true).'/components/com_improvemycity/js/improvemycity.js');	
		//$document->addScript(JURI::root(true).'/components/com_improvemycity/js/comments.js');	

		
		//add google maps
		$document->addScript("http://maps.google.com/maps/api/js?sensor=false&language=el&region=GR");

			$LAT = '40.54629751976399';
			$LON = '23.01861169311519';
	
		
		
		$googleMapInit = "
			var geocoder = new google.maps.Geocoder();
			var map;
			var marker;
			
			function blink() {
				var moo = $(\"#jform_address\").effect(\"highlight\", {color: '#60FF05'}, 2000);
			}
			
			function zoomIn() {
				map.setCenter(marker.getPosition());
				map.setZoom(map.getZoom()+1);
			}

			function zoomOut() {
				map.setCenter(marker.getPosition());
				map.setZoom(map.getZoom()-1);
			}
			
			
			function codeAddress() {
				var address = document.getElementById('jform_address').value + ' Θέρμη 57001';
				geocoder.geocode( { 'address': address, 'language': 'el'}, function(results, status) {
				  if (status == google.maps.GeocoderStatus.OK) {
					map.setCenter(results[0].geometry.location);
					marker.setPosition(results[0].geometry.location);
					
					if(true){	//check linker checkbox here
						document.getElementById('jform_latitude').value = results[0].geometry.location.lat();
						document.getElementById('jform_longitude').value = results[0].geometry.location.lng();					
					}
					
					updateMarkerAddress(results[0].formatted_address);			

				  } else {
					alert('Δεν μπορεί να εντοπιστεί η διεύθυνση. Status: ' + status);
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
			  //document.getElementById('info').innerHTML = [
				//latLng.lat(),
				//latLng.lng()
			  //].join(', ');
			  //update fields
			  document.getElementById('jform_latitude').value = latLng.lat();
			  document.getElementById('jform_longitude').value = latLng.lng();
			}

			function updateMarkerAddress(str) {
			  //document.getElementById('near_address').innerHTML = str;
			  document.getElementById('jform_address').value = str;
			}

			
			function initialize() {
			  var LAT = ".$LAT.";
			  var LON = ".$LON.";

			  var latLng = new google.maps.LatLng(LAT, LON);
			  map = new google.maps.Map(document.getElementById('mapCanvasNew'), {
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
			  
			  var infoString = 'Σύρετε τον <span style=\"color: red;\">κόκκινο</span> δείκτη για να<br />βελτιώσετε τη γεωγραφική θέση';
			  
				
			  var infowindow = new google.maps.InfoWindow({
				content: infoString
			  });
			  
			  
			  // Update current position info.
			  updateMarkerPosition(latLng);
			  geocodePosition(latLng);
			  
			  // Add dragging event listeners.
			  google.maps.event.addListener(marker, 'dragstart', function() {
				infowindow.close();
				//updateMarkerAddress('Μετακίνηση...');
			  });
			  
			  google.maps.event.addListener(marker, 'drag', function() {
				//updateMarkerStatus('Μετακίνηση...');
				//updateMarkerPosition(marker.getPosition());
			  });
			  
			  google.maps.event.addListener(marker, 'dragend', function() {
				//updateMarkerStatus('Drag ended');
				updateMarkerPosition(marker.getPosition());
				infowindow.open(map, marker);
				geocodePosition(marker.getPosition());
				blink();
			  });
			  
		  
			  
			  infowindow.open(map, marker);
			}

			// Onload handler to fire off the app.
			google.maps.event.addDomListener(window, 'load', initialize);
			
		";

		//add the javascript to the head of the html document
		$document->addScriptDeclaration($googleMapInit);
	
	
		$f = "
		Joomla.submitbutton = function(task) {
			if (task == 'issue.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
				
				Joomla.submitform(task);
			}
			//else {
			//	alert('failed');
			//}
		}
		";
		$document->addScriptDeclaration($f);	
	
	
	}
	
	
}