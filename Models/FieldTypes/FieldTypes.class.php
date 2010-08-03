<?

/**
 * PHP defined FieldType model
 * 
 * @author Azat Khuzhin <dohardgopro@gmail.com>
 * @package akAdmin
 * @licence GPLv2
 */

abstract class FieldTypes {
	/**
	 * List of enabled DB types
	 * In lower case!
	 * 
	 * @var array
	 */
	public $DBTypes = array();


	/**
	 * Fast init
	 * 
	 * @return object
	 */
	abstract static function getInstance();

	/**
	 * Get value
	 * This will be called for every DB item when select it from DB
	 * 
	 * @param string $value - value
	 * @return string
	 */
	abstract public function get($value = null);

	/**
	 * Get value
	 * This will be called for every DB item when select it from DB
	 * And it called when edit item (in FORM)
	 * 
	 * @param string $field - field
	 * @param string $value - value
	 * @param string $fieldTransltaion - translation for field
	 * @return string
	 */
	abstract public function getForEdit($field = null, $value = null, $fieldTransltaion = null);

	/**
	 * Get value
	 * This will be called for every DB item when select it from DB
	 * And it callled when search in Table
	 * 
	 * @param string $field - field
	 * @param string $value - value
	 * @return string
	 */
	abstract public function getForSearch($field = null, $value = null);

	/**
	 * Set value
	 * This will be called for every DB item when insert / update
	 * 
	 * @param string $value - value
	 * @return string
	 */
	abstract public function set($value = null);
}
