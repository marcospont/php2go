<?php

class Session extends Component implements Countable, IteratorAggregate, ArrayAccess
{
	private static $started = false;
	private static $closed = false;
	private static $destroyed = false;
	private static $regenerateId = false;
	private static $rememberSeconds = 1209600;

	public static function start($options) {
		if (self::$destroyed)
			throw new SessionException(__(PHP2GO_LANG_DOMAIN, 'Session was previously destroyed.'));
		if (!self::$started) {
			$file = $line = null;
			if (headers_sent($file, $line))
				throw new SessionException(__(PHP2GO_LANG_DOMAIN, 'Session can not be started because output has been sent to the browser: %s, line %d', array($file, $line)));
			if (defined('SID'))
				throw new SessionException(__(PHP2GO_LANG_DOMAIN, 'Session was already started by session.auto_start or session_start().'));
			self::setUserOptions($options);
			self::setDefaultOptions();
			session_start();
			self::$started = true;
		}
	}

	public static function isStarted() {
		return self::$started;
	}

	public static function close() {
		if (!self::$closed && !self::$destroyed) {
			session_write_close();
			self::$closed = true;
		}
	}

	public static function isClosed() {
		return self::$closed;
	}

	public static function destroy($removeCookie=true) {
		if (!self::$destroyed) {
			session_destroy();
			if ($removeCookie) {
		        if (isset($_COOKIE[session_name()])) {
		            $params = session_get_cookie_params();
		            setcookie(session_name(), false, 315554400, $params['path'], $params['domain'], $params['secure']);
				}
			}
			self::$destroyed = true;
		}
	}

	public static function isDestroyed() {
		return self::$destroyed;
	}

	public static function getName() {
		return session_name();
	}

	public static function getId() {
		return session_id();
	}

	public static function regenerateId() {
		if (!self::$regenerateId) {
			session_regenerate_id(true);
			self::$regenerateId = true;
		}
	}

	public static function remember($seconds=null) {
		$seconds = (int)$seconds;
		if ($seconds <= 0)
			$seconds = self::$rememberSeconds;
		self::setLifetime($seconds);
		self::regenerateId();
	}

	public static function forget() {
		self::setLifetime();
	}

	private static function setUserOptions($options=null) {
		if (is_array($options)) {
			if (isset($options['id'])) {
				if (!is_string($options['id']) || empty($options['id']))
					throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Session id must be a non empty string.'));
				session_id($options['id']);
			}
			if (isset($options['name'])) {
				if (!is_string($options['name']) || empty($options['name']))
					throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Session name must be a non empty string.'));
				session_name($options['name']);
			}
			if (isset($options['savePath'])) {
				if (!is_dir($options['savePath']) || !is_writeable($options['savePath']))
					throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Session save path must be a writeable directory.'));
				session_save_path($options['savePath']);
			}
			if (array_key_exists('gcProbability', $options)) {
				if ($options['gcProbability'] < 0 || $options['gcProbability'] > 100)
					throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Session "gcProbability" must be between 0 and 100.'));
				ini_set('session.gc_probability', $options['gcProbability']);
			}
			if (array_key_exists('gcMaxLifetime', $options)) {
				if ($options['gcMaxLifetime'] < 0)
					throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Session "gcMaxLifetime" must be greater than 0.'));
				ini_set('session.gc_maxlifetime', $options['gcMaxLifetime']);
			}
		}
	}

	private static function setDefaultOptions() {
		ini_set('session.use_only_cookies', '1');
		ini_set('session.use_trans_sid', '0');
		ini_set('session.cookie_lifetime', '0');
		ini_set('session.cookie_httponly', '1');
	}

	private static function setLifetime($seconds=0) {
		$params = session_get_cookie_params();
		session_set_cookie_params($seconds, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
	}

	public function contains($key) {
		return (isset($_SESSION[$key]));
	}

	public function keys() {
		return array_keys($_SESSION);
	}

	public function get($key) {
		return (array_key_exists($key, $_SESSION) ? $_SESSION[$key] : null);
	}

	public function set($key, $value) {
		$_SESSION[$key] = $value;
	}

	public function remove($key) {
		if (array_key_exists($key, $_SESSION)) {
			$value = $_SESSION[$key];
			unset($_SESSION[$key]);
			return $value;
		}
		return null;
	}

	public function clear() {
		foreach(array_keys($_SESSION) as $key)
			unset($_SESSION[$key]);
	}

	public function toArray() {
		return $_SESSION;
	}

	public function count() {
		return count($_SESSION);
	}

	public function getIterator() {
		return new ArrayIterator($_SESSION);
	}

	public function offsetExists($offset) {
		return $this->contains($offset);
	}

	public function offsetGet($offset) {
		return $this->get($offset);
	}

	public function offsetSet($offset, $value) {
		$this->set($offset, $value);
	}

	public function offsetUnset($offset) {
		$this->remove($offset);
	}
}
register_shutdown_function(array('Session', 'close'));

class SessionException extends Exception
{
}