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

jimport('joomla.application.component.controller');

class ImprovemycityController extends JController
{

	public function display($cachable = false, $urlparams = false)
	{
		
		$view = JRequest::getCmd('view', 'issues');
		JRequest::setVar('view', $view);
		$v = & $this->getView($view, 'html');
		$v->setModel($this->getModel($view), true); //the default model (true) :: $view is either issues or issue
		$v->setModel($this->getModel('discussions'));
		$v->display();

		return $this; 
	}
		
	function addIssue()
	{
		$view = JRequest::getCmd('view', 'addissue');
		JRequest::setVar('view', $view);
		
		
		$v = & $this->getView($view, 'html');
		$v->setModel($this->getModel($view));
		//$v->display();
		parent::display();
		
		return $this;
	}	
	
	
	/**
	* only called async from ajax	
	* function returns a list of all comments for the specific issueid or false if fail
	*/
	function addComment()
	{
		/* TODO: Admins must have different avatar */
		/* $user =& JFactory::getUser();	print_r($user);  http://forum.joomla.org/viewtopic.php?p=2730458 */
		JRequest::checkToken('post') or jexit('Invalid Token');
		
		$user =& JFactory::getUser();
		
		if(!$user->guest)
		{
			/* FOR DEBUGGING ONLY
			ob_start(); 
			echo ( JRequest::getVar('description', '', 'post', STRING, JREQUEST_ALLOWHTML) );
			$var = ob_get_contents(); 
			ob_end_clean(); 
			$fp=fopen('zlog.txt','w'); 
			fputs($fp,$var); 
			fclose($fp); 		
			*/
		
			//update comments
			$model = $this->getModel('discussions');
			//$descr = JRequest::getVar('description', '', 'post');
			$descr = $_POST['description'];
			$comments = $model->comment(JRequest::getVar('issue_id'), $user->id, $descr); 
 			
			if($comments == false){
				$ret['msg'] = JText::_('COMMENT_ERROR');
				echo json_encode($ret);
				return;
			}

			//notify admin by email
			$mail = $model->commentNotificationMail(JRequest::getVar('issue_id'), $user->id, $descr);
 			if ($mail == false ){

/* 				ob_start(); 
				echo 'mail failed';
				$var = ob_get_contents(); 
				ob_end_clean(); 
				$fp=fopen('zlog.txt','w'); 
				fputs($fp,$var); 
				fclose($fp); 
 */				
				$ret['msg'] = JText::_('Comment sent but no notification mail sent to administrator (Please refresh by pressing F5');
				echo json_encode($ret);
				return;
			}
			
			
			$ret['msg'] = JText::_('COMMENT_ADDED');
			//$ret['comments'] = json_encode($comments);
			$ret['comments'] = $comments;
			header("Content-Type: application/xhtml+xml; charset=utf-8");
			echo json_encode($ret);
			
			
			return;	
		}
		else {
			//$this->setRedirect(JRoute::_('index.php?option=com_users&view=login', false));
			$ret['msg'] = JText::_('ONLY_LOGGED_COMMENT');
			header("Content-Type: application/xhtml+xml; charset=utf-8");
			echo json_encode($ret);
			
		}
	}

	/**
	* only called async from ajax	
	* function returns vote counter or -1 if fail
	*/
	function addVote()
	{
		JRequest::checkToken('get') or jexit('Invalid Token');
		
		$user =& JFactory::getUser();
		if(!$user->guest)
		{
			//update vote
			$model = $this->getModel('issue');
			if($model->getHasVoted() == 0){
				$votes = $model->vote(); 
				if($votes == -1){
					$ret['msg'] = JText::_('VOTE_ERROR');
					echo json_encode($ret);
				}
			
				$ret['msg'] = JText::_('VOTE_ADDED');
				$ret['votes'] = $votes;
				echo json_encode($ret);
			}
			else{
				$ret['msg'] = JText::_('ALREADY_VOTED');
				echo json_encode($ret);			
			}
		}
		else {
			//$this->setRedirect(JRoute::_('index.php?option=com_users&view=login', false));
			$ret['msg'] = JText::_('ONLY_LOGGED_VOTE');
			echo json_encode($ret);
		}
		//return 0;
	}
		
	function smartLogin()
	{
		$view = JRequest::getCmd('view', 'smartLogin');
		JRequest::setVar('view', $view);
		
		$v = & $this->getView($view, 'html');
		///$v->setModel($this->getModel($view)); //$view=addissue
		//$v->display();
		parent::display();
		
		return $this;
	}
	
	/**
	* only called async from ajax as format=raw from ajax
	*/	
	function getMarkersAsXML()
	{
		JRequest::checkToken('get') or jexit('Invalid Token');
		$v = & $this->getView('issues', 'raw');
		$v->setModel($this->getModel('issues'), true);
		$v->display(); 
	}

	/**
	* only called async from ajax as format=raw from ajax
	*/	
	function getMarkerAsXML()
	{
		//JRequest::checkToken() or jexit('Invalid Token'); //for write
		JRequest::checkToken('get') or jexit('Invalid Token');	//for read
		
		$v = & $this->getView('issue', 'raw');
		$v->setModel($this->getModel('issue'), true);
		$v->display();
	}	
	

	function printIssue()
	{
		$v = & $this->getView('issue', 'print');		//view.print.php
		$v->setModel($this->getModel('issue'), true);	//load issue model
		$v->setModel($this->getModel('discussions'));	//load comments as well
		$v->display('print');							//template set to tmpl/default_print.php
		
	}
	
	/* 
		model loads and inside view.print.php all issues without paging
		are loaded.
	*/
	function printIssues()
	{
		$v = & $this->getView('issues', 'print');		//view.print.php
		$v->setModel($this->getModel('issues'), true);	//load issues model
		$v->display('print');							//template set to tmpl/default_print.php
	}
	
	
}
