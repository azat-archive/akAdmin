<?

/*
 * This file is part of the akAdmin package.
 * (c) 2010 Azat Khuzhin <dohardgopro@gmail.com>
 *
 * For the full copyright and license information, please view http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * Tree
 * 
 * @author Azat Khuzhin <dohardgopro@gmail.com>
 * @package akAdmin
 * @licence GPLv2
 */

class Tree extends Sections {
	/**
	 * Constructor
	 * 
	 * @return void
	 */
	public function __construct() {
		// init Sections model
		Sections::getInstance();
	}

	/**
	 * Fast init
	 * 
	 * @return object of Tree
	 */
	static function getInstance() {
		static $object;
		if (!$object) $object = new Tree;
		
		return $object;
	}
	
	/**
	 * Build full tree
	 * This function bust be called only with first arg (depth),
	 * other args for recursive calling this function it selfs!
	 * 
	 * @param int $uid - user id (to get it grants)
	 * @param int $depth - depth
	 * @param bool $root - root
	 * @param array $data - data
	 * @return array (from first parent to last) or null (if not sections)
	 */
	protected function build($uid, $depth = 10, $root = true, &$data = null) {
		$uid = (int)$uid;
		$depth = (int)$depth;
		if (!$uid || !$depth) return false;
		
		global $m;
		// formated query, to get sections and user grants, tableName field only for write ico
		static $formatedQuery =	'SELECT s.id, s.title, s.tableName, ' .
						'(SELECT grants FROM %s g WHERE g.uid = %u AND g.sid = s.id) grants ' .
						'FROM %s s WHERE s.pid = %u ORDER BY s.id ASC';
		
		static $depthCounter = 0;
		// if root -> than flush counter
		if ($root) $depthCounter = 0;
		
		// root -> get all root sections
		if ($root) {
			$result = $m->fetchAll($m->sprintf($formatedQuery, Grants::$table, $uid, self::$table, 0));
			$result = $this->build($uid, $depth, false, $result);
		}
		// recursive part of function
		elseif ($data) {
			// max depth limit
			if ((++$depthCounter) > $depth) return null;
			
			foreach ($data as &$item) {
				$item['childrens'] = $m->fetchAll($m->sprintf($formatedQuery, Grants::$table, $uid, self::$table, $item['id']));
				$item['childrens'] = $this->build($uid, $depth, false, $item['childrens']);
			}
			return $data;
		}
		// no data, just return null
		else {
			return null;
		}
		
		return $result;
	}

	/**
	 * Return full tree as string (in HTML)
	 * This function bust be called only with first arg (depth),
	 * other args for recursive calling this function it selfs!
	 * 
	 * To onkeyup it write section id
	 * 
	 * @param int $uid - user id (to get it grants)
	 * @param int $depth - depth
	 * @param bool $root - root
	 * @param array $data - data
	 * @return string
	 */
	public function write($uid, $depth = 10, $root = true, &$data = null) {
		$uid = (int)$uid;
		$depth = (int)$depth;
		if (!$uid || !$depth) return false;
		
		global $g;
		
		// get main tree
		if ($root) {
			$result = $this->build($uid, $depth);
			return $this->write($uid, $depth, false, $result);
		}
		// revursive part of function
		elseif ($data) {
			$string = '<ul id="fullTree">' . "\n";
			foreach ($data as &$item) {
				// flush
				$grantsString = '';
				// get users grants from int field -> array, which already exist
				$grants = $g->get(null, null, true, $item['grants']);
				if (is_array($grants)) {
					// check if has one
					$hasOne = false;
					foreach ($grants as $value) {
						if ($value) $hasOne = true;
					}
					// appends user grants, which already exist
					if ($hasOne) {
						$grantsString = '<span class="grants">' . "\n";
						$grantsString .= '<span class="all exist" onkeydown="return \'all\';">all</span>' . "\n";
						foreach ($grants as $grantName => $value) {
							$grantsString .= sprintf(
								'<span class="exist%s" onkeydown="return \'%s\';">%s</span>' . "\n",
								($value ? ' selected' : null), $grantName, $grantName
							);
						}
						$grantsString .= '</span>' . "\n";
					}
				}
				
				$string .= sprintf(
					'<li onkeyup="return %u;"><img src="/images/%s" alt="%s" />%s%s</li>' . "\n",
					$item['id'], ($item['tableName'] ? 'application-document.png' : 'blue-folder--arrow.png'), ($item['tableName'] ? 'Table' : 'Section'), $item['title'], $grantsString
				);
				if (isset($item['childrens']) && $item['childrens']) {
					$string .= $this->write($uid, $depth, false, $item['childrens']);
				}
			}
			$string .= '</ul>' . "\n";
			return $string;
		}
		else {
			return null;
		}
	}
}
