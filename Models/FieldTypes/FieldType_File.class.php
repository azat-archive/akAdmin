<?

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
	public $DBTypes = array('text', 'varchar', 'char');

	static function getInstance() {
		static $object;
		if (!$object) $object = new FieldType_File;
		return $object;
	}

	public function get($value = null) {
		if (!$value) return null;
		return sprintf('<a href="%s%s">%s</a>', self::$path, $value, $value);
	}

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

	public function getForSearch($field = null, $value = null) {
		throw new akException('WRITE THIS PART');
	}

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
			if ($oldFile && file_exists($oldFile)) {
				if (!unlink($oldFile)) {
					throw new akException('Error occured while deleting old file.');
				}
			}
			$newFileName = null;
		}
		// copy new file
		if ($newFile && $newFile['error'] != 4) {
			$newFileName = randomStr(10);
			while (file_exists(sprintf('%s/%s', realpath(dirRoot . self::$path), $newFileName))) {
				$newFileName = randomStr(10);
			}
			
			if (!copy($newFile['tmp_name'], sprintf('%s/%s', realpath(dirRoot . self::$path), $newFileName))) {
				throw new akException('Error occured while copy new file.');
			}
		}
		
		return $newFileName;
	}
}
