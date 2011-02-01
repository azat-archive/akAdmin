<?

/*
 * This file is part of the akAdmin package.
 * (c) 2010 Azat Khuzhin <dohardgopro@gmail.com>
 *
 * For the full copyright and license information, please view http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * Users controller
 * 
 * @author Azat Khuzhin <dohardgopro@gmail.com>
 * @package akAdmin
 * @licence GPLv2
 */

/**
 * User list / All users
 */
function all() {
	global $d, $u, $user;
	
	// is not admin => can`t see list of users
	if (!$user['isAdmin']) {
		$d->set('error', 'You don`t have access to this page.');
		return $d->content('default/error.php');
	}
	
	$d->set('title', 'List of all users');
	$d->set('users', $u->all());
	return $d->content('users/all.php');
}

/**
 * Auth user
 */
function auth($back = null) {
	// not authorized
	global $d, $u;
	
	if ($d->getRequestMethod() == 'post') {
		// captcha
		$c = akCaptcha::getInstance();
		
		if ($u->attemptsLimitExhausted()) {
			$d->set('error', sprintf('All attempts have been exhausted. Try again in %u seconds', Users::attemptsTime));
		} elseif (!$_POST['login'] || !$_POST['password']) {
			$d->set('error', 'Please fill fields "Login" and "Password".');
		} elseif (!$c->exists($_POST['captcha'])) {
			$d->set('error', 'Please enter the right symbols from picture.');
		} elseif ($u->auth(array('login' => $_POST['login'], 'password' => md5($_POST['password'])))) {
			if ($back) $back = base64_decode($back);
			if (!$back || mb_substr($back, 0, 1) != '/') $back = '/' . $back;
			$d->redirect($back);
		} else {
			$d->set('error', 'Error no such combination of login and password.');
		}
	}
	$d->set('title', 'Authorisation');
	return $d->content('users/auth.php');
}

/**
 * Logout user
 */
function logout() {
	global $u, $d;
	$u->logout();
	$d->redirect('/');
}

/**
 * User details
 */
function details($uid) {
	global $d, $u, $user;
	
	$uid = (int)$uid;
	// if non admin, and this is not current user profile
	if (!$user['isAdmin'] && $user['id'] != $uid) {
		$d->set('error', 'You don`t have access to this page.');
		return $d->content('default/error.php');
	}
	
	// no such user
	$userInfo = $u->details(array('id' => $uid));
	if (!$userInfo) {
		$d->redirect('/default/404');
	}
	
	$d->set('title', 'Details of user ' . $userInfo['login']);
	$d->set('userInfo', $userInfo);
	return $d->content('users/details.php');
}

/**
 * Add a user
 */
function add() {
	global $user, $d, $u;
	
	// is not admin => can`t add a user
	if (!$user['isAdmin']) {
		$d->set('error', 'You don`t have access to add user.');
		return $d->content('default/error.php');
	}
	
	if ($d->getRequestMethod() == 'post') {
		if ($_POST['login']) $_POST['login'] = strip_tags($_POST['login']);
		
		if (!$_POST['login'] || !$_POST['password']) {
			$d->set('error', 'Please fill field "Login" and "Password"');
		} elseif (mb_strlen($_POST['password']) < 5) {
			$d->set('error', 'Too small length of "Password"');
		} elseif (mb_strlen($_POST['login']) < 5) {
			$d->set('error', 'Too small length of "Login"');
		} elseif ($_POST['email'] && !isEmailValid($_POST['email'])) {
			$d->set('error', 'Such email is not valid');
		} elseif ($u->isSuchUserExist($_POST['login'])) {
			$d->set('error', 'Such user already exists. Please try another login name.');
		} elseif ($userId = $u->add(array('login' => $_POST['login'], 'password' => md5($_POST['password']), 'isAdmin' => (isset($_POST['isAdmin']) ? true : false), 'email' => $_POST['email']))) {
			$d->redirect('/user/details/' . $userId);
		} else {
			$d->set('error', 'Error occured while adding a new user. Please try again later.');
		}
	}
	
	$d->set('title', 'Add new user');
	return $d->content('users/add.php');
}

/**
 * Edit user
 */
