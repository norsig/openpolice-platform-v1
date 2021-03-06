<?php
/**
* @version      $Id:koowa.php 251 2008-06-14 10:06:53Z mjaz $
* @category		Koowa
* @package      Koowa_Filter
* @copyright    Copyright (C) 2007 - 2010 Johan Janssens and Mathias Verraes. All rights reserved.
* @license      GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
* @link 		http://www.koowa.org
*/

/**
 * Language filter for ISO codes like en-GB (lang-COUNTRY)
 * 
 * Only checks the format, it doesn't care whether the language or country actually exist
 *
 * @author		Mathias Verraes <mathias@joomlatools.org>
 * @category	Koowa
 * @package     Koowa_Filter
 */
class KFilterLang extends KObject implements KFilterInterface
{
	/**
	 * Validate a variable
	 *
	 * @param	mixed	Variable to be validated
	 * @return	bool	True when the variable is valid
	 */
	public function validate($var)
	{
		$var = trim($var);
   	   	$pattern = '/^[a-z]{2}-[A-Z]{2}$/';
    	return (empty($var)) 
    			|| (is_string($var) && preg_match($pattern, $var) == 1);
	}
	
	/**
	 * Sanitize a variable
	 *
	 * @param	mixed	Variable to be sanitized
	 * @return	string
	 */
	public function sanitize($var)
	{
		$var = trim($var);
		
		$parts 	= explode('-', $var, 2);
		if(2 != count($parts)) {
			return null;
		}
		
		$parts[0]	= substr(preg_replace('/[^a-z]*/', '', $parts[0]), 0, 2);
		$parts[1]	= substr(preg_replace('/[^A-Z]*/', '', $parts[1]), 0, 2);
    	$result = implode('-', $parts);
		
		// just making sure :-)
		if($this->validate($result)) {
			return $result;
		}
		
		return null;
	}
}