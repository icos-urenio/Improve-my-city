<?php
/**
 * @version     2.5.x
 * @package     com_improvemycity
 * @copyright   Copyright (C) 2011 - 2013 URENIO Research Unit. All rights reserved.
 * @license     GNU Affero General Public License version 3 or later; see LICENSE.txt
 * @author      Panagiotis Tsarchopoulos for the URENIO Research Unit
 * 
 * **** WARNING *****
 * DURING JSON REQUESTS, USERNAME AND PASSWORD ALTHOUGH TRANSMITTED ENCRYPTED, MIGHT BE STOLEN BY SNIFFERS AND USED AS IS. 
 * FOR MAXIMUM PROTECTION YOU ARE ADVISED TO --USE THIS CONTROLLER ON SSL--- (HTTPS) WEB-SERVERS ONLY.
 * THIS CONTROLLER IS DISABLED BY DEFAULT. YOU CAN ENABLE IT ON COMPONENT'S SETTINGS UNDER THE 'ADVANCED' TAB
 * YOU SHOULD ALWAYS SEND PASSWORD DECRYPTED LIKE THIS:
	
	-- HOW TO ENCRYPT THE PASSWORD BEFORE CALLING THE MOBILE.JSON CONTROLLER
	$key = 'secret key'; //the secret key as set on component's menu "API KEY" (Keys on client and server should MATCH )
	Follow the instructions on: http://www.androidsnippets.com/encrypt-decrypt-between-android-and-php
	Important: Key length must be 16 characters
	--
*/

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.controller');

class ImprovemycityControllerMobile extends JController
{
	private $enablejsoncontroller = 0;
	private $key = null;
	function __construct()
	{
		// Load the parameters.
		$app = JFactory::getApplication();
		$params	= $app->getParams();
		$this->enablejsoncontroller = $params->get('enablejsoncontroller');
		if(!$this->enablejsoncontroller)
			die('CONTROLLER MOBILE.JSON IS DISABLED');		
		parent::__construct();

		//populate key from DB
		$model = $this->getModel('keys');
		$key = $model->getSecretKey();
		$this->key = $key;
		
	}
	
	
	/* arguments: 
	 * limit=0 : get ALL issues, limit=5 get recent 5 issues
	 * showComments=1: includes issue's discussion, showComments=0 (default) discussion is not included
	 * x0up: longitude < x0up
	 * x0down: longitude > x0down
	 * y0up: latitude < y0up
	 * y0down: latitude > y0down
	 * */
	public function getIssues()
	{
		//get request
		$showComments = JRequest::getInt('showComments');
		$limit = JRequest::getInt('limit');
		//get boundaries
		$x0up 	= JRequest::getFloat('x0up');		
		$x0down	= JRequest::getFloat('x0down');
		$y0up 	= JRequest::getFloat('y0up');
		$y0down	= JRequest::getFloat('y0down');		
		
		
		//get model and items
		$items = array();
		if( !empty($x0up) && !empty($x0down) && !empty($y0up) && !empty($y0down)){
			$model = $this->getModel('issues');
			$items	= $model->getItemsInBoundaries($x0up, $x0down, $y0up, $y0down, $limit);
		}
		else {
			$model = $this->getModel('issues');
			$items	= $model->getItems();
		}
				
		//clean up and prepare for json
		foreach($items as $item){
			unset($item->params);
			if(!$showComments)
				unset($item->discussion);
		}
		//$document = &JFactory::getDocument();
		//$document->setMimeEncoding('text/xml');		
		echo json_encode($items);
		return;
	}	
	
