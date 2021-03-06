<?

/*
 * This file is part of the akAdmin package.
 * (c) 2010 Azat Khuzhin <dohardgopro@gmail.com>
 *
 * For the full copyright and license information, please view http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * Items model
 * 
 * @author Azat Khuzhin <dohardgopro@gmail.com>
 * @package akAdmin
 * @licence GPLv2
 */

class Items {
	/**
	 * Table to get items from
	 * 
	 * @var string
	 */
	public $table;
	/**
	 * Avaliable fields
	 * Your must add auto_increment field yourself!
	 * 
	 * @var array
	 */
	public $fields;
	/**
	 * Field types (@see class Fields)
	 * 
	 * @var array
	 */
	public $fieldTypes;
	/**
	 * Auto increment field in table
	 * 
	 * @var string
	 */
	public $fieldAutoIncrement;
	/**
	 * Last result (from function get())
	 * 
	 * @see this::get()
	 * @see this::details();
	 * @var array
	 */
	public $lastResult;


	/**
	 * Constructor
	 * 
	 * @see this::validate()
	 * @return void
	 */
	public function __construct($table = null, $fieldAutoIncrement = null, array $fields = null, array $fieldTypes = null) {
		if ($table && $fields) {
			$this->table = $table;
			$this->fields = $fields;
			try {
				$this->validate();
			} catch (ItemsException $e) {
				unset($this->table);
				unset($this->fields);
				// throwing next
				throw $e;
			}
		}
		if ($fieldTypes) $this->fieldTypes = $fieldTypes;
		if ($fieldAutoIncrement) $this->fieldAutoIncrement = $fieldAutoIncrement;
	}

	/**
	 * Fast init
	 * 
	 * @return object of Sections
	 */
	static function getInstance($table = null, $fieldAutoIncrement = null, array $fields = null, array $fieldTypes = null) {
		static $object;
		if (!$object) $object = new Items($table, $fieldAutoIncrement, $fields, $fieldTypes);
		
		return $object;
	}

	/**
	 * Get items from table
	 * 
	 * @see this::validate()
	 * @see this::$lastResult
	 * @param int $offset - offset
	 * @param int $limit - limit 
	 * @param string $sort - field to sort
	 * @param string $q - query to search to
	 * @param bool $forEdit - for edit or not (@see class Fields)
	 * @return mixed
	 */
	public function get($offset, $limit, $sort = null, $q = null, $forEdit = false) {
		$this->validate();
		
		global $m;
		
		$f = Fields::getInstance();
		
		if ($q) {
			$where = array();
			foreach ($this->fieldTypes as $field => &$type) {
				$where[] = Fields::$types[$type]->getForSearch($field, $q);
			}
			$where = join(' OR ', arrayEraseEmpty($where));
		} else {
			$where = null;
		}
		$resource = $m->sprintf(
			'SELECT `%s` FROM %s %s %s LIMIT %u, %u',
			join('`,`', array_map(array($m, 'escape'), array_keys($this->fields))), $this->table, ($where ? ' WHERE ' . $where : null), ($sort ? ' ORDER BY ' . $m->escape($sort) : null), $offset, $limit
		);
		
		$this->lastResult = $m->fetchAll($resource);
		// get for val every type of items
		if (is_array($this->lastResult)) {
			// PHP defined FieldTypes
			foreach ($this->lastResult as &$item) {
				foreach ($item as $key => &$field) {
					if ($this->fieldTypes && (isset($this->fieldTypes[$key]) && $this->fieldTypes[$key]) && isset(Fields::$types[$this->fieldTypes[$key]])) {
						if ($forEdit) {
							$field = Fields::$types[$this->fieldTypes[$key]]->getForEdit($key, $field, $this->fields[$key]);
						} else {
							$field = Fields::$types[$this->fieldTypes[$key]]->get($field);
						}
					}
					
					// mark search results, and not forEdit
					if ($q && !$forEdit) $field = preg_replace(sprintf('|(%s)|Uis', preg_quote($q)), '<strong>\1</strong>', $field);
				}
			}
		}
		
		return $this->lastResult;
	}
	
