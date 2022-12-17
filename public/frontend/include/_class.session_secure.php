<?php

/**
 * Este SessionManager inicia la sesion de PHP y la asegura bloqueando la cookie,
 * restringiendo la sesion a un navegador en especifico y regenerando el ID.
 */
class SessionManager
{
	public static function sessionStart($name, $limit = 0, $path = '/', $domain = null, $secure = null)
	{
		session_name(md5($name));
		$https = isset($secure) ? $secure : isset($_SERVER['HTTPS']);
		session_set_cookie_params($limit, $path, $domain, $https, true);
		session_start();

		if (self::validateSession()) {
			if (!self::preventHijacking()) {
				$_SESSION = array();
				$_SESSION['IPaddress'] = $_SERVER['REMOTE_ADDR'];
				$_SESSION['userAgent'] = $_SERVER['HTTP_USER_AGENT'];

				self::regenerateSession();
			} elseif (rand(1, 100) <= 5) {
				self::regenerateSession();
			}
		} else {
			$_SESSION = array();
			session_destroy();
			session_start();
		}
	}

	public static function regenerateSession()
	{
		if (isset($_SESSION['OBSOLETE']) || $_SESSION['OBSOLETE']) {
			return;
		}

		$_SESSION['OBSOLETE'] = true;
		$_SESSION['EXPIRES'] = time() + 10;

		session_regenerate_id(false);
		$newSession = session_id();
		session_write_close();
		session_id($newSession);
		session_start();
		unset($_SESSION['OBSOLETE']);
		unset($_SESSION['EXPIRES']);
	}

	protected static function validateSession()
	{
		if (isset($_SESSION['OBSOLETE']) && !isset($_SESSION['EXPIRES'])) {
			return false;
		}
		if (isset($_SESSION['EXPIRES']) && $_SESSION['EXPIRES'] < time()) {
			return false;
		}
		return true;
	}

	protected static function preventHijacking()
	{
		if (!isset($_SESSION['IPaddress']) || !isset($_SESSION['userAgent'])) {
			return false;
		}
		if ($_SESSION['IPaddress'] != $_SERVER['REMOTE_ADDR']) {
			return false;
		}
		if ($_SESSION['userAgent'] != $_SERVER['HTTP_USER_AGENT']) {
			return false;
		}
		return true;
	}
}
