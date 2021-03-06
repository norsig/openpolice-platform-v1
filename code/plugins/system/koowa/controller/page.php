<?php
/**
 * @version		$Id: page.php 2106 2010-05-26 19:30:56Z johanjanssens $
 * @category	Koowa
 * @package     Koowa_Controller
 * @copyright	Copyright (C) 2007 - 2010 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.koowa.org
 */

/**
 * Page Controller Class
 *
 * @author		Johan Janssens <johan@joomlatools.org>
 * @author 		Mathias Verraes <mathias@joomlatools.org>
 * @category	Koowa
 * @package     Koowa_Controller
 * @uses        KSecurityToken
 * @uses        KInflector
 * @uses        KHelperArray
 */
class KControllerPage extends KControllerAbstract
{
	/**
	 * Constructor
	 *
	 * @param array An optional associative array of configuration settings.
	 */
	public function __construct(array $options = array())
	{
		parent::__construct($options);

		// Register extra tasks
		$this->registerTask( 'disable', 'enable');
		$this->registerTask( 'apply'  , 'save'  );
		$this->registerTask( 'add'    , 'edit'  );
	}

	/*
	 * Generic edit action
	 * 
	 * @return void
	 */
	public function edit()
	{
		$cid = KInput::get('cid', array('post', 'get'), 'array.ints', null, array(0));
		$id	 = KInput::get('id', 'get', 'int', null, $cid[0]);
		 
		$this->setRedirect('view='.$this->getClassName('suffix').'&layout=form&id='.$id);
	}

	/*
	 * Generic save action
	 * 
	 * @return KDatabaseRow The row object containing the saved data
	 */
	public function save()
	{
		KSecurityToken::check() or die('Invalid token or time-out, please try again');
		
		// Get the post data from the request
		$data = $this->_getRequest('post');

		// Get the id
		$id	 = KInput::get('id', array('post', 'get'), 'int');

		// Get the table object attached to the model
		$component = $this->getClassName('prefix');
		$model     = $this->getClassName('suffix');
		$view	   = $model;

		$app   = KFactory::get('lib.joomla.application')->getName();
		$table = KFactory::get($app.'::com.'.$component.'.model.'.$model)->getTable();
		$row = $table->fetchRow($id)
			->setProperties($data)
			->save();

		
		switch($this->getTask())
		{
			case 'apply' :
				$redirect = 'view='.$view.'&layout=form&id='.$row->id;
				break;

			case 'save' :
			default     :
				$redirect = 'view='.KInflector::pluralize($view);
		}
		$redirect .= '&format='.KInput::get('format', 'get', 'cmd', null, 'html');

		$this->setRedirect($redirect);
		
		return $row;
	}
		
	/*
	 * Generic cancel action
	 * 
	 * @return void
	 */
	public function cancel()
	{
		$this->setRedirect(
			'view='.KInflector::pluralize($this->getClassName('suffix'))
			.'&format='.KInput::get('format', 'get', 'cmd', null, 'html')
			);
	}
	
	/*
	 * Generic delete function
	 *  
	 * @throws KControllerException
	 * @return void
	 */
	public function delete()
	{
		KSecurityToken::check() or die('Invalid token or time-out, please try again');
		
		$cid = KInput::get('cid', 'post', 'array.ints', null, array());

		if (count( $cid ) < 1) {
			throw new KControllerException(JText::sprintf( 'Select an item to %s', JText::_($this->getTask()), true ) );
		}

		// Get the table object attached to the model
		$component = $this->getClassName('prefix');
		$model     = $this->getClassName('suffix');
		$view	   = $model;

		$app   = KFactory::get('lib.joomla.application')->getName();
		$table = KFactory::get($app.'::com.'.$component.'.model.'.$model)->getTable();
		$table->delete($cid);
		
		$this->setRedirect(
			'view='.KInflector::pluralize($view)
			.'&format='.KInput::get('format', 'get', 'cmd', null, 'html')
		);
	}

	/*
	 * Generic enable action
	 * 
	 * @return void
	 */
	public function enable()
	{
		KSecurityToken::check() or die('Invalid token or time-out, please try again');
	
		$cid = KInput::get('cid', 'post', 'array.ints', null, array());

		$enable  = $this->getTask() == 'enable' ? 1 : 0;

		if (count( $cid ) < 1) {
			throw new KControllerException(JText::sprintf( 'Select a item to %s', JText::_($this->getTask()), true ));
		}

		// Get the table object attached to the model
		$component = $this->getClassName('prefix');
		$model     = $this->getClassName('suffix');
		$view	   = $model;
		
		$app   = KFactory::get('lib.joomla.application')->getName();
		$table = KFactory::get($app.'::com.'.$component.'.model.'.$model)->getTable();
		$table->update(array('enabled' => $enable), $cid);
	
		$this->setRedirect(
			'view='.KInflector::pluralize($view)
			.'&format='.KInput::get('format', 'get', 'cmd', null, 'html')
		);
	}
	
	/**
	 * Generic method to modify the access level of items
	 * 
	 * @return void
	 */
	public function access()
	{
		KSecurityToken::check() or die('Invalid token or time-out, please try again');
		
		$cid 	= KInput::get('cid', 'post', 'array.ints', null, array());
		$access = KInput::get('access', 'post', 'int');
		
		// Get the table object attached to the model
		$component = $this->getClassName('prefix');
		$model     = $this->getClassName('suffix');
		$view	   = $model;

		$app   = KFactory::get('lib.joomla.application')->getName();
		$table = KFactory::get($app.'::com.'.$component.'.model.'.$model)->getTable();
		$table->update(array('access' => $access), $cid);
	
		$this->setRedirect(
			'view='.KInflector::pluralize($view)
			.'&format='.KInput::get('format', 'get', 'cmd', null, 'html'), 
			JText::_( 'Changed items access level')
		);
	}
	
	/**
	 * Generic method to modify the order of the items
	 * 
	 * @return KDatabaseRow The row object containing the reordered row
	 */
	public function order()
	{
		KSecurityToken::check() or die('Invalid token or time-out, please try again');
		
		$id 	= KInput::get('id', 'post', 'int');
		$change = KInput::get('order_change', 'post', 'int');
		
		// Get the table object attached to the model
		$component = $this->getClassName('prefix');
		$name      = KInflector::pluralize($this->getClassName('suffix'));
		$view	   = $name;

		$app   = KFactory::get('lib.joomla.application')->getName();
		$table = KFactory::get($app.'::com.'.$component.'.table.'.$name);
		$row   = $table->fetchRow($id)
			->order($change);
		
		$this->setRedirect(
			'view='.$view
			.'&format='.KInput::get('format', 'get', 'cmd', null, 'html')
		);
		
		return $row;
	}

	/**
	 * Wrapper for JRequest::get(). Override this method to modify the GET/POST data before saving
	 *
	 * @see		JRequest::get()
	 * @todo    Replace with a KInput solution
	 * 
	 * @param	string	$hash	to get (POST, GET, FILES, METHOD)
	 * @param	int		$mask	Filter mask for the variable
	 * @return	mixed	Request hash
	 * @return array
	 */
	protected function _getRequest($hash = 'default', $mask = 0)
	{
		return JRequest::get($hash, $mask);
	}
}
