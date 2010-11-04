<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin');

class plgSystemMultisite extends JPlugin
{
	public function onAfterRoute()
	{
		$app  = JFactory::getApplication();
		$user = JFactory::getUser();
		
		//Get the site
		$site = $app->getSite();
		
		//Re-login
		if($app->getUserState('application.site') != $site && !$user->get('guest'))
		{
			// Fork the session to prevent session fixation issues
			$session = JFactory::getSession();
			$session->fork();
			
			$app->_createSession($session->getId());
			
			// Import the user plugin group
			JPluginHelper::importPlugin('user');

			$response = array(
				'username' 		=> $user->get('username'),
				'email'	   		 => $user->get('username'),
				'fullname' 		 => $user->get('fullname'),
				'password_clear' => ''
			);
			
			$options = array(
				'group' 		=> 'Public Backend',
				'autoregister' 	=> false,
			);
			
			$results = $app->triggerEvent('onLoginUser', array($response, $options));
			
			if(JError::isError($results[0])) 
			{
				$app->triggerEvent('onLoginFailure', array((array)$response));
				
				//Log the user out
				$app->logout();
			}
		}
		
		// Set session
		JFactory::getApplication()->setUserState('application.site', $site);
	}
	
	public function onAfterRender()
	{
		$app  = JFactory::getApplication();
		$site = $app->getSite();
		
		//Exception for the default site
		if($site != 'default') 
		{
			if($app->isSite()) 
			{
				//Make images paths absolute
				$body = str_replace(JURI::base().'images/', JURI::base(true).'/sites/'.$site.'/images/', JResponse::getBody());
				$body = str_replace(array('"images/','"/images/') , '"/sites/'.$site.'/images/', $body);
			
				JResponse::setBody($body);
			}
		
			if($app->isAdmin()) 
			{
				$index = $app->getCfg('sef_rewrite') ? ''  : 'index.php/';
			
				//Make images paths absolute
				$body = str_replace(array('../images', './images'), JURI::root(true).'/sites/'.$site.'/images', JResponse::getBody());
				
				JResponse::setBody($body);
			}
		}
	}
}