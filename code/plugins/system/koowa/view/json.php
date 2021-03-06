<?php
/**
 * @version     $Id: json.php 2106 2010-05-26 19:30:56Z johanjanssens $
 * @category	Koowa
 * @package     Koowa_View
 * @subpackage  Json
 * @copyright   Copyright (C) 2007 - 2010 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license     GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.koowa.org
 */

/**
 * View JSON Class
 *
 * @author      Mathias Verraes <mathias@joomlatools.org>
 * @category	Koowa
 * @package     Koowa_View
 * @subpackage  Json
 */
class KViewJson extends KViewAbstract
{
	public function __construct(array $options = array())
	{
		parent::__construct($options);

		//Set the correct mime type
		$this->_document->setMimeEncoding('application/json');
	}

    public function display($tpl = null)
    {
    	echo json_encode($this->getProperties());
    }
}