<?php
class LibMultimedia_Item_Document extends LibMultimedia_Item_Abstract {
	
	/**
	 * vytvori novy dokument
	 * 
	 * @param LibMultimedia_Item_User $user majitel dokumentu
	 * @param unknown_type $name jmeno noveho dokumentu
	 * @param unknown_type $content obsah
	 * @param unknown_type $contentMimeType typ
	 * @return LibMultimedia_Item_Document
	 */
	public static function create(LibMultimedia_Item_User $user, $name, $content, $contentMimeType = "") {
		
	}
	
	/**
	 * nacte dokument ze serveru
	 * 
	 * @param string $uuid uuid dokumentu
	 * @return LibMultimedia_Item_Document
	 */
	public static function load($id) {
		
	}
	
	/**
	 * vytvori novou revizi dokumentu
	 * 
	 * @param string $content novy obsah dokumentu
	 * @param string $name nove jmeno dokumentu, pokud je potreba
	 * @return LibMultimedia_Item_Document
	 */
	public function revision($content, $name = NULL) {
		
	}
	
	/**
	 * odstrani dokument z databaze
	 */
	public function delete() {
		
	}
	
	/**
	 * stahne a vraci obsah dokumentu
	 *
	 * @return string
	 */
	public function getContent() {
	
	}
	
	/**
	 * vraci seznam adresaru, ve kterych je dokument pritomen
	 *
	 * @return array<LibMultimedia_Item_Directory>
	 */
	public function getDirectories() {
	
	}
	
	/**
	 * vraci celou historii dokumentu
	 * 
	 * @return array<LibMultimedia_Item_Document>
	 */
	public function getHistory() {
		
	}
	
	/**
	 * vraci dalsi revizi dokumentu
	 * 
	 * @return LibMultimedia_Item_Document
	 */
	public function getNext() {
		
	}
	
	/**
	 * vraci predchozi revizi dokumentu
	 * 
	 * @return LibMultimedia_Item_Document
	 */
	public function getPrev() {
		
	}
	
	/**
	 * vraci informace o dokumentu
	 */
	public function info() {
		
	}
}