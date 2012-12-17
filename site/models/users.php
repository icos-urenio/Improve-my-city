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

/**
 * Model
 */
class ImprovemycityModelUsers extends JModel
{
	
	public function authenticateUser($username, $password)
	{

		$response = array();
		
		// Joomla does not like blank passwords
		if (empty($password)) {
			$response['error_message'] = JText::_('JGLOBAL_AUTH_EMPTY_PASS_NOT_ALLOWED');
			return $response;
		}
		
		// Get a database object
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->select('id, password');
		$query->from('#__users');
		$query->where('username=' . $db->Quote($username));
		$query->where('block=0');
		
		
		$db->setQuery($query);
		$result = $db->loadObject();
		
		if ($result) {
			$parts	= explode(':', $result->password);
			$crypt	= $parts[0];
			$salt	= @$parts[1];
			$testcrypt = JUserHelper::getCryptedPassword($password, $salt);
		
			if ($crypt == $testcrypt) {
				$user = JUser::getInstance($result->id); // Bring this in line with the rest of the system
				$response['id'] = $user->id;
				$response['email'] = $user->email;
				$response['fullname'] = $user->name;
				if (JFactory::getApplication()->isAdmin()) {
					$response['language'] = $user->getParam('admin_language');
				}
				else {
					$response['language'] = $user->getParam('language');
				}
				$response['error_message'] = '';
			} else {
				$response['error_message'] = JText::_('JGLOBAL_AUTH_INVALID_PASS');
			}
		} else {
			$response['error_message'] = JText::_('JGLOBAL_AUTH_NO_USER');
		}
		return $response;
	}
	
	
	public function getUserVotes($userid)
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		$query->select('a.id, a.improvemycityid, a.votingdate');
		$query->from('`#__improvemycity_votes` AS a');
		$query->where('a.userid = ' . $userid);
		
		$db->setQuery($query);
		$result = $db->loadRowList();
		
