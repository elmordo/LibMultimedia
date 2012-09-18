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
	
	public function init();
	
	public function __construct(array $params) {
		
	}
	
	/**
	 * vraci vyzadanou hodnotu
	 * 
	 * @param string $name jmeno hodnoty
	 * @return mixed
	 */
	public function __get($name) {
		// kontrola, jesti hodnota existuje
		if (!isset($this->_data[$name])) throw new LibMultimedia_Exception("Value '" . $name . "' not exists.");
		
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
		if (!isset($this->_data[$name])) throw new LibMultimedia_Exception("Value '" . $name . "' not exists.");
		
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
}