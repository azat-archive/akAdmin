<?

/*
 * This file is part of the akAdmin package.
 * (c) 2010 Azat Khuzhin <dohardgopro@gmail.com>
 *
 * For the full copyright and license information, please view http://www.gnu.org/licenses/gpl-2.0.html
 */

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
		if (!$field || !$value || !is_numeric($value)) return null;
		
		global $m;
		return sprintf('`%s` LIKE "%%%s%%"', $m->escape($field), $m->escape((float)$value));
	}

	/**
	 * @see parent::set()
	 */
	public function set($value = null) {
		return (float)$value;
	}
}
