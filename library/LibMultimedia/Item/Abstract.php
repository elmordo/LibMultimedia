<?php
class LibMultimedia_Item_Abstract {
	
	/**
	 * aktualni data instance
	 * 
	 * @var array
	 */
	protected $_data = array();
	
	/**
	 * puvodni nacteda data
	 * 
	 * @var array
	 */
	protected $_cleanData = array();
	
	/**
	 * seznam zmenenych hodnot
	 * 
	 * @var array
	 */
	protected $_changed = array();
	
	/**
	 * seznam sloupcu, ktere jsou povoleny
	 * 
	 * @var array<string>
	 */
	protected $_allowedColumns = array();
	
	/**
	 * pripojeni k cilovemu serveru
	 * 
	 * @var LibMultimedia_Connection
	 */
	protected $_connection;
	
	/**
	 * prepinac kompletniho nacteni
	 */
	protected $_isComplete = true;
	
	/**
	 * jmeno sloupce s identifikatorem
	 */
	protected $_identifier = null;
	
	/**
	 * inicializacni funkce k prepsani potomkem
	 */
	public function init() {
		
	}
	
	public function __construct(array $params) {
		// nastaveni defaultnich hodnot parametru
		$params = array_merge(array(
			"connection" => LibMultimedia_Connection::getDefaultConnection(),
			"data" => array(),
			"allowedColumns" => null
		), $params);
		
		// nastaveni povolenych sloupcu, pokud zadne nastavene nejsou
		if (is_null($params["allowedColumns"])) $params["allowedColumns"] = array_keys($params["data"]);
		
		// nastaveni dat
		$this->_cleanData = $this->_data = $params["data"];
		$this->_allowedColumns = $params["allowedColumns"];
		$this->_connection = $params["connection"];
	}
	
	/**
	 * vraci vyzadanou hodnotu
	 * 
	 * @param string $name jmeno hodnoty
	 * @return mixed
	 */
	public function __get($name) {
		// kontrola, jesti hodnota existuje
		if (!in_array($name, $this->_allowedColumns)) throw new LibMultimedia_Item_Exception("Value '" . $name . "' not exists.");
		
		return $name;
	}
	
	/**
	 * nastavi zadanou hodnotu
	 * 
	 * @param string $name jmeno hodnoty
	 * @param mixed $value nova hodnota
	 */
	public function __set($name, $value) {
		// kontrola, jesti hodnota existuje
		if (!in_array($name, $this->_allowedColumns)) throw new LibMultimedia_Item_Exception("Value '" . $name . "' not exists.");
				
		// nastaveni nove hodnoty a oznaceni zmeny
		$this->_data[$name] = $value;
		$this->_changed[$name] = true;
	}
	
	/**
	 * zrusi zmeny dat
	 */
	public function reset() {
		$this->_changed = array();
		$this->_data = $this->_cleanData;
		
		return $this;
	}
	
	/**
	 * zkontroluje, zda je objekt kompletne nacteny
	 */
	public function loadIfNotComplete() {
		if (!$this->_isComplete) {
			// objekt neni plnohotnotny, dojde k jeho znovunacteni
			$this->_reload();
		}
	}
	
	public function toArray() {
		return $this->_data;
	}
	
	/**
	 * ulozi data na server
	 */
	public abstract function save();
	
	/**
	 * nacte data ze serveru
	 * 
	 * @param string $id identifikator objektu
	 * @param LibMultimedia_Connection $connection pripojeni k serveru
	 * @return LibMultimedia_Item_Abstract
	 */
	public abstract static function load($id, LibMultimedia_Connection $connection = null);
	
	/**
	 * vytvori novy objekt
	 * 
	 * @param array $data vychozi data
	 * @param LibMultimedia_Connection $connection pripojeni k serveru
	 * @return LibMultimedia_Item_Abstract 
	 */
	public abstract static function create(array $data, $connection = null);
	
	/**
	 * znovunacte data ze serveru
	 * @return void
	 */
	public abstract function reload();
	
	/**
	 * okopiruje objekt
	 * 
	 * @param LibMultimedia_Item_Abstract $o puvodni objekt
	 */
	protected function _clone(LibMultimedia_Item_Abstract $o) {
		$this->_data = $o->_data;
		$this->_cleanData = $o->_cleanData;
		$this->_changed = $o->_changed;
		$this->_allowedColumns = $o->_allowedColumns;
	}
}
