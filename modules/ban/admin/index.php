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
	require_once __DIR__ . DIRECTORY_SEPARATOR . 'header.php';

	xoops_cp_header();
	if (class_exists("ModuleAdmin"))
	{
    	$indexAdmin = new ModuleAdmin();
    	echo $indexAdmin->addNavigation(basename($_SERVER['PHP_SELF']).'?op='.$_REQUEST['op']);	
	}
	
	switch($_REQUEST['op']) {
	case "members":	
	
		switch ($_REQUEST['fct'])
		{
			case "edit":
				ban_adminMenu(2);
				echo edit_members_form();
				break;
			case "new":
				ban_adminMenu(3);
				echo edit_members_form();
				break;
			case "delete":
				$id = intval($_REQUEST['id']);				
				$membershandler = xoops_getmodulehandler('members','ban');
				$member = $membershandler->get($id);	
				if ($membershandler->delete($member, true))			
					redirect_header('index.php', 3, 'Member Item Delete Successfully');
				else
					redirect_header('index.php', 3, 'Member Item Delete Unsuccessfully');
				break;
				exit;
			case "save":	
	
				include_once( $GLOBALS['xoops']->path('/modules/ban/class/recaptchalib.php') );
				$resp = recaptcha_check_answer ($GLOBALS['xoopsModuleConfig']['private_key'], $_SERVER["REMOTE_ADDR"],
												$_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
				if (!$resp->is_valid) {
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
				$member->setVar('ip4', $_REQUEST['ip4']);			
				$member->setVar('ip6', $_REQUEST['ip6']);
				$member->setVar('long', $_REQUEST['long']);
				$member->setVar('proxy-ip4', $_REQUEST['proxy_ip4']);
				$member->setVar('proxy-ip6', $_REQUEST['proxy_ip6']);			
				$member->setVar('network-addy', $_REQUEST['network_addy']);
				$member->setVar('mac-addy', $_REQUEST['mac_addy']);
				$member->setVar('made', $_REQUEST['made']);

				if ($membershandler->insert($member))	{					
					redirect_header('index.php', 3, 'Member Item Updated Successfully');
				} else {
					redirect_header('index.php', 3, 'Member Item Updated Unsuccessfully');
				}
				exit;
				break;
			default:
				ban_adminMenu(2);
				sel_members_form();
				
		}
		break;
	case "categories":	
	
		switch ($_REQUEST['fct'])
		{
			case "edit":
				ban_adminMenu(4);
				edit_categories_form();
				break;
			case "new":
				ban_adminMenu(5);
				edit_categories_form();
				break;
			case "delete":
				$id = intval($_REQUEST['id']);				
				$categorieshandler = xoops_getmodulehandler('categories','ban');
				$categories = $categorieshandler->get($id);	
				if ($categorieshandler->delete($categories, true))			
					redirect_header('index.php', 3, 'Category Item Delete Successfully');
				else
					redirect_header('index.php', 3, 'Category Item Delete Unsuccessfully');
				break;
				exit;
			case "save":
	
				$id = intval($_REQUEST['id']);				
				$categorieshandler = xoops_getmodulehandler('categories','ban');
				if ($id>0)
					$categories = $categorieshandler->get($id);	
				else
					$categories = $categorieshandler->create();	
	
				$categories->setVar('category_name', $_REQUEST['category_name']);

				if ($categorieshandler->insert($categories))			
					redirect_header('index.php', 3, 'Category Item Updated Successfully');
				else
					redirect_header('index.php', 3, 'Category Item Updated Unsuccessfully');
					
				exit;
				break;
			default:
				ban_adminMenu(4);
				sel_categories_form();
				
		}
		break;
	default:

		ban_adminMenu(1);
		sel_categories_form();
		sel_members_form();
		
	}
	
	ban_footer_adminMenu();
	require_once __DIR__ . DIRECTORY_SEPARATOR . 'footer.php';
?>