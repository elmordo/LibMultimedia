<?php
class LibMultimedia_Item_User extends LibMultimedia_Item_Abstract {
	
	const OBJECT = "user";
	
	protected $_groupList = array();
	
	protected $_roleList = array();
	
	protected $_home = null;
	
	/**
	 * vytvor noveho uzivatele
	 * 
	 * @param string $login login
	 * @param string $password heslo
	 * @param string $email email na uzivatele
	 * @return LibMultimedia_Item_User
	 */
	public static function create(array $data, $connection = null) {
		// pripojeni
		if (!$connection)
			$connection = LibMultimedia_Connection::getDefaultConnection();
		
		// parametry
		$params = array(
				"user[password]" => $data["password"],
				"user[email]" => $data["email"]
		);
		
		// odeslani pozadavku
		$connection->sendRequest(self::OBJECT, "post", $data["login"], $params, Zend_Http_Client::POST);
		
		return self::load($login, $connection);
	}
	
	/**
	 * nacte uzivatele ze serveru
	 * 
	 * @param string $login login hledaneho uzivatele
	 * @return LibMultimedia_Item_User
	 */
	public static function load($id, $connection = null) {
		// pripojeni
		if (!$connection)
			$connection = LibMultimedia_Connection::getDefaultConnection();
		
		// nacteni odpovedi
		$response = $connection->sendRequest(self::OBJECT, "get", $login, array(), Zend_Http_Client::GET);
		$content = Zend_Json::decode($response);
		
		// poskladani dat odpovedi
		$contentRef = $content["user"];
		$contentRef["groups"] = $content["group"];
		$contentRef["roles"] = $content["role"];
		
		// sestaveni parametru
		$params = array(
				"data" => $contentRef,
				"connection" => $connection
		);
		
		return new self($params, true);
	}
	
	public function __construct($params, $complete = false) {
		// zpracovani skupin
		$groups = $params["data"]["groups"];
		
		// zpracovani roli
		$roles = $params["data"]["roles"];
		
		// odebrani sloupcu
		unset($groups["data"]["groups"], $groups["data"]["roles"]);
		
		// nastaveni povolenych sloupcu
		$params["allowedColumns"] = array(
				"id",
				"username",
				"email",
				"root_directory_id"
		);
		
		// zavolani konstruktoru predka
		parent::__construct($params);
		
		// nastaveni, jestli je nacteni kompletni
		$this->_isComplete = $complete;
	}
	
	/**
	 * vraci domovsky adresar uzivatele
	 * 
	 * @return LibMultimedia_Item_Directory
	 */
	public function getHomeDirectory() {
		// kontrola, jestli je home nacten
		if (!is_object($this->_home)) {
			// nacteni home
			$this->_home = LibMultimedia_Item_Directory::load($this->_home);
		}
		
		return $this->_home;
	}
	
	public function save() {
		
	}
	
	public function reload() {
		// pokud se nejedna o plnohodnotny objekt, nacte se ze serveru
		if (!$this->_isComplete) {
			
		}
	}
}