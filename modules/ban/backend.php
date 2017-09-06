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

include dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'mainfile.php';

if ($GLOBALS['xoopsModuleConfig']['htaccess']==true) {
	$url = XOOPS_URL.'/ban/'.basename($_SERVER['PHP_SELF']).'?'.$_SERVER['QUERY_STRING'];
	if ($_SERVER['REQUEST_URI'].'?'.$_SERVER['QUERY_STRING']!=$url && strpos($_SERVER['REQUEST_URI'], 'odules/')>0) {
		header( "HTTP/1.1 301 Moved Permanently" ); 
		header('Location: '.$url);
		exit(0);
	}
}

$GLOBALS['xoopsLogger']->activated = false;
if (function_exists('mb_http_output')) {
    mb_http_output('pass');
}
header('Content-Type:text/xml; charset=utf-8');

include_once $GLOBALS['xoops']->path('class/template.php');
$tpl = new XoopsTpl();
$tpl->xoops_setCaching(2);
$tpl->xoops_setCacheTime(30);
$compile_id = (isset($_REQUEST['twit'])?1:2);
$xoopsCachedTemplateId = 'mod_'.$GLOBALS['xoopsModule']->getVar('dirname').'|'.md5(str_replace(XOOPS_URL, '', $GLOBALS['xoopsRequestUri']));
if (!$tpl->is_cached('db:system_rss.html', $xoopsCachedTemplateId, $compile_id)) {
    xoops_load('XoopsLocal');
    $tpl->assign('channel_title', XoopsLocal::convert_encoding(htmlspecialchars($xoopsConfig['sitename'], ENT_QUOTES)));
    $tpl->assign('channel_link', XOOPS_URL . '/');
    $tpl->assign('channel_desc', XoopsLocal::convert_encoding(htmlspecialchars($xoopsConfig['slogan'], ENT_QUOTES)));
    $tpl->assign('channel_lastbuild', formatTimestamp(time(), 'rss'));
    $tpl->assign('channel_webmaster', $xoopsConfig['adminmail']);
    $tpl->assign('channel_editor', $xoopsConfig['adminmail']);
    $tpl->assign('channel_category', 'News');
    $tpl->assign('channel_generator', 'XOOPS');
    $tpl->assign('channel_language', _LANGCODE);
    $tpl->assign('image_url', XOOPS_URL . '/images/logo.png');
    $dimention = getimagesize(XOOPS_ROOT_PATH . '/images/logo.png');
    if (empty($dimention[0])) {
        $width = 88;
    } else {
        $width = ($dimention[0] > 144) ? 144 : $dimention[0];
    }
    if (empty($dimention[1])) {
        $height = 31;
    } else {
        $height = ($dimention[1] > 400) ? 400 : $dimention[1];
    }
    $tpl->assign('image_width', $width);
    $tpl->assign('image_height', $height);

   	$banmemberhandler =& xoops_getmodulehandler('members','ban');
	$criteria = new CriteriaCompo(new Criteria('1', '1' ));
	$criteria->setSort('`member_id`');
	$criteria->setOrder('DESC');
	$num = isset($_REQUEST['num'])?intval($_REQUEST['num']):35;
	if ($num<200)
		$criteria->setLimit($num);
	else
		$criteria->setLimit(20);
	
	$banned = $banmemberhandler->getObjects($criteria, true);

    foreach($banned as $id => $ban) {
		
		if (strlen($ban->getVar('ip4'))>0)
			$category = 'IPv4';
		else
			$category = 'IPv6';
		
		$title = '#Ban '.$id.' : #IP (#'.$category.') '.$ban->ipaddy(). ' : #Netbios '.$ban->getVar('network-addy');

		$tpl->append('items', array(
			'title' => XoopsLocal::convert_encoding(htmlspecialchars($title, ENT_QUOTES)) ,
			'link' => $ban->getURL(),
			'guid' => $ban->getURL(),
			'category' => $category,
			'pubdate' => formatTimestamp($ban->getVar('made'), 'rss') ,
			'description' => XoopsLocal::convert_encoding(htmlspecialchars($ban->story(), ENT_QUOTES))
			)
		);
	}
}
$tpl->display('db:system_rss.html');


/**
 * *#@+
 * Xoops Stripe Licence System Key
 */
function xoStripeKey($xoops_key)
{
    $uu = 0;
    $num = 3;
    $length = strlen($xoops_key);
    $strip = floor(strlen($xoops_key) / 6);
    for ($i = 0; $i < strlen($xoops_key); $i++) {
        if ($i < $length) {
            $uu++;
            if ($uu == $strip) {
                $ret .= substr($xoops_key, $i, 1) . '-';
                $uu = 0;
            } else {
                if (substr($xoops_key, $i, 1) != '-') {
                    $ret .= substr($xoops_key, $i, 1);
                } else {
                    $uu--;
                }
            }
        }
    }
    $ret = str_replace('--', '-', $ret);
    if (substr($ret, 0, 1) == '-') {
        $ret = substr($ret, 2, strlen($ret));
    }
    if (substr($ret, strlen($ret) - 1, 1) == '-') {
        $ret = substr($ret, 0, strlen($ret) - 1);
    }
    return $ret;
}
?>