<?php
class LibMultimedia_Item_Directory extends LibMultimedia_Item_Abstract {
	
	/**
	 * vytvori novy adresar
	 * 
	 * @param LibMultimedia_Item_User $user majitel adresare
	 * @param unknown_type $name jmeno aresare
	 * @param LibMultimedia_Item_Directory $parent rodicovsky adresar
	 * @return LibMultimedia_Item_Directory
	 */
	public static function create(LibMultimedia_Item_User $user, $name, LibMultimedia_Item_Directory $parent = null) {
		
	}
	
	/**
	 * nacte adresar dle identifikacniho cisla
	 * 
	 * @param int $id identifikacni cislo adresare
	 * @return LibMultimedia_Item_Directory
	 */
	public static function load($id) {
		
	}
	
	/**
	 * smaze adresar ze serveru
	 */
	public function delete() {
		
	}
	
	/**
	 * vraci podrizene adresare
	 * 
	 * return array<LibMultimedia_Item_Directory>
	 */
	public function children() {
		
	}
	
	/**
	 * vraci informace o adresari
	 * -name -> jmeno adresare
	 */
	public function info() {
		
	}
	
	/**
	 * vraci rodice adresare
	 * 
	 * @return LibMultimedia_Item_Directory
	 */
	public function getParent() {
		
	}
}