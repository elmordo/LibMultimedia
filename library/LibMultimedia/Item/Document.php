<?php
class LibMultimedia_Item_Document extends LibMultimedia_Item_Abstract {
	
	const OBJECT = "document";

	/**
	 * nejaktualnejsi revize
	 * 
	 * @var LibMultimedia_Item_Document
	 */
	protected $_last;
	
	/**
	 * historie dokumentu
	 * 
	 * @var array<LibMultimedia_Item_Document>
	 */
	protected $_history = array();
	
	/**
	 * obsah dokumentu
	 * 
	 * @var string 
	 */
	protected $_content = null;
	
	/**
	 * pole adresaru, ve kterych je mozne dokument nalezt
	 * 
	 * @var array<LibMultimedia_Item_Directory>
	 */
	protected $_directories = array();
	
	/**
	 * index pozice revize dokumentu v historii
	 * 
	 * @var int
	 */
	protected $_posIndex;
	
	protected $_identifier = "uuid";
	
	public static function load($id, LibMultimedia_Connection $connection = null) {
		// vyhodnoceni pripojeni
		if (!$connection) $connection = LibMultimedia_Connection::getDefaultConnection();
		
		// nacteni dat
		$data = Zend_Json::decode($connection->sendRequest(self::OBJECT, "get", $id, array(), Zend_Http_Client::GET));
		
		// priprava dat pro konstrukotr
		$document = $data["document"];
		$document["master"] = $data["master"];
		$document["directory"] = $data["directory"];
		$document["history"] = $data["history"];
		
		$params = array(
				"data" => $document,
				"connection" => $connection
		);
		
		return new self($params, true);
	}

	public static function create(array $data, $connection = null) {
		// kontrola pripojeni
		if (!$connection) $connection = LibMultimedia_Connection::getDefaultConnection();
		
		// kontrola dat dokumentu
		if (!isset($data["document"]["document_name"], $data["document"]["content"])) throw new LibMultimedia_Item_Exception("Invalid data of Document object");
		
		// odeslani dotazu
		$response = $connection->sendRequest(self::OBJECT, "post", null, $data, Zend_Http_Client::POST);
		$response = Zend_Json::decode($response);
		
		return self::load($response["document"]["uuid"], $connection);
	}
	
	public function __construct(array $params, $complete = false) {
		// priprava dat
		$params = array_merge(array("connection" => LibMultimedia_Connection::getDefaultConnection()), $params);
		
		// zpracovani specialnich udaju
		$data = $params["data"];
		
		// priprava dat
		$data = array_merge(array("history" => array(), "master" => null, "directory" => array()), $data);
		
		// zpracovani predka a historie
		$this->_last = new self(array("data" => $data["master"], "connection" => $params["connection"]));
		$index = 0;
		
		foreach ($data["history"] as $item) {
			$this->_history[] = new self(array(
					"data" => $item,
					"connection" => $params["connection"]
			));

			// kontrola uuid. Pokud je uuid zaznamu v historii rovne uuid dokumentu, nastavi se pozice
			if ($item["uuid"] == $data["uuid"]) $this->_posIndex = $i;
			
			$index++;
		}
		
		// zapis dat adresaru
		foreach ($data["directory"] as $item) {
			$this->_directories[] = new LibMultimedia_Item_Directory(array(
					"data" => $item,
					"connection" => $params["connection"]
			));
		}
		
		unset($data["history"], $data["directory"], $data["master"]);
		
		// konstruktor predka
		parent::__construct($params);
	}
	
	/**
	 * prida dokument do adresare
	 * zmena se provede okamzine a neni nutne ji ukladat
	 * 
	 * @param LibMultimedia_Item_Directory $directory
	 * @return self
	 */
	public function addToDirectory(LibMultimedia_Item_Directory $directory) {
		// sestaveni parametru pro pridani
		$params = array(
				"directory" => array(
						"id" => $directory->id,
						"method" => "post"
				)
		);
		
		// odeslani dat
		$this->_directories[] = $directory;
	}
	
	/**
	 * vytvori novou revizi dokumentu
	 * 
	 * @param string $content novy obsah dokumentu
	 * @param string $name nove jmeno dokumentu, pokud je potreba
	 * @return LibMultimedia_Item_Document
	 */
	public function revision($content = null, $name = NULL) {
		// sestaveni dotazu
		$params = array("document" => array());
		
		if (!is_null($content)) $params["document"]["content"] = $content;
		
		if (!is_null($name)) $params["document"]["document_name"] = $name;
		
		// kontrola, jeslti je neco k provedeni
		if (empty($params["document"])) return $this;
		
		// odeslani dotazu
		$this->_connection->sendRequest(self::OBJECT, "put", $this->_data[$this->_identifier], $params, Zend_Http_Client::POST);
	}
	
	/**
	 * odstrani dokument z databaze
	 */
	public function delete() {
		throw new LibMultimedia_Item_Exception("This version of library do not support document deletion");
	}
	
	/**
	 * stahne a vraci obsah dokumentu
	 *
	 * @return string
	 */
	public function getContent() {
		// kontrola nacteni obsahu
		if (is_null($this->_content)) {
			// nacteni obsahu ze serveru
			$this->_content = $this->_connection->sendRequest(self::OBJECT, "download", $this->_data[$this->_identifier], array(), Zend_Http_Client::GET);
		}
		
		return $this->_content;
	}
	
	/**
	 * vraci seznam adresaru, ve kterych je dokument pritomen
	 *
	 * @return array<LibMultimedia_Item_Directory>
	 */
	public function getDirectories() {
		return $this->_directories;
	}
	
	/**
	 * vraci celou historii dokumentu
	 * 
	 * @return array<LibMultimedia_Item_Document>
	 */
	public function getHistory() {
		return $this->_history;
	}
	
	/**
	 * vraci dalsi revizi dokumentu
	 * 
	 * @return LibMultimedia_Item_Document
	 */
	public function getNext() {
		$index = $this->_posIndex + 1;
		
		return isset($this->_history[$index]) ? $this->_history[$index] : null;
	}
	
	/**
	 * vraci predchozi revizi dokumentu
	 * 
	 * @return LibMultimedia_Item_Document
	 */
	public function getPrev() {
		$index = $this->_posIndex - 1;
		
		return isset($this->_history[$index]) ? $this->_history[$index] : null;
	}
	
	/**
	 * odebere dokument z adresare
	 * zmena se provede okamzite a neni potreba dokument ukladat
	 * 
	 * @param LibMultimedia_Item_Directory $directory
	 * @return self
	 */
	public function removeFromDirectory(LibMultimedia_Item_Directory $directory) {
		// sestavni dotazu
		$params = array(
				"directory" => array(
						"id" => $directory->id,
						"method" => "delete"
				)
		);
		
		// odeslani dotazu
		$this->_connection->sendRequest(self::OBJECT, "delete", $this->_data[$this->_identifier], $params, Zend_Http_Client::POST);
		
		// odstraneni udaju o adresari ze seznamu
		$buffer = array();
		
		foreach ($this->_directories as $dir) {
			if ($dir->id != $directory->id) $buffer[] = $dir;
		}
		
		// prepis noveho seznamu adresaru
		$this->_directories = $dirs;
	}
	
	public function reload() {
		
	}
	
	protected function _clone(LibMultimedia_Item_Document $o) {
		// naklonovani predka
		parent::_clone($o);
		
		// prepis udaju
		$this->_content = $o->_content;
		$this->_history = $o->_history;
		$this->_directories = $o->_directories;
		$this->_posIndex = $o->_posIndex;
		$this->_last = $o->_last;
	}
}