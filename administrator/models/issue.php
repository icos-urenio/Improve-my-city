<?php
/**
 * @version     2.0
 * @package     com_improvemycity
 * @copyright   Copyright (C) 2011 - 2012 URENIO Research Unit. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      URENIO Research Unit
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
				$item->votes = 0;	/* changed temporarly from 1*/
				
			//keep issue status to session so before saving to check for changes...
			$session =& JFactory::getSession();
			$session->set( 'previousIssueStatus', $item->currentstatus );
		}
		
		return $item;
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
		
		/*TODO: Tide up the following lines to a decent member function */
		
		//get link to the issue
		$issueLink = 'http://' . $_SERVER['HTTP_HOST'] . JRoute::_('index.php?option=com_improvemycity&view=issue&issue_id='.$table->id);
		$issueLink = str_replace('/administrator', '', $issueLink);
		
		$user =& JFactory::getUser($table->userid);	//user's id needed. Not admin's
		$app = JFactory::getApplication();
		$mailfrom	= $app->getCfg('mailfrom');
		$fromname	= $app->getCfg('fromname');
		$sitename	= $app->getCfg('sitename');		

		$session =& JFactory::getSession();
		$prev 	 = $session->get( 'previousIssueStatus', -1 );		
		$current = $table->currentstatus;		
		
		/* (A) ****---   Send notification mail for status change to the user who submitted the issue */		
		
		$issueRecipient = $user->email;		
		if($prev != -1 && $issueRecipient != ''){	//just in case anything wrong with session...
			$subject = 'Αλλαγή κατάστασης αιτήματος';

			$mail = JFactory::getMailer();
			$mail->isHTML(true);
			$mailer->Encoding = 'base64';
			$mail->addRecipient($issueRecipient);
			$mail->setSender(array($mailfrom, $fromname));
			$mail->setSubject($sitename.': '.$subject);

			if($prev == 1 && $current == 2){	//open to ack
				$body = '';
				$body .= 'To αίτημα σας με τίτλο "'.$table->title.'" προωθήθηκε στην αρμόδια υπηρεσία του Δήμου.' . '<br />';
				$body .= 'Μπορείτε να δείτε <a href="'.$issueLink.'">εδώ</a> τα σχετικά σχόλια (αν υπάρχουν).' . '<br />';
				$body .= 'Μόλις το αίτημά σας διεκπεραιωθεί θα λάβετε ενημερωτικό email.' . '<br />';
				$body .= '<br />' . $issueLink;
				
				$mail->setBody($body);
				$sent = $mail->Send();			
				
				//inform admin with message than an email is sent to the user
				JFactory::getApplication()->enqueueMessage( 'Η κατάσταση του αιτήματος άλλαξε. Ένα ενημερωτικό email στάλθηκε στον χρήστη: ' . $user->email . ' (' . $user->name .')' );				
			}
			if( ($prev == 2 && $current == 3) || ($prev == 1 && $current == 3) ){	//ack to close or open to close
				
				$body = '';
				$body .= 'To αίτημα σας με τίτλο "'.$table->title.'" διεκπεραιώθηκε από την αρμόδια υπηρεσία του Δήμου.' . '<br />';
				$body .= 'Μπορείτε να δείτε <a href="'.$issueLink.'">εδώ</a> τα σχετικά σχόλια (αν υπάρχουν).' . '<br />';
				$body .= '<br />' . $issueLink;
				
				$mail->setBody($body);
				$sent = $mail->Send();				
				
				//inform admin with message than an email is sent to the user
				JFactory::getApplication()->enqueueMessage( 'Το αίτημα διεκπεραιώθηκε. Ένα ενημερωτικό email στάλθηκε στον χρήστη: ' . $user->email . ' (' . $user->name .')' );				
			}

		}
			
	}
}