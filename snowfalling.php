<?php
/**
 * @Copyright
 * @package     SnowFalling
 * @author      Clemens Neubauer {@link cn-tools@gmx.at#}
 * @version     0-0-1
 * @date        Created on 13-Feb-2014
 * @link        Project Site {@link https://github.com/cn-tools/plg_snowfall}
 *
 * @license GNU/GPL
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
defined('_JEXEC') or die('Restricted access');

class plgContentSnowFalling extends JPlugin
{
    public function onContentBeforeDisplay($context, &$row, &$params, $page = 0)
    {
		if (($this->params->get('snowmax'))
		 && (intval($this->params->get('snowmax'))>0)
		 && ($this->check_in_date_range($this->params->get('startmonth'), $this->params->get('startday'), $this->params->get('endmonth'), $this->params->get('endday')))){
			$document = JFactory::getDocument();

			$script = 'var snowzindex='.$this->params->get('snowzindex').'; ';
			$script .= 'var snowmax='.$this->params->get('snowmax').'; ';
			$script .= 'var sinkspeed='.($this->params->get('sinkspeed')/10).'; ';
			$script .= 'var snowminsize='.$this->params->get('snowminsize').'; ';
			$script .= 'var snowmaxsize='.$this->params->get('snowmaxsize').'; ';
			$script .= 'var snowingzone='.$this->params->get('snowingzone').'; ';
			$script .= 'var snowaddid="'.uniqid('cnts',TRUE).$this->params->get('snowaddid').'"; ';
			
			if (($this->params->get('snowtextimage')=='2') && ($this->params->get('snowimage'))){
				$script .= 'var snowletter=\'<img src="'.JURI::base().htmlspecialchars($this->params->get('snowimage')).'" />\'; ';
			} elseif (!$this->params->get('snowletter')) {
				$script .= 'var snowletter="*"; ';
			} else {
				$script .= 'var snowletter="'.$this->params->get('snowletter').'"; ';
			}
			if (!$this->params->get('snowcolor')){
				$script .= 'var snowcolor=new Array("#AAAACC","#DDDDFF","#CCCCDD","#F3F3F3","#F0FFFF"); ';
			}else{
				$script .= 'var snowcolor=new Array('.$this->params->get('snowcolor').'); ';
			}
			if (!$this->params->get('snowtype')){
				$script .= 'var snowtype=new Array("Arial Black","Arial Narrow","Times","Comic Sans MS"); ';
			}else{
				$script .= 'var snowtype=new Array('.$this->params->get('snowtype').'); ';
			}

			$script .= 'initsnow(); ';
			
			$document->addScriptDeclaration($script);
	
			$document->addScript('plugins/content/snowfalling/snow.js', 'text/javascript');
		};
	}
	private function check_in_date_range($start_month, $start_day, $end_month, $end_day)
	{
		$lFlag = FALSE;
		$lAktMonth = intval(date('m'));
		$lAktDay = intval(date('d'));
		$lStartMonth = intval($start_month);
		$lStartDay = intval($start_day);
		$lEndMonth = intval($end_month);
		$lEndDay = intval($end_day);
		
		
		if ($lEndMonth < $lStartMonth){
			// over year
			if ($lAktMonth > $lStartMonth) {
				$lFlag = TRUE;
			} elseif (($lAktMonth == $lStartMonth) && ($lAktDay >= $lStartDay)) {
				$lFlag = TRUE;
			} elseif ($lAktMonth < $lEndMonth) {
				$lFlag = TRUE;
			} elseif (($lAktMonth == $lEndMonth) && ($lAktDay <= $lEndDay)) {
				$lFlag = TRUE;
			}
		} elseif ((($lAktMonth > $lStartMonth) || (($lAktMonth == $lStartMonth) && ($lAktDay >= $lStartDay)))
			&& (($lAktMonth < $lEndMonth) || (($lAktMonth == $lEndMonth) && ($lAktDay <= $lEndDay))))
		{
			$lFlag = TRUE;
		}
		
		return $lFlag;
	}		
}
