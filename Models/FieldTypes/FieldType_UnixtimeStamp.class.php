<?

/**
 * UnixtimeStamp FieldType
 * 
 * @author Azat Khuzhin <dohardgopro@gmail.com>
 * @package akAdmin
 * @licence GPLv2
 */

class FieldType_UnixtimeStamp extends FieldTypes {
	/**
	 * Format of date
	 * 
	 * @var string
	 */
	static $dateFormat = 'Y-m-d H:i:s';
	/**
	 * @see parent::$DBTypes
	 */
	public $DBTypes = array('text', 'varchar', 'char', 'int', 'bigint');

	static function getInstance() {
		static $object;
		if (!$object) $object = new FieldType_UnixtimeStamp;
		return $object;
	}

	/**
	 * @see parent::get()
	 */
	public function get($value = null) {
		return date(self::$dateFormat, $value);
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
		return strtotime($value);
	}
}