	//testing zip
	public function getIssuesZipped()
	{
		//get request
		$showComments = JRequest::getInt('showComments');
		$limit = JRequest::getInt('limit');
		//get boundaries
		$x0up 	= JRequest::getFloat('x0up');
		$x0down	= JRequest::getFloat('x0down');
		$y0up 	= JRequest::getFloat('y0up');
		$y0down	= JRequest::getFloat('y0down');
	
	
		//get model and items
		$items = array();
		if( !empty($x0up) && !empty($x0down) && !empty($y0up) && !empty($y0down)){
			$model = $this->getModel('issues');
			$items	= $model->getItemsInBoundaries($x0up, $x0down, $y0up, $y0down, $limit);
		}
		else {
			$model = $this->getModel('issues');
			$items	= $model->getItems();
		}
	
		//clean up and prepare for json
		foreach($items as $item){
			unset($item->params);
			if(!$showComments)
				unset($item->discussion);
		}
		//$document = &JFactory::getDocument();
		//$document->setMimeEncoding('text/xml');
		if(function_exists('ob_gzhandler')){
			$document = &JFactory::getDocument();
			//$document->setMimeEncoding('application/json', true);
			//JResponse::setHeader('Content-Encoding','gzip');
			JResponse::setheader("Content-Type: text/html; charset=ISO-8859-1",true);
			//ob_start('ob_gzhandler');
			//echo json_encode($items);
			//$var = ob_get_clean();//ob_end_flush();
			//echo $var;
			//echo gzcompress(json_encode($items), 9);
			echo gzdeflate(json_encode($items), 1);
			return;
		}
		
		echo json_encode($items);
		return;
	}	
	
	
	/* arguments:
	 * issueId=X : get issue with ID = X
	* showComments=1: includes issue's discussion, showComments=0 (default) discussion is not included
	* */	
	public function getIssue()
	{
		//get request
		$showComments = JRequest::getInt('showComments');
		$issueId = JRequest::getInt('issueId');
	
		//get model and items
		$model = $this->getModel('issue');

		$item = $model->getItem($issueId);
		if($item == null){			
			echo json_encode('IssueId: ' .$issueId.' not found');
			return;
		}
		
		//clean up and prepare for json
		unset($item->params);
		if(!$showComments)
			unset($item->discussion);

		echo json_encode($item);
		return;
	}	
	
	public function getCategories()
	{
		//get model and categories
		$model = $this->getModel('issues');
		$categories	= $model->getSimpleCategories();
		
		echo json_encode($categories);
		return;
	}
	
	public function getTimestamp()
	{
		//get model and timestamp
		$model = $this->getModel('issues');
		$timestamp = $model->getTimestamp();
		
		echo json_encode($timestamp);
		return;
	}	
	
	public function getCategoryTimestamp()
	{
		//get model and timestamp
		$model = $this->getModel('issues');
		$timestamp = $model->getCategoryTimestamp();
	
		echo json_encode($timestamp);
		return;
	}	
	
	
	/* BELOW FUNCTIONS NEED valid username and encrypted_password */ 
	
	
	public function addIssue()
	{
		$username = JRequest::getVar('username');
		$password = JRequest::getVar('password');
		//Check authentication
		$auth = $this->authenticate($username, $password);
		if(!empty($auth['error_message'])){
			echo json_encode("Authentication failed");
			return;
		}
		
		$userid = $auth['id'];		
		$title = JRequest::getVar('title');
		$title = strip_tags($title);
		$catid = JRequest::getVar('catid');
		$address = JRequest::getVar('address');
		$address = strip_tags($address);
		$description = JRequest::getVar('description');
		$description = strip_tags($description);
		$latitude = JRequest::getVar('latitude');
		$longitude = JRequest::getVar('longitude');
		
		if(strstr($title, '|') != false) $title = substr(strstr($title, '|'),1);
		if(strstr($address, '|') != false) $address = substr(strstr($address, '|'),1);
		if(strstr($description, '|') != false) $description = substr(strstr($description, '|'),1);
		
		//get model
		$model = $this->getModel('addissue');

		$data['title'] = $title;
		$data['catid'] = $catid;
		$data['latitude'] = $latitude;
		$data['longitude'] = $longitude;
		$data['address'] = $address;
		$data['description'] = $description;
		$data['userid'] = $userid;
		$data['inserted_by'] = 1; //1 for mobile
		
		$s = $model->save($data);
		echo json_encode($s); //0 or 1
		return;
	}

	/*
	 * return number of votes after voting, 0 if fail, -1 if already voted
	 */
	public function voteIssue()
	{
		$username = JRequest::getVar('username');
		$password = JRequest::getVar('password');
		//Check authentication
		$auth = $this->authenticate($username, $password);
		if(!empty($auth['error_message'])){
			echo json_encode("Authentication failed");
			return;
		}
		
		$userid = $auth['id'];		
		$issueId = JRequest::getInt('issueId');
				
		//get model
		$model = $this->getModel('issue');
		
		//check if user has already voted for the issue
		$hasVoted = $model->getHasVoted($issueId, $userid);
		if($hasVoted){
			echo json_encode("-1");
			return;
		}	
		
		//do the voting
		$newVotes = $model->vote($issueId, $userid);		
		echo json_encode($newVotes); //number of votes after voting or 0 if fail
		return;
	}
	
