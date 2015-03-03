<?php
/**
 * This class provides an API to LiUs KOBRA system.
 *
 * Setup the class by calling
 *   Kobra::$USER = 'user name';
 *   Kobra::$API_KEY = 'api key';
 * After that, one of the find functions can be used to lookup a student.
 *
 * NOTE: If your server fails to verify the SSL protocol you can set
 * Kobra::$VERIFY_SSL = false. Keep in mind that this is very insecure.
 *
 * @author Jonas Sandström
 */
class Generic_Kobra {
	public static $URL = 'xxx';
	public static $USER = 'xxx';
	public static $API_KEY = 'xxx';
	public static $VERIFY_SSL = false;
	// return type array aliases
	const BLOCKED = 'blocked';
	const LASTNAME = 'last_name';
	const SURNAME = 'last_name';
	const EMAIL = 'email';
	const FIRSTNAME = 'first_name';
	const PERSONALNUMBER = 'personal_number';
	const SSN = 'personal_number';
	const RFIDNUMBER = 'rfid_number';
	const RFID = 'rfid_number';
	const BARCODENUMBER = 'barcode_number';
	const BARCODE = 'barcode_number';
	const UNION = 'union';
	const LIUID = 'liu_id';
	/**
	 * @param	$param
	 * @param	$type	json, xml or array
	 * @return	mixed
	 */
	public static function find($param, $type = 'array') {
		// Check for @-sign in email
		if (strpos($param, '@') !== false)
			return self::findByEmail($param, $type);
		// Crude SSN check. Triggered on xxxxxx-xxxx
		elseif (strlen($param) == 11 && strpos($param, '-') == 6)
			return self::findBySSN($param, $type);
		// Only LiU-ID left to check for that isn't a number
		elseif (!is_numeric($param))
			return self::findByLiuID($param, $type);
		// Assume barcode numbers are longer than 12 characters
		elseif (strlen($param) > 12)
			return self::findByBarcode($param, $type);
		// Fallback to RFID number
		else
			return self::findByRFID($param, $type);
	}
	public static function findByLiuID($param, $type = 'array') {
		/*if ($data = self::load($type, 'liuid', $param))
			return $data;*/
		$result = self::rattle("liu_id=$param", $type);
		//self::save($result, $type, 'liuid', $param);
		return $result;
	}
	public static function findByEmail($param, $type = 'array') {
		/*if ($data = self::load($type, 'email', $param))
			return $data;*/
		$result = self::rattle("email=$param", $type);
		//self::save($result, $type, 'email', $param);
		return $result;
	}
	public static function findBySSN($param, $type = 'array') {
		/*if ($data = self::load($type, 'ssn', $param))
			return $data;*/
		$result = self::rattle("personal_number=$param", $type);
		//self::save($result, $type, 'ssn', $param);
		return $result;
	}
	public static function findByRFID($param, $type = 'array') {
		/*if ($data = self::load($type, 'rfid', $param))
			return $data;*/
		$result = self::rattle("rfid_number=$param", $type);
		//self::save($result, $type, 'rfid', $param);
		return $result;
	}
	public static function findByBarcode($param, $type = 'array') {
		/*if ($data = self::load($type, 'barcode', $param))
			return $data;*/
		$result = self::rattle("barcode_number=$param", $type);
		//self::save($result, $type, 'barcode', $param);
		return $result;
	}
	public static function findByPersonalNumber($param, $type = 'array') {
		return self::findBySSN($param, $type);
	}
	protected static function load($type, $by, $param) {
		if (isset($_SESSION[$type][$by][$param]))
			return $_SESSION[$type][$by][$param];
		else
		{
			$sessionType = new Zend_Session_Namespace($type);
			$sessionLiuID->$by = $param;
			return $_SESSION[$type][$by][$param];
		}
	}
	protected static function save($data, $type, $by, $param) {
		if (!isset($_SESSION['Kobra']))
			$_SESSION['Kobra'] = array();
		$_SESSION[$type][$by][$param] = $data;
	}
	protected static function rattle($args, $type) {
		if (!strlen($user = self::$USER) || !strlen($passwd = self::$API_KEY))
			return user_error('Username or API key missing', E_USER_WARNING);
		$response = (strtolower($type) == 'xml') ? '.xml' : '.json';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, self::$URL.$response);//
		curl_setopt($ch, CURLOPT_USERPWD, "$user:$passwd");
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		// Following is new
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirects
		curl_setopt($ch, CURLOPT_TIMEOUT, 3); // times out after 4s
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, Generic_Kobra::$VERIFY_SSL);
		$result = curl_exec($ch);
		$result = self::unescape_utf16($result);
		if (strtolower($type) == 'array')
			$result = json_decode($result, true);
			curl_close($ch);
		return $result;
	}
	protected static function unescape_utf16($string) {
		/* go for possible surrogate pairs first */
		$string = preg_replace_callback(
			'/\\\\u(D[89ab][0-9a-f]{2})\\\\u(D[c-f][0-9a-f]{2})/i',
			array('Generic_Kobra', 'callback'), $string);
		/* now the rest */
		$string = preg_replace_callback('/\\\\u([0-9a-f]{4})/i',
			array('Generic_Kobra', 'callback'), $string);
		return $string;
	}
	protected static function callback($matches) {
		if (count($matches) == 3)
			$d = pack("H*", $matches[1].$matches[2]);
		else
			$d = pack("H*", $matches[1]);
		return mb_convert_encoding($d, "UTF-8", "UTF-16BE");
	}
}