		return $result;		
	}
	
	public function userExists($username)
	{
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		
		$query->select('COUNT(a.id) AS num');
		$query->from('`#__users` AS a');
		$query->where('a.username = ' . $db->Quote($username));

		$db->setQuery($query);
		$result = $db->loadResult();
		
		return $result;		
	}

	
	public function register($temp)
	{

		$config = JFactory::getConfig();
		$db		= $this->getDbo();
		$params = JComponentHelper::getParams('com_users');
	
		// Initialise the table with JUser.
		$user = new JUser;
		
		//$data = (array)$this->getData();
		$data['groups'] = array();
		
		// Get the default new user group, Registered if not specified.
		$system	= $params->get('new_usertype', 2);
		
		$data['groups'][] = $system;		
		
		// Merge in the registration data.
		foreach ($temp as $k => $v) {
			$data[$k] = $v;
		}
	
		// Prepare the data for the user object.
		$data['email']		= $data['email1'];
		$data['password']	= $data['password1'];
		$useractivation = $params->get('useractivation');
		$sendpassword = $params->get('sendpassword', 1);
	
		// Check if the user needs to activate their account.
		if (($useractivation == 1) || ($useractivation == 2)) {
			$data['activation'] = JApplication::getHash(JUserHelper::genRandomPassword());
			$data['block'] = 1;
		}

		// Bind the data.
		if (!$user->bind($data)) {
			$this->setError(JText::sprintf('COM_USERS_REGISTRATION_BIND_FAILED', $user->getError()));
			//return false;
			return JText::sprintf('COM_USERS_REGISTRATION_BIND_FAILED', $user->getError());
		}
	
		// Load the users plugin group.
		JPluginHelper::importPlugin('user');
	
		// Store the data.
		if (!$user->save()) {
			$this->setError(JText::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $user->getError()));
			//return false;
			return JText::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $user->getError());
		}
	
		// Compile the notification mail values.
		$data = $user->getProperties();
		$data['fromname']	= $config->get('fromname');
		$data['mailfrom']	= $config->get('mailfrom');
		$data['sitename']	= $config->get('sitename');
		$data['siteurl']	= JUri::root();
	
		// Handle account activation/confirmation emails.
		if ($useractivation == 2)
		{
			// Set the link to confirm the user email.
			$uri = JURI::getInstance();
			$base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
			$data['activate'] = $base.JRoute::_('index.php?option=com_users&task=registration.activate&token='.$data['activation'], false);
	
			$emailSubject	= JText::sprintf(
					'COM_USERS_EMAIL_ACCOUNT_DETAILS',
					$data['name'],
					$data['sitename']
			);
	
			if ($sendpassword)
			{
				$emailBody = JText::sprintf(
						'COM_USERS_EMAIL_REGISTERED_WITH_ADMIN_ACTIVATION_BODY',
						$data['name'],
						$data['sitename'],
						$data['siteurl'].'index.php?option=com_users&task=registration.activate&token='.$data['activation'],
						$data['siteurl'],
						$data['username'],
						$data['password_clear']
				);
			}
			else
			{
				$emailBody = JText::sprintf(
						'COM_USERS_EMAIL_REGISTERED_WITH_ADMIN_ACTIVATION_BODY_NOPW',
						$data['name'],
						$data['sitename'],
						$data['siteurl'].'index.php?option=com_users&task=registration.activate&token='.$data['activation'],
						$data['siteurl'],
						$data['username']
				);
			}
		}
		elseif ($useractivation == 1)
		{
			// Set the link to activate the user account.
			$uri = JURI::getInstance();
			$base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
			$data['activate'] = $base.JRoute::_('index.php?option=com_users&task=registration.activate&token='.$data['activation'], false);
	
			$emailSubject	= JText::sprintf(
					'COM_USERS_EMAIL_ACCOUNT_DETAILS',
					$data['name'],
					$data['sitename']
			);
	
			if ($sendpassword)
			{
				$emailBody = JText::sprintf(
						'COM_USERS_EMAIL_REGISTERED_WITH_ACTIVATION_BODY',
						$data['name'],
						$data['sitename'],
						$data['siteurl'].'index.php?option=com_users&task=registration.activate&token='.$data['activation'],
						$data['siteurl'],
						$data['username'],
						$data['password_clear']
				);
			}
			else
			{
				$emailBody = JText::sprintf(
						'COM_USERS_EMAIL_REGISTERED_WITH_ACTIVATION_BODY_NOPW',
						$data['name'],
						$data['sitename'],
						$data['siteurl'].'index.php?option=com_users&task=registration.activate&token='.$data['activation'],
						$data['siteurl'],
						$data['username']
				);
			}
		}
		else
		{
	
			$emailSubject	= JText::sprintf(
					'COM_USERS_EMAIL_ACCOUNT_DETAILS',
					$data['name'],
					$data['sitename']
			);
	
			$emailBody = JText::sprintf(
					'COM_USERS_EMAIL_REGISTERED_BODY',
					$data['name'],
					$data['sitename'],
					$data['siteurl']
			);
		}
	
		// Send the registration email.
		$return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $data['email'], $emailSubject, $emailBody);
	
		//Send Notification mail to administrators
		if (($params->get('useractivation') < 2) && ($params->get('mail_to_admin') == 1)) {
			$emailSubject = JText::sprintf(
					'COM_USERS_EMAIL_ACCOUNT_DETAILS',
					$data['name'],
					$data['sitename']
			);
	
			$emailBodyAdmin = JText::sprintf(
					'COM_USERS_EMAIL_REGISTERED_NOTIFICATION_TO_ADMIN_BODY',
					$data['name'],
					$data['username'],
					$data['siteurl']
			);
	
			// get all admin users
			$query = 'SELECT name, email, sendEmail' .
					' FROM #__users' .
					' WHERE sendEmail=1';
	
			$db->setQuery( $query );
			$rows = $db->loadObjectList();
	
			// Send mail to all superadministrators id
			foreach( $rows as $row )
			{
				$return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $row->email, $emailSubject, $emailBodyAdmin);
	
				// Check for an error.
				if ($return !== true) {
					$this->setError(JText::_('COM_USERS_REGISTRATION_ACTIVATION_NOTIFY_SEND_MAIL_FAILED'));
					//return false;
					return JText::_('COM_USERS_REGISTRATION_ACTIVATION_NOTIFY_SEND_MAIL_FAILED');
				}
			}
		}
		// Check for an error.
		if ($return !== true) {
			$this->setError(JText::_('COM_USERS_REGISTRATION_SEND_MAIL_FAILED'));
	
			// Send a system message to administrators receiving system mails
			$db = JFactory::getDBO();
			$q = "SELECT id
			FROM #__users
			WHERE block = 0
			AND sendEmail = 1";
			$db->setQuery($q);
			$sendEmail = $db->loadColumn();
			if (count($sendEmail) > 0) {
				$jdate = new JDate();
				// Build the query to add the messages
				$q = "INSERT INTO ".$db->quoteName('#__messages')." (".$db->quoteName('user_id_from').
				", ".$db->quoteName('user_id_to').", ".$db->quoteName('date_time').
				", ".$db->quoteName('subject').", ".$db->quoteName('message').") VALUES ";
				$messages = array();
	
				foreach ($sendEmail as $userid) {
					$messages[] = "(".$userid.", ".$userid.", '".$jdate->toSql()."', '".JText::_('COM_USERS_MAIL_SEND_FAILURE_SUBJECT')."', '".JText::sprintf('COM_USERS_MAIL_SEND_FAILURE_BODY', $return, $data['username'])."')";
				}
				$q .= implode(',', $messages);
				$db->setQuery($q);
				$db->query();
			}
			//return false;
			return JText::_('COM_USERS_REGISTRATION_SEND_MAIL_FAILED');
		}
	
		if ($useractivation == 1)
			return "useractivate";
		elseif ($useractivation == 2)
		return "adminactivate";
		else
			return $user->id;
	}
	
	
}
