<?

/*
 * This file is part of the akAdmin package.
 * (c) 2010 Azat Khuzhin <dohardgopro@gmail.com>
 *
 * For the full copyright and license information, please view http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * WYSIWYG HTML FieldType using jwysiwyg
 * 
 * @link https://github.com/akzhan/jwysiwyg
 * 
 * @author Azat Khuzhin <dohardgopro@gmail.com>
 * @package akAdmin
 * @licence GPLv2
 */

class FieldType_WYSIWYG_HTML extends FieldType_HTML {
	static function getInstance() {
		static $object;
		if (!$object) $object = new FieldType_WYSIWYG_HTML;
		return $object;
	}

	/**
	 * @see parent::getForEdit()
	 */
	public function getForEdit($field = null, $value = null, $fieldTransltaion = null) {
		return sprintf(
			'<label><strong>%s</strong><textarea name="%s" class="wysiwyg">%s</textarea></label>',
			$fieldTransltaion, $field, $value
		);
	}
}