	public function addComment()
	{
		$username = JRequest::getVar('username');
		$password = JRequest::getVar('password');
		//Check authentication
		$auth = $this->authenticate($username, $password);
		if(!empty($auth['error_message'])){
			echo json_encode("Authentication failed");
			return;
		}
		
		$userid = $auth['id'];
		$issueId = JRequest::getInt('issueId');
		$description = JRequest::getVar('description');
		
		$description = strip_tags($description);
		
		//get model 
		$model = $this->getModel('discussions');
		$lastComment = $model->comment($issueId, $userid, $description);
		
		echo json_encode($lastComment);
		return;
	}
	
	public function getUserInfo()
	{
		$username = JRequest::getVar('username');
		$password = JRequest::getVar('password');
		//Check authentication
		$auth = $this->authenticate($username, $password);
		echo json_encode($auth);
		return;
	}	
	
	public function getUserVotes()
	{
		$username = JRequest::getVar('username');
		$password = JRequest::getVar('password');
		//Check authentication
		$auth = $this->authenticate($username, $password);
		if(!empty($auth['error_message'])){
			echo json_encode("Authentication failed");
			return;
		}
	
		$userid = $auth['id'];
	
		//get model and items
		$model = $this->getModel('users');
	
		$item = $model->getUserVotes($userid);
		if($item == null){
			echo json_encode('0');
			return;
		}
	
		//prepare for json
		echo json_encode($item);
		return;
	}
		
	private function authenticate($username, $encrypted_password)
	{
		$code = "";
		for ($i = 0; $i < strlen($encrypted_password); $i += 2) {
			$code .= chr(hexdec(substr($encrypted_password, $i, 2)));
		}
		
		$iv = $this->key; //Initialization vector same as key
		$key= $this->key;
		
		$td = mcrypt_module_open('rijndael-128', '', 'cbc', $iv);
		mcrypt_generic_init($td, $key, $iv);
		$decrypted_password = mdecrypt_generic($td, $code);
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		$decrypted_password = utf8_encode(trim(substr($decrypted_password,0,16)));
		
		//get model
		$model = $this->getModel('users');
		$response = $model->authenticateUser($username, $decrypted_password);
		return $response;
	}
	
	
	//User registration using JUser
	public function registerUser()
	{
		if(JComponentHelper::getParams('com_users')->get('allowUserRegistration') == 0) {
			echo 'Registration is not allowed in this site';
			return;
		}
		
		$username = JRequest::getVar('username');
		$name = JRequest::getVar('name');
		$email = JRequest::getVar('email');
		$encrypted_password = JRequest::getVar('password');		
		
		if(empty($email) || empty($encrypted_password) || empty($name) || empty($username)){
			echo 'Wrong input';
			return;
		}
		
		//password should be decrypted first and then stored by JUser
		$code = "";
		for ($i = 0; $i < strlen($encrypted_password); $i += 2) {
			$code .= chr(hexdec(substr($encrypted_password, $i, 2)));
		}
		$iv = $this->key; //Initialization vector same as key
		$key= $this->key;
		$td = mcrypt_module_open('rijndael-128', '', 'cbc', $iv);
		mcrypt_generic_init($td, $key, $iv);
		$decrypted_password = mdecrypt_generic($td, $code);
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		$decrypted_password = utf8_encode(trim(substr($decrypted_password,0,16)));		
		

		//check if username exists
		$model = $this->getModel('users');

		if($model->userExists($username)){
			//echo json_encode(JText::_('COM_USERS_USER_ALREADY_EXISTS'));
			echo JText::_('COM_USERS_USER_ALREADY_EXISTS');
			return;
		}

		//create user with username = email, email = email, password = decrypted_password, name = name; 
		$temp = array('username' => $username, 'email1' => $email, 'password1' => $decrypted_password, 'name' => $name);
		$return = $model->register($temp);
		
		$ret = '';
		if ($return === 'adminactivate'){
			$ret = JText::_('COM_USERS_REGISTRATION_COMPLETE_VERIFY');
		} elseif ($return === 'useractivate') {
			$ret = JText::_('COM_USERS_REGISTRATION_COMPLETE_ACTIVATE');
		} else {
			//$ret = JText::_('COM_USERS_REGISTRATION_SAVE_SUCCESS');
			$ret = $return;
		}		
		
		echo $ret;
		return;
		
	}
	
}
