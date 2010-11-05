<?

/*
 * This file is part of the akAdmin package.
 * (c) 2010 Azat Khuzhin <dohardgopro@gmail.com>
 *
 * For the full copyright and license information, please view http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * FancyBox Image FieldType
 * 
 * @link http://fancybox.net/
 * 
 * @author Azat Khuzhin <dohardgopro@gmail.com>
 * @package akAdmin
 * @licence GPLv2
 */

class FieldType_FancyBox_Image extends FieldType_Image {
	static function getInstance() {
		static $object;
		if (!$object) $object = new FieldType_FancyBox_Image;
		return $object;
	}

	/**
	 * @see parent::get()
	 */
	public function get($value = null) {
		if (!$value || !isset(self::$previewSizes[0])) return null;
		
		preg_match('@(\d+?)x(\d+?)@Uis', self::$previewSizes[0], $size);
		return sprintf(
			'<a href="%s%s" class="fancyboxImage"><img src="%s%u_%u_%u_%s" alt="" title="Zoom in image" /></a>',
			self::$path, $value, self::$previewPath, $size[1], $size[2], self::$previewQuality, $value
		);
	}
}
