<?php
/**
 * @version     2.5.x
 * @package     com_improvemycity
 * @copyright   Copyright (C) 2011 - 2012 URENIO Research Unit. All rights reserved.
 * @license     GNU Affero General Public License version 3 or later; see LICENSE.txt
 * @author      Ioannis Tsampoulatidis for the URENIO Research Unit
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
//the name of the class must be the name of your component + InstallerScript
//for example: com_contentInstallerScript for com_content.
class com_improvemycityInstallerScript
{
	/*
	 * $parent is the class calling this method.
	 * $type is the type of change (install, update or discover_install, not uninstall).
	 * preflight runs before anything else and while the extracted files are in the uploaded temp folder.
	 * If preflight returns false, Joomla will abort the update and undo everything already done.
	 */
	function preflight( $type, $parent ) {
		
		$jversion = new JVersion();

		// Installing component manifest file version
		$this->release = $parent->get( "manifest" )->version;
		
		// Manifest file minimum Joomla version
		$this->minimum_joomla_release = $parent->get( "manifest" )->attributes()->version;   

		// Show the essential information at the install/update back-end
		echo '<p>Installing component manifest file version = ' . $this->release;
		echo '<br />Current manifest cache commponent version (if any) = ' . $this->getParam('version');
		echo '<br />Installing component manifest file minimum Joomla version = ' . $this->minimum_joomla_release;
		echo '<br />Current Joomla version = ' . $jversion->getShortVersion();

		// abort if the current Joomla release is older
		if( version_compare( $jversion->getShortVersion(), $this->minimum_joomla_release, 'lt' ) ) {
			Jerror::raiseWarning(null, 'Cannot install com_improvemycity in a Joomla release prior to '.$this->minimum_joomla_release);
			return false;
		}
 
		// abort if the component being installed is not newer than the currently installed version
		if ( $type == 'update' ) {
			$oldRelease = $this->getParam('version');
			$rel = $oldRelease . ' to ' . $this->release;
			if ( version_compare( $this->release, $oldRelease, 'lt' ) ) {
				Jerror::raiseWarning(null, 'Incorrect version sequence. Cannot upgrade ' . $rel);
				return false;
			}
		}
		else { $rel = $this->release; }
 
		//echo '<p>' . JText::_('COM_IMPROVEMYCITY_PREFLIGHT_' . $type . ' ' . $rel) . '</p>';
		
	}
 
	/*
	 * $parent is the class calling this method.
	 * install runs after the database scripts are executed.
	 * If the extension is new, the install method is run.
	 * If install returns false, Joomla will abort the install and undo everything already done.
	 */
	function install( $parent ) {
		//echo '<p>' . JText::_('COM_IMPROVEMYCITY_INSTALL to ' . $this->release) . '</p>';
		
		// You can have the backend jump directly to the newly installed component configuration page
		// $parent->getParent()->setRedirectURL('index.php?option=com_improvemycity');
	}
 
	/*
	 * $parent is the class calling this method.
	 * update runs after the database scripts are executed.
	 * If the extension exists, then the update method is run.
	 * If this returns false, Joomla will abort the update and undo everything already done.
	 */
	function update( $parent ) {
		//echo '<p>' . JText::_('COM_IMPROVEMYCITY_UPDATE_ to ' . $this->release) . '</p>';
		$params['version'] = 'ImproveMyCity version ' . $this->release;
		// You can have the backend jump directly to the newly updated component configuration page
		// $parent->getParent()->setRedirectURL('index.php?option=com_improvemycity');
	}
 
	/*
	 * $parent is the class calling this method.
	 * $type is the type of change (install, update or discover_install, not uninstall).
	 * postflight is run after the extension is registered in the database.
	 */
	function postflight( $type, $parent ) {
		// always create or modify these parameters
		$params['version'] = 'ImproveMyCity version ' . $this->release;
 
		// define the following parameters only if it is an original install
		if ( $type == 'install' ) {
			$params['latitude'] = '40.54629751976399';
			$params['longitude'] = '23.01861169311519';
			$params['zoom'] = '17';
			$params['maplanguage'] = 'en';
			$params['mapregion'] = 'GB';
			$params['searchterm'] = '';
			$params['closestatus'] = '1';
			$params['allowcommentingonclose'] = '1';
			$params['allowvotingonclose'] = '1';
			
			$params['mailnewissueuser'] = '0';
			$params['mailnewissueadmins'] = '0';
			$params['mailnewcommentuser'] = '0';
			$params['mailnewcommentadmins'] = '0';
			$params['mailcategorychangeadmins'] = '0';
			$params['mailstatuschangeuser'] = '0';
			
			$params['loadjquery'] = '1';
			$params['loadbootstrap'] = '1';
			$params['loadbootstrapcss'] = '1';
			$params['popupmodal'] = '0';
			$params['credits'] = '1';
			
			$params['showcomments'] = '1';
			$params['approveissue'] = '0';
			$params['loadjqueryui'] = '1';	
					
			$params['showrelativedates'] = '1';			
			$params['dateformat'] = 'm/d/Y h:i:s';
						
			$params['enablejsoncontroller'] = '0';			
		}
 
		$this->setParams( $params );
 
		//echo '<p>' . JText::_('COM_IMRPOVEMYCITY_POSTFLIGHT ' . $type . ' to ' . $this->release) . '</p>';
	}

	/*
	 * $parent is the class calling this method
	 * uninstall runs before any other action is taken (file removal or database processing).
	 */
	function uninstall( $parent ) {
		//echo '<p>' . JText::_('COM_IMPROVEMYCITY_UNINSTALL ' . $this->release) . '</p>';
	}
 
	/*
	 * get a variable from the manifest file (actually, from the manifest cache).
	 */
	function getParam( $name ) {
		$db = JFactory::getDbo();
		$db->setQuery('SELECT manifest_cache FROM #__extensions WHERE name = "com_improvemycity"');
		$manifest = json_decode( $db->loadResult(), true );
		return $manifest[ $name ];
	}
 
	/*
	 * sets parameter values in the component's row of the extension table
	 */
	function setParams($param_array) {
		if ( count($param_array) > 0 ) {
			// read the existing component value(s)
			$db = JFactory::getDbo();
			$db->setQuery('SELECT params FROM #__extensions WHERE name = "com_improvemycity"');
			$params = json_decode( $db->loadResult(), true );
			// add the new variable(s) to the existing one(s)
			foreach ( $param_array as $name => $value ) {
				$params[ (string) $name ] = (string) $value;
			}
			// store the combined new and existing values back as a JSON string
			$paramsString = json_encode( $params );
			
			$db->setQuery('UPDATE #__extensions SET params = ' .
				$db->quote( $paramsString ) .
				' WHERE name = "com_improvemycity"' );
				$db->query();
		}
	}
}
