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
class ImprovemycityViewAddissue extends JView
{
	protected $script;
	protected $state;
	protected $item;
	protected $form;
	protected $params;
	protected $return_page;
	protected $pageclass_sfx;
	protected $guest;
	protected $language = '';
	protected $region = '';
	protected $lat = '';
	protected $lon = '';
	protected $searchterm = '';
	protected $zoom;	
	protected $loadjquery;
	protected $loadbootstrap;
	protected $loadbootstrapcss;
	protected $popupmodal;
	protected $showcomments;
	protected $approveissue;
	protected $loadjqueryui;
	
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

		$script = $this->get('Script');
		$this->script = $script;	//validating category (not 0)

		$lang = $this->params->get('maplanguage');
		$region = $this->params->get('mapregion');
		$lat = $this->params->get('latitude');
		$lon = $this->params->get('longitude');
		$term = $this->params->get('searchterm');
		$zoom = $this->params->get('zoom');
		$this->loadjquery = $this->params->get('loadjquery');
		$this->loadbootstrap = $this->params->get('loadbootstrap');
		$this->loadbootstrapcss = $this->params->get('loadbootstrapcss');
		$this->popupmodal = $this->params->get('popupmodal');
		$this->showcomments = $this->params->get('showcomments');
		$this->approveissue = $this->params->get('approveissue');
		$this->loadjqueryui = $this->params->get('loadjqueryui');
		
		$this->language = (empty($lang) ? "en" : $lang);
		$this->region = (empty($region) ? "GB" : $region);
		$this->lat = (empty($lat) ? 40.54629751976399 : $lat);
		$this->lon = (empty($lon) ? 23.01861169311519 : $lon);
		$this->searchterm = (empty($term) ? "" : $term);
		$this->zoom = (empty($zoom) ? 17 : $zoom);
		
				
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
		
		if($this->loadbootstrapcss == 1)
			$document->addStyleSheet(JURI::root(true).'/components/com_improvemycity/bootstrap/css/bootstrap.min.css');					
		
		$document->addStyleSheet(JURI::root(true).'/components/com_improvemycity/css/improvemycity.css');	

		$ie  = '<!--[if lt IE 9]>' . "\n";
		$ie .= '<link rel="stylesheet" href="'.JURI::root(true).'/components/com_improvemycity/css/ie.css'.'">' . "\n";
		$ie .= '<![endif]-->' . "\n";
		//$document->addStyleDeclaration($ie); 	//do not work
		$document->addCustomTag($ie);			//work :)
	
		//add scripts
		if($this->loadjquery == 1){
			$document->addScript(JURI::root(true).'/components/com_improvemycity/js/jquery-1.7.1.min.js');
			//jquery noConflict
			$document->addScriptDeclaration( 'var jImc = jQuery.noConflict();' );
		}
		
		if($this->loadjqueryui == 1){
			$document->addScript(JURI::root(true).'/components/com_improvemycity/js/jquery-ui-1.8.18.custom.min.js');
		}
		
		
		$document->addScript(JURI::root(true).'/components/com_improvemycity/js/improvemycity.js');	
		
		/* category validation only works on server-side with the JRule regex, make it validate on client-side as well*/
		$document->addScript(JURI::root() . $this->script);
		//$document->addScript(JURI::root() . "/components/com_improvemycity/views/addissue/submitbutton.js");
		
		//add google maps
		$document->addScript("https://maps.google.com/maps/api/js?sensor=false&language=".$this->language."&region=" . $this->region);

		$LAT = $this->lat;
		$LON = $this->lon;

		$googleMapInit = "
			var geocoder = new google.maps.Geocoder();
			var map;
			var marker;
			
			function blink() {
				var moo = jImc(\"#jform_address\").effect(\"highlight\", {color: '#60FF05'}, 2000);
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
				var address = document.getElementById('jform_address').value + ' ".$this->searchterm."';
				geocoder.geocode( { 'address': address, 'language': '".$this->language."'}, function(results, status) {
				  if (status == google.maps.GeocoderStatus.OK) {
					map.setCenter(results[0].geometry.location);
					marker.setPosition(results[0].geometry.location);
					
					if(true){	//check linker checkbox here
						document.getElementById('jform_latitude').value = results[0].geometry.location.lat();
						document.getElementById('jform_longitude').value = results[0].geometry.location.lng();					
					}
					
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

			function updateMarkerPosition(latLng) {
			  //update fields
			  document.getElementById('jform_latitude').value = latLng.lat();
			  document.getElementById('jform_longitude').value = latLng.lng();
			}

			function updateMarkerAddress(str) {
			  document.getElementById('jform_address').value = str;
			}

			
			function initialize() {
			  var LAT = ".$LAT.";
			  var LON = ".$LON.";

			  var latLng = new google.maps.LatLng(LAT, LON);
			  map = new google.maps.Map(document.getElementById('mapCanvasNew'), {
				zoom: ".$this->zoom.",
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
			  });
			  
			  google.maps.event.addListener(marker, 'drag', function() {

			  });
			  
			  google.maps.event.addListener(marker, 'dragend', function() {
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
