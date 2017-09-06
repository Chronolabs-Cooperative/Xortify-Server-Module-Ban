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

	define('_BAN_MF_MEMBER_ID', 'Ban ID');
	define('_BAN_MF_CATEGORY_ID', 'Category ID');
	define('_BAN_MF_UID', 'User ID');
	define('_BAN_MF_UNAME', 'Username');
	define('_BAN_MF_EMAIL', 'Email');
	define('_BAN_MF_IP4', 'IPv4');
	define('_BAN_MF_IP6', 'IPv6');
	define('_BAN_MF_LONG', 'Network Pointer');
	define('_BAN_MF_PROXY_IP4', 'Proxy IPv4');
	define('_BAN_MF_PROXY_IP6', 'Proxy IPv6');
	define('_BAN_MF_NETWORK_ADDY', 'Netbios Name');
	define('_BAN_MF_MAC_ADDY', 'Mac Address');
	define('_BAN_MF_COUNTRY_CODE', 'Country Code');
	define('_BAN_MF_COUNTRY_NAME', 'Country');
	define('_BAN_MF_REGION_NAME', 'Region');
	define('_BAN_MF_CITY_NAME', 'City');
	define('_BAN_MF_POSTCODE', 'Postcode');
	define('_BAN_MF_LATITUDE', 'Latitude');
	define('_BAN_MF_LONGITUDE', 'Longitude');
	define('_BAN_MF_TIMEZONE', 'Timezone');
	define('_BAN_MF_MADE', 'Ban Made');
	define('_BAN_MF_CATEGORY', 'Category of Ban');
	define('_BAN_MF_CARD_EXPIRE_MONTH', 'Card Expiry Month');
	define('_BAN_MF_CARD_EXPIRE_YEAR', 'Card Expiry Month');
	define('_BAN_MF_CARD_TYPE', 'Card Type');
	define('_BAN_MF_CARD_COUNTRY_USED', 'Country Used');
	define('_BAN_MF_CARD_COUNTRY_COLLECTED', 'Country Collected');
	define('_BAN_MF_CARD_COUNTRY_SHIPPED', 'Country Shipped');
	define('_BAN_MF_CARD_SHIPPING_TO', 'Shipping To');
	define('_BAN_MF_CARD_SHIPPING_FROM', 'Shipping From');
	
	//Email subjects
	define('_BAN_MF_EMAIL_BANNOTICE', 'One of your resources %s is being abused!');
	

	//Version 2.01
	define('_BAN_MF_CARDNUMBER', 'Credit/Store Card number');
	define('_BAN_MF_CARDTYPE', 'Credit/Store Card Type');
	define('_BAN_MF_CARDEXPIREMONTH', 'Card Month of Expiry');
	define('_BAN_MF_CARDEXPIREYEAR', 'Card Year of Expiry');
	define('_BAN_MF_CARDCOUNTRYUSE', 'Card was given in country');
	define('_BAN_MF_CARDCOUNTRYCOLLECTED', 'Card was collect by country');
	define('_BAN_MF_CARDCOUNTRYSHIPPED', 'Card was shipping to country');
	
?>