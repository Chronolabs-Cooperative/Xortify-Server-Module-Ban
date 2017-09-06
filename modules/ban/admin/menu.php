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


global $adminmenu;
$adminmenu=array();
$adminmenu[1]['title'] = _BAN_ADMENU1;
$adminmenu[1]['link'] = "admin/index.php";
$adminmenu[2]['title'] = _BAN_ADMENU2;
$adminmenu[2]['link'] = "admin/index.php?op=members";
$adminmenu[3]['title'] = _BAN_ADMENU3;
$adminmenu[3]['link'] = "admin/index.php?op=members&fct=new";
$adminmenu[4]['title'] = _BAN_ADMENU4;
$adminmenu[4]['link'] = "admin/index.php?op=categories";
$adminmenu[5]['title'] = _BAN_ADMENU5;
$adminmenu[5]['link'] = "admin/index.php?op=categories&fct=new";
?>