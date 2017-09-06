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

if (!class_exists("ip2location_lite")) {
/*	Checks the Siteinfo Array
 *  check_siteinfo($siteinfo)
 *
 *  @subpackage ipinfodb
 */
final class ip2location_lite{
	/*
	 * Errors Occured Array
	 */
	protected $errors = array();
	
	/*
	 * Service URI
	 */
	protected $service = 'api.ipinfodb.com';
	
	/*
	 * Version of array
	*/
	protected $version = 'v3';

	/*
	 * API Key
	 */
	protected $apiKey = '';

	/*  API Info DB __constructor
	 *  __construct()
	 */
	public function __construct(){}

	/*  API Info DB __constructor
	 *  __construct()
	 */
	public function __destruct(){}

	/*  Sets API Key 
	 *  setKey($key)
	 *  
	 *  @param string $key		API Key from ipinfodb.com
	 *  @return string
	 */
	public function setKey($key) {
		if(!empty($key)) $this->apiKey = $key;
	}

	/*  Return API Errors
	 *  getError()
	 *  
	 *  @return string
	 */
	public function getError() {
		return implode("\n", $this->errors);
	}

	/*  API Country Responder
	 *  getCountry($host)
	 *  
	 *  @param string $host		Host of API
	 *  @return array
	 */
	public function getCountry($host) {
		return $this->getResult($host, 'ip-country');
	}

	/*  API City Responder
	 *  getCity($host)
	 *  
	 *  @param string $host		Host of API
	 *  @return array
	 */
	 public function getCity($host) {
		return $this->getResult($host, 'ip-city');
	}

	/*  API Repsonse Stripper
	 *  getResult($host, $name)
	 *  
	 *  @param string $host		Host of API
	 *  @param string $name		Name Data for Query
	 *  @return array
	 */
	private function getResult($host, $name) {
		$ip = @gethostbyname($host);

		if(preg_match('/^(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}$/', $ip)){
			$xml = @file_get_contents('http://' . $this->service . '/' . $this->version . '/' . $name . '/?key=' . $this->apiKey . '&ip=' . $ip . '&format=xml');

			try{
				$response = @new SimpleXMLElement($xml);

				foreach($response as $field=>$value){
					$result[(string)$field] = (string)$value;
				}

				return $result;
			}
			catch(Exception $e){
				$this->errors[] = $e->getMessage();
				return;
			}
		}

		$this->errors[] = '"' . $host . '" is not a valid IP address or hostname.';
		return;
	}
}
}
?>