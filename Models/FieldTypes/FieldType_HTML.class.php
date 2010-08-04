<?

/*
 * This file is part of the akAdmin package.
 * (c) 2010 Azat Khuzhin <dohardgopro@gmail.com>
 *
 * For the full copyright and license information, please view http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * HTML FieldType
 * 
 * @TODO Add WYSIWYG editor (i.e. TinyMCE)
 * 
 * @author Azat Khuzhin <dohardgopro@gmail.com>
 * @package akAdmin
 * @licence GPLv2
 */

class FieldType_HTML extends FieldTypes {
	/**
	 * @see parent::$DBTypes
	 */
	public $DBTypes = array('text', 'varchar', 'char');

	static function getInstance() {
		static $object;
		if (!$object) $object = new FieldType_HTML;
		return $object;
	}

	/**
	 * @see parent::get()
	 */
	public function get($value = null) {
		return htmlspecialchars($value);
	}

	/**
	 * @see parent::getForEdit()
	 */
	public function getForEdit($field = null, $value = null, $fieldTransltaion = null) {
		return sprintf(
			'<label><strong>%s</strong><textarea name="%s">%s</textarea></label>',
			$fieldTransltaion, $field, htmlspecialchars($value)
		);
	}

	/**
	 * @see parent::getForSearch()
	 */
	public function getForSearch($field = null, $value = null) {
		if (!$field || !$value) return null;
		
		global $m;
		return sprintf('`%s` LIKE "%%%s%%"', $m->escape($field), $m->escape(htmlspecialchars_decode($value)));
	}

	/**
	 * @see parent::set()
	 */
	public function set($value = null) {
		return htmlspecialchars_decode($value);
	}
}
