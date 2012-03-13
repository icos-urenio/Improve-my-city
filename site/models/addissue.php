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

require_once JPATH_COMPONENT_ADMINISTRATOR.'/models/issue.php';



/**
 * Model
 */
class ImprovemycityModelAddissue extends ImprovemycityModelIssue
{

	/**
	 * Get the return URL.
	 *
	 * @return	string	The return URL.
	 * @since	1.6
	 */
	public function getReturnPage()
	{
		return base64_encode(JRoute::_('index.php?option=com_improvemycity&view=issues'));
	}
	
	
	protected function populateState()
	{
		$app = JFactory::getApplication();
		
		// Load the parameters.
		$params	= $app->getParams();
		$this->setState('params', $params);	
		
		$return = JRequest::getVar('return', null, 'default', 'base64');

		if (!JUri::isInternal(base64_decode($return))) {
			$return = null;
		}

		$this->setState('return_page', base64_decode($return));		
	}

	 
	/**
	* Get file (photo) from POST and save it
	* following the http://forum.joomla.org/viewtopic.php?t=650699 guidelines... override save on model... 
	*/	
	public function save($data)
	{
		$data = JRequest::getVar('jform', array(), 'post', 'array');
		$file = JRequest::getVar('jform', array(), 'files', 'array');
		
		if ($file) {
			//$filename = JFile::makeSafe($file['name']['photo']);
			$filename = $file['name']['photo'];
			
			if($filename!=''){
				if($file['type']['photo'] != 'image/jpeg' && $file['type']['photo'] != 'image/png'){
					$this->setError(JText::_('ONLY_PNG_JPG'));
					return false;	
				}
		
				$src = $file['tmp_name']['photo'];
				$dest =  JPATH_SITE. DS ."images". DS . "improvemycity" . DS . JFactory::getUser()->get('id') . DS . "images" . DS . $filename;
				$thumb_dest =  JPATH_SITE. DS ."images". DS . "improvemycity" . DS . JFactory::getUser()->get('id') . DS . "images" . DS . "thumbs" . DS . $filename;

				//resize image here
				include_once(JPATH_COMPONENT.'/helpers/simpleimage.php');
				
				$image = new SimpleImage();
				$image->load($src);
				
				$width = $image->getWidth();
				$height = $image->getHeight();
				$new_height = $height;
				$new_width = $width;
				
				$target_width = 800;	//TODO: GET FROM PARAMETERS
				$target_height = 600;	//TODO: GET FROM PARAMETERS

				$target_ratio = $target_width / $target_height;
				$img_ratio = $width / $height;
				
				if ($target_ratio > $img_ratio) {
					$new_height = $target_height;
					$new_width = $img_ratio * $target_height;
				} else {
					$new_height = $target_width / $img_ratio;
					$new_width = $target_width;
				}

				if ($new_height > $target_height) {
					$new_height = $target_height;
				}
				if ($new_width > $target_width) {
					$new_height = $target_width;
				}		
				
				if($width > $target_width && $height > $target_height){
					if($new_height != $height || $new_width != $width){
						$image->resize($new_width,$new_height);
						$image->save($src);		
					}
				}
				 
				//always use constants when making file paths, to avoid the possibilty of remote file inclusion
				
				if(!JFile::upload($src, $dest)) 
				{
					echo JText::_( 'ERROR MOVING FILE' );
						return;
				}
				else
				{
					// success, exit with code 0 for Mac users, otherwise they receive an IO Error
					JPath::setPermissions($dest);
					//CREATE THUMBNAIL HERE
					$image->load($dest);
					$image->resize(80,60);
					$pathToThumb = JPATH_SITE.DS.'images'.DS.'improvemycity'.DS.JFactory::getUser()->get('id').DS.'images'.DS.'thumbs';
					if (!JFolder::exists($pathToThumb)){
						JFolder::create($pathToThumb);
					}
					
					//$thumb = $pathToThumb.DS.$fileName;
					$image->save($thumb_dest);
					JPath::setPermissions($thumb_dest);
					
					//update data with photo path
					$data['photo'] = 'images/improvemycity/'.JFactory::getUser()->get('id').'/images/thumbs/'.$filename;
					//$data['thumb'] = 'images/improvemycity/'.JFactory::getUser()->get('id').'/images/thumbs/'.$filename;
					//$data['photo'] = 'images'.'/'.'improvemycity'.'/'.JFactory::getUser()->get('id').'/'.'images'.'/'.$fileName;
				}					
					
				/*
				if (JFile::upload($src, $dest, false) ){
					//update data with photo path
					$data['photo'] = 'images/improvemycity/'.JFactory::getUser()->get('id').'/images/'.$filename;
				} 
				*/
			}    
		}	
		
		
		// Initialise variables;
		$dispatcher = JDispatcher::getInstance();
		$table      = $this->getTable();
		$key         = $table->getKeyName();
		$pk         = (!empty($data[$key])) ? $data[$key] : (int)$this->getState($this->getName().'.id');
		$isNew      = true;

		// Include the content plugins for the on save events.
		JPluginHelper::importPlugin('content');

		// Allow an exception to be thrown.
		try
		{
		// Load the row if saving an existing record.
		if ($pk > 0) {
		$table->load($pk);
		$isNew = false;
		}

		// Bind the data.
		if (!$table->bind($data)) {
		$this->setError($table->getError());
		return false;
		}

		// Prepare the row for saving
		$this->prepareTable($table);

		// Check the data.
		if (!$table->check()) {
		$this->setError($table->getError());
		return false;
		}

		// Trigger the onContentBeforeSave event.
		$result = $dispatcher->trigger($this->event_before_save, array($this->option.'.'.$this->name, &$table, $isNew));
		if (in_array(false, $result, true)) {
		$this->setError($table->getError());
		return false;
		}

		// Store the data.
		if (!$table->store()) {
		$this->setError($table->getError());
		return false;
		}

		// Clean the cache.
		$this->cleanCache();

		// Trigger the onContentAfterSave event.
		$dispatcher->trigger($this->event_after_save, array($this->option.'.'.$this->name, &$table, $isNew));
		}
		catch (Exception $e)
		{
		$this->setError($e->getMessage());

		return false;
		}

		$pkName = $table->getKeyName();

		if (isset($table->$pkName)) {
		$this->setState($this->getName().'.id', $table->$pkName);
		}
		$this->setState($this->getName().'.new', $isNew);


		/**
		*----------------------   Send notification mail 
		**/
		//get the link to the newly created issue
		$issueLink = $_SERVER['HTTP_HOST'] .  JRoute::_('index.php?option=com_improvemycity&view=issue&issue_id='.$table->id);
		
		//get the recipient email as defined in the "note" field of the selected category
		$issueRecipient = '';
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$query->select('a.note as note');
		$query->from('`#__categories` AS a');
		
		$query->where('a.id = ' . $data['catid']);		
		$db->setQuery($query);
		$result = $db->loadResult();
		if(!empty($result)){
			$issueRecipient = $result;
		}		

		//various information like username, title, etc set on subject and body
		$user = JFactory::getUser();

		$app = JFactory::getApplication();
		$mailfrom	= $app->getCfg('mailfrom');
		$fromname	= $app->getCfg('fromname');
		$sitename	= $app->getCfg('sitename');

		$subject = 'New issue is submitted by ' . $user->name  ;
		$body = 'A <a href="'.$issueLink.'">new issue</a> is submitted by '. $user->name . ' entitled: ' . $data['title'] . ' at ' . $data['address'];

		$mail = JFactory::getMailer();
		$mail->addRecipient($issueRecipient);
		$mail->setSender(array($mailfrom, $fromname));
		$mail->setSubject($sitename.': '.$subject);
		$mail->setBody($body);
		$sent = $mail->Send();

		return true;		

	}
	
}
