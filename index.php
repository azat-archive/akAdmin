<?

/*
 * This file is part of the akAdmin package.
 * (c) 2010 Azat Khuzhin <dohardgopro@gmail.com>
 *
 * For the full copyright and license information, please view http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * Index
 * Dispatcher
 * 
 * @author Azat Khuzhin <dohardgopro@gmail.com>
 * @package akAdmin
 * @licence GPLv2
 */

require_once dirname(__FILE__) . '/common.php';

/**
 * Controllers from "Controllers/default.php"
 */
$d->add('/', 'default.php', 'main');
$d->add('/default/error', 'default.php', 'error');
$d->add('/default/error/:q', 'default.php', 'error');
$d->add('/default/404', 'default.php', 'notFound');

/// ONLY FOR NON-AUTHORIZED USERS
if (!isset($user) || !$user) {
	/**
	 * Controllers from "Controllers/users.php"
	 */
	$d->add('/user/auth', 'users.php', 'auth');
	$d->add('/user/auth', 'users.php', 'auth', 'post');
	$d->add('/user/auth/:back', 'users.php', 'auth'); // back parameter encode in base64
	$d->add('/user/auth/:back', 'users.php', 'auth', 'post'); // back parameter encode in base64
	$d->setCallbacks(null, null, 'defaultAuth', null); // rewrite all other paths to auth page
}
/// ONLY FOR AUTHORIZED USERS
else {
	/**
	 * Controllers from "Controllers/users.php"
	 */
	$d->add('/users', 'users.php', 'all');
	$d->add('/user/logout', 'users.php', 'logout');
	$d->add('/user/details/:uid', 'users.php', 'details');
	$d->add('/user/add', 'users.php', 'add');
	$d->add('/user/add', 'users.php', 'add', 'post');
	$d->add('/user/edit/:uid', 'users.php', 'edit');
	$d->add('/user/edit/:uid', 'users.php', 'edit', 'post');
	$d->add('/user/duplicate/:uid/:num', 'users.php', 'duplicate');
	$d->add('/user/erase/:uid', 'users.php', 'erase');
	$d->add('/user/grants/:uid', 'users.php', 'grants');
	$d->add('/user/grants/:uid', 'users.php', 'grants', 'post');

	/**
	 * Controllers from "Controllers/sections.php"
	 */
	$d->add('/sections', 'sections.php', 'all');
	$d->add('/section/add', 'sections.php', 'add');
	$d->add('/section/add', 'sections.php', 'add', 'post');
	$d->add('/section/edit/:sid', 'sections.php', 'edit');
	$d->add('/section/edit/:sid', 'sections.php', 'edit', 'post');
	$d->add('/section/erase/:sid', 'sections.php', 'erase');
	$d->add('/section/details/:sid', 'sections.php', 'details');
	$d->add('/section/details/:sid/page/:page', 'sections.php', 'details');
	$d->add('/section/details/:sid/page/:page/sort/:sort', 'sections.php', 'details');
	$d->add('/section/details/:sid/search/:q', 'sections.php', 'details');
	$d->add('/section/details/:sid/search/:q/page/:page', 'sections.php', 'details');
	$d->add('/section/details/:sid/search/:q/page/:page/sort/:sort', 'sections.php', 'details');
	$d->add('/ajax/sections/?q=:q', 'sections.php', 'ajaxAll');
	$d->add('/ajax/sections/:excludeId/?q=:q', 'sections.php', 'ajaxAll');
	$d->add('/section/settings/:sid', 'sections.php', 'settings');
	$d->add('/section/settings/:sid', 'sections.php', 'settings', 'post');

	/**
	 * Controllers from "Controllers/items.php"
	 */
	$d->add('/section/items/:sid/add', 'items.php', 'add');
	$d->add('/section/items/:sid/add', 'items.php', 'add', 'post');
	$d->add('/section/items/:sid/edit/:id', 'items.php', 'edit');
	$d->add('/section/items/:sid/edit/:id', 'items.php', 'edit', 'post');
	$d->add('/section/items/:sid/erase/:id', 'items.php', 'erase');
	$d->add('/section/items/:sid/erase', 'items.php', 'erase', 'post'); // multi delete only
	$d->add('/section/items/:sid/duplicate/:id/:num', 'items.php', 'duplicate');
	$d->add('/section/items/:sid/duplicate/:num', 'items.php', 'duplicate', 'post'); // multi duplicate only
}

/**
 * CAPTCHA
 */
$d->add('/captcha', 'captcha.php', 'get');

$d->run();