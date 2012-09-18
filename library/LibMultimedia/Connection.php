<?php
class LibMultimedia_Connection {
	
	/**
	 * vychozi spojeni k serveru
	 * 
	 * @var LibMultimedia_Connection
	 */
	protected static $_defaultConnection;
	
	/**
	 * SESSID relace pripojeni ke knihovne
	 */
	protected $_mlc;
	
	/**
	 * hostitelsky server
	 * 
	 * @var string
	 */
	protected $_host;
	
	/**
	 * uzivatelske jmeno
	 * 
	 * @var string
	 */
	protected $_username;
	
	/**
	 * heslo uzivatele
	 * 
	 * @var string
	 */
	protected $_password;
	
	private function __construct($host, $user, $password) {
		// nastaveni hostitele
		$this->_host = $host;
		
		// vygenerovani url
		$url = self::_buildUrl("user", "signin");
		
		// pripojeni a vygenerovani SESSID
		$params = array("user[username]" => $user, "user[password]" => $password);
		$request = $this->_getRequest($url, Zend_Http_Client::POST, $params);
		
		// odeslani pozadavku a nastaveni pozadovanych dat
		$response = $request->request();
		
		if (!$response->getStatus() != 200) throw new LibMultimedia_Connection_Exception("Invalid login");
		
		$mlc = $this->_getMlc($response->getHeader("Set-Cookie"));
		
		if (!$mlc) throw new LibMultimedia_Connection_Exception("Id cookie has not found");
		
		// nastaveni cookie
		$this->_mlc = $mlc;
	}
	
	/**
	 * odesle dotaz a vraci retezec odpovedi
	 * 
	 * @param string $object typ objektu
	 * @param string $action pozadovana akce
	 * @param string $id identifikator
	 * @param array $params pole parametru
	 * @param string $method metoda dotazu (POST nebo GET)
	 * @throws LibMultimedia_Connection_Exception
	 * @return string
	 */
	public function sendRequest($object, $action, $id, array $params, $method) {
		// vygenerovani url
		$url = $this->_buildUrl($object, $action, $id);
		
		// vygenerovani requestu
		$request = $this->_getRequest($url, $method, $params);
		
		// odeslani dat
		$response = $request->request();
		
		if (!$response->getStatus() != 200) throw new LibMultimedia_Connection_Exception("Request error");
		
		return $response->getBody();
	}
	
	/**
	 * vyfiltruje cookie _mlc z textoveho retezce
	 *
	 * @param string $cookieString
	 * @return string
	 */
	protected function _getMlc($cookieString) {
		$cookies = explode(";", $cookieString);
		$found = false;
	
		foreach($cookies as $cookie) {
			//orezani mezer
			$cookie = trim($cookie);
	
			//rozlozeni na jmeno a hodnotu
			list($cookieName, $cookieVal) = explode("=", $cookie);
	
			//kontorla jestli je hodnota spravna
			if($cookieName == "_mlc") {
				$found = true;
				break;
			}
		}
	
		if($found)
			return trim($cookieVal);
	
		return false;
	}
	
	protected function _getRequest($url, $method, array $params = null) {
		// priprava requestu
		$request = new Zend_Http_Client($url);
		$request->setMethod($method);
		
		// nastaveni cookie sessid
		if ($this->_mlc)
			$request->setCookie("_mlc", $this->_mlc);
		
		// nastaveni parametru
		switch ($method) {
			case $request::POST: 
				$request->setParameterPost($params);
				break;
				
			case $request::GET:
				$request->setParameterGet($params);
				break;
				
			default:
				throw new LibMultimedia_Connection_Exception("Unknown HTTP method");
		}
		
		return $request;
	}
	
	public static function getDefaultConnection() {
		return self::$_defaultConnection;
	}
	
	public static function setDefaultConnection(LibMultimedia_Connection $connection) {
		self::$_defaultConnection = $connection;
	}
	
	public static function connect($host, $username, $psw) {
		$connection = new self($host, $username, $psw);
		
		// pokud neni vychozi spojeni nastaveno, nastavi se
		if (!self::$_defaultConnection) self::$_defaultConnection = $connection;
		
		return $connection;
	}
	
	protected static function _buildUrl($object, $action, $id = null) {
		// vygenerovani a vraceni url
		switch ($object) {
			case "user" :
				switch ($action) {
					case "signin":
						return "/user/signin";
						
					case "post":
						return "/user/$id/post";
						
					case "get":
						return "/user/$id/get";
						
					default:
						throw new LibMultimedia_Connection_Exception("Unknown action '$action' in user object");
				}
				
			default:
				throw new LibMultimedia_Connection_Exception("Unknown object '$object'");
		}
	}
}