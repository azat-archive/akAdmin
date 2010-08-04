<?

/*
 * This file is part of the akAdmin package.
 * (c) 2010 Azat Khuzhin <dohardgopro@gmail.com>
 *
 * For the full copyright and license information, please view http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * Set common settings (using file includes/config.php)
 * 
 * @author Azat Khuzhin <dohardgopro@gmail.com>
 * @package akAdmin
 * @licence GPLv2
 */
 
require_once dirname(__FILE__) . '/includes/config.php';
require_once dirIncludes . 'autoload.php';
require_once dirIncludes . 'functions.php';
require_once dirIncludes . 'httpStatuses.php';
require_once dirControllers . 'default.php';

session_start();

set_include_path(sprintf('%s:%s:%s:%s', get_include_path(), dirRoot, dirAkLib, dirModels));
mb_internal_encoding(charset);

// log
$l = akLog::getInstance(true, !debug);

// dispatcher
$d = akMVC::getInstance();
$d->setPaths(dirModels, dirViews, dirControllers);
$d->setDebug(debug);
$d->setCallbacks(null, null, 'notFound', 'error');

// db
try {
	$m = db::getInstance(dbServer, dbPort, dbUser, dbPassword, dbCharset, dbName);
} catch (akException $e) {
	$l->add($e->getMessage());
	
	$d->set('error', 'Can`t connect to DB');
	echo $d->content('default/error.php');
	die;
}

// users
$u = Users::getInstance();
// check if no admin users, then create one
// default user: 		admin
// default password:	secretPassword
if (!$u->num()) {
	$u->add(array('login' => 'admin', 'password' => md5('secretPassword'), 'isAdmin' => true));
	$l->add('Default user add');
}
$u->testAuth();

// grants
$g = Grants::getInstance();
