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


	
	require (dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR.'mainfile.php');
		if ($GLOBALS['xoopsModuleConfig']['htaccess']==true) {
		$url = XOOPS_URL.'/ban/'.basename($_SERVER['PHP_SELF']).'?'.$_SERVER['QUERY_STRING'];
		if ($_SERVER['REQUEST_URI'].'?'.$_SERVER['QUERY_STRING']!=$url && strpos($_SERVER['REQUEST_URI'], 'odules/')>0) {
			header( "HTTP/1.1 301 Moved Permanently" ); 
			header('Location: '.$url);
			exit(0);
		}
	}
	
	require_once (__DIR__ . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'functions.php');	
	require_once (__DIR__ . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'forms.php');

	switch ((string)$_REQUEST['op']) {
	default:
	case "latest":
				

		if ($GLOBALS['xoopsModuleConfig']['htaccess']==true && !isset($_GET['extra'])) {
			if ($_SERVER['REQUEST_URI']!=XOOPS_URL . '/ban/latest/'.(isset($_REQUEST['num'])?$_REQUEST['num']:'45')  . '/ipsec.html') {
				header( "HTTP/1.1 301 Moved Permanently" );
				header('Location: '.XOOPS_URL . '/ban/latest/'.(isset($_REQUEST['num'])?$_REQUEST['num']:'45') . '/ipsec.html');
				exit(0);
			}
		}
		
		$membershandler = xoops_getmodulehandler('members','ban');
		$criteria = new CriteriaCompo();
		$criteria->setOrder('DESC');
		$criteria->setSort('made');
		if (isset($_REQUEST['num'])&&$_REQUEST['num']<200)
			$criteria->setLimit(intval($_REQUEST['num']));
		else
			$criteria->setLimit(45);
		
		$bans = $membershandler->getObjects($criteria, true);
		
		$xoopsOption['template_main'] = 'ban_member.html';
		include $GLOBALS['xoops']->path('/header.php');								
		$xoTheme->addStylesheet(XOOPS_URL."/modules/ban/language/".$GLOBALS['xoopsConfig']['language']."/ban_style.css");
		$lbn = array();
		foreach($bans as $key => $ban){
			$ii++;
			$lbn[$ii]['id'] = $ban->getVar('member_id');
			$lbn[$ii]['ip'] = $ban->ipaddy();
			$lbn[$ii]['network_addy'] = $ban->getVar('network-addy');
			$lbn[$ii]['email'] = $ban->getVar('email');
			$lbn[$ii]['uname'] = $ban->getVar('uname');
			$lbn[$ii]['countrycode'] = $ban->getVar('country-code');
			$lbn[$ii]['countryname'] = $ban->getVar('country-name');
			$lbn[$ii]['postcode'] = $ban->getVar('postcode');
			$lbn[$ii]['when'] = date(_MEDIUMDATESTRING, $ban->getVar('made'));				
		}
		
		$GLOBALS['xoopsTpl']->assign('php_self', $_SERVER['PHP_SELF']);
		$GLOBALS['xoopsTpl']->assign('latestbans', $lbn);		
		$GLOBALS['xoopsTpl']->assign('display_name', $GLOBALS['xoopsModuleConfig']['display_name']);		
		$GLOBALS['xoopsTpl']->assign('display_text', $GLOBALS['xoopsModuleConfig']['display_text']);		
		include $GLOBALS['xoops']->path('/footer.php');		
		exit(0);
		break;
	case "member":
		
		$membershandler = xoops_getmodulehandler('members','ban');
		$ban = $membershandler->get($_REQUEST['id']);
		
		if (!is_object($ban)) {
			header( "HTTP/1.1 301 Moved Permanently" );
			header('Location: '.XOOPS_URL.'/modules/ban/?op=latest&num=35');
			exit(0);
		}
		
		if ($GLOBALS['xoopsModuleConfig']['htaccess']==true && !isset($_GET['extra'])) {
			if ($_SERVER['REQUEST_URI']!=$ban->getURL() ) {
				header( "HTTP/1.1 301 Moved Permanently" );
				header('Location: '.$ban->getURL());
				exit(0);
			}
		}
		
		$dontinclude = array();
		$dontinclude[] = 'suid';
		$xoopsOption['template_main'] = 'ban_member_display.html';
		include $GLOBALS['xoops']->path('/header.php');								
		$xoTheme->addStylesheet(XOOPS_URL."/modules/ban/language/".$GLOBALS['xoopsConfig']['language']."/ban_style.css");
		$bandata = $ban->toArray();
		foreach($bandata as $field => $value) {
			if ($field!='comments'&&!empty($value)&&!in_array($field, $dontinclude)&&$value!='00:00'&&(intval($value)!=0||is_string($value))) {
				$titleconst = '_BAN_MF_'. strtoupper(str_replace('-', '_', $field));
				$GLOBALS['xoopsTpl']->append('ban', array('title'=>defined($titleconst)?constant($titleconst):$titleconst,
														  'value'=>$value));
			}
		}
		$GLOBALS['xoopsTpl']->assign('ip_whois', $ban->lookupIP());
		$GLOBALS['xoopsTpl']->assign('domain_whois', $ban->lookupDomain($ban->getDomains()));
		$GLOBALS['xoopsTpl']->assign('email_notices', $ban->getEmailAddresses());
		$GLOBALS['xoopsTpl']->assign('display_name', $GLOBALS['xoopsModuleConfig']['display_name']);		
		$GLOBALS['xoopsTpl']->assign('display_text', $GLOBALS['xoopsModuleConfig']['display_text']);
		$GLOBALS['xoopsTpl']->assign('xoops_pagetitle', $ban->ipaddy() . ' :: ' . $ban->getTitle());
		include_once XOOPS_ROOT_PATH."/modules/tag/include/tagbar.php";
		$GLOBALS['xoopsTpl']->assign('tagbar', tagBar($ban->getVar('member_id'), $ban->getVar('category_id')));
		include $GLOBALS['xoops']->path('/include/comment_view.php');
		include $GLOBALS['xoops']->path('/footer.php');		
		exit(0);
		break;
		
	case "members":
	
		if ( $_REQUEST['fct']=='save' ) {

			xoops_load("captcha");
 			$xoopsCaptcha =& XoopsCaptcha::getInstance();
			if (! $xoopsCaptcha->verify() ) {
				redirect_header('index.php', 5, 'You didn\'t solve the puzzel correctly');
				exit(0);
			}
					
			$id = intval($_REQUEST['id']);				
			$membershandler = xoops_getmodulehandler('members','ban');
			if ($id>0)
				$member = $membershandler->get($id);	
			else
				$member = $membershandler->create();	
		
			if (empty($_REQUEST['network_addy']))
				$_REQUEST['network_addy'] = @gethostbyaddr( ((empty($_REQUEST['ip4']))?$_REQUEST['ip6']:$_REQUEST['ip4']) ); 

			if (empty($_REQUEST['long']))
				$_REQUEST['long'] = @ip2long( ((empty($_REQUEST['ip4']))?$_REQUEST['ip6']:$_REQUEST['ip4']) ); 
			
			$member->setVar('category_id', intval($_REQUEST['category_id']));
			$member->setVar('uid', intval($_REQUEST['uid']));			
			$member->setVar('uname', $_REQUEST['uname']);
			$member->setVar('email', $_REQUEST['email']);
			$member->setVar('ip4', $_REQUEST['ip4']);			
			$member->setVar('ip6', $_REQUEST['ip6']);
			$member->setVar('long', $_REQUEST['long']);
			$member->setVar('proxy-ip4', $_REQUEST['proxy_ip4']);
			$member->setVar('proxy-ip6', $_REQUEST['proxy_ip6']);			
			$member->setVar('network-addy', $_REQUEST['network_addy']);
			$member->setVar('mac-addy', $_REQUEST['mac_addy']);
			$member->setVar('tags', $_REQUEST['tags']);
			$member->setVar('card-number', $_REQUEST['card-number']);
			$member->setVar('card-type', $_REQUEST['card-type']);
			$member->setVar('card-expire-month', $_REQUEST['card-expire-month']);
			$member->setVar('card-expire-year', $_REQUEST['card-expire-year']);
			$member->setVar('card-country-use', $_REQUEST['card-country-use']);
			$member->setVar('card-country-collected', $_REQUEST['card-country-collected']);
			$member->setVar('card-country-shipped', $_REQUEST['card-country-shipped']);
			$member->setVar('made', $_REQUEST['made']);
						
			if ($membershandler->insert($member))	{					
				redirect_header('index.php', 3, 'Member Item Updated Successfully');
			} else {
				redirect_header('index.php', 3, 'Member Item Updated Unsuccessfully');
			}
			exit(0);
			break;
		} else {
			redirect_header('index.php', 3, 'Member Item Updated Unsuccessfully');
			exit(0);
		}
		break;
	case "create":
		

		if ($GLOBALS['xoopsModuleConfig']['htaccess']==true && !isset($_GET['extra'])) {
			if ($_SERVER['REQUEST_URI']!=XOOPS_URL . '/ban/create/ipsec.html') {
				header( "HTTP/1.1 301 Moved Permanently" );
				header('Location: '.XOOPS_URL . '/ban/create/ipsec.html');
				exit(0);
			}
		}

		include $GLOBALS['xoops']->path('/header.php');
			
		$GLOBALS['xoTheme']->addStylesheet(XOOPS_URL."/modules/ban/language/".$GLOBALS['xoopsConfig']['language']."/ban_style.css");
		
		$GLOBALS['xoopsTpl']->assign('ban_member_form', edit_members_form());
		$GLOBALS['xoopsTpl']->display(__DIR . DIRECTORY_SEPARATOR . 'templates'. DIRECTORY_SEPARATOR . 'ban_index.html');
		include $GLOBALS['xoops']->path('/footer.php');
		break;
	}

?>