<?php

class AuthorizationFilter extends ActionFilter
{
	protected $rules = array();

	public function __construct(array $rules=array()) {
		$this->loadRules($rules);
	}

	protected function loadRules(array $rules) {
		foreach ($rules as $i => $item) {
			if (is_array($item) && isset($item[0]))
				$this->rules[] = AuthorizationFilterRule::factory($item);
			else
				throw new Exception(__(PHP2GO_LANG_DOMAIN, 'Invalid authorization rule on index %d.', array($i)));
		}
	}

	protected function preFilter(ActionFilterChain $chain) {
		$app = Php2Go::app();
		$request = $app->getRequest();
		$authenticator = $app->getAuthenticator();
		$method = strtolower($request->getMethod());
		$ip = $request->getUserAddress();
		foreach ($this->rules as $rule) {
			$allow = $rule->validate($authenticator, $chain->controller, $chain->action, $ip, $method);
			if ($allow < 0) {
				$this->authorizationDenied($authenticator, $rule);
				return false;
			} elseif ($allow) {
				break;
			}
		}
		return true;
	}

	protected function authorizationDenied(Authenticator $authenticator, AuthorizationFilterRule $rule) {
		if (!$authenticator->getValid())
			$authenticator->raiseEvent('onAuthenticationRequired');
		throw new AuthorizationFilterException($rule);
	}
}

class AuthorizationFilterException extends AuthorizationException
{
	public $rule;

	public function __construct(AuthorizationFilterRule $rule) {
		parent::__construct($rule->resolveMessage());
		$this->rule = $rule;
	}
}

class AuthorizationFilterRule
{
	protected $allow;

	protected $actions = array();

	protected $users = array();

	protected $roles = array();

	protected $ips = array();

	protected $methods = array();

	protected $expression = null;

	protected $message = null;

	public static function factory($options) {
		if (!is_array($options) || !isset($options[0]))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid authorization rule specification.'));
		$rule = new AuthorizationFilterRule();
		$rule->allow = (array_shift($options) == 'allow');
		if (isset($options[0])) {
			$value = array_shift($options);
			switch ($value) {
				case '*' :
				case '?' :
				case '@' :
					$options['users'] = array($value);
					break;
				default :
					throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid authorization rule specification.'));
			}
		}
		foreach ($options as $k => $v) {
			switch ($k) {
				case 'actions' :
				case 'methods' :
				case 'users' :
				case 'roles' :
				case 'ips' :
					(is_string($v)) && ($v = array_map('trim', explode(',', $v)));
					(is_array($v)) && ($rule->{$k} = $v);
					break;
				case 'expression' :
				case 'message' :
					$rule->message = $v;
					break;
			}
		}
		return $rule;
	}

	public function validate(Authenticator $auth, Controller $controller, Action $action, $ip, $method) {
		if ($this->actionMatches($action) &&
			$this->userMatches($auth) &&
			$this->roleMatches($auth) &&
			$this->ipMatches($ip) &&
			$this->methodMatches($method) &&
			$this->expressionMatches($auth)
		) {
			return ($this->allow ? 1 : -1);
		}
		return 0;
	}

	public function resolveMessage() {
		if ($this->message !== null)
			return $this->message;
		return __(PHP2GO_LANG_DOMAIN, 'You are not authorized to perform this action.');
	}

	protected function actionMatches(Action $action) {
		return (empty($this->actions) || in_array($action->getId(), $this->actions));
	}

	protected function userMatches($auth) {
		if (!empty($this->users)) {
			foreach ($this->users as $user) {
				if ($user == '*')
					return true;
				elseif ($user == '?' && !$auth->getValid())
					return true;
				elseif ($user == '@' && $auth->getValid())
					return true;
				elseif ($auth->getValid() && strcasecmp($user, $auth->getUser()->getName()))
					return true;
			}
			return false;
		}
		return true;
	}

	protected function roleMatches($auth) {
		if (!empty($this->roles) && $auth->getValid()) {
			$userRole = $auth->getUser()->role;
			if ($userRole !== null) {
				foreach ($this->roles as $role) {
					if ($userRole == $role)
						return true;
				}
			}
			return false;
		}
		return true;
	}

	protected function ipMatches($ip) {
		if (!empty($this->ips)) {
			foreach ($this->ips as $rule) {
				if ($rule === '*' || $rule == $ip || (($pos = strpos($rule, '*')) !== false && !strncmp($ip, $rule, $pos)))
					return true;
			}
			return false;
		}
		return true;
	}

	protected function methodMatches($method) {
		return (empty($this->methods) || in_array($method, $this->methods));
	}

	protected function expressionMatches($auth) {
		return (!$this->expression || Util::evaluateExpression($this->expression, array('user' => $auth->getUser())));
	}
}