	public function getEmpty() {
		$emptyItem = array();
		$f = Fields::getInstance();
		
		// PHP defined FieldTypes
		foreach ($this->fields as $key => $fieldTranslation) {
			if ($this->fieldTypes && (isset($this->fieldTypes[$key]) && $this->fieldTypes[$key]) && isset(Fields::$types[$this->fieldTypes[$key]])) {
				$emptyItem[] = Fields::$types[$this->fieldTypes[$key]]->getForEdit($key, null, $this->fields[$key]);
			}
		}
		
		return $emptyItem;
	}

	/**
	 * Get num of results from table
	 * 
	 * @see this::validate()
	 * @see this::$lastResult
	 * @param string $q - query to search to
	 * @return mixed
	 */
	public function getNum($q = null) {
		$this->validate();
		
		global $m;
		
		if ($q) {
			$f = Fields::getInstance();
			$where = array();
			foreach ($this->fieldTypes as $field => &$type) {
				$where[] = Fields::$types[$type]->getForSearch($field, $q);
			}
			$where = join(' OR ', arrayEraseEmpty($where));
		} else {
			$where = null;
		}
		
		$num = $m->fetch($m->sprintf('SELECT COUNT(*) count FROM %s %s', $this->table, ($where ? ' WHERE ' . $where : null)));
		if ($num) $num = $num['count'];
		
		return $num;
	}

	/**
	 * Get item details
	 * 
	 * @see this::validate()
	 * @param int $id - id
	 * @param bool $forEdit - for edit or not (@see class Fields)
	 * @return mixed
	 */
	public function details($id, $forEdit = false) {
		$this->validate();
		
		global $m;
		
		$this->lastResult = $m->fetch($m->sprintf(
			'SELECT `%s` FROM %s WHERE %s = %u',
			join('`,`', array_map(array($m, 'escape'), array_keys($this->fields))), $this->table, $m->escape($this->fieldAutoIncrement), $id
		));
		
		if ($this->lastResult) {
			// PHP defined FieldTypes
			$f = Fields::getInstance();
			foreach ($this->lastResult as $key => &$field) {
				if ($this->fieldTypes && (isset($this->fieldTypes[$key]) && $this->fieldTypes[$key]) && isset(Fields::$types[$this->fieldTypes[$key]])) {
					if ($forEdit) {
						$field = Fields::$types[$this->fieldTypes[$key]]->getForEdit($key, $field, $this->fields[$key]);
					} else {
						$field = Fields::$types[$this->fieldTypes[$key]]->get($field);
					}
				}
			}
		}
		
		return $this->lastResult;
	}

	/**
	 * Erase / delete item
	 * 
	 * @see this::validate()
	 * @param int or array of int $ids - ids
	 * @return mixed
	 */
	public function erase($ids) {
		$this->validate();
		
		if (!$ids || !$this->fieldAutoIncrement) return false;
		
		if (!is_array($ids)) $ids = array((int)$ids);
		else $ids = array_map('intval', $ids);
		
		if (empty($ids)) return false;
		
		global $m;
		
		$result = $m->fetchAll($m->sprintf(
			'SELECT `%s` FROM %s WHERE %s IN (%s)',
			join('`,`', array_map(array($m, 'escape'), array_keys($this->fields))), $this->table, $m->escape($this->fieldAutoIncrement), join(',', $ids)
		));
		if (!$result) return false;
		
		// PHP defined FieldTypes
		$f = Fields::getInstance();
		foreach ($result as &$item) {
			foreach ($item as $key => &$field) {
				if ($this->fieldTypes && (isset($this->fieldTypes[$key]) && $this->fieldTypes[$key]) && isset(Fields::$types[$this->fieldTypes[$key]])) {
					// check if method is overriden
					try {
						Fields::$types[$this->fieldTypes[$key]]->erase($field);
					} catch (FieldTypesException $e) {}
				}
			}
		}
		
		return $m->sprintf(
			'DELETE FROM %s WHERE %s IN (%s)',
			$this->table, $m->escape($this->fieldAutoIncrement), join(',', $ids)
		);
	}

