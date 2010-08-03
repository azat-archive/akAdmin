<?

/**
 * String FieldType
 * 
 * @author Azat Khuzhin <dohardgopro@gmail.com>
 * @package akAdmin
 * @licence GPLv2
 */

class FieldType_String extends FieldTypes {
	/**
	 * @see parent::$DBTypes
	 */
	public $DBTypes = array('text', 'varchar', 'char');

	static function getInstance() {
		static $object;
		if (!$object) $object = new FieldType_String;
		return $object;
	}

	/**
	 * @see parent::get()
	 */
	public function get($value = null) {
		return strip_tags($value);
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
		if (!$field || !$value) return null;
		
		global $m;
		return sprintf('`%s` LIKE "%%%s%%"', $m->escape($field), $m->escape(strip_tags($value)));
	}

	/**
	 * @see parent::set()
	 */
	public function set($value = null) {
		return strip_tags($value);
	}
}
