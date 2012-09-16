<?php
/**
 * @version     2.5.x
 * @package     com_improvemycity
 * @copyright   Copyright (C) 2011 - 2012 URENIO Research Unit. All rights reserved.
 * @license     GNU Affero General Public License version 3 or later; see LICENSE.txt
 * @author      URENIO Research Unit
 * 
 * **** WARNING *****
 * DURING JSON REQUESTS, USERNAME AND PASSWORD ALTHOUGH TRANSMITTED ENCRYPTED, MIGHT BE STOLEN BY SNIFFERS AND USED AS IS. 
 * FOR MAXIMUM PROTECTION YOU ARE ADVISED TO USE THIS CONTROLLER ON SSL (HTTPS) SERVERS ONLY.
 * THIS CONTROLLER IS DISABLED BY DEFAULT. YOU CAN ENABLE IT ON COMPONENT'S SETTINGS UNDER THE 'ADVANCED' TAB
 * YOU SHOULD ALWAYS SEND PASSWORD DECRYPTED LIKE THIS:
	
	-- HOW TO ENCRYPT THE PASSWORD BEFORE CALLING THE MOBILE.JSON CONTROLLER
	$key = 'secret key'; //the secret key as set on component's setting under advanced tab (MUST MATCH THE ONE ON JOOMLA SERVER)
	$password = ' the actual user password '; // *Important* note the spaces *Important*
    $encrypted_password = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $password, MCRYPT_MODE_CBC, md5(md5($key))));
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
		$this->key = $params->get('secretkey');
		parent::__construct();
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
		
		//get boundaries
		$x0up 	= JRequest::getFloat('x0up');		
		$x0down	= JRequest::getFloat('x0down');
		$y0up 	= JRequest::getFloat('y0up');
		$y0down	= JRequest::getFloat('y0down');		
		
		//get model and items
		$items = array();
		if( !empty($x0up) && !empty($x0down) && !empty($y0up) && !empty($y0down)){
			$model = $this->getModel('issues');
			$items	= $model->getItemsInBoundaries($x0up, $x0down, $y0up, $y0down);
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
		
		//get model
		$model = $this->getModel('addissue');

		$data['title'] = $title;
		$data['catid'] = $catid;
		$data['latitude'] = $latitude;
		$data['longitude'] = $longitude;
		$data['address'] = $address;
		$data['description'] = $description;
		$data['userid'] = $userid;
		
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
	
	private function authenticate($username, $encrypted_password)
	{
		//make sure GET is correct (according to RFC 2396 plus sign is %2B)
		$encrypted_password = urlencode($encrypted_password);
		$encrypted_password = str_replace("+", "%2B",$encrypted_password);
		$encrypted_password = urldecode($encrypted_password);		
		
		$decrypted_password = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($this->key), base64_decode($encrypted_password), MCRYPT_MODE_CBC, md5(md5($this->key))), "\0");
		$decrypted_password = trim($decrypted_password);
		//echo $decrypted_password;die;
		//echo $this->key;die;
		
		//get model
		$model = $this->getModel('users');
		$response = $model->authenticateUser($username, $decrypted_password);
		return $response;
	}
}
