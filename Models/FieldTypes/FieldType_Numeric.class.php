<?

/**
 * Numeric FieldType
 * 
 * @author Azat Khuzhin <dohardgopro@gmail.com>
 * @package akAdmin
 * @licence GPLv2
 */

class FieldType_Numeric extends FieldTypes {
	/**
	 * @see parent::$DBTypes
	 */
	public $DBTypes = array('text', 'varchar', 'char', 'int', 'float', 'bigint', 'tinyint');

	static function getInstance() {
		static $object;
		if (!$object) $object = new FieldType_Numeric;
		return $object;
	}

	/**
	 * @see parent::get()
	 */
	public function get($value = null) {
		return (float)$value;
	}

	/**
	 * @see parent::getForEdit()
	 */
	public function getForEdit($field = null, $value = null, $fieldTransltaion = null) {
		return sprintf(
			'<label><strong>%s</strong><input type="text" name="%s" value="%s" /></label>',
			$fieldTransltaion, $field, $this->get($value)
		);
	}

	/**
	 * @see parent::getForSearch()
	 */
	public function getForSearch($field = null, $value = null) {
		throw new akException('WRITE THIS PART');
	}

	/**
	 * @see parent::set()
	 */
	public function set($value = null) {
		return (float)$value;
	}
}
