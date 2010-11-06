<?

/*
 * This file is part of the akAdmin package.
 * (c) 2010 Azat Khuzhin <dohardgopro@gmail.com>
 *
 * For the full copyright and license information, please view http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * File FieldType
 * 
 * @author Azat Khuzhin <dohardgopro@gmail.com>
 * @package akAdmin
 * @licence GPLv2
 */

class FieldType_File extends FieldTypes {
	/**
	 * Path under DOCUMENT_ROOT
	 * 
	 * @var string
	 */
	static $path = '/tmp/';
	/**
	 * @see parent::$DBTypes
	 */
	public $DBTypes = array('text', 'varchar', 'char');

	static function getInstance() {
		static $object;
		if (!$object) $object = new FieldType_File;
		return $object;
	}

	/**
	 * @see parent::get()
	 */
	public function get($value = null) {
		if (!$value) return null;
		return sprintf('<a href="%s%s">%s</a>', self::$path, $value, $value);
	}

	/**
	 * @see parent::getForEdit()
	 */
	public function getForEdit($field = null, $value = null, $fieldTransltaion = null) {
		return sprintf(
			'<label><strong>%s</strong><input type="file" name="%s" /></label>' .
			'<input type="checkbox" name="%s[delete]" title="Will be deleted automaticly if new is uploaded" />Delete %s?' .
			'<input type="hidden" name="%s[old]" value="%s" />',
			$fieldTransltaion, $field,
			$field, ($value ? sprintf('<a href="%s%s">old</a>', self::$path, $value) : 'old'),
			$field, $value
		);
	}

	/**
	 * @see parent::getForSearch()
	 */
	public function getForSearch($field = null, $value = null) {
		if (!$field || !$value) return null;
		
		global $m;
		return sprintf('`%s` LIKE "%%%s%%"', $m->escape($field), $m->escape($value));
	}

	/**
	 * @see parent::set()
	 * 
	 * @throws FieldType_FileException if error occured while deleting file or uploading new file
	 */
	public function set($value = null) {
		// old file (to delete)
		$oldFile = ($value['old'] ? sprintf('%s/%s', realpath(dirRoot . self::$path), $value['old']) : null);
		// new file (from $_FILES array)
		$newFile = (isset($value['file']) ? $value['file'] : null);
		// is need to delete old file?
		$delete = isset($value['delete']);
		// new files is the old file, while new file is not uploaded
		$newFileName = $value['old'];
		
		// delete old file, if we select checkbox "delete" or new file is uploaded
		if ($delete || ($newFile && $newFile['error'] != 4)) {
			$this->erase($value['old']);
			$newFileName = null;
		}
		// copy new file
		if ($newFile && $newFile['error'] != 4) {
			$newFileInfo = pathinfo($newFile['name']);
			$newFileName = genFileName(realpath(dirRoot . self::$path), $newFileInfo['extension']);
			
			if (!copy($newFile['tmp_name'], sprintf('%s/%s', realpath(dirRoot . self::$path), $newFileName))) {
				throw new FieldType_FileException(sprintf('Error occured while copy new file. Path: "%s".', self::$path));
			}
		}
		
		return $newFileName;
	}

	/**
	 * @see parent::erase()
	 * 
	 * @throws FieldType_FileException if error occured while deleting file
	 */
	public function erase($value = null) {
		$valueFileName = sprintf('%s/%s', realpath(dirRoot . self::$path), $value);
		if ($value && file_exists($valueFileName)) {
			if (!unlink($valueFileName)) {
				throw new FieldType_FileException(sprintf('Error occured while deleting old file. Path: "%s".', self::$path));
			}
		}
	}
}

/**
 * File FieldType Exceptionssss
 * 
 * @author Azat Khuzhin <dohardgopro@gmail.com>
 * @package akAdmin
 * @licence GPLv2
 */
class FieldType_FileException extends FieldTypesException {}
