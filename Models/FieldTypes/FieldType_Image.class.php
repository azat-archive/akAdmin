<?

/*
 * This file is part of the akAdmin package.
 * (c) 2010 Azat Khuzhin <dohardgopro@gmail.com>
 *
 * For the full copyright and license information, please view http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * Image FieldType
 * 
 * @author Azat Khuzhin <dohardgopro@gmail.com>
 * @package akAdmin
 * @licence GPLv2
 */

class FieldType_Image extends FieldTypes {
	/**
	 * Path under DOCUMENT_ROOT
	 * 
	 * @var string
	 */
	static $path = '/tmp/';
	/**
	 * Path to image previews under DOCUMENT_ROOT
	 * 
	 * @var string
	 */
	static $previewPath = '/tmp/previews/';
	/**
	 * Preview quality
	 * Do not edit this if your don`t understand for what this!
	 * 
	 * @var int
	 */
	static $previewQuality = 90;
	/**
	 * Preview sizes
	 * Do not delete first element of this array if your don`t understand for what this!
	 * 
	 * @var array
	 */
	static $previewSizes = array('100x100');
	/**
	 * @see parent::$DBTypes
	 */
	public $DBTypes = array('text', 'varchar', 'char');

	static function getInstance() {
		static $object;
		if (!$object) $object = new FieldType_Image;
		return $object;
	}

	/**
	 * @see parent::get()
	 */
	public function get($value = null) {
		if (!$value || !isset(self::$previewSizes[0])) return null;
		
		preg_match('@(\d+?)x(\d+?)@Uis', self::$previewSizes[0], $size);
		return sprintf(
			'<a href="%s%s"><img src="%s%u_%u_%u_%s" alt="" title="Zoom in image" /></a>',
			self::$path, $value, self::$previewPath, $size[1], $size[2], self::$previewQuality, $value
		);
	}

	/**
	 * @see parent::getForEdit()
	 */
	public function getForEdit($field = null, $value = null, $fieldTransltaion = null) {
		return sprintf(
			'<label><strong>%s</strong><input type="file" name="%s" /></label>' .
			'<input type="checkbox" name="%s[delete]" title="Will be deleted automaticly if new is uploaded" />Delete old?' .
			'%s' .
			'<input type="hidden" name="%s[old]" value="%s" />',
			$fieldTransltaion, $field,
			$field, $this->get($value),
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
	 * @throws akException
	 * 				if error occured while deleting image or uploading new image
	 * 				but not if error occured file creating or deleting preview for image
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
		// preview sizes
		preg_match_all('@(\d+?)x(\d+?)(?:,|$)@Uis', join(',', self::$previewSizes), $previewSizes, PREG_SET_ORDER);
		
		
		// delete old file, if we select checkbox "delete" or new file is uploaded
		if ($delete || ($newFile && $newFile['error'] != 4)) {
			if ($oldFile && file_exists($oldFile)) {
				if (!unlink($oldFile)) {
					throw new akException(sprintf('Error occured while deleting old file. Path: "%s".', self::$path));
				}
				// delete all previews
				foreach (glob(sprintf('%s/[0-9]*_[0-9]*_[0-9]*_%s', realpath(dirRoot . self::$previewPath), $newFileName), GLOB_NOSORT) as $file) {
					unlink($file);
				}
			}
			$newFileName = null;
		}
		// copy new file
		if ($newFile && $newFile['error'] != 4) {
			$ext = akExec::getFileExt($newFile['name']);
			$newFileName = sprintf('%s.%s', randomStr(10), $ext);
			while (file_exists(sprintf('%s/%s', realpath(dirRoot . self::$path), $newFileName))) {
				$newFileName = sprintf('%s.%s', randomStr(10), $ext);
			}
			
			if (!copy($newFile['tmp_name'], sprintf('%s/%s', realpath(dirRoot . self::$path), $newFileName))) {
				throw new akException(sprintf('Error occured while copy new file. Path: "%s".', self::$path));
			}
			
			// generate previews
			$image = akImage::getInstance(sprintf('%s/%s', realpath(dirRoot . self::$path), $newFileName));
			foreach ($previewSizes as $size) {
				$image->resize(
					$size[1], $size[2],
					sprintf('%s/%u_%u_%u_%s', realpath(dirRoot . self::$previewPath), $size[1], $size[2], self::$previewQuality, $newFileName),
					self::$previewQuality,
					akImage::resizeByXY,
					$zoom = false
				);
			}
		}
		
		return $newFileName;
	}
}
