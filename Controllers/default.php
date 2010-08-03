<?

/**
 * Default controller
 * 
 * @author Azat Khuzhin <dohardgopro@gmail.com>
 * @package akAdmin
 * @licence GPLv2
 */

/**
 * Main
 */
function main() {
	global $user;
	
	redirect('/' . ($user ? 'sections' : 'user/auth'));
}

/**
 * Return an error
 * 
 * ## Die after this function
 * 
 * @param string $error - error message
 * @param string $description - description
 */
function error($error = null, $description = null) {
	global $d, $httpStatuses;
	
	// if this is a exception
	if ($error instanceof Exception || $error instanceof akException) {
		$description = $error->getMessage();
		$error = sprintf('Uncatched exception of instance %s', get_class($error));
	}
	
	// send mail to admins
	ob_start();
	var_dump(debug_backtrace(), $error);
	$trace = ob_get_contents();
	ob_end_clean();
	$message = sprintf('<p>Description: %s</p><p>Trace:</p>%s', $description, $trace);
	msend(devEmails, 'Error occured', $message);
	
	if (!headers_sent()) header($_SERVER['SERVER_PROTOCOL'] . ' 502 ' . $httpStatuses[502]);
	if (isset($error) && $error && isset($description) && $description) {
		$d->set('error', array('title' => $error, 'description' => $description));
	} else {
		$d->set('error', $error);
	}
	echo $d->content('default/error.php');
	die;
}

/**
 * Return a not found
 */
function notFound() {
	global $d, $httpStatuses;
	
	if (!headers_sent()) header($_SERVER['SERVER_PROTOCOL'] . ' 404 ' . $httpStatuses[404]);
	$d->set(
		'error',
		array(
			'title' => $httpStatuses[404],
			'description' => 'This page is not found!',
		)
	);
	return $d->content('default/error.php');
}

/**
 * Default auth page
 * 
 * If user not auth, than pages like "/sections" are not avaliable,
 * And then working this function,
 * redirecting to auth, with back parameter encode in base64
 */
function defaultAuth() {
	redirect('/user/auth/' . base64_encode($_SERVER['REQUEST_URI']));
}