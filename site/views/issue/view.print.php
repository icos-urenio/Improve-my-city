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
	protected $state;
	protected $print;
	protected $item;
	protected $params;
	protected $pageclass_sfx;
	protected $guest;
	protected $voted;
	protected $hasVoted;
	protected $language = '';
	protected $region = '';
	protected $lat = '';
	protected $lon = '';
	protected $searchterm = '';
	protected $categoryIcon = '';
	
	function display($tpl = null)
	{
		$app		= JFactory::getApplication();
		$this->params		= $app->getParams();
		$this->print	= JRequest::getBool('print');
		//remove || from title
		$strip_title = $this->params->get('page_title');
		$strip_title = str_replace('||', '', $strip_title);
		$this->params->set('page_title', $strip_title);
		
		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));
		
		$lang = $this->params->get('maplanguage');
		$region = $this->params->get('mapregion');
		$lat = $this->params->get('latitude');
		$lon = $this->params->get('longitude');
		$term = $this->params->get('searchterm');
		
		$this->language = (empty($lang) ? "en" : $lang);
		$this->region = (empty($region) ? "GB" : $region);
		$this->lat = (empty($lat) ? 40.54629751976399 : $lat);
		$this->lon = (empty($lon) ? 23.01861169311519 : $lon);
		$this->searchterm = (empty($term) ? "" : $term);
		
	
		// Get some data from the models
		$this->state	= $this->get('State');
		//$this->item	= $this->get('Item');
		
		/*note: I am using multiple models so I have to specify not only the method but the model as well - http://docs.joomla.org/Using_multiple_models_in_an_MVC_component */
		//TODO: Check if it would be best to move these lines to the model instead of view (here)...
		$this->item	= $this->get('Item');
		$this->hasVoted = $this->get('HasVoted');
		$this->assign('discussion', $this->get('Items', 'discussions'));		
		$this->categoryIcon = $this->get('CategoryIcon');
		
		//check if user is logged
		$user =& JFactory::getUser();
		$this->guest = $user->guest;		
		
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

	
	//leave this as is... maybe more markers will appear in future versions..
	protected function getMarkerArrayFromItem() {
		$ar[] = array('name'=>$this->item->title,
					'description'=>$this->item->description,
					'catid'=>$this->item->catid,
					'id'=>$this->item->id,
					'lat'=>$this->item->latitude,
					'lng'=>$this->item->longitude
					);
		return $ar;
	}	
	
	protected function setDocument() 
	{
		$document = JFactory::getDocument();
		
		$document->addStyleSheet(JURI::root(true).'/components/com_improvemycity/bootstrap/css/bootstrap.min.css');			
		$document->addStyleSheet(JURI::root(true).'/components/com_improvemycity/css/improvemycity.css');	
		$document->addStyleSheet(JURI::root(true).'/components/com_improvemycity/css/improvemycity_print.css');	
		$document->addStyleSheet(JURI::root(true).'/components/com_improvemycity/css/print.css', 'text/css', 'print');	

		
		//add jquery
		$document->addScript(JURI::root(true).'/components/com_improvemycity/js/jquery-1.7.1.min.js');
		//jquery noConflict
		$document->addScriptDeclaration( 'var jImc = jQuery.noConflict();' );
				
		//$document->addScript(JURI::root(true).'/components/com_improvemycity/js/jquery-ui.min.js');
		$document->addScript(JURI::root(true).'/components/com_improvemycity/js/improvemycity.js');	
		
		//add google maps
		$document->addScript("https://maps.google.com/maps/api/js?sensor=false&language=".$this->language."&region=" . $this->region);
		$document->addScript(JURI::root(true).'/components/com_improvemycity/js/infobox_packed.js');	
		
		$document->addScriptDeclaration('var jsonMarkers = '.json_encode($this->getMarkerArrayFromItem()).';');
		
		$LAT = $this->lat;
		$LON = $this->lon;

		$googleMap = "
			var geocoder = new google.maps.Geocoder();
			var map = null;
			var gmarkers = [];
			
			function zoomIn() {
				map.setCenter(marker.getPosition());
				map.setZoom(map.getZoom()+1);
			}

			function zoomOut() {
				map.setCenter(marker.getPosition());
				map.setZoom(map.getZoom()-1);
			}
			
			// Creating a LatLngBounds object
			var bounds = new google.maps.LatLngBounds();			


			function initialize() {
				var LAT = ".$LAT.";
				var LON = ".$LON.";

				var latLng = new google.maps.LatLng(LAT, LON);
				map = new google.maps.Map(document.getElementById('mapCanvas'), {
				zoom: 16,
				center: latLng,
				panControl: false,
				streetViewControl: false,
				zoomControlOptions: {
					style: google.maps.ZoomControlStyle.SMALL
				},
				mapTypeId: google.maps.MapTypeId.ROADMAP
				});

				for (var i = 0; i < jsonMarkers.length; i++) {
					var name = jsonMarkers[i].name;
					var description = jsonMarkers[i].description;
					var catid = jsonMarkers[i].catid;
					var id = jsonMarkers[i].id;
					var point = new google.maps.LatLng(
						parseFloat(jsonMarkers[i].lat),
						parseFloat(jsonMarkers[i].lng)
					);
					
					var icon = '" . JURI::root().$this->categoryIcon."';
					var shadow = '" . JURI::root(true). "/components/com_improvemycity/images/shadow.png". "';
					var marker = new google.maps.Marker({
						map: map,
						position: point,
						title: name,
						icon: icon,
						shadow: shadow
					});
					
					marker.catid = catid;
					marker.id = id;
					marker.description = description;
					gmarkers.push(marker);
				}

				resetBounds();

			}

			function resetBounds() {
				var a = 0;
				bounds = null;
				bounds = new google.maps.LatLngBounds();
				for (var i=0; i<gmarkers.length; i++) {
					if(gmarkers[i].getVisible()){
						a++;
						bounds.extend(gmarkers[i].position);	
					}
				}
				if(a > 0){
					map.fitBounds(bounds);
					var listener = google.maps.event.addListener(map, 'idle', function() { 
					  if (map.getZoom() > 16) map.setZoom(16); 
					  google.maps.event.removeListener(listener); 
					});
				}
			}

			// Onload handler to fire off the app.
			//google.maps.event.addDomListener(window, 'load', initialize);
			
		";

		$documentReady = "
		
		jImc(document).ready(function() {
			initialize();
		});
		";
				
		//add the javascript to the head of the html document
		$document->addScriptDeclaration($googleMap);
		$document->addScriptDeclaration($documentReady);
		
		if ($this->print)
		{
			//$document->addStyleSheet(JURI::root(true).'/components/com_improvemycity/css/improvemycity_print.css');	
		}		
	}
}
