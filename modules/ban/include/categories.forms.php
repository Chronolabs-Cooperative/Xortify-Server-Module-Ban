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
 * Edit Categories Form
 * edit_categories_form()
 *
 */
function edit_categories_form()
{

	if (isset($_REQUEST['id']))
	{
		$id = intval($_REQUEST['id']);				
		$categorieshandler = xoops_getmodulehandler('categories','ban');
		$categories = $categorieshandler->get($id);	
		$category_id = $categories->getVar('category_id');
		$category_name = $categories->getVar('category_name');	
		$category_weight = $categories->getVar('category_weight');	
		$title = 'Edit categories Item';
	} else {
		$category_id = 0;
		$category_weight = 1;
		$category_name = '';
		$title = 'New categories Item';
	}
	
	$form = new XoopsThemeForm($title, "edititem", "", "post");

	$form->addElement(new XoopsFormText('Name:', "category_name", 35, 128, $category_name));
	//$form->addElement(new XoopsFormText('Weight:', "weight", 5, 3, $weight));

	$form->addElement(new XoopsFormHidden("id", $category_id));
	$form->addElement(new XoopsFormHidden("op", "categories"));		
	$form->addElement(new XoopsFormHidden("fct", "save"));		
	$form->addElement(new XoopsFormButton('', 'contents_submit', _SUBMIT, "submit"));
	$form->display();
}


/**
 * Select Categories Form
 * sel_categories_form()
 *
 */
function sel_categories_form()
{

	$form = new XoopsThemeForm('Current Categories', "current", "", "post");

	$categorieshandler = xoops_getmodulehandler('categories','ban');
	$criteria = new Criteria('1', 1);
	$categories = $categorieshandler->getObjects($criteria);	
	$element = array();
	foreach($categories as $key => $item)
	{
		$element[$key] = new XoopsFormElementTray('Item '.$item->getVar('id').':');
		$element[$key]->addElement(new XoopsFormLabel('', '<a href="index.php?op=categories&fct=edit&id='.$item->getVar('category_id').'">Edit</a>&nbsp;<a href="index.php?op=categories&fct=delete&id='.$item->getVar('category_id').'">Delete</a>'));
		//$element[$key]->addElement(new XoopsFormText('Weight:', 'weight['.$item->getVar('category_id').']', 4, 5, $item->getVar('category_weight')));			
		$element[$key]->addElement(new XoopsFormText('Name:', 'category_name['.$item->getVar('category_id').']', 25,128,$item->getVar('category_name')));
		$element[$key]->addElement(new XoopsFormHidden('id['.$key.']', $item->getVar('category_id')));
		$form->addElement($element[$key]);
	}
	$form->addElement(new XoopsFormHidden("op", "categories"));		
	$form->addElement(new XoopsFormHidden("fct", "saveall"));				
	$form->addElement(new XoopsFormButton('', 'contents_submit', _SUBMIT, "submit"));
	
	$form->display();
			
}
		
?>