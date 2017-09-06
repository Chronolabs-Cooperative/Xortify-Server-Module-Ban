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

/*	Searchs the Ban Area for Details on search form
 *  ban_search($queryarray, $andor, $limit, $offset, $userid)
*
*  @param string $queryarray			Array to be converted to JSON
*  @param boolean $andor				To Search AND/OR Inclusive
*  @param integer $limit				Limit to items to return
*  @param integer $offset				Start Offset in search
*  @param integer $userid				UID of user to search under
*  @return array
*/
function ban_search($queryarray, $andor, $limit, $offset, $userid)
{
	$ret = array();
	$ban_handler = xoops_getmodulehandler('members', 'ban');
	$criteria = array();
	if ($queryarray == ''||$userid>0){
		$keywords= '';
		$hightlight_key = '';
		$criteria_final = new CriteriaCompo(new Criteria('suid', $userid), "AND");
	} else {
		$criteria_final = new CriteriaCompo();
		foreach($queryarray as $id => $keyword) {
			$criteria[$id] = new CriteriaCompo(new Criteria('`ip4`', $keyword), 'OR');
			$criteria[$id]->add(new Criteria('`ip6`', $keyword), 'OR');
			$criteria[$id]->add(new Criteria('`proxy-ip4`', $keyword), 'OR');
			$criteria[$id]->add(new Criteria('`proxy-ip6`', $keyword), 'OR');
			$criteria[$id]->add(new Criteria('`network-addy`', $keyword), 'OR');
			$criteria[$id]->add(new Criteria('`uname`', $keyword), 'OR');
			$criteria[$id]->add(new Criteria('`email`', $keyword), 'OR');
			$criteria[$id]->add(new Criteria('`mac-addy`', $keyword), 'OR');
		}
		foreach($criteria as $id => $crit) {
			$criteria_final->add($criteria[$id], $andor);
		}
	}	

	$criteria_final->setLimit($limit);
	$criteria_final->setStart($offset);
	$criteria_final->setSort('`made`');
	$criteria_final->setOrder('DESC');
	$bansObj = $ban_handler->getObjects($criteria_final, true);
	
	foreach ($bansObj as $member_id => $result) {
		$item['image'] = "images/ban.png";
		$item['link'] = "index.php?op=member&id=" . $member_id;
		$item['title'] = "Ban: " . $result->ipaddy() . ' : '.$result->getVar('network-addy');
		$item['time'] = $result->getVar('made');
		$item['uid'] = $result->getVar('suid');
		$ret[] = $item;
		unset($item);
	}	
	
	return $ret;
}

?>