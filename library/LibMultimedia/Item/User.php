<?php
class LibMultimedia_Item_User extends LibMultimedia_Item_Abstract {
	
	/**
	 * vytvor noveho uzivatele
	 * 
	 * @param unknown_type $login
	 * @param unknown_type $password
	 * @return LibMultimedia_Item_User
	 */
	public static function create($login, $password);
	
	/**
	 * nacte uzivatele ze serveru
	 * 
	 * @param string $login login hledaneho uzivatele
	 * @return LibMultimedia_Item_User
	 */
	public static function load($login) {
		
	}
	
	/**
	 * vraci domovsky adresar uzivatele
	 * 
	 * @return LibMultimedia_Item_Directory
	 */
	public function getHomeDirectory() {
		
	}
	
	public function save() {
		
	}
}