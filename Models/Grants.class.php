<?

/**
 * Grants model
 * 
 * @TODO THOROUGHLY CHECK ALL
 * 
 * @author Azat Khuzhin <dohardgopro@gmail.com>
 * @package akAdmin
 * @licence GPLv2
 */

class Grants {
	/**
	 * Grants table
	 * 
	 * @var string
	 */
	static $table = '%s_akadmin_users_grants';
	/**
	 * Grants
	 * Using pow of "2"
	 * 
	 * @var array
	 */
	static $grants = array(
		'select' => 1, // get items
		'insert' => 2, // add items
		'delete' => 4, // delete items
		'update' => 8, // edit items
		'alter' => 	16, // alter section info
		'drop' => 	32, // delete /erase section
	);


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
	 * @return object of Grants
	 */
	static function getInstance() {
		static $object;
		if (!$object) $object = new Grants;
		
		return $object;
	}

	/**
	 * Get users grants
	 * (A power of to)
	 * 
	 * @param int $uid - user id
	 * @param int $sid - grants for section or table with id = $sid
	 * @param bool $asArray - as array (i.e. for HTML forms)
	 * @param int $grantsValue - grants (not from db)
	 * @return bool
	 */
	public function get($uid, $sid, $asArray = false, $grantsValue = null) {
		global $user, $m;
		
		$uid = (int)$uid;
		$sid = (int)$sid;
		if (!$grantsValue && (!$uid || !$sid)) return false;
		
		// grants from db, which already exist
		if (!$grantsValue) {
			$params = $m->andJoin(array('uid' => $uid, 'sid' => (int)$sid));
			$grants = $m->fetch($m->sprintf('SELECT * FROM %s WHERE %s', self::$table, $params));
			$grantsBin = $grants['grants'];
		} else {
			$grants = $grantsBin = (int)$grantsValue;
		}
		
		if ($asArray) {
			$result = self::$grants;
			foreach ($result as $name => &$value) {
				$value = ($value & $grantsBin ? true : false);
			}
			return $result;
		}
		return $grants;
	}

	/**
	 * Check users grants
	 * 
	 * If type is not set, than check for all grants
	 * If types == all -> than als grants
	 * 
	 * @param int $uid - user id
	 * @param int $sid - grants for section or table with id = $sid
	 * @param mixed $types - types of grants (array('select', 'update' ...)) or just 'select'
	 * @return bool
	 */
	public function check($uid, $sid, $types = null) {
		global $m;
		
		$uid = (int)$uid;
		$sid = (int)$sid;
		if ($types && !is_array($types)) $types = array($types);
		if (!$uid || !$sid || ($types && !$this->validateGrants($types))) return false;
		
		$grants = $this->get($uid, $sid, false);
		if ($grants) {
			$grant = $this->getGrants($types);
			return ($grants['grants'] & $grant);
		}
		return false;
	}

	/**
	 * Update user grants
	 * 
	 * Previous grants will be erased!
	 * If types == all -> than als grants
	 * 
	 * @param int $uid - user id
	 * @param int $sid - grants for section or table with id = $sid
	 * @param mixed $types - types of grants (array('select', 'update' ...)) or just 'select'
	 * @return bool
	 */
	public function update($uid, $sid, $types = null) {
		global $m;
		
		$uid = (int)$uid;
		$sid = (int)$sid;
		if ($types && !is_array($types)) $types = array($types);
		if (!$sid || !$uid || ($types && !$this->validateGrants($types))) return false;
		
		$grants = $this->get($uid, $sid);
		// update existed grants
		if ($grants) {
			$grant = $this->getGrants($types);
			
			// update if need
			if ($grant == 0) {
				return $m->sprintf(
					'DELETE FROM %s WHERE id = %u',
					self::$table, $grants['id']
				);
			} else {
				return $m->sprintf(
					'UPDATE %s SET grants = %u, editTime = %u WHERE id = %u',
					self::$table, $grant, time(), $grants['id']
				);
			}
		}
		// add grants
		else {
			$grant = $this->getGrants($types);
			
			return $m->sprintf(
				'INSERT INTO %s SET grants = %u, uid = %u, createTime = %u, sid = %u',
				self::$table, $grant, $uid, time(), $sid
			);
		}
		return false;
	}

	/**
	 * Delete users grants
	 * 
	 * @param array $data - data to ger users with
	 * @return bool
	 */
	public function erase(array $data) {
		global $m;
		
		return $m->sprintf('DELETE FROM %s WHERE %s', self::$table, $m->andJoin($data));
	}

	/**
	 * Check is all grants are valid
	 * 
	 * @see self::$grants
	 * @param array $types - types of grants (array('select', 'update' ...))
	 * @return bool
	 */
	protected function validateGrants(array $types = null) {
		if (!$types) return false;
		// all grants
		if (count($types) == 1 && $types[0] == 'all') return true;
		
		foreach ($types as &$type) {
			if (!isset(self::$grants[$type])) return false;
		}
		return true;
	}

	/**
	 * Get integer value of grants from array
	 * 
	 * @param array $types - types of grants (array('select', 'update' ...))
	 * @param int $existedGrants - existed grants (already heaved by user) [not used by me]
	 * @return int
	 */
	protected function getGrants(array $types = null, $existedGrants = null) {
		if ($types && !$this->validateGrants($types)) return false;
		// if type - all grants -> than no different what grants he already have
		if (count($types) == 1 && $types[0] == 'all') {
			$existedGrants = 0;
			$types = array_keys(self::$grants);
		}
		
		$grant = (int)$existedGrants;
		if (!$types) return $grant;
		
		foreach (array_unique($types) as $type) {
			$grant += self::$grants[$type];
		}
		return $grant;
	}
}
