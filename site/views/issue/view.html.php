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
class ImprovemycityViewIssue extends JView
{
	protected $state;
	protected $item;
	protected $params;
	protected $pageclass_sfx;
	protected $guest;
	protected $voted;
	protected $hasVoted;
	
	function display($tpl = null)
	{
		$app		= JFactory::getApplication();
		$this->params		= $app->getParams();
		
		//remove || from title
		$strip_title = $this->params->get('page_title');
		$strip_title = str_replace('||', '', $strip_title);
		$this->params->set('page_title', $strip_title);
		
		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));
		
		
		
		//while inserting new issue: if model return false it redirects to view=issue&layout=edit	
		$layout = JRequest::getCmd('layout', 'default');		
		// Check for edit form.
		if ($layout == 'edit') {
			echo $this->displayError();
			return false;
		}		
		
		
		
		// Get some data from the models
		$this->state	= $this->get('State');
		//$this->item	= $this->get('Item');
		
		/*note: I am using multiple models so I have to specify not only the method but the model as well - http://docs.joomla.org/Using_multiple_models_in_an_MVC_component */
		//TODO: Check if it would be best to move these lines to the model instead of view (here)...
		$this->item	= $this->get('Item');
		$this->hasVoted = $this->get('HasVoted');
		$this->assign('discussion', $this->get('Items', 'discussions'));		
		
		
		//check if user is logged
		$user =& JFactory::getUser();
		$this->guest = $user->guest;		
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		
		//update hits
		$model = $this->getModel();
		$model->hit();		
		
        parent::display($tpl);
		
		// Set the document
		$this->setDocument();
		
		
		
	}

	
	protected function displayError(){
		$link = JRoute::_('index.php?option=com_improvemycity&controller=improvemycity&task=addIssue');
		
		$html = '<div style="text-align:center;">';
		$html .= '<img src="' . JURI::root(true).'/components/com_improvemycity/images/error.png' . '" /><br />';
		$html .= '<a href="'.$link.'">'.JText::_('BACK_TO_FORM').'</a>';
		$html .= '</div>';
		return $html;
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
		$document->addScript(JURI::root(true).'/components/com_improvemycity/js/infobox_packed.js');	
		
		$document->addScriptDeclaration('var jsonMarkers = '.json_encode($this->getMarkerArrayFromItem()).';');
		
		$LAT = $this->item->latitude;
		$LON = $this->item->longitude;
		if($LAT == '' || $LON == ''){
			$LAT = '20.54629751976399';
			$LON = '23.01861169311519';
		}
		
		
		$googleMapInit = "
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
			var infoWindow = null;
			var infoBox = null;

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

				infoWindow = new google.maps.InfoWindow;
				
				var infoBoxOptions = {
					disableAutoPan: false
					,maxWidth: 0
					,pixelOffset: new google.maps.Size(-100, 0)
					,zIndex: null
					,boxStyle: { 
					  background: \"url(" . JURI::base().'components/com_infomap/images/tipbox.gif' . ") -40px 0 no-repeat\"
					  ,opacity: 0.75
					  ,width: \"200px\"
					 }
					,closeBoxMargin: \"10px 2px 2px 2px\"
					,closeBoxURL: \"http://www.google.com/intl/en_us/mapfiles/close.gif\"
					,infoBoxClearance: new google.maps.Size(1, 1)
					,isHidden: false
					,pane: \"floatPane\"
					,enableEventPropagation: false
				};
				infoBox = new InfoBox(infoBoxOptions);				

				for (var i = 0; i < jsonMarkers.length; i++) {
					var name = jsonMarkers[i].name;
					var description = jsonMarkers[i].description;
					var catid = jsonMarkers[i].catid;
					var id = jsonMarkers[i].id;
					//var photos = markers[i].photos;
					

					var point = new google.maps.LatLng(
						parseFloat(jsonMarkers[i].lat),
						parseFloat(jsonMarkers[i].lng)
					);



					var html = '<strong>' + name + '</strong>';
					
					var icon = '" . JURI::root(true). "/components/com_improvemycity/images/marker.png". "';
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
					//marker.photos = photos;
					
					marker.description = description;
					
					//bindInfoWindow(marker, map, infoWindow, html);
					bindInfoBox(marker, map, infoBox, html);
					
					gmarkers.push(marker);
				}

				resetBounds();
				$(\"#loading\").hide();
				
			}

			function bindInfoWindow(marker, map, infoWindow, html) {
			  google.maps.event.addListener(marker, 'click', function() {
				infoWindow.setContent(html);
				infoWindow.open(map, marker);
				//map.panTo(marker.getPosition());
				//showInfo(marker);
			  });
			}
			
			//alternative to infoWindow is the infoBox
			function bindInfoBox(marker, map, infoWindow, html) {
			
				var boxText = document.createElement(\"div\");
				boxText.style.cssText = \"border: 1px solid black; margin-top: 8px; background-color: yellow; padding: 5px;\";
				boxText.innerHTML = html;			
		
				google.maps.event.addListener(marker, 'click', function() {
					infoBox.setContent(boxText);
					infoBox.open(map, marker);
					map.panTo(marker.getPosition());
					showInfo(marker);
				});
			  
				google.maps.event.addListener(marker, 'mouseover', function() {
					infoBox.setContent(boxText);
					infoBox.open(map, marker);
				});			  
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
			
			
			function markerclick(i) {
				google.maps.event.trigger(gmarkers[i],'click');
			}			

			function markerclick2(id) {
				var index;
				for (var i=0; i<gmarkers.length; i++) {				
					if(gmarkers[i].id == id){
						index = i;
					}
				}
				google.maps.event.trigger(gmarkers[index],'click');
			}			

			// == rebuilds the sidebar to match the markers currently displayed ==
			function makeSidebar() {
				var html = '<ul>';
				for (var i=0; i<gmarkers.length; i++) {
					if (gmarkers[i].getVisible()) {
						html += '<li><a href=\"javascript:markerclick(' + i + ');\">' + gmarkers[i].title + '<\/a><\/li>';
					}
				}
				html += '<\/ul>';
				document.getElementById('infobar').innerHTML = html;
			}

			function showInfo(marker){
				$(\"#markerInfo\").html('');


				html = '';
				html += '<h2>' + marker.title + '</h2>';
				html += '<p>' + marker.description + '</p>';

				if(marker.photos != ''){
					html += '<h3>Φωτογραφίες</h3>';
					html += createInfoImages(marker);
				}

				
				$(\"#markerInfo\").html(html);
				if(marker.photos != ''){
					$(\"a[rel='photos']\").colorbox();	
				}
				
				$(\"#wrapper-info\").show(500);
			}
			
			function createInfoImages(marker){
				var arr = marker.photos.split(';');
				var html = '';
				for(i = 0; i < arr.length; i++){
					if(arr[i] != ''){
						var thumb = '" . JURI::root(true). "/images/improvemycity/". "' + marker.id + '/images/thumbs/' + arr[i];
						var img = '" . JURI::root(true). "/images/improvemycity/". "' + marker.id + '/images/' + arr[i];
						html += '<a title=\"'+marker.title+'\" rel=\"photos\" href=\"'+img+'\">';
						html +=  '<img src=\"' ;
						html +=	thumb;
						html += '\" />';
						html += '</a>';
					}
				}				
				return html;
			}
			
			
			
			// Onload handler to fire off the app.
			google.maps.event.addDomListener(window, 'load', initialize);
			
		";

		//add the javascript to the head of the html document
		$document->addScriptDeclaration($googleMapInit);
	
	
	}
	
	
}