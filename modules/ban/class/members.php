<?php
/**
 * Xortify Bans & Unbans Function
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       Chronolabs Coopertive (Australia)  http://web.labs.coop
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package         bans
 * @subpackage		ban
 * @author         	Simon Roberts (wishcraft) - meshy@labs.coop
 */


if (!defined('XOOPS_ROOT_PATH')) {
	exit();
}

xoops_loadLanguage('keys', 'global');

require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'functions.php');

/**
 * Class for Ban Profiler
 * @author Simon Roberts (simon@labs.coop)
 * @copyright copyright (c) 2010-2013 labs.coop
 */
class banMembers extends XoopsObject
{
	
	var $_whois_url = 'http://whois.xortify.com/v1/%s/html.api';
	
    function __construct($fid = null)
    {
        $this->initVar('member_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('category_id', XOBJ_DTYPE_INT, null, false);		
		$this->initVar('suid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('uid', XOBJ_DTYPE_INT, null, false);	
        $this->initVar('uname', XOBJ_DTYPE_TXTBOX, null, false, 64);
        $this->initVar('email', XOBJ_DTYPE_TXTBOX, null, false, 255);
		$this->initVar('ip4', XOBJ_DTYPE_TXTBOX, null, true, 255);
		$this->initVar('ip6', XOBJ_DTYPE_TXTBOX, null, false, 65535);
		$this->initVar('long', XOBJ_DTYPE_TXTBOX, null, false, 120);
		$this->initVar('proxy-ip4', XOBJ_DTYPE_TXTBOX, null, false, 255);
		$this->initVar('proxy-ip6', XOBJ_DTYPE_TXTBOX, null, false, 65535);						
		$this->initVar('network-addy', XOBJ_DTYPE_TXTBOX, null, false, 255);
		$this->initVar('mac-addy', XOBJ_DTYPE_TXTBOX, null, false, 255);	
		$this->initVar('country-code', XOBJ_DTYPE_TXTBOX, null, false, 3);
		$this->initVar('country-name', XOBJ_DTYPE_TXTBOX, null, false, 128);
		$this->initVar('region-name', XOBJ_DTYPE_TXTBOX, null, false, 128);
		$this->initVar('city-name', XOBJ_DTYPE_TXTBOX, null, false, 128);
		$this->initVar('postcode', XOBJ_DTYPE_TXTBOX, null, false, 15);
		$this->initVar('latitude', XOBJ_DTYPE_DECIMAL, null, false);
		$this->initVar('longitude', XOBJ_DTYPE_DECIMAL, null, false);
		$this->initVar('timezone', XOBJ_DTYPE_TXTBOX, null, false, 6);
		$this->initVar('made', XOBJ_DTYPE_INT, null, false);
		// Fraud System Added for Card Numbers of Any Origin
		$this->initVar('card-number', XOBJ_DTYPE_TXTBOX, null, false, 42);
		$this->initVar('card-number-parts', XOBJ_DTYPE_ARRAY, array(), false);
		$this->initVar('card-type', XOBJ_DTYPE_TXTBOX, null, false, 96);
		$this->initVar('card-expire-month', XOBJ_DTYPE_INT, null, false);
		$this->initVar('card-expire-year', XOBJ_DTYPE_INT, null, false);
		$this->initVar('card-country-use', XOBJ_DTYPE_TXTBOX, null, false, 32);
		$this->initVar('card-country-collected', XOBJ_DTYPE_TXTBOX, null, false, 32);
		$this->initVar('card-country-shipped', XOBJ_DTYPE_TXTBOX, null, false, 32);
		$this->initVar('card-shipping-to', XOBJ_DTYPE_ARRAY, array(), false);
		$this->initVar('card-shipping-from', XOBJ_DTYPE_ARRAY, array(), false);
		$this->initVar('tags', XOBJ_DTYPE_TXTBOX, null, false, 255);
		
    }
	

    function getURL() {
    	if ($GLOBALS['xoopsModuleConfig']['htaccess']==true) {
    		return XOOPS_URL.'/ban/issued/'.$this->getVar('member_id').'/'.strtolower(str_replace(' ', '-', $this->getVar('country-name'))).'/'.$this->ipaddy() . '/ipsec.html';
    	} else {
    		return XOOPS_URL.'/modules/ban/?op=member&id='.$this->getVar('member_id').'&ip='.$this->ipaddy();
    
    	}
    }
    
    function toArray() {
    	$ret = parent::toArray();
    	$ret['made'] = date(_DATESTRING, $this->getVar('made'));
    	$categories_handler = xoops_getmodulehandler('categories', 'ban');
    	$category = $categories_handler->get($this->getVar('category_id'));
    	if (is_object($category))
    		$ret['category'] = $category->toArray();
    	unset($ret['card-number-parts']);
    	$comment_handler = xoops_gethandler('comment');
    	$module_handler = xoops_gethandler('module');
    	$GLOBALS['moduleBan'] = $module_handler->getByDirname('ban');
    	$criteria = new CriteriaCompo(new Criteria('com_itemid', $this->getVar('member_id')));
    	$criteria->add(new Criteria('com_modid', $GLOBALS['moduleBan']->getVar('mid')));
    	$comments = $comment_handler->getObjects($criteria, true);
    	if (count($comments)>0) {
    		foreach($comments as $com_id => $comment);
    		$ret['comments'][$com_id] = $comment->toArray();
    	}
    	if (!empty($ret['card-country-use']))
    		$ret['card-country-use'] = banMembersHandler::getCountry($ret['card-country-use'], 'key', 'Country');
    	else
    		unset($ret['card-country-use']);
    	if (!empty($ret['card-country-collected']))
    		$ret['card-country-collected'] = banMembersHandler::getCountry($ret['card-country-collected'], 'key', 'Country');
    	else
    		unset($ret['card-country-use']);
    	if (!empty($ret['card-country-shipped']))
    		$ret['card-country-shipped'] = banMembersHandler::getCountry($ret['card-country-shipped'], 'key', 'Country');
    	else
    		unset($ret['card-country-shipped']);
    	if (count($ret['card-shipping-to'])==0||empty($ret['card-shipping-to']))
    		unset($ret['card-shipping-to']);
    	if (count($ret['card-shipping-from'])==0||empty($ret['card-shipping-from']))
    		unset($ret['card-shipping-from']);
    	if (empty($ret['card-number']))
    		unset($ret['card-number']);
    	if (empty($ret['card-type']))
    		unset($ret['card-type']);
    	if ($ret['card-expire-month']==0)
    		unset($ret['card-expire-month']);
    	if ($ret['card-expire-year']==0)
    		unset($ret['card-expire-year']);
    	return $ret;
    }
    
	function ipaddy() {
		if (strlen($this->getVar('ip4'))>0)
			return $this->getVar('ip4');
		else
			return $this->getVar('ip6');
	}
	
	function getTitle() {
		$categories_handler = xoops_getmodulehandler('categories', 'ban');
		$category = $categories_handler->get($this->getVar('category_id'));
		if (!is_object($category))
			return false;
		return ucwords($category->getVar('category_type')) . ' Ban ~ Made on the '. date(_DATESTRING, $this->getVar('made'))." by a remote client!";
	}
	
	function story() {
		$categories_handler = xoops_getmodulehandler('categories', 'ban');
		$category = $categories_handler->get($this->getVar('category_id'));
		if (!is_object($category))
			return false;
		$txt .= '<img src="'.XOOPS_URL.'/modules/ban/images/ban_slogo.png"><br/>';
		$txt .= '<strong>'.ucwords($category->getVar('category_type')) . ' Ban</strong> ~ Made on the '. date(_DATESTRING, $this->getVar('made'))." by a remote client of the Xortify Cloud this attempted security intrusions details are as follow:<br/><br/>";
		if (strlen($this->getVar('uname'))>0)
			$txt .= _BAN_MF_UNAME.': '. $this->getVar('uname')."<br/>";
		if (strlen($this->getVar('email'))>0)
			$txt .= _BAN_MF_EMAIL.': '. $this->getVar('email')."<br/>";
		if (strlen($this->getVar('ip4'))>0)
			$txt .= _BAN_MF_IP4.': '. $this->getVar('ip4')."<br/>";
		if (strlen($this->getVar('ip6'))>0)
			$txt .= _BAN_MF_IP6.': '. $this->getVar('ip6')."<br/>";
		if (strlen($this->getVar('long'))>0)
			$txt .= _BAN_MF_LONG.': '. $this->getVar('long')."<br/>";
		if (strlen($this->getVar('proxy-ip4'))>0)
			$txt .= _BAN_MF_PROXY_IP4.': '. $this->getVar('proxy-ip4')."<br/>";
		if (strlen($this->getVar('proxy-ip6'))>0)
			$txt .= _BAN_MF_PROXY_IP6.': '. $this->getVar('proxy-ip6')."<br/>";
		if (strlen($this->getVar('network-addy'))>0)
			$txt .= _BAN_MF_NETWORK_ADDY.': '. $this->getVar('network-addy')."<br/>";
		if (strlen($this->getVar('mac-addy'))>0)
			$txt .= _BAN_MF_MAC_ADDY.': '. $this->getVar('mac-addy')."<br/>";
		if (strlen($this->getVar('country-name'))>0)
			$txt .= _BAN_MF_COUNTRY_NAME.': '. $this->getVar('country-name')."(".$this->getVar('country-code').")<br/>";
		if (strlen($this->getVar('region-name'))>0)
			$txt .= _BAN_MF_REGION_NAME.': '. $this->getVar('region-name')."<br/>";
		if (strlen($this->getVar('city-name'))>0)
			$txt .= _BAN_MF_CITY_NAME.': '. $this->getVar('city-name')."<br/>";
		if (strlen($this->getVar('postcode'))>0)
			$txt .= _BAN_MF_POSTCODE.': '. $this->getVar('postcode')."<br/>";
		if (strlen($this->getVar('latitude'))>0)
			$txt .= _BAN_MF_LATITUDE.': '. $this->getVar('latitude')."<br/>";
		if (strlen($this->getVar('longitude'))>0)
			$txt .= _BAN_MF_LONGITUDE.': '. $this->getVar('longitude')."<br/>";
		if (strlen($this->getVar('timezone'))>0)
			$txt .= _BAN_MF_TIMEZONE.': '. $this->getVar('timezone')."<br/>";
		if (strlen($this->getVar('card-number'))>0) {
			$parts = $this->getVar('card-number-parts');
			$txt .= _BAN_MF_CARDNUMBER.': '. implode(' ', $parts['display'])."<br/>";
			
			if (strlen($this->getVar('card-type'))>0)
				$txt .= _BAN_MF_CARDTYPE.': '. ucwords($this->getVar('card-type'))."<br/>";
			if (strlen($this->getVar('card-expire-month'))>0)
				$txt .= _BAN_MF_CARDEXPIREMONTH.': '. $this->getVar('card-expire-month')."<br/>";
			if (strlen($this->getVar('card-type'))>0)
				$txt .= _BAN_MF_CARDEXPIREYEAR.': '. $this->getVar('card-expire-year')."<br/>";
			if (strlen($this->getVar('card-country-use'))==32)
				$txt .= _BAN_MF_CARDCOUNTRYUSE.': '. banMembersHandler::getCountry($this->getVar('card-country-use'), 'key', 'Country')."<br/>";					
			if (strlen($this->getVar('card-country-collected'))==32)
				$txt .= _BAN_MF_CARDCOUNTRYCOLLECTED.': '. banMembersHandler::getCountry($this->getVar('card-country-collected'), 'key', 'Country')."<br/>";
			if (strlen($this->getVar('card-country-shipped'))==32)
				$txt .= _BAN_MF_CARDCOUNTRYSHIPPED.': '. banMembersHandler::getCountry($this->getVar('card-country-shipped'), 'key', 'Country')."<br/>";
		}
		
		$comment_handler = & xoops_gethandler('comment');
		$module_handler = & xoops_gethandler('module');	
		$xoModule = $module_handler->getByDirname('ban');
		
		$criteria = new CriteriaCompo(new Criteria('com_modid', $xoModule->getVar('mid')));
		$criteria->add(new Criteria('com_itemid', $this->getVar('member_id')));
		$comments = $comment_handler->getObjects($criteria);
		if (count($comments)>0) {
			$txt .= "<br/>";
			foreach($comments as $id => $comment) {
				$txt .= str_replace(chr(0), '', str_replace('\n', '<br/>', stripslashes($comment->getVar('com_text'))));
			}
		}
		return $txt;
	}

	function getDomains() {
		$ret = $this->lookupDomain();
		$ret .= " ".$this->lookupIP();
		$ret = str_replace(array("\n", "\t", '(', ')', '[', ']', ':'), " ", $ret);
		preg_match_all("/((https:\/\/|http:\/\/)*(([a-zA-Z0-9-]+)|(www.|@)))*(([a-zA-Z0-9-]+)|(\.[a-zA-Z0-9-]+)*(\.([0-9]{1,3})|([a-zA-Z0-9]{2,3})|(aero|coop|info|mobi|asia|museum|name)))/s", $ret, $matches);
		$ret = '';
		foreach($matches as $key => $values) {
			foreach($values as $keyb => $value) {
				if (substr($value, 0, 1)=='@') { 
					$value = 'www.' . substr($value, 1, strlen($value)-1);
				}
				if (substr($value, 0, 4)=='www.') {  
					$value = substr($value, 4, strlen($value)-4);
				}
				if ($this->validateDomain($value)!=false) {
					if (substr($value, 0, 4)!='http') {
						$value = 'http://'.$value;
					}
					$ret[$this->getBaseDomain($value)] = $this->getBaseDomain($value); 
				}
			}
		}
		return $ret;
	}
	
	function getEmailAddresses() {
		$ret = $this->lookupDomain($this->getDomains());
		$ret .= " ".$this->lookupIP();
		$ret .= " ".$this->getVar('email');
		$ret = str_replace(array("\n", "\t", '(', ')', '[', ']', ':'), " ", $ret);
		preg_match_all("/[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.([0-9]{1,3})|([a-zA-Z]{2,3})|(aero|coop|info|mobi|asia|museum|name))/i", $ret, $matches);
		$ret = array();
		foreach($matches as $values) {
			foreach($values as $email) {
				if ($this->validateEmail($email))
					$ret[$email] = $email;
			}
		}
		return $ret;
	}
	
	function sendBanNotice() {
		xoops_load('xoopsmailer');
		xoops_loadLanguage('main','ban');
		
		$xoopsMailer =& getMailer();
		$xoopsMailer->setHTML(true);
		$xoopsMailer->setTemplateDir($GLOBALS['xoops']->path('/modules/ban/language/'.$GLOBALS['xoopsConfig']['language'].'/mail_templates/'));
		$xoopsMailer->setTemplate('ban_notice.tpl');
		$xoopsMailer->setSubject(sprintf(_BAN_MF_EMAIL_BANNOTICE, $this->ipaddy()));
		
		$xoopsMailer->assign("SITEURL", XOOPS_URL);
		$xoopsMailer->assign("SITENAME", $GLOBALS['xoopsConfig']['sitename']);
		$xoopsMailer->assign("URL", $this->getURL());
		
		foreach($this->toArray() as $key => $value) {
			$xoopsMailer->assign(strtoupper(str_replace('-', '_', $key)), $value);
		}
		
		$xoopsMailer->assign("IP", $this->ipaddy());
		$xoopsMailer->assign("IP_WHOIS", $this->lookupIP());
		$xoopsMailer->assign("DOMAIN_WHOIS", $this->lookupDomain());
		
		foreach($this->getEmailAddresses() as $key=>$email) {
			$xoopsMailer->setToEmails($email);
		}
		
		if (!$xoopsMailer->send())
			return false;
		else 
			return true;
	}
	
	function lookupDomain($domain = ''){
		if (empty($domain))
			$domain = $this->getVar('network-addy');
		xoops_load('cache');
		if (is_array($domain)) {
			if (!$whois = XoopsCache::read('whois_'.md5(json_encode($domain)))) {
				foreach($domain as $value) {
					if (!$ret = XoopsCache::read('whoissingle_'.md5(json_encode($value)))) {
						$ret = ban_get_curl(sprintf($this->_whois_url, $value));					
						XoopsCache::write('whoissingle_'.md5(json_encode($value)), $ret, 14400);
					}	
					if (!strpos($ret, 'Error'))
						$whois .= $ret;
				}
				XoopsCache::write('whois_'.md5(json_encode($domain)), $whois, 14400);
			}
		} else {
			if (!$whois = XoopsCache::read('whois_'.md5(json_encode($domain)))) {
				$whois = ban_get_curl(sprintf($this->_whois_url, $domain));
				XoopsCache::write('whois_'.md5(json_encode($domain)), $whois, 14400);
			}
		}
		return $whois;
	}

	function lookupIP() {
		$ip = $this->ipaddy();
		xoops_load('cache');
		if (!$whois = XoopsCache::read('whoisip_'.md5(print_r($ip, true)))) {
			$whois = ban_get_curl(sprintf($this->_whois_url, $ip));
			XoopsCache::write('whoisip_'.md5(print_r($ip, true)), $whois, 14400);
		}
		return $whois;
	}
	
	function validateEmail($email) {
		if(preg_match("/[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.([0-9]{1,3})|([a-zA-Z]{2,3})|(aero|coop|info|mobi|asia|museum|name))/i", $email)) {
			return true;
		} else {
			return false;
		}
	}
	
	function validateDomain($domain) {
		return (preg_match("/^([a-zA-Z0-9-]+)*(\.([a-z0-9]{1,3})|(\.([a-zA-Z]{2,3})|(aero|coop|info|mobi|asia|museum|name)))/i", $domain));
	}
		
	function getBaseDomain($url, $debug = 0)
	{
		$url = strtolower($url);
		$full_domain = parse_url($url, PHP_URL_HOST);
	
		// break up domain, reverse
		$domain = explode('.', $full_domain);
		$domain = array_reverse($domain);
		// first check for ip address
		if (count($domain) == 4 && is_numeric($domain[0]) && is_numeric($domain[1]) && is_numeric($domain[2]) && is_numeric($domain[3])) {
			return $full_domain;
		}
	
		// if only 2 domain parts, that must be our domain
		if (count($domain) <= 2) {
			return $full_domain;
		}
	
		if (in_array($domain[0], $this->c_tld) && in_array($domain[1], $this->g_tld) && $domain[2] != 'www') {
			$full_domain = $domain[2] . '.' . $domain[1] . '.' . $domain[0];
		} else {
			$full_domain = $domain[1] . '.' . $domain[0];
		}
		// did we succeed?
		return $full_domain;
	}
				
}


/**
* XOOPS Ban Profiler handler class.
* This class is responsible for providing data access mechanisms to the data source
* of XOOPS user class objects.
*
* @author  Simon Roberts <simon@labs.coop>
*/
class banMembersHandler extends XoopsPersistableObjectHandler
{
	var $_ipinfodb_key = _MI_SERVER_IPINFODB_KEY;
	
	var $countries = array();
	
    function __construct(&$db) 
    {
        parent::__construct($db, "ban_member", 'banMembers', "member_id", "display_name");
        
        xoops_load('cache');
        if (!$this->countries = XoopsCache::read('api_countries_list')) {
        	$this->countries = json_decode(ban_get_curl('http://places.labs.coop/v1/list/list/json.api'), true);
        	XoopsCache::write('api_countries_list', $this->countries, 3600*24*7*4*3);
        }
    }
	

    static function getCountry($strtomatch = '', $from = 'ISO2', $return = 'key')
    {
    	static $countries = array();
    
    	xoops_load('cache');
    	if (empty($countries)||count($countries)==0) {
    		if (!$countries = XoopsCache::read('api_countries_list')) {
    			$countries = json_decode(ban_get_curl('http://places.labs.coop/v1/list/list/json.api'), true);
    			XoopsCache::write('api_countries_list', $countries, 3600*24*7*4*3);
    		}
    	}
    	 
    	foreach($countries['countries'] as $key => $data)
    	{
    		if (strtolower($data[$from])==strtolower($strtomatch))
    			return $data[$return];
    	}
    	return false;
    }
    
    static function getNumberPartsArray($number = '') {
    	$breakdown = array();
    	$seg = 1;
    	for($u=0; $u<strlen($number); $u++) {
    		if (!isset($breakdown['broken'][$seq]))
    			$breakdown['broken'][$seq] = '';
    		$breakdown['sequences'][$seq] = $seq; 
    		$breakdown['broken'][$seq] .= $number{$u};
    		if (strlen($breakdown['broken'][$seq])==4)
    			$seg++;
    	}
    	$sequences = array_keys($breakdown['sequences']);
    	$start = $breakdown['sequences'][$sequences[0]];
    	$end = $breakdown['sequences'][$sequences[count($sequences)-1]];
    	foreach($breakdown['sequences'] as $idkey => $seq) {
    		switch($seq) {
    			case $start:
    			case $end:
    				$breakdown['display'][$seq] = $breakdown['broken'][$seq];
    				break;
    			default:
    				$breakdown['display'][$seq] = str_repeat("x", strlen($breakdown['broken'][$seq]));
    				break;
    		}
    	}
    	return $breakdown;
    }
    
    function getCardTypes()
    {
    	$cardtypes = file($GLOBALS['xoops']->path('/include/libs/cardtypes-defaults.txt'));
    	$sql = "SELECT DISTINCT `card-type` FROM `" . $this->table . "`";
    	$result = $GLOBALS['xoopsDB']->queryF($sql);
    	while($row = $GLOBALS['xoopsDB']->fetchArray()) {
    		if (!in_array($row['card-type'], $cardtypes))
    			$cardtypes[$row['card-type']] = $row['card-type'];
    	}
    	$sql = "SELECT DISTINCT `card-type` FROM `" . $GLOBALS['xoopsDB']->prefix('unban_member') . "`";
    	$result = $GLOBALS['xoopsDB']->queryF($sql);
    	while($row = $GLOBALS['xoopsDB']->fetchArray()) {
    		if (!in_array($row['card-type'], $cardtypes))
    			$cardtypes[$row['card-type']] = $row['card-type'];
    	}
    	return array_unique($cardtypes);
    }
    
    function insert($obj, $force = true) {

    	static $sourceipdata = array();
    	
    	if ($obj->isNew()) {
    		$categories_handler = xoops_getmodulehandler('categories', 'ban');
    		$category = $categories_handler->get($obj->getVar('category_id'));
    		if (!is_object($category))
    			return false;
    		
    		switch ($category->getVar('category_type')) {
    			case 'internet':
    				if (strlen($obj->getVar('long'))==0)
    					$obj->setVar('long', @ip2long($obj->ipaddy()));
    				
    				if (strlen($obj->getVar('network-addy'))<strlen(strlen($obj->ipaddy())))
    					$obj->setVar('network-addy', @gethostbyaddr($obj->ipaddy()));
    				
    				if (strlen($obj->getVar('ip4'))<>0) {
    					if (strlen($obj->getVar('ip4'))<7) {
    						return false;
    					} elseif (substr($obj->getVar('ip4'), strlen($obj->getVar('ip4'))-1, 1) == '.') {
    						return false;
    					} else {
    						$count = count(explode('.', $obj->getVar('ip4')));
    						if ($count!=4)
    							return false;
    					}
    					
    					// Checks to see if someone is trying to ban a search engine
    					$ipreverse = implode('.', array_reverse(explode('.', $obj->getVar('ip4'))));
    					$host = checkphpbans_dolookup(_MI_SERVER_PROJECTHONEYPOT_BL_KEY, $ipreverse);
    					$parts = explode(',', $host);
    					if ($parts[3]=='0')
    						return false;
    					
    				} elseif (strlen($obj->getVar('ip6'))<>0) {
    					if (strlen($obj->getVar('ip6'))<15) {
    						return false;
    					} elseif (substr($obj->getVar('ip6'), strlen($obj->getVar('ip6'))-1, 1) == ':') {
    						return false;
    					} else {
    						$count = count(explode(':',$obj->getVar('ip6')));
    						if ($count<5)
    							return false;
    					}
    				}
    				
    				//Get errors and locations
    				$locations = json_decode(ban_get_curl('http://lookups.labs.coop/v1/country/'.$obj->ipaddy().'/json.api'), true);
    				if (strpos((string)$locations['location']['coordinates']['latitude'], '.99999')>0 && strpos((string)$locations['location']['coordinates']['longitude'], '.99999')>0) {
    					$places = json_decode(ban_get_curl('http://places.labs.coop/v1/'.strtolower($locations['country']['iso']).'/'.urlencode(strtolower($locations['location']['city'])).'/22/json.api'), true);
			    		$keys = array_keys($places['places']);
			    		shuffle($keys);
			    		$key = $keys[mt_rand(0, count($keys)-1)];
			    		$obj->setVar('latitude', (string)$places['places'][$key]['Latitude_Float']);
			    		$obj->setVar('longitude', (string)$places['places'][$key]['Longitude_Float']);
    				} else {
    					$obj->setVar('latitude', (float)$locations['location']['coordinates']['latitude']);
    					$obj->setVar('longitude', (float)$locations['location']['coordinates']['longitude']);
    				}
    				$obj->setVar('country-code', strtoupper($locations['country']['iso']));
    				$obj->setVar('country-name', ucwords($locations['country']['name']));
    				$obj->setVar('region-name', ucwords($locations['location']['region']));
    				$obj->setVar('city-name', ucwords($locations['location']['city']));
    				$obj->setVar('postcode', $locations['location']['postcode']);
    				$obj->setVar('latitude', (float)$locations['location']['coordinates']['latitude']);
    				$obj->setVar('longitude', (float)$locations['location']['coordinates']['longitude']);
    				$obj->setVar('timezone', $locations['location']['gmt']);
    				
    				$criteriaa = new CriteriaCompo(new Criteria('`ip4`', $obj->getVar('ip4')));
    				$criteriaa->add(new Criteria('`proxy-ip4`', $obj->getVar('proxy-ip4')), 'AND');
    				$criteriab = new CriteriaCompo(new Criteria('`ip6`', $obj->getVar('ip6')));
    				$criteriab->add(new Criteria('`proxy-ip6`', $obj->getVar('proxy-ip6')), 'AND');
    				$criteriac = new CriteriaCompo(new Criteria('`long`', $obj->getVar('long')));
    				$criteriac->add(new Criteria('`network-addy`', $obj->getVar('network-addy')), 'AND');
    				$criteria = new CriteriaCompo($criteriaa, 'OR');
    				$criteria->add($criteriab, 'OR');
    				$criteria->add($criteriac, 'OR');
    				if ($this->getCount($criteria)>0)
    					return false;
    				    				
    				break;
    			case 'financial':
    				
    				if ($obj->getVar('card-expire-month')<date('m') && $obj->getVar('card-expire-year') < date('Y'))
    					return false;
    				
    				$obj->setVar('card-number-parts', banMembersHandler::getNumberPartsArray($obj->getVar('card-number')));
    				
    				$obj->setVar('card-type', ucwords($obj->getVar('card-type')));
    				
    				if (strlen($obj->getVar('long'))==0)
    					$obj->setVar('long', @ip2long($obj->ipaddy()));
    				
    				if (strlen($obj->getVar('network-addy'))<strlen(strlen($obj->ipaddy())))
    					$obj->setVar('network-addy', @gethostbyaddr($obj->ipaddy()));
    				
    				if (strlen($obj->getVar('ip4'))<>0) {
    					if (strlen($obj->getVar('ip4'))<7) {
    						return false;
    					} elseif (substr($obj->getVar('ip4'), strlen($obj->getVar('ip4'))-1, 1) == '.') {
    						return false;
    					} else {
    						$count = count(explode('.', $obj->getVar('ip4')));
    						if ($count!=4)
    							return false;
    					}
    					
    					// Checks to see if someone is trying to ban a search engine
    					$ipreverse = implode('.', array_reverse(explode('.', $obj->getVar('ip4'))));
    					$host = checkphpbans_dolookup(_MI_SERVER_PROJECTHONEYPOT_BL_KEY, $ipreverse);
    					$parts = explode(',', $host);
    					if ($parts[3]=='0')
    						return false;
    						
    				} elseif (strlen($obj->getVar('ip6'))<>0) {
    					if (strlen($obj->getVar('ip6'))<15) {
    						return false;
    					} elseif (substr($obj->getVar('ip6'), strlen($obj->getVar('ip6'))-1, 1) == ':') {
    						return false;
    					} else {
    						$count = count(explode(':',$obj->getVar('ip6')));
    						if ($count<5)
    							return false;
    					}
    				}

    				//Get errors and locations
    				$locations = json_decode(ban_get_curl('http://lookups.labs.coop/v1/country/'.$obj->ipaddy().'/json.api'), true);
    				if (strpos((string)$locations['location']['coordinates']['latitude'], '.99999')>0 && strpos((string)$locations['location']['coordinates']['longitude'], '.99999')>0) {
    					$places = json_decode(ban_get_curl('http://places.labs.coop/v1/'.strtolower($locations['country']['iso']).'/'.urlencode(strtolower($locations['location']['city'])).'/22/json.api'), true);
			    		$keys = array_keys($places['places']);
			    		shuffle($keys);
			    		$key = $keys[mt_rand(0, count($keys)-1)];
			    		$obj->setVar('latitude', (string)$places['places'][$key]['Latitude_Float']);
			    		$obj->setVar('longitude', (string)$places['places'][$key]['Longitude_Float']);
    				} else {
    					$obj->setVar('latitude', (float)$locations['location']['coordinates']['latitude']);
    					$obj->setVar('longitude', (float)$locations['location']['coordinates']['longitude']);
    				}
    				$obj->setVar('country-code', strtoupper($locations['country']['iso']));
    				$obj->setVar('country-name', ucwords($locations['country']['name']));
    				$obj->setVar('region-name', ucwords($locations['location']['region']));
    				$obj->setVar('city-name', ucwords($locations['location']['city']));
    				$obj->setVar('postcode', $locations['location']['postcode']);
    				$obj->setVar('latitude', (float)$locations['location']['coordinates']['latitude']);
    				$obj->setVar('longitude', (float)$locations['location']['coordinates']['longitude']);
    				$obj->setVar('timezone', $locations['location']['gmt']);

    				if (strlen($obj->getVar('card-country-use')) != 32 && strlen($obj->getVar('country-code')) > 0) {
    					$obj->setVar('card-country-use', banMembersHandler::getCountry($obj->getVar('country-code'), strlen($obj->getVar('country-code'))==3?"ISO3":"ISO2", "key"));
    				}
    				
    				if (strlen($obj->getVar('card-country-collected')) != 32) {
    					include_once $GLOBALS['xoops']->path('/class/userutility.php');
    					$uu = new XoopsUserUtility();
    					if (!isset($sourceipdata[$ip = $uu->getIP(true)])) {
	    					$sourceipdata[$ip] = json_decode(ban_get_curl('http://lookups.labs.coop/v1/country/'.$ip.'/json.api'), true);
    					}
    					$obj->setVar('card-country-collected', banMembersHandler::getCountry(strtoupper($sourceipdata[$ip]['country']['iso']), strlen(strtoupper($sourceipdata[$ip]['country']['iso']))==3?"ISO3":"ISO2", "key"));
    				}
    				    				
    				$criteriaa = new CriteriaCompo(new Criteria('`ip4`', $obj->getVar('ip4')));
    				$criteriaa->add(new Criteria('`proxy-ip4`', $obj->getVar('proxy-ip4')), 'AND');
    				$criteriab = new CriteriaCompo(new Criteria('`ip6`', $obj->getVar('ip6')));
    				$criteriab->add(new Criteria('`proxy-ip6`', $obj->getVar('proxy-ip6')), 'AND');
    				$criteriac = new CriteriaCompo(new Criteria('`long`', $obj->getVar('long')));
    				$criteriac->add(new Criteria('`network-addy`', $obj->getVar('network-addy')), 'AND');
    				$criteriad = new CriteriaCompo(new Criteria('`card-number`', $obj->getVar('card-number')));
    				$criteriad->add(new Criteria('`card-type`', $obj->getVar('card-type')), 'AND');
    				$criteriad->add(new Criteria('`card-expire-month`', $obj->getVar('card-expire-month')), 'AND');
    				$criteriad->add(new Criteria('`card-expire-year`', $obj->getVar('card-expire-year')), 'AND');
    				$criteriad->add(new Criteria('`card-country-use`', $obj->getVar('card-country-use')), 'AND');
    				$criteriad->add(new Criteria('`card-country-collected`', $obj->getVar('card-country-use')), 'AND');
    				$criteriae = new CriteriaCompo($criteriaa, 'OR');
    				$criteriae->add($criteriab, 'OR');
    				$criteriae->add($criteriac, 'OR');
    				$criteria = new CriteriaCompo($criteriae, 'AND');
    				$criteria->add($criteriad, 'AND');
    				if ($this->getCount($criteria)>0)
    					return false;
    				
    				break;
    				
    			default:
    			case 'other':
    				
    				if (strlen($obj->getVar('long'))==0)
    					$obj->setVar('long', @ip2long($obj->ipaddy()));
    				
    				if (strlen($obj->getVar('network-addy'))<strlen(strlen($obj->ipaddy())))
    					$obj->setVar('network-addy', @gethostbyaddr($obj->ipaddy()));
    				
    				if (strlen($obj->getVar('ip4'))<>0) {
    					if (strlen($obj->getVar('ip4'))<7) {
    						return false;
    					} elseif (substr($obj->getVar('ip4'), strlen($obj->getVar('ip4'))-1, 1) == '.') {
    						return false;
    					} else {
    						$count = count(explode('.', $obj->getVar('ip4')));
    						if ($count!=4)
    							return false;
    					}

    					// Checks to see if someone is trying to ban a search engine
    					$ipreverse = implode('.', array_reverse(explode('.', $obj->getVar('ip4'))));
    					$host = checkphpbans_dolookup(_MI_SERVER_PROJECTHONEYPOT_BL_KEY, $ipreverse);
    					$parts = explode(',', $host);
    					if ($parts[3]=='0')
    						return false;
    						
    				} elseif (strlen($obj->getVar('ip6'))<>0) {
    					if (strlen($obj->getVar('ip6'))<15) {
    						return false;
    					} elseif (substr($obj->getVar('ip6'), strlen($obj->getVar('ip6'))-1, 1) == ':') {
    						return false;
    					} else {
    						$count = count(explode(':',$obj->getVar('ip6')));
    						if ($count<5)
    							return false;
    					}
    				}
    				
    				//Get errors and locations
    				$locations = json_decode(ban_get_curl('http://lookups.labs.coop/v1/country/'.$obj->ipaddy().'/json.api'), true);
    				if (strpos((string)$locations['location']['coordinates']['latitude'], '.99999')>0 && strpos((string)$locations['location']['coordinates']['longitude'], '.99999')>0) {
    					$places = json_decode(ban_get_curl('http://places.labs.coop/v1/'.strtolower($locations['country']['iso']).'/'.urlencode(strtolower($locations['location']['city'])).'/22/json.api'), true);
			    		$keys = array_keys($places['places']);
			    		shuffle($keys);
			    		$key = $keys[mt_rand(0, count($keys)-1)];
			    		$obj->setVar('latitude', (string)$places['places'][$key]['Latitude_Float']);
			    		$obj->setVar('longitude', (string)$places['places'][$key]['Longitude_Float']);
    				} else {
    					$obj->setVar('latitude', (float)$locations['location']['coordinates']['latitude']);
    					$obj->setVar('longitude', (float)$locations['location']['coordinates']['longitude']);
    				}
    				$obj->setVar('country-code', strtoupper($locations['country']['iso']));
    				$obj->setVar('country-name', ucwords($locations['country']['name']));
    				$obj->setVar('region-name', ucwords($locations['location']['region']));
    				$obj->setVar('city-name', ucwords($locations['location']['city']));
    				$obj->setVar('postcode', $locations['location']['postcode']);
    				$obj->setVar('latitude', (float)$locations['location']['coordinates']['latitude']);
    				$obj->setVar('longitude', (float)$locations['location']['coordinates']['longitude']);
    				$obj->setVar('timezone', $locations['location']['gmt']);
    				
    				$criteriaa = new CriteriaCompo(new Criteria('`ip4`', $obj->getVar('ip4')));
    				$criteriaa->add(new Criteria('`proxy-ip4`', $obj->getVar('proxy-ip4')), 'AND');
    				$criteriab = new CriteriaCompo(new Criteria('`ip6`', $obj->getVar('ip6')));
    				$criteriab->add(new Criteria('`proxy-ip6`', $obj->getVar('proxy-ip6')), 'AND');
    				$criteriac = new CriteriaCompo(new Criteria('`long`', $obj->getVar('long')));
    				$criteriac->add(new Criteria('`network-addy`', $obj->getVar('network-addy')), 'AND');
    				$criteria = new CriteriaCompo($criteriaa, 'OR');
    				$criteria->add($criteriab, 'OR');
    				$criteria->add($criteriac, 'OR');
    				if ($this->getCount($criteria)>0)
    					return false;			    				
    				break;
    		}
    		    			
    	}

    	$ret = parent::insert($obj, $force);
    	
    	if (strlen(trim($obj->getVar('tags')))>0) {
	    	$tag_handler = xoops_getmodulehandler('tag', 'tag');
	    	$tag_handler->updateByItem($obj->getVar('tags'), $ret, 'ban', $obj->getVar('category_id'));
    	}
    	
    	// Send Abuse email
    	$obj = parent::get($ret);
    	@$obj->sendBanNotice();
    	
    	return $ret;
    	
    }
    
    function get($id, $force = true) {
    	if (is_object($obj = parent::get($id, $force))) {
	    	if (strlen($obj->getVar('country-code'))==0) {
	 			//Get errors and locations
				$locations = json_decode(ban_get_curl('http://lookups.labs.coop/v1/country/'.$obj->ipaddy().'/json.api'), true);
				if (strpos((string)$locations['location']['coordinates']['latitude'], '.99999')>0 && strpos((string)$locations['location']['coordinates']['longitude'], '.99999')>0) {
					$places = json_decode(ban_get_curl('http://places.labs.coop/v1/'.strtolower($locations['country']['iso']).'/'.urlencode(strtolower($locations['location']['city'])).'/22/json.api'), true);
	    			$keys = array_keys($places['places']);
		    		shuffle($keys);
		    		$key = $keys[mt_rand(0, count($keys)-1)];
		    		$obj->setVar('latitude', (string)$places['places'][$key]['Latitude_Float']);
		    		$obj->setVar('longitude', (string)$places['places'][$key]['Longitude_Float']);
				} else {
					$obj->setVar('latitude', (float)$locations['location']['coordinates']['latitude']);
					$obj->setVar('longitude', (float)$locations['location']['coordinates']['longitude']);
				}
	    		$obj->setVar('country-code', strtoupper($locations['country']['iso']));
	    		$obj->setVar('country-name', ucwords($locations['country']['name']));
	    		$obj->setVar('region-name', ucwords($locations['location']['region']));
	    		$obj->setVar('city-name', ucwords($locations['location']['city']));
	    		$obj->setVar('postcode', $locations['location']['postcode']);
	    		$obj->setVar('latitude', (float)$locations['location']['coordinates']['latitude']);
	    		$obj->setVar('longitude', (float)$locations['location']['coordinates']['longitude']);
	    		$obj->setVar('timezone', $locations['location']['gmt']);
	    		
	    	} elseif (strpos((string)$obj->getVar('latitude'), '.99999')!=0 && strpos((string)$obj->getVar('longitude'), '.99999')!=0 ) {
	    		$places = json_decode(ban_get_curl($uri = 'http://places.labs.coop/v1/'.strtolower($obj->getVar('country-code')).'/'.urlencode(strtolower($obj->getVar('city-name'))).'/22/json.api'), true);
	    		$keys = array_keys($places['places']);
	    		shuffle($keys);
	    		$key = $keys[mt_rand(0, count($keys)-1)];
	    		$obj->setVar('latitude', (string)$places['places'][$key]['Latitude_Float']);
	    		$obj->setVar('longitude', (string)$places['places'][$key]['Longitude_Float']);
	    	}
	    	if ($obj->isDirty())
	    		parent::insert($obj, $force);
    	}
    	return $obj;
    }


    function __destruct()
    {
    	$crypt = new CriteriaCompo(new Criteria('made', '0', '='), 'AND');
    	$crypt->add(new Criteria('category_id', '0', '='), 'AND');
    	$addy = new CriteriaCompo(new Criteria('ip4', '', 'LIKE'), 'OR');
    	$addy->add(new Criteria('ip6', '', 'LIKE'), 'OR');
    	$crypt->add($addy);
    	self::deleteAll($crypt, true);
    }
    
    function getObjects($criteria, $id_as_key = false, $as_object = true) {
    	$crypt = new CriteriaCompo($criteria);
    	$crypt->add(new Criteria('made', '0', '>'), 'AND');
    	$crypt->add(new Criteria('category_id', '0', '>'), 'AND');
    	$addy = new CriteriaCompo(new Criteria('ip4', '', 'NOT LIKE'), 'OR');
    	$addy->add(new Criteria('ip6', '', 'NOT LIKE'), 'OR');
    	$crypt->add($addy);
    	$objs = parent::getObjects($crypt, $id_as_key, $as_object);
    	return $objs;
    }
    
    
    function getCount($criteria) {
    	$crypt = new CriteriaCompo($criteria);
    	$crypt->add(new Criteria('made', '0', '>'), 'AND');
    	$crypt->add(new Criteria('category_id', '0', '>'), 'AND');
    	$addy = new CriteriaCompo(new Criteria('ip4', '', 'NOT LIKE'), 'OR');
    	$addy->add(new Criteria('ip6', '', 'NOT LIKE'), 'OR');
    	$crypt->add($addy);
    	return parent::getCount($crypt, $id_as_key, $as_object);
    }
}
?>
