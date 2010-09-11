<?

/*
 * This file is part of the akAdmin package.
 * (c) 2010 Azat Khuzhin <dohardgopro@gmail.com>
 *
 * For the full copyright and license information, please view http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * Users model
 * 
 * Password field is a md5()
 * 
 * @author Azat Khuzhin <dohardgopro@gmail.com>
 * @package akAdmin
 * @licence GPLv2
 */

class Users {
	/**
	 * Attempts time,
	 * after what user can try to login again
	 */
	const attemptsTime = 600;
	/**
	 * Attempts limit
	 */
	const attemptsLimit = 5;
	/**
	 * Array of attempts
	 * Link to _SESSION array
	 * 
	 * @var array
	 */
	static $attempts;
	/**
	 * Table with users
	 * 
	 * @var string
	 */
	static $table = '%s_akadmin_users';
	/**
	 * Auth user time (COOKIE live time)
	 * 
	 * @default 30 days
	 * @var int
	 */
	static $authTime;

	/**
	 * Constructor
	 * 
	 * @return void
	 */
	public function __construct() {
		self::$table = sprintf(self::$table, project);
		
		if (!self::$authTime) self::$authTime = (time() + 60*60*24*30);
	}

	/**
	 * Fast init
	 * 
	 * @return object of Users
	 */
	static function getInstance() {
		static $object;
		if (!$object) $object = new Users;
		
		return $object;
	}

	/**
	 * Get all users
	 * 
	 * @param array $data - data tor get user with
	 * @return array or false
	 */
	public function all(array $data = null) {
		global $m;
		
		if ($data) $data = ' WHERE ' . $m->andJoin($data);
		return $m->fetchAll($m->sprintf('SELECT * FROM %s%s', self::$table, $data));
	}

	/**
	 * Get number of users
	 * 
	 * @param array $where - where conditions
	 * @return int or false
	 */
	public function num(array $where = null) {
		global $m;
		
		if ($where) $where = 'WHERE ' . $m->andJoin($where);
		
		$num = $m->fetch($m->sprintf('SELECT COUNT(id) count FROM %s %s', self::$table, $where));
		
		if ($num) return $num['count'];
		return false;
	}

	/**
	 * Auth user
	 * 
	 * @param array $data - data to get user with
	 * @return bool
	 */
	public function auth(array $data) {
		global $d, $u;
		
		$userInfo = $this->details($data, 0, 1);
		if ($userInfo) {
			setcookie('login', $userInfo['login'], self::$authTime, '/', domain);
			setcookie('password', $userInfo['password'], self::$authTime, '/', domain);
			
			$_SESSION['user'] = $userInfo;
			$GLOBALS['user'] = &$_SESSION['user'];
			$d->set('user', $GLOBALS['user']);
			
			// update last auth time
			$u->edit(array('lastTime' => time()), $userInfo['id']);
			
			return true;
		}
		return false;
	}

	/**
	 * Logout user
	 * 
	 * @return void
	 */
	public function logout() {
		global $d;
		
		setcookie('login', null, 0, '/', domain);
		setcookie('password', null, 0, '/', domain);
		unset($_SESSION['user']);
		unset($GLOBALS['user']);
		$d->set('user', null);
	}

	/**
	 * Test auth
	 * (get user data from COOKIE or from SESSION and write it to SESSION and to tpl vars)
	 * 
	 * @return void
	 */
	public function testAuth() {
		global $m, $d, $l;
		
		// from cookie
		if (isset($_COOKIE['login']) && $_COOKIE && isset($_COOKIE['password']) && $_COOKIE['password']) {
			$params = array(
				'login' => $_COOKIE['login'],
				'password' => $_COOKIE['password'],
			);
		}
		// from session
		elseif (isset($_SESSION['user']) && $_SESSION['user']) {
			$params = array('id' => $_SESSION['user']['id']);
		}
		
		// try to get user info
		if (isset($params)) {
			$userInfo = $this->details($params, 0, 1);
			// auth success
			if ($userInfo) {
				$_SESSION['user'] = $userInfo;
				// if no cookies => set
				if (!isset($_COOKIE['login']) || !isset($_COOKIE['password']) || !$_COOKIE['login'] || !$_COOKIE['password']) {
					setcookie('login', $userInfo['login'], self::$authTime, '/', domain);
					setcookie('password', $userInfo['password'], self::$authTime, '/', domain);
				}
				
				// create new global var
				$GLOBALS['user'] = &$_SESSION['user'];
				// create new tmp var
				$d->set('user', $GLOBALS['user']);
				
				$l->sadd('User auth success, login: %20s, id: %u', $userInfo['login'], $userInfo['id']);
			} else {
				$l->add('Faild to auth user');
			}
		}
	}

	/**
	 * Get user details
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
	 * Add a user
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
	 * Edit a user info
	 * 
	 * @param array $data - data to edit
	 * @param int $id - user id
	 * @return bool
	 */
	public function edit(array $data, $id) {
		global $m;
		
		$data['editTime'] = time();
		return $m->sprintf('UPDATE %s SET %s WHERE id = %u', self::$table, $m->join($data), $id);
	}

	/**
	 * Delete / Erase user
	 * 
	 * @param int $id - user id
	 * @return bool
	 */
	public function erase($id) {
		global $m, $g;
		
		if ($g->erase(array('uid' => $id)) && $m->sprintf('DELETE FROM %s WHERE id = %u', self::$table, $id)) {
			return true;
		}
		return false;
	}

	/**
	 * Check is such user already exists
	 * Return true is it exists
	 * 
	 * @param string $login - login
	 * @param string $currentLogin - current login ( login != curentLogin)
	 * @return bool
	 */
	static function isSuchUserExist($login, $currentLogin = null) {
		global $m;
		
		$params['login'] = $login;
		if ($currentLogin) $params['login !='] = $currentLogin;
		
		return ($m->numRows($m->sprintf('SELECT id FROM %s WHERE %s LIMIT 1', self::$table, $m->andJoin($params))) ? true : false);
	}
	
	/**
	 * Check attempts limit exhausted
	 * 
	 * @see this::attemptsTime
	 * @see this::attemptsLimit
	 * 
	 * @return bool (true if limit is exhausted, otherwise false)
	 */
	public function attemptsLimitExhausted() {
		$this->addAttempt();
		
		// delete old attempts
		foreach (self::$attempts as $key => &$attempt) {
			if ((time() - $attempt) >= self::attemptsTime) {
				unset(self::$attempts[$key]);
			}
		}
		// check
		return (count(self::$attempts) > self::attemptsLimit);
	}
	
	/**
	 * Add attempt to list
	 * 
	 * @return void
	 */
	public function addAttempt() {
		if (!is_array(self::$attempts)) {
			if (!isset($_SESSION['attemptsCount'])) $_SESSION['attemptsCount'] = array();
			self::$attempts = &$_SESSION['attemptsCount'];
		}
		
		self::$attempts[] = time();
	}
}
