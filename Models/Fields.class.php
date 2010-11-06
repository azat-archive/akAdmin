<?

/*
 * This file is part of the akAdmin package.
 * (c) 2010 Azat Khuzhin <dohardgopro@gmail.com>
 *
 * For the full copyright and license information, please view http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * Fields model
 * 
 * This model contain tranlation for fields
 * And PHP defined field types
 * 
 * @author Azat Khuzhin <dohardgopro@gmail.com>
 * @package akAdmin
 * @licence GPLv2
 */

class Fields {
	/**
	 * Table with translation of fields names
	 * +hidden som fields
	 * +type of fields
	 * 
	 * @var string
	 */
	static $table = '%s_akadmin_sections_fields';
	/**
	 * Avaliable types
	 * For every type must exist class FieldType_{VALUE} in folder ./FieldTypes
	 * 
	 * Please add aditional types to the and of array!
	 * 
	 * @var array
	 */
	static $avaliableTypes = array(
		'HTML',
		'Text',
		'Numeric',
		'File',
		'Image',
		'UnixtimeStamp',
		'Bool',
		'String',
		'WYSIWYG',
		'FancyBoxImage',
		'FileDuplicate',
	);
	/**
	 * Array of types with types by DB type
	 * Every class of type must have array with list of enabled DB types
	 * Array is a public property of function and it name is "DBTypes"
	 *
	 * @see FieldTypes.class.php 
	 * @var array
	 */
	static $avaliableTypesByDBTypes = array();
	/**
	 * Array of objects with types by DB type
	 * Every class of type must have array with list of enabled DB types
	 * Array is a public property of function and it name is "DBTypes"
	 *
	 * @see FieldTypes.class.php 
	 * @var array
	 */
	static $typesByDBTypes = array();
	/**
	 * Array of objects with types
	 * 
	 * @var array
	 */
	static $types = array();


	/**
	 * Constructor
	 * 
	 * @return void
	 */
	public function __construct() {
		self::$table = sprintf(self::$table, project);
		
		// Array of objects with types
		if (empty(self::$types)) {
			$path = realpath(dirname(__FILE__) . '/FieldTypes/') . '/';
			require_once $path . 'FieldTypes.class.php';
			
			foreach (self::$avaliableTypes as $type) {
				$className = 'FieldType_' . $type;
				require_once $path . $className . '.class.php';
				// adapted for PHP < 5.3
				self::$types[$type] = call_user_func($className . '::getInstance');
			}
		}
		
		// Array of objects with types by DB type
		if (empty(self::$typesByDBTypes)) {
			foreach (self::$types as $typeName => &$type) {
				foreach ($type->DBTypes as $DBType) {
					self::$typesByDBTypes[$DBType][] = &$type;
					self::$avaliableTypesByDBTypes[$DBType][] = $typeName;
				}
			}
		}
	}

	/**
	 * Fast init
	 * 
	 * @return object of Fields
	 */
	static function getInstance() {
		static $object;
		if (!$object) $object = new Fields;
		
		return $object;
	}

	/**
	 * Get translation for field
	 * 
	 * @param int $sid - section id
	 * @param string $field - field name
	 * @param int $id - id of field to get (if it isset than, item will be selec by id, not by sid and field)
	 * @param bool $showAll - show all or not (default: not)
	 * @return mixed
	 */
	public function getTranslation($sid = null, $field = null, $id = null, $showAll = false) {
		if ((!$sid || !$field) && !$id) return false;
		
		global $m;
		if ($id) {
			$v = $m->sprintf(
				'SELECT * FROM %s WHERE id = %u%s LIMIT 1',
				self::$table, $id, (!$showAll ? ' AND hidden = TRUE' : null)
			);
		} else {
			$v = $m->sprintf(
				'SELECT * FROM %s WHERE sid = %u AND field = "%s"%s LIMIT 1',
				self::$table, $sid, $m->escape($field), (!$showAll ? ' AND hidden = FALSE' : null)
			);
		}
		
		return $m->fetch($v);
	}

	/**
	 * Get translation for field [Multi]
	 * 
	 * @param int $sid - section id
	 * @param bool $showAll - show all or not (default: not)
	 * @return mixed
	 */
	public function getTranslationForSection($sid = null, $showAll = false) {
		global $m;
		
		return $m->fetchAll($m->sprintf(
			'SELECT * FROM %s WHERE sid = %u%s ORDER BY sequence DESC',
			self::$table, $sid, (!$showAll ? ' AND hidden = FALSE' : null)
		));
	}

	/**
	 * Erase / Delete / Drop translation for field
	 * 
	 * @param int $sid - section id
	 * @param string $field - field name
	 * @param int $id - id of field to delete (if it isset than, item will be delete by id, not by sid and field)
	 * @return bool
	 */
	public function eraseTranslation($sid = null, $field = null, $id = null) {
		if ((!$sid || !$field) && !$id) return false;
		
		global $m;
		return (
			$id ?
			$m->sprintf('DELETE FROM %s WHERE id = %u', self::$table, $id) :
			$m->sprintf('DELETE FROM %s WHERE sid = %u AND field = "%s"', self::$table, $sid, $m->escape($field))
		);
	}

	/**
	 * Update / Insert translation for field
	 * Update will be refresh by ($sid & $field) or $id
	 * If item with $id is not existed, than will be created new item, and return it id
	 * 
	 * @param int $sid - section id
	 * @param string $field - field name
	 * @param int $id - id of field to update (if it isset than, item will be update by id, not by sid and field)
	 * @param array $data - data to update
	 * @return bool
	 */
	public function updateTranslation($sid = null, $field = null, $id = null, array $data) {
		if ((!$sid || !$field) && !$id) return false;
		
		global $m;
		if (($id && $this->getTranslation(null, null, $id, true)) || $this->getTranslation($sid, $field, null, true)) {
			return $m->sprintf(
				'UPDATE %s SET %s WHERE %s',
				self::$table, $m->join($data), ($id ? sprintf('id = %u', $id) : sprintf('sid = %u AND field = "%s"', $sid, $m->escape($field)))
			);
		} else {
			$data['sid'] = $sid;
			$data['field'] = $field;
			
			return $m->sprintf('INSERT INTO %s SET %s', self::$table, $m->join($data));
		}
	}
}
