<?php
/**
 * @version     $Id: scatter.php 2106 2010-05-26 19:30:56Z johanjanssens $
 * @category	Koowa
 * @package     Koowa_Chart
 * @subpackage  Google
 * @copyright   Copyright (C) 2007 - 2010 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license     GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link        http://www.koowa.org
 */

/**
 * Google Chart Scatter
 *
 * @author      Mathias Verraes <mathias@joomlatools.org>
 * @category	Koowa
 * @package     Koowa_Chart
 * @subpackage  Google
 * @version     1.0
 */
class KChartGoogleScatter extends KChartGoogle
{
    // ('Scatter' => 's');
    protected $_type    = 's';

    /**
     * Constructor
     * 
     * @throws KChartException
     */
    public function __construct()
    {
        throw new KChartException(__CLASS__. ' is not implemented yet.');
    }
}