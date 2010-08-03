<?

/**
 * Captcha controller
 * 
 * @author Azat Khuzhin <dohardgopro@gmail.com>
 * @package akAdmin
 * @licence GPLv2
 */

/**
 * Get captcha
 */
function get() {
	echo akCaptcha::getInstance();
}
