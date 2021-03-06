<?php
/**
 * @version     $Id: stacked.php 2106 2010-05-26 19:30:56Z johanjanssens $
 * @category	Koowa
 * @package     Koowa_Chart
 * @subpackage  Google
 * @copyright   Copyright (C) 2007 - 2010 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license     GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link        http://www.koowa.org
 */

/**
 * Google Chart Bar Stacked
 *
 * @author      Mathias Verraes <mathias@joomlatools.org>
 * @category	Koowa
 * @package     Koowa_Chart
 * @subpackage  Google
 * @version     1.0
 */
class KChartGoogleBarStacked extends KChartGoogleBar
{
    //'BarStackedHorizontal' => 'bhs', 'BarStackedVertical' => 'bvs');
    protected $_type    = 'bvs';

    /**
     * Set bar count
     */
    protected function _setBarCount()
    {
        $this->totalBars = KChartGoogleHelper::getMaxCountOfArray($this->_data);
    }

    /**
     * Set to horizontal
     *
     * @return this
     */
    public function setHorizontal()
    {
        $this->_type = 'bhs';
        return $this;
    }

    /**
     * Set to vertical (default)
     *
     * @return this
     */
    public function setVertical()
    {
        $this->_type = 'bvs';
        return $this;
    }


    protected function scaleValues()
    {
        $this->setScalar();
        $this->_scaledValues = KChartGoogleHelper::getScaledArray($this->_data, $this->_scalar);
    }

    function setScalar()
    {
        $maxValue = 100;
        $maxValue = max($maxValue, KChartGoogleHelper::getMaxOfArray(KChartGoogleHelper::addArrays($this->_data)));
        if($maxValue <100) {
            $this->_scalar = 1;
        }
        else {
            $this->_scalar = 100/$maxValue;
        }
    }

}