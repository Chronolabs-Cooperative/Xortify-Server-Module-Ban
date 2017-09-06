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


/**
 * Search for Unbanned Items Form
 * search_members_form()
 *
 * @return string
 */
function edit_members_form() {
	
	if (isset($_REQUEST['id']))
	{
		$id = intval($_REQUEST['id']);				
		$membershandler = xoops_getmodulehandler('members','ban');
		$members = $membershandler->get($id);	
		$category_id = $members->getVar('category_id');
		$uid = $members->getVar('uid');
		$uname = $members->getVar('uname');
		$email = $members->getVar('email');
		$ip4 = $members->getVar('ip4');
		$ip6 = $members->getVar('ip6');
		$long = $members->getVar('long');
		$proxy_ip4 = $members->getVar('proxy-ip4');
		$proxy_ip6 = $members->getVar('proxy-ip6');			
		$network_addy = $members->getVar('network-addy');
		$mac_addy = $members->getVar('mac-addy');			
		$made = $members->getVar('made');						
		$title = 'Edit Banned Item';
	} else {
		$category_id = 0;
		if (is_object($GLOBALS['xoopsUser'])) {
			$uid = $GLOBALS['xoopsUser']->getVar('uid');
			$uname = $GLOBALS['xoopsUser']->getVar('uname');
		} else {
			$uid = 0;
			$uname = '';
		}
		if ($_SERVER["HTTP_X_FORWARDED_FOR"] != ""){ 
   			$ip = (string)$_SERVER["HTTP_X_FORWARDED_FOR"]; 
			$is_proxied = true;
	        $proxy_ip = $_SERVER["REMOTE_ADDR"]; 
	        $network_addy = @gethostbyaddr($ip); 
			$long = @ip2long($ip);
			if (is_ipv6($ip)) {
				$ip6 = $ip;
				$proxy_ip6 = $proxy_ip;
			} else {
				$ip4 = $ip;
				$proxy_ip4 = $proxy_ip;
			}
	    }else{ 
			$is_proxied = false;
       		$ip = (string)$_SERVER["REMOTE_ADDR"]; 
	        $network_addy = @gethostbyaddr($ip); 
			$long = @ip2long($ip);
			if (is_ipv6($ip)) {
				$ip6 = $ip;
			} else {
				$ip4 = $ip;
			}
	    } 
	    exec('arping -c 1 '.$_SERVER["REMOTE_ADDR"],$user_mac);
	    $mac_addy = substr($user_mac[1],strpos($user_mac[1],':')-2, '17');
		$made = time();						
		$title = 'New Banned Item';
	}
	
	$form = new XoopsThemeForm($title, "edititem", "", "post");
	if (!empty($ip4)||$title == 'New Banned Item')
		$form->addElement(new XoopsFormText('IPv4:', "ip4", 35, 255, $ip4));		
	if (!empty($ip6)||$title == 'New Banned Item')
		$form->addElement(new XoopsFormText('IPv6:', "ip6", 35, 255, $ip6));		
	if ($is_proxied!=false||$title == 'New Banned Item') {
		if (!empty($proxy_ip4)||$title == 'New Banned Item')
			$form->addElement(new XoopsFormText('Proxy IPv4:', "proxy_ip4", 35, 255, $proxy_ip4));
		if (!empty($proxy_ip6)||$title == 'New Banned Item')				
			$form->addElement(new XoopsFormText('Proxy IPv6:', "proxy_ip6", 35, 255, $proxy_ip6));			
	}
	$form->addElement(new XoopsFormText('Network Address:', "network_addy", 35, 128, $network_addy));					
	$form->addElement(new XoopsFormText('Mac Address:', "mac_addy", 35, 17, $mac_addy));
	$form->addElement(new XoopsFormText('Email Address:', "email", 35, 255, $email));
	$form->addElement(new XoopsFormText('Credit/Store Card Number:', "card-number", 35, 128, ''));
	$form->addElement(new banFormSelectCardtype('Credit/Store Card Type:', "card-type", '', 1, false));
	$form->addElement(new banFormSelectMonth('Card Expiry Month:', "card-expire-month", '', 1, false));
	$form->addElement(new banFormSelectYear('Card Expiry Year:', "card-expire-year", '', 1, false));
	$form->addElement(new banFormSelectCountry(_BAN_MF_CARDCOUNTRYUSE.':', "card-country-use", '', 1, false));
	$form->addElement(new banFormSelectCountry(_BAN_MF_CARDCOUNTRYCOLLECTED.':', "card-country-collected", '', 1, false));
	$form->addElement(new banFormSelectCountry(_BAN_MF_CARDCOUNTRYSHIPPED.':', "card-country-shipped", '', 1, false));
	include_once XOOPS_ROOT_PATH . "/modules/tag/include/formtag.php";
	$form->addElement(new XoopsFormTag("tags", 60, 255, $id, $category_id));
	$form->addElement(new banFormSelectCategory('Banning Category:', "category_id", $category_id, 1, false));
	$form->addElement(new XoopsFormCaptcha('Solve Puzzel:'));
	$form->addElement(new XoopsFormHidden("long", $long));
	$form->addElement(new XoopsFormHidden("made", $made));
	$form->addElement(new XoopsFormHidden("uid", $uid));
	$form->addElement(new XoopsFormHidden("uname", $uname));
	$form->addElement(new XoopsFormHidden("id", $id));
	$form->addElement(new XoopsFormHidden("op", "members"));		
	$form->addElement(new XoopsFormHidden("fct", "save"));		
	$form->addElement(new XoopsFormButton('', 'contents_submit', _SUBMIT, "submit"));
	return $form->display();
}
	
/**
 * Select Memeber of Unban Form
 * sel_members_form()
 *
 */
function sel_members_form()
{

	$form = new XoopsThemeForm('Current Banned List', "current", "", "post");

	$membershandler = xoops_getmodulehandler('members','ban');
	$criteria = new Criteria('1', 1);
	$members = $membershandler->getObjects($criteria);	
	$element = array();
	foreach($members as $key => $item)
	{
		$element[$key] = new XoopsFormElementTray('Item '.$item->getVar('member_id').':');
		if ($item->getVar('uid')>0)
			$element[$key]->setDescription('Banned By <a href="'.XOOPS_URL.'/userinfo.php?uid='.$item->getVar('uid').'">'.$item->getVar('uname').'</a>');
		$element[$key]->addElement(new XoopsFormLabel('', '<a href="index.php?op=members&fct=edit&id='.$item->getVar('member_id').'">Edit</a>&nbsp;<a href="index.php?op=members&fct=delete&id='.$item->getVar('member_id').'">Delete</a>'));
		$element[$key]->addElement(new XoopsFormLabel('IP:', ''.$item->getVar('ip4').''));			
		$element[$key]->addElement(new XoopsFormLabel('Network Address:', $item->getVar('network-addy')));
		$form->addElement($element[$key]);
	}
	return $form->display();
	
}
		
?>