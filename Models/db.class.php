<?

/*
 * This file is part of the akAdmin package.
 * (c) 2010 Azat Khuzhin <dohardgopro@gmail.com>
 *
 * For the full copyright and license information, please view http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * DB model
 * 
 * @author Azat Khuzhin <dohardgopro@gmail.com>
 * @package akAdmin
 * @licence GPLv2
 */

class db extends akMySQLQuery {
	/**
	 * Constructor
	 * 
	 * @see parent::__construct()
	 * @return void
	 */
	public function __construct($server = null, $port = null, $user = null, $password = null, $charset = null, $db = null) {
		$this->connect($server, $port, $user, $password, $charset, $db);
	}

	/**
	 * Fast init
	 * 
	 * @return object of db
	 */
	static function getInstance($server = null, $port = null, $user = null, $password = null, $charset = null, $db = null) {
		static $object;
		if (!$object) $object = new db($server, $port, $user, $password, $charset, $db);
		
		return $object;
	}
	
	/**
	 * Wrapper for parent::query
	 * That add log message
	 */
	public function query($query) {
		global $l;
		$l->sadd('Query executed: %s', $query);
		
		return parent::query($query);
	}
}
