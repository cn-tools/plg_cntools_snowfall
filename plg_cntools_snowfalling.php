<?php
/**
 * @Copyright
 * @package     SnowFalling
 * @author      Clemens Neubauer {@link cn-tools@gmx.at#}
 * @version     0-0-1
 * @date        Created on 13-Feb-2014
 * @link        Project Site {@link https://github.com/cn-tools/plg_cntools_snowfall}
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

class plgSystemPlg_CNTools_SnowFalling extends JPlugin
{
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
	}

	function onAfterRender()
    {
		$app = JFactory::getApplication();
		if($app->isAdmin() === true)
		{
			return;
		}
		
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
			
			$buffer = JResponse::getBody();
			$pos = strrpos($buffer, "</head>");
			if($pos > 0)
			{
				$buffer = substr($buffer, 0, $pos).'<script src="'.JURI::base().'plugins' . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . 'plg_cntools_snowfalling' . DIRECTORY_SEPARATOR . 'snow.js" type="text/javascript"></script>'.'<script type="text/javascript">'.$script.'</script>'.substr($buffer, $pos);
			}		
			
			JResponse::setBody($buffer);
			
			return true;
		};
	}
	private function check_in_date_range($start_month, $start_day, $end_month, $end_day)
	{
		$lFlag = FALSE;
		$lCurrMonth = intval(date('m'));
		$lCurrDay = intval(date('d'));
		$lStartMonth = intval($start_month);
		$lStartDay = intval($start_day);
		$lEndMonth = intval($end_month);
		$lEndDay = intval($end_day);
		
		
		if ($lEndMonth < $lStartMonth){
			// over year
			if ($lCurrMonth > $lStartMonth) {
				$lFlag = TRUE;
			} elseif (($lCurrMonth == $lStartMonth) && ($lCurrDay >= $lStartDay)) {
				$lFlag = TRUE;
			} elseif ($lCurrMonth < $lEndMonth) {
				$lFlag = TRUE;
			} elseif (($lCurrMonth == $lEndMonth) && ($lCurrDay <= $lEndDay)) {
				$lFlag = TRUE;
			}
		} elseif ((($lCurrMonth > $lStartMonth) || (($lCurrMonth == $lStartMonth) && ($lCurrDay >= $lStartDay)))
			&& (($lCurrMonth < $lEndMonth) || (($lCurrMonth == $lEndMonth) && ($lCurrDay <= $lEndDay))))
		{
			$lFlag = TRUE;
		}
		
		return $lFlag;
	}		
}
?>