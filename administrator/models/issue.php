<?php
/**
 * @version     2.5.x
 * @package     com_improvemycity
 * @copyright   Copyright (C) 2011 - 2012 URENIO Research Unit. All rights reserved.
 * @license     GNU Affero General Public License version 3 or later; see LICENSE.txt
 * @author      Ioannis Tsampoulatidis for the URENIO Research Unit
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Improvemycity model.
 */
class ImprovemycityModelIssue extends JModelAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_IMPROVEMYCITY';


	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'Issue', $prefix = 'ImprovemycityTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		An optional array of data for the form to interogate.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	JForm	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Initialise variables.
		$app	= JFactory::getApplication();

		// Get the form.
		$form = $this->loadForm('com_improvemycity.issue', 'issue', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		
		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_improvemycity.edit.issue.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 * @since	1.6
	 */
	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk)) {
			//Do any procesing on fields here if needed
			if($item->votes == 0 || $item->votes == '')
				$item->votes = 0;	
				
			//keep issue status to session so before saving to check for changes...
			$session =& JFactory::getSession();
			$session->set( 'previousIssueStatus', $item->currentstatus );
			$session->set( 'previousCatid', $item->catid );
		}
		
		return $item;
	}

	public static function getFeedRoute($url)
	{
		// Get the router.
		$app = JApplication::getInstance('site');
		$router = $app->getRouter();
		// Make sure that we have our router
		if (!$router)
		{
			return null;
		}
		if ((strpos($url, '&') !== 0) && (strpos($url, 'index.php') !== 0))
		{
			return $url;
		}
		// Build route.
		$uri = $router->build($url);
		$url = $uri->toString(array('path', 'query', 'fragment'));
		// Replace spaces.
		$url = preg_replace('/\s/u', '%20', $url);
		// Replace '/administrator'
		$url = str_replace('/administrator', '', $url);
		// Strip .html, just in case
		$url = str_replace('.html', '', $url);
		$url = htmlspecialchars($url);
		return $url;
	}




	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @since	1.6
	 */
	protected function prepareTable(&$table)
	{
		jimport('joomla.filter.output');

		if (empty($table->id)) {

			// Set ordering to the last item if not set
			if (@$table->ordering === '') {
				$db = JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__improvemycity');
				$max = $db->loadResult();
				$table->ordering = $max+1;
			}
			$table->reported = date('Y-m-d H:i:s');
		}
		
		
		if($table->reported == '0000-00-00 00:00:00'){
			$table->reported = date('Y-m-d H:i:s');
			$table->acknowledged = '0000-00-00 00:00:00';		
			$table->closed = '0000-00-00 00:00:00';		
		}
		
		if($table->currentstatus == '2'){
			if($table->acknowledged == '0000-00-00 00:00:00'){
				$table->acknowledged = date('Y-m-d H:i:s');
				$table->closed = '0000-00-00 00:00:00';
			}
		}
		if($table->currentstatus == '3'){
			if($table->closed == '0000-00-00 00:00:00'){
				$table->closed = date('Y-m-d H:i:s');
			}
			
		}
		
		if($table->userid == 0){
			$user =& JFactory::getUser();
			$table->userid = $user->id;  
		}
		

		//all notifications should be sent on user's language if set
		$user =& JFactory::getUser($table->userid);	//user's id needed. Not admin's
		
		$lang = JFactory::getLanguage();
		$user_language = $user->getParam('language');
		if($user_language == ''){
			$user_language = JComponentHelper::getParams('com_languages')->get('site'); //the site's default
		}

		//SEF links
		$issueLink = $this->getFeedRoute('index.php?option=com_improvemycity&view=issue&issue_id='.$table->id);
		$issueLink = 'http://' . $_SERVER['HTTP_HOST'] . $issueLink;
		//$issueLink = str_replace('component/improvemycity', '<your alias here>', $issueLink);
		$issueLink .= '?language='.$user_language;

		$issueAdminLink = JURI::root() . 'administrator/' . 'index.php?option=com_improvemycity&view=issue&task=issue.edit&id='.$table->id.'&language='.$user_language; 

		$extension = 'com_improvemycity';
		$base_dir = JPATH_ADMINISTRATOR;
		$reload = true;
		$lang->load($extension, $base_dir, $user_language, $reload);		


		$app = JFactory::getApplication();
		$mailfrom	= $app->getCfg('mailfrom');
		$fromname	= $app->getCfg('fromname');
		$sitename	= $app->getCfg('sitename');		

		$session =& JFactory::getSession();
		$prev 	 = $session->get( 'previousIssueStatus', -1 );		
		$prevCatid = $session->get( 'previousCatid', -1 );		
		
		$current = $table->currentstatus;		
		
		$app = JFactory::getApplication();
		
		// Load the parameters.
		$settings = &JComponentHelper::getParams('com_improvemycity');
		$mailCategoryChangeAdmins = $settings->get('mailcategorychangeadmins');		
		$mailStatusChangeUser = $settings->get('mailstatuschangeuser');		
		
		/* (A) ****---  Send notification mail for status change to the user who submitted the issue */		
		if($mailStatusChangeUser == 1) {
			$issueRecipient = $user->email;		
			if($prev != -1 && $issueRecipient != ''){	//just in case anything wrong with session...
				$subject = JText::_('COM_IMPROVEMYCITY_MAIL_USER_STATUS_CHANGE_SUBJECT');

				$mail = JFactory::getMailer();
				$mail->isHTML(true);
				$mailer->Encoding = 'base64';
				$mail->addRecipient($issueRecipient);
				$mail->setSender(array($mailfrom, $fromname));
				$mail->setSubject($sitename.': '.$subject);

				if($prev == 1 && $current == 2){	//open to ack
					
					$body = sprintf(JText::_('COM_IMPROVEMYCITY_CHANGE_STATUS_BODY_ACK')
							, $table->title
							, $issueLink
							, $issueLink );
											
					$mail->setBody($body);
					$sent = $mail->Send();			
					
					//inform admin with message than an email is sent to the user
					JFactory::getApplication()->enqueueMessage( sprintf(JText::_('COM_IMPROVEMYCITY_STATUS_CHANGE_INFO_MESSAGE_ACK'), $user->name, $user->email) );
					
				}
				if( ($prev == 2 && $current == 3) || ($prev == 1 && $current == 3) ){	//ack to close or open to close
					
					$body = sprintf(JText::_('COM_IMPROVEMYCITY_CHANGE_STATUS_BODY_CLOSE')
							, $table->title
							, $issueLink
							, $issueLink );					
					
					$mail->setBody($body);
					$sent = $mail->Send();				
					
					//inform admin with message than an email is sent to the user
					JFactory::getApplication()->enqueueMessage( sprintf(JText::_('COM_IMPROVEMYCITY_STATUS_CHANGE_INFO_MESSAGE_CLOSE'), $user->name, $user->email) );				
				}

			}
		}//settings

		/* (Β) ****---  Send notification mail if category is changed to the appropriate admins (as defined on -updated- category note field)*/		
		if($mailCategoryChangeAdmins == 1 && isset($table->id)) {
			
			//get the recipient email(s) as defined in the "note" field of the selected category
			$issueRecipient = '';
			$db		= $this->getDbo();
			$query	= $db->getQuery(true);
			$query->select('a.note as note, a.title as title');
			$query->from('`#__categories` AS a');
			
			$query->where('a.id = ' . $table->catid);		
			$db->setQuery($query);
			//$result = $db->loadResult();
			$row = $db->loadAssoc();
			if(!empty($row)){
				$issueRecipient = $row['note'];
				$arRecipient = explode(";",$issueRecipient);
				$arRecipient = array_filter($arRecipient, 'strlen');
				$categoryTitle = $row['title'];
			}

			if($prevCatid != $table->catid && !empty($issueRecipient)) {	//only if category note contains email(s) and category has changed
				$subject = sprintf(JText::_('COM_IMPROVEMYCITY_MAIL_ADMINS_NEW_ISSUE_SUBJECT'), $user->name, $user->email);
				
				$body = sprintf(JText::_('COM_IMPROVEMYCITY_MAIL_ADMINS_NEW_ISSUE_BODY')
						, $categoryTitle
						, $table->title
						, $table->address
						, $issueLink
						, $issueLink
						, $issueAdminLink
						, $issueAdminLink );
				
				$mail = JFactory::getMailer();
				$mail->isHTML(true);
				$mail->Encoding = 'base64';
				foreach($arRecipient as $recipient)
					$mail->addRecipient($recipient);
				$mail->setSender(array($mailfrom, $fromname));
				$mail->setSubject($sitename.': '.$subject);
				$mail->setBody($body);
				$sent = $mail->Send();
			}

		}//settings

	}
}
