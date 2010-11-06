<?

/*
 * This file is part of the akAdmin package.
 * (c) 2010 Azat Khuzhin <dohardgopro@gmail.com>
 *
 * For the full copyright and license information, please view http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * File Duplicate FieldType
 * Duplicate file on diplicate record
 * 
 * @author Azat Khuzhin <dohardgopro@gmail.com>
 * @package akAdmin
 * @licence GPLv2
 */

class FieldType_FileDuplicate extends FieldType_File {
	static function getInstance() {
		static $object;
		if (!$object) $object = new FieldType_FileDuplicate;
		return $object;
	}

	/**
	 * @see parent::duplicate()
	 * 
	 * @throws FieldType_FileException if error occured while copy file
	 */
	public function duplicate($value = null) {
		$oldFile = $value;
		$oldFilePath = sprintf('%s/%s', realpath(dirRoot . self::$path), $value);
		if ($oldFile && file_exists($oldFilePath)) {
			$oldFileInfo = pathinfo($oldFile);
			$newFileName = genFileName(realpath(dirRoot . self::$path), $oldFileInfo['extension']);
			$newFilePath = sprintf('%s/%s', realpath(dirRoot . self::$path), $newFileName);
			
			if (!copy($oldFilePath, $newFilePath)) {
				throw new FieldType_FileDuplicateException(sprintf('Error occured while createing a duplicate of file "%s". Path: "%s".', $oldFilePath, self::$path));
			}
			
			return $newFileName;
		}
	}
}

/**
 * File Duplicate FieldType Exceptionssss
 * 
 * @author Azat Khuzhin <dohardgopro@gmail.com>
 * @package akAdmin
 * @licence GPLv2
 */
class FieldType_FileDuplicateException extends FieldTypesException {}
