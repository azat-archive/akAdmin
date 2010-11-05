<?

/*
 * This file is part of the akAdmin package.
 * (c) 2010 Azat Khuzhin <dohardgopro@gmail.com>
 *
 * For the full copyright and license information, please view http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * Bool FieldType
 * 
 * @author Azat Khuzhin <dohardgopro@gmail.com>
 * @package akAdmin
 * @licence GPLv2
 */

class FieldType_Bool extends FieldTypes {
	/**
	 * @see parent::$DBTypes
	 */
	public $DBTypes = array('text', 'varchar', 'char', 'int', 'float', 'bigint', 'bool', 'boolean', 'tinyint');

	static function getInstance() {
		static $object;
		if (!$object) $object = new FieldType_Bool;
		return $object;
	}

	/**
	 * @see parent::get()
	 */
	public function get($value = null) {
		return ($value ? 'yep' : 'nope');
	}

	/**
	 * @see parent::getForEdit()
	 */
	public function getForEdit($field = null, $value = null, $fieldTransltaion = null) {
		return sprintf(
			'<label><strong>%s</strong><input type="radio" name="%s" value="1" %s/>Yep<input type="radio" name="%s" value="0" %s/>Nope</label>',
			$fieldTransltaion, $field, ($value ? ' checked' : null), $field, (!$value ? ' checked' : null)
		);
	}

	/**
	 * @see parent::getForSearch()
	 */
	public function getForSearch($field = null, $value = null) {
		if (!$field || !in_array($value, array('yep', 'nope'))) return null;
		
		global $m;
		return sprintf('`%s` = %s', $m->escape($field), ($value == 'yep' ? 'TRUE' : 'FALSE'));
	}

	/**
	 * @see parent::set()
	 */
	public function set($value = null) {
		return (bool)$value;
	}
}
