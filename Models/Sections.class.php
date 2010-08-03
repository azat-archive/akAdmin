<?

/**
 * Sections model
 * 
 * @author Azat Khuzhin <dohardgopro@gmail.com>
 * @package akAdmin
 * @licence GPLv2
 */

class Sections {
	/**
	 * Table with tables / sections
	 * 
	 * @var string
	 */
	static $table = '%s_akadmin_sections';

	/**
	 * Constructor
	 * 
	 * @return void
	 */
	public function __construct() {
		self::$table = sprintf(self::$table, project);
	}

	/**
	 * Fast init
	 * 
	 * @return object of Sections
	 */
	static function getInstance() {
		static $object;
		if (!$object) $object = new Sections;
		
		return $object;
	}

	/**
	 * All sections
	 * Simple list (for autocomplete)
	 * 
	 * This function check user grants
	 * 
	 * @example to ge root sections $data = array('parentid' = 0)
	 * 
	 * @param array $data - data to get sections with
	 * @param int $offset - offset
	 * @param int $limit - limit
	 * @return array of false
	 */
	public function simpleList(array $data = null, $offset = 0, $limit = 1) {
		global $m, $user;
		
		if ($data) $data = ' WHERE ' . $m->andJoin($data);
		// check grants
		if (!$user['isAdmin']) {
			$checkGrants = sprintf(
				'id IN (SELECT sid FROM %s WHERE uid = %u AND (grants & %u))',
				Grants::$table, $user['id'], Grants::$grants['select']
			);
			$data = ($data ? sprintf('%s AND %s', $data, $checkGrants) : sprintf(' WHERE ', $checkGrants));
		}
		
		$resource = $m->sprintf('SELECT id, title FROM %s%s ORDER BY id DESC LIMIT %u, %u', self::$table, $data, $offset, $limit);
		return ($limit == 1 ? $m->fetch($resource) : $m->fetchAll($resource));
	}

	/**
	 * List of all sections for list
	 * 
	 * This function check user grants
	 * 
	 * @example to ge root sections $data = array('parentid' = 0)
	 * 
	 * @param array $data - data to get sections with
	 * @return array of false
	 */
	public function all(array $data = null) {
		global $m, $user;
		
		if ($data) $data = ' WHERE ' . $m->andJoin($data);
		// check grants
		if (!$user['isAdmin']) {
			$checkGrants = sprintf(
				'id IN (SELECT sid FROM %s WHERE uid = %u AND (grants & %u))',
				Grants::$table, $user['id'], Grants::$grants['select']
			);
			$data = ($data ? sprintf('%s AND %s', $data, $checkGrants) : sprintf(' WHERE ', $checkGrants));
		}
		
		$resource = $m->sprintf(
			'SELECT s.*, ' .
			'(SELECT COUNT(id) FROM %s WHERE tableName = "" AND pid = s.id) subSections, ' .
			'(SELECT COUNT(id) FROM %s WHERE tableName != "" AND pid = s.id) tables ' .
			'FROM %s s%s',
			self::$table,
			self::$table,
			self::$table, $data
		);
		return $m->fetchAll($resource);
	}

	/**
	 * Add a section
	 * 
	 * @param array $data - new user data
	 * @return int or false
	 */
	public function add(array $data) {
		global $m;
		
		$data['createTime'] = time();
		if ($m->sprintf('INSERT INTO %s SET %s', self::$table, $m->join($data))) {
			return $m->insertId();
		}
		return false;
	}

	/**
	 * Edit a section info
	 * 
	 * @param array $data - data to edit
	 * @param int $id - section id
	 * @return bool
	 */
	public function edit(array $data, $id) {
		global $m;
		
		$data['editTime'] = time();
		return $m->sprintf('UPDATE %s SET %s WHERE id = %u', self::$table, $m->join($data), $id);
	}

	/**
	 * Get section details
	 * 
	 * @param array $data - data to get user with
	 * @param int $offset - offset
	 * @param int $limit - limit
	 * @return array or null
	 */
	public function details(array $data, $offset = 0, $limit = 1) {
		global $m;
		
		$result = $m->sprintf('SELECT * FROM %s WHERE %s%s', self::$table, $m->andJoin($data), ($limit ? sprintf(' LIMIT %u, %u', $offset, $limit) : null));
		if (!$result) return false;
		
		return ($limit == 1 ? $m->fetch($result) : $m->fetchAll($result));
	}

	/**
	 * Delete / Erase section
	 * 
	 * @param int $id - section id
	 * @return bool
	 */
	public function erase($id) {
		global $m;
		
		if ($m->sprintf('DELETE FROM %s WHERE id = %u', self::$table, $id)) {
			/// @TODO delete grants using Grants model
			return true;
		}
		return false;
	}

	/**
	 * Build parent tree
	 * 
	 * @param array $info - info about section
	 * @return array (from first parent to last) or null (if not pid n current section)
	 */
	public function buildTree($info) {
		$result = array($info);
		
		if (!isset($info['pid']) || !$info['pid']) return $result;
		
		global $m;
		$pid = &$info['pid'];
		
		while ($pid) {
			$parentInfo = $m->fetch($m->sprintf('SELECT id, pid, title FROM %s WHERE id = %u LIMIT 1', self::$table, $pid));
			$result[] = $parentInfo;
			
			$pid = &$parentInfo['pid'];
		}
		return array_reverse($result);
	}

	/**
	 * Check if table is exists
	 * 
	 * @param string $tableName - name of table
	 * @return bool
	 */
	public function isTableExists($tableName) {
		global $m;
		
		return ($m->numRows($m->sprintf('SHOW TABLES LIKE "%s"', $tableName)) ? true : false);
	}

	/**
	 * Get table fields
	 * 
	 * @param string $tableName - name of table
	 * @param bool $full - return all fields and it's types and indexes (default: false), and add type without length and extra
	 * @return mixed
	 * 
	 * @throws akException
	 */
	public function getTableFields($tableName, $full = false) {
		if (!$this->isTableExists($tableName)) throw new akException('Such table is not exist');
		
		global $m;
		
		$result = $m->fetchAll($m->sprintf('DESCRIBE %s', $m->escape($tableName)));
		if ($full) {
			foreach ($result as $key => $value) {
				preg_match('@^([^\s\(]+)@is', $value['Type'], $type);
				$result[$key]['DBType'] = mb_strtolower($type[1]);
			}
			
			return $result;
		} else {
			$fields = array();
			
			foreach ($result as $value) {
				$fields[] = $value['Field'];
			}
			return $fields;
		}
	}

	/**
	 * Get auto_increment field from table
	 * 
	 * @param string $tableName - name of table
	 * @return mixed
	 * 
	 * @throws akException
	 */
	public function getTableAutoIncrementField($tableName, $full = false) {
		$fields = $this->getTableFields($tableName, true);
		foreach ($fields as $field) {
			if (isset($field['Extra']) && preg_match('@auto_increment@Uis', $field['Extra'])) {
				return $field['Field'];
			}
		}
		return null;
	}
}
