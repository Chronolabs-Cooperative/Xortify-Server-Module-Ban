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
	die('XOOPS root path not defined');
}
$modversion = array();

$modversion['dirname'] = basename(dirname(__FILE__));
$modversion['name'] = ucfirst(basename(dirname(__FILE__)));
$modversion['version']     = "2.01";
$modversion['releasedate'] = "2013-03-03";
$modversion['status']      = "Stable";
$modversion['description'] = _MI_BAN_DESC;
$modversion['credits']     = "Banner Profile";
$modversion['author']      = "Wishcraft";
$modversion['help']        = "";
$modversion['license']     = "This banning profiler.";
$modversion['official']    = 0;
$modversion['image']       = "images/ban_slogo.png";

//Search
$modversion['hasSearch'] = 1;
$modversion['search']['file'] = "include/search.inc.php";
$modversion['search']['func'] = "ban_search";

// Update
$modversion['onUpdate'] = "include/upgrade.php";

// Main
$modversion['hasMain'] = 1;

// Main Menu
$modversion['sub'][1]['name'] = _BAN_MI_LATESTBANS;
$modversion['sub'][1]['url'] = "index.php?op=latest&num=35";
$modversion['sub'][2]['name'] = _BAN_MI_CREATEBAN;
$modversion['sub'][2]['url'] = "index.php?op=create";	

// Admin
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = "admin/index.php";
$modversion['adminmenu'] = "admin/menu.php";

// Mysql file
$modversion['sqlfile']['mysql'] = "sql/mysql.sql";

// Tables created by sql file
$modversion['tables'][0] = "ban_member";
$modversion['tables'][1] = "ban_categories";

$i = 0;
// Config items

$i++;
$modversion['config'][$i]['name'] = 'display_name';
$modversion['config'][$i]['title'] = '_BAN_MI_DISPLAYNAME';
$modversion['config'][$i]['description'] = '_UNBAN_MI_DISPLAYNAME_DESC';
$modversion['config'][$i]['formtype'] = 'textarea';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = 'Currently Latest Banned IP & Network names';

$i++;
$modversion['config'][$i]['name'] = 'display_text';
$modversion['config'][$i]['title'] = '_BAN_MI_DISPLAYTEXT';
$modversion['config'][$i]['description'] = '_UNBAN_MI_DISPLAYTEXT_DESC';
$modversion['config'][$i]['formtype'] = 'textarea';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = 'This is the top list of banned IP\'s they are the latest on the network and are available with the unbanned option to remove from the network banned list!';

$i++;
$modversion['config'][$i]['name'] = 'htaccess';
$modversion['config'][$i]['title'] = '_BAN_MI_HTACCESS';
$modversion['config'][$i]['description'] = '_BAN_MI_HTACCESS_DESC';
$modversion['config'][$i]['formtype'] = 'yesno';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 0;
// Templates

// Templates
$i = 0;

$i++;
$modversion['templates'][$i]['file'] = 'ban_index.html';
$modversion['templates'][$i]['description'] = 'Picture Profile Screen';

$i++;
$modversion['templates'][$i]['file'] = 'ban_member.html';
$modversion['templates'][$i]['description'] = 'Member Picture Profile Screen';

$i++;
$modversion['templates'][$i]['file'] = 'ban_member_display.html';
$modversion['templates'][$i]['description'] = 'Member Ban Display Profile Screen';

$modversion['hasComments'] = 1;
$modversion['comments']['itemName'] = 'id';
$modversion['comments']['pageName'] = 'index.php';
$modversion['comments']['extraParams'] = array('op');

?>
