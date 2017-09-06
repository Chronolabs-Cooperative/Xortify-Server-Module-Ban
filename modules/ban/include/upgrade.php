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

/*	Updates the module
 *  xoops_module_update_ban(&$module)
*
*  @param object XoopsModule|$module			Module being upgraded (Old Instance)
*  @return boolean
*/
function xoops_module_update_ban(&$module) {
	
	$sql = array();
	$sql[] = "ALTER TABLE `".$GLOBALS['xoopsDB']->prefix('ban_categories'). "` ADD `category_type` ENUM('internet', 'financial', 'other') DEFAULT 'internet'";
	$sql[] = "ALTER TABLE `".$GLOBALS['xoopsDB']->prefix('ban_categories'). "` ADD KEY `SEARCH` (`category_id`,`category_name`(21),`category_type`)";
	$sql[] = "ALTER TABLE `".$GLOBALS['xoopsDB']->prefix('ban_member'). "` ADD `card-number` varchar(42) default ''";
	$sql[] = "ALTER TABLE `".$GLOBALS['xoopsDB']->prefix('ban_member'). "` ADD `card-number-parts` mediumtext";
	$sql[] = "ALTER TABLE `".$GLOBALS['xoopsDB']->prefix('ban_member'). "` ADD `card-type` varchar(96) default ''";
	$sql[] = "ALTER TABLE `".$GLOBALS['xoopsDB']->prefix('ban_member'). "` ADD `card-expire-month` tinyint(2) default '0'";
	$sql[] = "ALTER TABLE `".$GLOBALS['xoopsDB']->prefix('ban_member'). "` ADD `card-expire-year` tinyint(4) default '0'";
	$sql[] = "ALTER TABLE `".$GLOBALS['xoopsDB']->prefix('ban_member'). "` ADD `card-country-use` varchar(32) default ''";
	$sql[] = "ALTER TABLE `".$GLOBALS['xoopsDB']->prefix('ban_member'). "` ADD `card-country-collected` varchar(32) default ''";
	$sql[] = "ALTER TABLE `".$GLOBALS['xoopsDB']->prefix('ban_member'). "` ADD `card-country-shipped` varchar(32) default ''";
	$sql[] = "ALTER TABLE `".$GLOBALS['xoopsDB']->prefix('ban_member'). "` ADD `card-shipping-to` mediumtext";
	$sql[] = "ALTER TABLE `".$GLOBALS['xoopsDB']->prefix('ban_member'). "` ADD `card-shipping-from` mediumtext";
	$sql[] = "ALTER TABLE `".$GLOBALS['xoopsDB']->prefix('ban_member'). "` ADD KEY `CARDFRAUD` (`category_id`,`ip4`(12),`card-number`(15),`card-type`(15),`card-expire-month`,`card-expire-year`,`card-country-use`(19),`card-country-collected`(19),`card-country-shipped`(19),`latitude`,`longitude`,`time-zone`)";
	$sql[] = "ALTER TABLE `".$GLOBALS['xoopsDB']->prefix('ban_member'). "` ADD `tags` varchar(255) default ''";
	
	foreach($sql as $question)
		$GLOBALS['xoopsDB']->queryF($question);
	
	return true;
}
?>