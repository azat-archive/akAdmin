<?

/*
 * This file is part of the akAdmin package.
 * (c) 2010 Azat Khuzhin <dohardgopro@gmail.com>
 *
 * For the full copyright and license information, please view http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * Autoload
 * 
 * @author Azat Khuzhin <dohardgopro@gmail.com>
 * @package akAdmin
 * @licence GPLv2
 */

function __autoload($class) {
	global $l;
	
	// my classes
	if (mb_substr($class, 0, 2) == 'ak') $class = sprintf('%s/%s.class.php', $class, $class);
	// no extension
	if (mb_substr($class, -4) != '.php') {
		// file not exists => add .class
		if (!is_readable($class . '.php')) $class = sprintf('%s.class.php', $class);
		else $class .= '.php';
	}
	
	// log
	if ($l && $l instanceof akLog) {
		$l->sadd('Require class %20s', $class);
	}
	
	require_once $class;
}