	/**
	 * Duplicate / copy item
	 * 
	 * @see this::validate()
	 * @param int or array of int $ids - ids
	 * @return mixed
	 * 
	 * @throws ItemsException
	 */
	public function duplicate($ids, $num) {
		$this->validate();
		
		if (!$ids || !$this->fieldAutoIncrement || (int)$num < 1) return false;
		
		if (!is_array($ids)) $ids = array((int)$ids);
		else $ids = array_map('intval', $ids);
		
		if (empty($ids)) return false;
		
		global $m;
		
		$result = $m->fetchAll($m->sprintf(
			'SELECT `%s` FROM %s WHERE %s IN (%s)',
			join('`,`', array_map(array($m, 'escape'), array_keys($this->fields))), $this->table, $m->escape($this->fieldAutoIncrement), join(',', $ids)
		));
		if (!$result) return false;
		
		// PHP defined FieldTypes
		$f = Fields::getInstance();
		foreach ($result as &$item) {
			foreach ($item as $key => &$field) {
				if ($this->fieldTypes && (isset($this->fieldTypes[$key]) && $this->fieldTypes[$key]) && isset(Fields::$types[$this->fieldTypes[$key]])) {
					// check if method is overriden
					try {
						$field = Fields::$types[$this->fieldTypes[$key]]->duplicate($field);
					} catch (FieldTypesException $e) {}
				}
			}
			$cId = $item[$this->fieldAutoIncrement];
			// delete auto increment field, it update by DB server
			unset($item[$this->fieldAutoIncrement]);
			
			// create duplicates
			for ($i = 1; $i <= $num; $i++) {
				if (!$this->add($item, false)) {
					throw new ItemsException($i, $cId);
				}
			}
		}
		return true;
	}

	/**
	 * Update data for update
	 * 
	 * @see class Fields
	 * @param array $data - data to update
	 * @return array
	 */
	protected function transformDataForUpdate(array &$data = null) {
		$f = Fields::getInstance();
		
		foreach ($data as $key => &$field) {
			if ($this->fieldTypes && (isset($this->fieldTypes[$key]) && $this->fieldTypes[$key]) && isset(Fields::$types[$this->fieldTypes[$key]])) {
				$field = Fields::$types[$this->fieldTypes[$key]]->set($field);
			}
		}
		
		return $data;
	}

	/**
	 * Update item details
	 * 
	 * @see this::validate()
	 * @param array $data - data to edit
	 * @param int $id - id
	 * @return mixed
	 */
	public function edit(array $data, $id) {
		$this->validate(false);
		$this->transformDataForUpdate($data);
		
		global $m;
		return $m->sprintf('UPDATE %s SET %s WHERE %s = %u', $this->table, $m->join($data), $m->escape($this->fieldAutoIncrement), $id);
	}

	/**
	 * Add item
	 * 
	 * @see this::validate()
	 * @param array $data - data to add
	 * @param bool $transform - transform data using PHP defined FieldTypes or not (@see this::duplicate())
	 * @return mixed
	 */
	public function add(array $data, $transform = true) {
		$this->validate(false);
		if ($transform) $this->transformDataForUpdate($data);
		
		global $m;
		if ($m->sprintf('INSERT INTO %s SET %s', $this->table, $m->join($data))) {
			return $m->insertId();
		}
		return false;
	}

	/**
	 * Validate
	 * 
	 * @param bool $validateFields - validate fields property or not (insert, erase, update methods are not need it)
	 * @return void
	 * 
	 * @throws ItemsException
	 */
	protected function validate($validateFields = true) {
		if (!$this->table) throw new ItemsException(sprintf('Table "%s" is not set', $this->table));
		if ($validateFields && !$this->fields) throw new ItemsException(sprintf('Fields in table "%s" is not set', $this->table));
	}
}

/**
 * Items Exception model
 * 
 * @author Azat Khuzhin <dohardgopro@gmail.com>
 * @package akAdmin
 * @licence GPLv2
 */
class ItemsException extends BaseException {}
