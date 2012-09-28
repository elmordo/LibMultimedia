<?php
class LibMultimedia_Item_Directory extends LibMultimedia_Item_Abstract {
	
	const OBJECT = "directory";
	
	/**
	 * prime podadresare
	 * 
	 * @var array<LibMultimedia_Directory>
	 */
	protected $_childrenDirs = array();
	
	/**
	 * dokumenty v adresari
	 * 
	 * @var array<LibMultimedia_Document>
	 */
	protected $_childrenDocs = array();
	
	/**
	 * rodicovsky adresar
	 * 
	 * @var LibMultimedia_Item_Directory
	 */
	protected $_parent = null;
	
	/**
	 * cesta k adresari od rootu
	 * 
	 * @var array<LibMultimedia_Item_Directory>
	 */
	protected $_path = array();
	
	public static function create(array $config, $connection = null) {
		
	}
	
	public static function load($id, LibMultimedia_Connection $connection = null) {
		// priprava spojeni
		if (!$connection) $connection = LibMultimedia_Connection::getDefaultConnection();
		
		// nacteni dat
		$data = $connection->sendRequest(self::OBJECT, "get", $id, array(), Zend_Http_Client::GET);
		$data = Zend_Json::decode($data);
		
		// sestaveni informaci o adresari
		$directory = $data["this"];
		$directory["dirs"] = $data["subDirs"];
		$directory["documents"] = $data["files"];
		$directory["path"] = $data["path"];
		$directory["parent"] = $data["parent"];
		
		// zpracovani dat
		$params = array(
				"data" => $directory,
				"connection" => $connection
		);
		
		// vytvoreni objektu
		return new self($params, true);
	}
	
	public function __construct($params, $complete = false) {
		// rozsireni parametru
		$params = array_merge(array("data" => array(), "connection" => LibMultimedia_Connection::getDefaultConnection()), $params);
		
		// nacteni podruznych dat a jejich odstraneni
		$params["data"] = array_mege(array("dirs" => array(), "documents" => array(), "path" => array(), "parent" => null), $params["data"]);
		
		$dirs = $params["data"]["dirs"];
		$documents = $params["data"]["documents"];
		$path = $params["data"]["path"];
		$parent = $params["data"]["parent"];
		
		unset($params["data"]["dirs"], $params["data"]["documents"], $params["data"]["path"], $params["data"]["parent"]);
		
		// konstruktor predka
		parent::__construct($params);
		$this->_isComplete = $complete;
		
		// vytvoreni doplnkovych objektu
		if ($parent) {
			$this->_parent = new self($this->_preprarePartialParams($parent, $params["connection"]));
		}
		
		// podrizene adresare
		foreach ($dirs as $item) {
			$this->_childrenDirs[] = new self($this->_preprarePartialParams($item, $params["connection"]));
		}
		
		// cesta
		foreach ($path as $item) {
			$this->_path[] = new self($this->_preprarePartialParams($item, $params["connection"]));
		}
		
		// podrizene dokumenty
		foreach ($documents as $item) {
			$docParams = array(
					"data" => $item,
					"connection" => $params["connection"]
			);
			
			$this->_childrenDocs[] = new LibMultimedia_Item_Document($docParams);
		}
	}
	
	/**
	 * smaze adresar ze serveru
	 */
	public function delete() {
		$this->_connection->sendRequest(self::OBJECT, "delete", $this->_data[$this->_idnetifier], array(), Zend_Http_Client::GET);
	}
	
	/**
	 * vraci seznam podrizenych adresaru
	 * 
	 * @return array<LibMultimedia_Directories>
	 */
	public function getDirectories() {
		return $this->_childrenDirs;
	}
	
	/**
	 * vraci seznam podrizenych dokumentu
	 * 
	 * @return array<LibMultimedia_Document>
	 */
	public function getDocumnets() {
		return $this->_childrenDocs;
	}
	
	/**
	 * vraci podrizene adresare a soubory
	 * 
	 * return array<LibMultimedia_Item_Directory|LibMultimedia_Item_Document>
	 */
	public function children() {
		return array_merge($this->_childrenDirs, $this->_childrenDocs);
	}
	
	/**
	 * vraci rodice adresare
	 * 
	 * @return LibMultimedia_Item_Directory
	 */
	public function getParent() {
		return $this->_parent;
	}
	
	/**
	 * vraci cestu od korene k adresari
	 * 
	 * @return array<LibMultimedia_Item_Directory>
	 */
	public function path() {
		return $this->_path;
	}
	
	protected function _preprarePartialParams($dirInfo, $connection) {
		return array(
				"data" => $dirInfo,
				"connection" => $connection
		);
	}
}