<?

/*
 * This file is part of the akAdmin package.
 * (c) 2010 Azat Khuzhin <dohardgopro@gmail.com>
 *
 * For the full copyright and license information, please view http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * Config
 * 
 * @author Azat Khuzhin <dohardgopro@gmail.com>
 * @package akAdmin
 * @licence GPLv2
 */

// www dirs
define('dirImages', '/images/');
define('dirCss', '/css/');
define('dirJs', '/js/');

// server dirs
define('dirRoot', realpath(__DIR__ . '/../') . '/');
define('dirIncludes', dirRoot . 'includes/');
define('dirAkLib', dirIncludes . 'akLib/');
define('dirUpload', dirRoot . 'upload/');
define('dirTmp', dirRoot . 'tmp/');
// MVC
define('dirModels', dirRoot . 'Models/');
define('dirViews', dirRoot . 'Views/');
define('dirControllers', dirRoot . 'Controllers/');

// db
define('dbName', 'akAdmin');
define('dbUser', 'akAdminUser');
define('dbPassword', 'akAdminPass');
define('dbServer', ':/var/run/mysqld/mysqld.sock');
define('dbPort', null);
define('dbCharset', 'UTF8');

// iconv charset and for mb_* functions
define('charset', 'UTF-8');

// mails
define('devEmails', 'dohardgopro@gmail.com');

// cookie domain
define('domain', '.akAdmin');

// debug
define('debug', true);

// user-defined settings (only english alphabet!)
define('project', 'test');
// how much items per page
define('itemsPerPage', 100);

// version
define('akAdminVersion', 2.0);
