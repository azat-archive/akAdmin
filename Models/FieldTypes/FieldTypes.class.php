<?

/*
 * This file is part of the akAdmin package.
 * (c) 2010 Azat Khuzhin <dohardgopro@gmail.com>
 *
 * For the full copyright and license information, please view http://www.gnu.org/licenses/gpl-2.0.html
 */

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
	 * This will be called for every DB item when get it
	 * 
	 * @param string $value - value
	 * @return mixed
	 */
	abstract public function get($value = null);

	/**
	 * Get value for edit
	 * This will be called for every DB item when select it from DB
	 * And it called when edit item (in FORM)
	 * 
	 * @param string $field - field
	 * @param string $value - value
	 * @param string $fieldTransltaion - translation for field
	 * @return mixed
	 */
	abstract public function getForEdit($field = null, $value = null, $fieldTransltaion = null);

	/**
	 * Get value for search
	 * This will be called for every DB item when select it from DB
	 * And it callled when search in Table
	 * 
	 * @attention This method needs manual mysql escape (@see db::escape(), $GLOBALS['m']->escape())
	 * 
	 * @param string $field - field
	 * @param string $value - value
	 * @return mixed
	 */
	abstract public function getForSearch($field = null, $value = null);

	/**
	 * Set value
	 * This will be called for every DB item when insert / update
	 * 
	 * @param string $value - value
	 * @return mixed
	 */
	abstract public function set($value = null);

	/**
	 * Erase / delete value
	 * This will be called for every DB item when delete / erase
	 * 
	 * @example for delete file
	 * 
	 * @param string $value - value
	 * @return void
	 */
	public function erase($value = null) {
		throw new FieldTypesException('Must been overridden in children');
	}

	/**
	 * Duplicate / copy value
	 * This will be called for every DB item when duplicate / copy
	 * 
	 * @example for duplicate file or create a link of file
	 * 
	 * @param string $value - value
	 * @return mixed
	 */
	public function duplicate($value = null) {
		throw new FieldTypesException('Must been overridden in children');
	}
}

/**
 * PHP defined FieldType Exception model
 * 
 * @author Azat Khuzhin <dohardgopro@gmail.com>
 * @package akAdmin
 * @licence GPLv2
 */
class FieldTypesException extends BaseException {}
