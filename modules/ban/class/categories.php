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
/**
 * Class for Ban Profiler
 * @author Simon Roberts (simon@labs.coop)
 * @copyright copyright (c) 2010-2013 labs.coop
 * @package bans
 */
class banCategories extends XoopsObject
{

    function banCategories($fid = null)
    {
        $this->initVar('category_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('category_name', XOBJ_DTYPE_TXTBOX, null, true, 128);
        $this->initVar('category_type', XOBJ_DTYPE_ENUM, null, true, false, false, file($GLOBALS['xoops']->path('/include/libs/category-enumeration.txt')));
    }

    function toArray() {
    	$ret = parent::toArray();
    	return $ret;
    }
    
}


/**
* XOOPS Ban Profiler handler class.
* This class is responsible for providing data access mechanisms to the data source
* of XOOPS user class objects.
*
* @author  Simon Roberts <simon@labs.coop>
* @package bans
*/
class banCategoriesHandler extends XoopsPersistableObjectHandler
{
    function __construct(&$db) 
    {
        parent::__construct($db, "ban_categories", 'banCategories', "category_id", "category_name");
    }
	
}
?>