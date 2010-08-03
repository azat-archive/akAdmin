<?

/**
 * Text FieldType
 * 
 * @author Azat Khuzhin <dohardgopro@gmail.com>
 * @package akAdmin
 * @licence GPLv2
 */

class FieldType_Text extends FieldTypes {
	/**
	 * @see parent::$DBTypes
	 */
	public $DBTypes = array('text', 'varchar', 'char');

	static function getInstance() {
		static $object;
		if (!$object) $object = new FieldType_Text;
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
			'<label><strong>%s</strong><textarea name="%s">%s</textarea></label>',
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
		return strip_tags($value);
	}
}
