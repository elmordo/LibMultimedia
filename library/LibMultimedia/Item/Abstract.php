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
	protected $_idnetifier = null;
	
	public function init();
	
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
	
	/**
	 * ulozi data na server
	 */
	public abstract function save();
	
	public abstract static function load($id, $connection = null);
	
	public abstract static function create(array $data, $connection = null);
	
	protected abstract function reload();
}
