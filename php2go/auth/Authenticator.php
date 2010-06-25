<?php

class Authenticator extends Component
{
	const ADAPTER_DB = 'db';

	protected $adapter;
	protected $valid = false;
	protected $user;
	protected $usernameParam = 'username';
	protected $passwordParam = 'password';
	protected $rememberParam = 'remember';
	protected $rememberParamValue = 1;
	protected $rememberSeconds;
	protected $returnUriSessionKey = 'returnUri';
	protected $userSessionKey = 'user';
	protected $autoRegenerateId = true;
	protected $successRoute;
	protected $errorRoute;
	protected $logoutRoute;
	protected $loginRoute;

	public function __construct() {
		$this->user = $this->restoreUser();
		if ($this->user)
			$this->valid = true;
		$this->registerEvents(array('onAuthenticationRequired', 'onAuthenticate', 'onError', 'onLogout'));
		Php2Go::app()->addEventListener('onEndRequest', array($this, 'saveUser'));
	}

	public function getAdapter() {
		if ($this->adapter === null)
			throw new Exception(__(PHP2GO_LANG_DOMAIN, 'Authenticator does not have an adapter.'));
		return $this->adapter;
	}

	public function setAdapter($adapter) {
		if (!$adapter instanceof AuthenticatorAdapter)
			$adapter = AuthenticatorAdapter::factory($adapter);
		$this->adapter = $adapter;
	}

	public function getValid() {
		return $this->valid;
	}

	public function getUser() {
		return $this->user;
	}

	public function getUsernameParam() {
		return $this->usernameParam;
	}

	public function setUsernameParam($name) {
		$this->usernameParam = $name;
	}

	public function getPasswordParam() {
		return $this->passwordParam;
	}

	public function setPasswordParam($name) {
		$this->passwordParam = $name;
	}

	public function getRememberParam() {
		return $this->rememberParam;
	}

	public function setRememberParam($name) {
		$this->rememberParam = $name;
	}

	public function getRememberParamValue() {
		return $this->rememberParamValue;
	}

	public function setRememberParamValue($value) {
		$this->rememberParamValue = $value;
	}

	public function getRememberSeconds() {
		return $this->rememberSeconds;
	}

	public function setRememberSeconds($seconds) {
		$seconds = (int)$seconds;
		if ($seconds < 0)
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Seconds to remember the user must be greater than 0.'));
		$this->rememberSeconds = $seconds;
	}

	public function getUserSessionKey() {
		return $this->userSessionKey;
	}

	public function setUserSessionKey($key) {
		if (!is_string($key) || empty($key))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Session key to store the user must be a non empty string.'));
		$this->userSessionKey = $key;
	}

	public function getAutoRegenerateId() {
		return $this->autoRegenerateId;
	}

	public function setAutoRegenerateId($autoRegenerateId) {
		$this->autoRegenerateId = (bool)$autoRegenerateId;
	}

	public function getLoginRoute() {
		return $this->loginRoute;
	}

	public function setLoginRoute($route) {
		$this->loginRoute = ltrim($route, '/');
	}

	public function getSuccessRoute() {
		return $this->successRoute;
	}

	public function setSuccessRoute($route) {
		$this->successRoute = ltrim($route, '/');
	}

	public function getErrorRoute() {
		return $this->errorRoute;
	}

	public function setErrorRoute($route) {
		$this->errorRoute = ltrim($route, '/');
	}

	public function getLogoutRoute() {
		return $this->logoutRoute;
	}

	public function setLogoutRoute($route) {
		$this->logoutRoute = ltrim($route, '/');
	}

	public function authenticate() {
		$credentials = $this->parseCredentials();
		if ($credentials) {
			extract($credentials);
			if ($properties = $this->getAdapter()->authenticate($username, $password)) {
				if ($remember) {
					Session::remember($this->rememberSeconds);
				} else {
					Session::forget();
					if ($this->autoRegenerateId)
						Session::regenerateId();
				}
				$this->user = new UserContainer($username, (is_array($properties) ? $properties : array()));
				$this->user->loginTime = microtime(true);
				$this->valid = true;
				$this->raiseEvent('onAuthenticate');
			} else {
				$this->raiseEvent('onError');
				throw new AuthenticatorException(__(PHP2GO_LANG_DOMAIN, 'Invalid username or password.'));
			}
		}
	}

	public function logout() {
		Session::forget();
		$this->destroyUser();
		$this->user = null;
		$this->valid = false;
		$this->raiseEvent('onLogout');
	}

	protected function onAuthenticationRequired() {
		$app = Php2Go::app();
		$request = $app->getRequest();
		if ($request->isAjax() || $request->isFlash()) {
			throw new AuthenticatorException(__(PHP2GO_LANG_DOMAIN, 'This action requires authentication.'));
		} else {
			$route = $this->loginRoute;
			if ($route === null)
				throw new Exception(__(PHP2GO_LANG_DOMAIN, 'Authenticator does not have a login route.'));
			$current = $app->getRoute();
			if (strpos($current, $route) === false) {
				$app->getSession()->set($this->returnUriSessionKey, $request->getUrl());
				$app->getResponse()->redirect($app->createUrl($route));
			}
		}
	}

	protected function onAuthenticate() {
		$app = Php2Go::app();
		$returnUri = $app->getSession()->get($this->returnUriSessionKey);
		if ($returnUri !== null) {
			$app->getSession()->remove($this->returnUriSessionKey);
			$app->getResponse()->redirect($returnUri);
		} else {
			$route = $this->successRoute;
			if ($route === null)
				throw new Exception(__(PHP2GO_LANG_DOMAIN, 'Authenticator does not have a success route.'));
			$app->getResponse()->redirect($app->createUrl($route));
		}
	}

	protected function onError() {
		if ($this->errorRoute) {
			$app = Php2Go::app();
			$response = $app->getResponse();
			$response->redirect($app->createUrl($this->errorRoute));
		}
	}

	protected function onLogout() {
		$route = $this->logoutRoute;
		if ($route === null)
			throw new Exception(__(PHP2GO_LANG_DOMAIN, 'Authenticator does not have a logout route.'));
		$app = Php2Go::app();
		$app->getResponse()->redirect($app->createUrl($route));
	}

	protected function restoreUser() {
		$data = Php2Go::app()->getSession()->get($this->userSessionKey);
		if ($data)
			return new UserContainer($data['name'], $data['properties']);
		return null;
	}

	public function saveUser() {
		if ($this->valid)
			Php2Go::app()->getSession()->set($this->userSessionKey, $this->user->toArray());
	}

	protected function destroyUser() {
		Php2Go::app()->getSession()->remove($this->userSessionKey);
	}

	protected function parseCredentials() {
		$req = Php2Go::app()->getRequest();
		$username = $req->getPost($this->usernameParam);
		$password = $req->getPost($this->passwordParam);
		$remember = $req->getPost($this->rememberParam);
		if ($username && $password) {
			return array(
				'username' => $username,
				'password' => $password,
				'remember' => ($remember == $this->rememberParamValue)
			);
		}
		return false;
	}
}

class AuthenticatorException extends HttpException
{
	public function __construct($message) {
		parent::__construct(403, $message);
	}
}