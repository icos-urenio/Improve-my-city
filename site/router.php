<?php
/**
 * @version     2.5.x
 * @package     com_improvemycity
 * @copyright   Copyright (C) 2011 - 2012 URENIO Research Unit. All rights reserved.
 * @license     GNU Affero General Public License version 3 or later; see LICENSE.txt
 * @author      Ioannis Tsampoulatidis for the URENIO Research Unit
 */

/**
 * @param	array	A named array
 * @return	array
 */
function ImprovemycityBuildRoute(&$query)
{
	$segments = array();

	if (isset($query['view'])) {
		$segments[] = $query['view'];
		unset($query['view']);
	}
	if (isset($query['issue_id'])) {
		$segments[] = $query['issue_id'];
		unset($query['issue_id']);
	}	
	if (isset($query['task'])) {
		$segments[] = $query['task'];
		unset($query['task']);
	}

	if (isset($query['controller'])) {
		$segments[] = $query['controller'];
		unset($query['controller']);
	}
			
	return $segments;
}


function improvemycityParseRoute( $segments )
{
       $vars = array();
	   switch($segments[0])
       {
			case 'issue':
				$vars['view'] = 'issue';
				$vars['issue_id'] = (int) $segments[1];				   
			break;
			case 'issues':
				$vars['view'] = 'issues';
				if(@$segments[1] == 'addIssue')	//@ when canceling addnewissue
					$vars['task'] = 'addIssue';
				if(@$segments[2] == 'improvemycity')
					$vars['controller'] = 'improvemycity';
			break;
			case 'addIssue':
				$vars['task'] = 'addIssue';
				$vars['controller'] = 'improvemycity';
			break;			
			case 'addComment':
				$vars['task'] = 'addComment';
				$vars['controller'] = 'improvemycity';
			break;
			case 'smartLogin':
				$vars['task'] = 'smartLogin';
				$vars['controller'] = 'improvemycity';
			break;	
			case 'printIssue':
				$vars['task'] = 'printIssue';
				$vars['controller'] = 'improvemycity';
				$vars['issue_id'] = (int) $segments[0];
			break;				
			case 'printIssues':
				$vars['task'] = 'printIssues';
				$vars['controller'] = 'improvemycity';
			break;			
       }
	   
	   //TODO: revision needed...
	   if(isset($segments[1])){
			switch($segments[1]){
				case 'addVote':
					$vars['task'] = 'addVote';
					$vars['controller'] = 'improvemycity';
					$vars['issue_id'] = (int) $segments[0];
				break;
				case 'addComment':
					$vars['task'] = 'addComment';
					$vars['controller'] = 'improvemycity';
					$vars['issue_id'] = (int) $segments[0];
				break;				
				case 'printIssue':
					$vars['task'] = 'printIssue';
					$vars['controller'] = 'improvemycity';
					$vars['issue_id'] = (int) $segments[0];
				break;			
				case 'printIssues':
					$vars['task'] = 'printIssues';
					$vars['controller'] = 'improvemycity';
				break;						
			}
	   }
	   
       return $vars;
}
