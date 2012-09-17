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
JRequest::checkToken() or jexit( 'Invalid Token' );

jimport('joomla.application.component.controllerform');

class ImprovemycityControllerIssue extends JControllerForm
{
	
	public function cancel($key = 'issue_id')
	{
		/* Never edit a record just redirect */
		//parent::cancel($key);	
		// Redirect to the return page.
		$this->setRedirect($this->getReturnPage());
	}
	
	
	/**
	 * Get the return URL.
	 *
	 * If a "return" variable has been passed in the request
	 *
	 * @return	string	The return URL.
	 */
	protected function getReturnPage()
	{
		$return = JRequest::getVar('return', null, 'default', 'base64');

		if (empty($return) || !JUri::isInternal(base64_decode($return))) {
			return JURI::base();
		}
		else {
			return base64_decode($return);
		}
	}	
	
	public function getModel($name = 'addissue', $prefix = '', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}	
	
	
	//TODO: maybe to put this on a model instead ?
	/**
	 * Send Email.
	 * taken from \components\com_contact\controllers\contact.php  
	 * If a "return" variable has been passed in the request
	 * 
	 * @return boolen	True if successfully send.
	 */	
	private function _sendEmail($data, $contact)
	{
		$app		= JFactory::getApplication();
		$params 	= JComponentHelper::getParams('com_contact');
		if ($contact->email_to == '' && $contact->user_id != 0) {
			$contact_user = JUser::getInstance($contact->user_id);
			$contact->email_to = $contact_user->get('email');
		}
		$mailfrom	= $app->getCfg('mailfrom');
		$fromname	= $app->getCfg('fromname');
		$sitename	= $app->getCfg('sitename');
		$copytext 	= JText::sprintf('COM_CONTACT_COPYTEXT_OF', $contact->name, $sitename);

		$name		= $data['contact_name'];
		$email		= $data['contact_email'];
		$subject	= $data['contact_subject'];
		$body		= $data['contact_message'];

		// Prepare email body
		$prefix = JText::sprintf('COM_CONTACT_ENQUIRY_TEXT', JURI::base());
		$body	= $prefix."\n".$name.' <'.$email.'>'."\r\n\r\n".stripslashes($body);

		$mail = JFactory::getMailer();
		$mail->addRecipient($contact->email_to);
		$mail->addReplyTo(array($email, $name));
		$mail->setSender(array($mailfrom, $fromname));
		$mail->setSubject($sitename.': '.$subject);
		$mail->setBody($body);
		$sent = $mail->Send();

		//If we are supposed to copy the sender, do so.

		// check whether email copy function activated
		if ( array_key_exists('contact_email_copy',$data)  ) {
			$copytext		= JText::sprintf('COM_CONTACT_COPYTEXT_OF', $contact->name, $sitename);
			$copytext		.= "\r\n\r\n".$body;
			$copysubject	= JText::sprintf('COM_CONTACT_COPYSUBJECT_OF', $subject);

			$mail = JFactory::getMailer();
			$mail->addRecipient($email);
			$mail->addReplyTo(array($email, $name));
			$mail->setSender(array($mailfrom, $fromname));
			$mail->setSubject($copysubject);
			$mail->setBody($copytext);
			$sent = $mail->Send();
		}

		return $sent;
	}
}