function edit($uid) {
	global $d, $u, $user;
	
	$uid = (int)$uid;
	// if non admin, and this is not current user profile
	if (!$user['isAdmin'] && $user['id'] != $uid) {
		$d->set('error', 'You don`t have access to this page');
		return $d->content('default/error.php');
	}
	
	// no such user
	$userInfo = $u->details(array('id' => $uid));
	if (!$userInfo) {
		$d->redirect('/default/404');
	}
	
	if ($d->getRequestMethod() == 'post') {
		if ($_POST['login']) $_POST['login'] = strip_tags($_POST['login']);
		
		if (!$_POST['login']) {
			$d->set('error', 'Please fill field "Login"');
		} elseif ($_POST['password'] && mb_strlen($_POST['password']) < 5) {
			$d->set('error', 'Too small length of "Password"');
		} elseif (mb_strlen($_POST['login']) < 5) {
			$d->set('error', 'Too small length of "Login"');
		} elseif ($_POST['email'] && !isEmailValid($_POST['email'])) {
			$d->set('error', 'Such email is not valid');
		} elseif ($u->isSuchUserExist($_POST['login'], $userInfo['login'])) {
			$d->set('error', 'Such user already exists. Please try another login name.');
		} else  {
			$params = array(
				'login' => $_POST['login'],
				'email' => $_POST['email']
			);
			// only admin can edit user-admin-status
			if ($user['isAdmin']) $params['isAdmin'] = (isset($_POST['isAdmin']) ? true : false);
			if ($_POST['password']) $params['password'] = md5($_POST['password']);
			
			if ($u->edit($params, $uid)) {
				$d->redirect('/user/details/' . $uid);
			} else {
				$d->set('error', sprintf('Error occured while editing user %s. Please try again later.', $userInfo['login']));
			}
		}
	}
	
	$d->set('title', 'Edit user ' . $userInfo['login']);
	$d->set('userInfo', $userInfo);
	return $d->content('users/edit.php');
}

/**
 * Duplicate / Copy user
 */
function duplicate($uid, $num) {
	global $d, $user, $u;
	
	$uid = (int)$uid;
	$num = (int)$num;
	
	// not all params are set
	if (!$uid || !$num) {
		$d->redirect('/default/error');
	}
	
	// check grants
	if (!$user['isAdmin']) {
		$d->set('error', 'Your don`t have permission to create user.');
		return $d->content('default/error.php');
	}
	
	// no such user
	$userInfo = $u->details(array('id' => $uid));
	if (!$userInfo) {
		$d->redirect('/default/404');
	}
	
	// delete params, that are different for every user
	unset($userInfo['createTime'], $userInfo['editTime'], $userInfo['lastTime'], $userInfo['id']);
	
	// creating copies/ duplicate
	for ($inc = 1; $inc <= $num; $inc++) {
		// create new name
		$jInc = 1;
		$uName = sprintf('[%u] Copy %s', $inc, $userInfo['login']);
		// check for exists of this copy (user with such login)
		while (Users::isSuchUserExist($uName)) {
			$uName = sprintf('[%u.%u] Copy %s', $inc, $jInc++, $userInfo['login']);
		}
		$userInfo['login'] = $uName;
		
		// create a copy
		if (!$u->add($userInfo)) {
			$d->set('error', sprintf('Error occured while creating %u copy of user %s. Please try again later.', $inc, $userInfo['login']));
			return $d->content('default/error.php');
		}
	}
	
	$d->redirect('/users');
}

/**
 * Erase / Delete user
 */
function erase($uid) {
	global $d, $user, $u;
	
	$uid = (int)$uid;
	
	// check grants
	if (!$user['isAdmin']) {
		$d->set('error', 'Your don`t have permission to delete user.');
		return $d->content('default/error.php');
	}
	
	// error occured
	if (!$u->erase($uid)) {
		$d->set('error', sprintf('Error occured while deleting user %s. Please try again later.', $sectionInfo['title']));
		return $d->content('default/error.php');
	}
	
	// success delete, redirect to user list
	$d->redirect('/users');
}

/**
 * Edit / See user grants
 */
function grants($uid = null) {
	global $g;
	
	$uid = (int)$uid;
	
	if (!$uid) $d->redirect('/default/404');
	
	global $d, $user, $u;
	// is not admin => can`t edit user grants
	if (!$user['isAdmin']) {
		$d->set('error', 'You don`t have access to this page.');
		return $d->content('default/error.php');
	}
	
	// no such user
	$userInfo = $u->details(array('id' => $uid));
	if (!$userInfo) {
		$d->redirect('/default/404');
	}
	
	if ($d->getRequestMethod() == 'post') {
		if (!isset($_POST['id']) || !$_POST['id']) {
			$d->set('error', 'No grants for changing selected');
		} else {
			foreach ($_POST['id'] as $id => &$value) {
				// delete not modified grants
				foreach ($value as $k => $v) {
					if ($v == 'exist') unset($value[$k]);
				}
				// if something have
				if ($value) {
					foreach ($value as $k => $v) {
						if (!$v || $v == 'false') unset($value[$k]);
					}
					$value = (is_array($value) && !empty($value) ? array_keys($value) : null);
					
					if (!$g->update($uid, $id, $value)) {
						$errorOcurred = true;
						$d->set('error', 'Error ocurred. Can`t set some of grants.');
						break;
					}
				}
			}
			// if not errors redirect to user details
			if (!isset($errorOcurred)) {
				$d->redirect('/user/details/' . $uid);
			}
		}
	}
	$d->set('fullTree', Tree::getInstance()->write($uid));
	$d->set('title', sprintf('Grants for user %s', $userInfo['login']));
	return $d->content('users/grants.php');
}
