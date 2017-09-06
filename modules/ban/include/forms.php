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
    
    error_reporting(E_ALL);
    
	xoops_load('XoopsFormLoader');
	
	// Include Internal Form Objects
	include_once dirname(dirname(__DIR__)) . "/tag/include/formtag.php";
	require_once dirname(__DIR__) . "/class/formselectcountry.php";
	require_once dirname(__DIR__) . "/class/formselectcategory.php";
	require_once dirname(__DIR__) . "/class/formselectmember.php";
	require_once dirname(__DIR__) . "/class/formselectcardtype.php";
	require_once dirname(__DIR__) . "/class/formselectmonth.php";
	require_once dirname(__DIR__) . "/class/formselectyear.php";
	
	// Include Forms
	require_once dirname(__DIR__) . "/include/members.forms.php";
	require_once dirname(__DIR__) . "/include/categories.forms.php";
	$module_handler =& xoops_gethandler( 'module' );
	$config_handler =& xoops_gethandler( 'config' );
	$xoModule =& $module_handler->getByDirname('ban');
	$GLOBALS['xoopsModuleConfig'] = $config_handler->getConfigList( $xoModule->getVar( 'mid' ) );
	
?>