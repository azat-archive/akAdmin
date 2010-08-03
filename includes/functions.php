<?

/**
 * Default functions
 * 
 * @author Azat Khuzhin <dohardgopro@gmail.com>
 * @package akAdmin
 * @licence GPLv2
 */

/**
 * Redirect
 * 
 * @param string $url
 * @return void
 */
function redirect($url = null) {
	if (!headers_sent() && $url) {
		header('Location: ' . $url);
		die;
	}
}

/**
 * Get from some vars
 * For HTML forms
 * 
 * @param mixed $var1
 * @param mixed $var2
 * @param mixed $varN
 * @return mixed
 */
function getFromSome() {
	$args = func_get_args();
	
	foreach ($args as &$arg) {
		if ($arg !== false && $arg !== null) return $arg;
	}
	return null;
}
function gfs() { return call_user_func_array('getFromSome', func_get_args());}

/**
 * Check is email valid
 * 
 * @param string $email - email
 * @return bool
 */
function isEmailValid($email) {
	return (preg_match('/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+$/is', $email) ? true : false);
}

/**
 * Send emails
 * 
 * @param string $to - to
 * @param string $subject - subject
 * @param string $message - message
 * @param string $from - from
 * @param string $contentType - content-type
 * @return bool
 */
function msend($to, $subject, $message, $from = null, $contentType = 'text/html') {
	if (!function_exists('code')) {
		function code($str) {
			return sprintf('=?UTF-8?B?%s?=', base64_encode($str));
		}
	}
	
	$headers  = sprintf('From: %s <%s>', code($_SERVER['HTTP_HOST']), ($from ? $from : 'dev@' . $_SERVER['HTTP_HOST'])) . "\n";
	$headers .= 'MIME-Version: 1.0' . "\n";
	$headers .= sprintf('Content-Type: %s; charset="%s"', $contentType, charset) . "\n";
	$headers .= 'Content-Transfer-Encoding: 8bit' . "\n\n";
	
	return mail($to, code($subject), str_replace("\r\n", "\n", $message), $headers/*, '-fdev@' . $_SERVER['HTTP_HOST']*/);
}

/**
 * Date in human view
 *
 * Tomorrow
 * Yesterday
 * E.t.c.
 *
 * @param int $unix_timestamp - date in unix timestamp
 * @param string format - format for date
 * @return string
 */
function date_human($format, $unix_timestamp) {
	if (!$format || !$unix_timestamp) return null;

	if (date('Ymd', $unix_timestamp) == date('Ymd', strtotime('-1 day'))) {
		return 'yesterday';
	} elseif (date('Ymd Hi', $unix_timestamp) == date('Ymd Hi')) {
		return 'just now';
	} elseif (date('Ymd', $unix_timestamp) == date('Ymd')) {
		return 'today';
	} elseif (date('Ymd', $unix_timestamp) == date('Ymd', strtotime('+1 day'))) {
		return 'tomorrow';
	}
	
	return date($format, $unix_timestamp);
}
function dh($format, $unix_timestamp) {return date_human($format, $unix_timestamp);}

/**
 * if string is empty it return white space symbol (in HTML &nbsp)
 * Otherwise this string
 * 
 * @param string $str - string
 * @return string
 */
function e($str) {
	return ($str ? $str : '&nbsp;');
}

/**
 * Return limited text,
 * If length of $text more then $length,
 * then split text to $length, and add $add
 * 
 * @param string $text - text
 * @param int $length - length
 * @param strring $add - additional
 * @param bool $substrOnly - substr using (if true than string is splited not by white space)
 * @return string
 */
function getTextLimited($text, $length = 300, $add = '...', $substrOnly = false) {
	if (mb_strlen($text) > $length) {
		if (!$substrOnly) {
			$i = 0;
			$tmp = '';
			$str = preg_split('@\s@Uis', $text);
			if (!$str) return null;
			
			while (mb_strlen($tmp) < $length) {
				$tmp .= $str[$i++] . ' ';
			}
			$text = trim($tmp) . $add;
		} else {
			$text = mb_substr($text, 0, $length) . $add;
		}
	}
	return $text;
}

/**
 * Generate random string
 * 
 * @param int $length
 * @return string
 */
function randomStr($length = 10) {
	$str = '';
	for ($i = 0; $i < $length; $i++) {
		$str .= (rand(1, 2) == 2 ? rand(0, 9) : chr(rand(ord('a'), ord('z'))));
	}
	return $str;
}

/**
 * Delete from array empty items
 * 
 * @param array $array - array
 * @return array
 */
function arrayEraseEmpty(array &$array) {
	foreach ($array as $k => &$value) {
		if (!$value) unset($array[$k]);
	}
	return $array;
}