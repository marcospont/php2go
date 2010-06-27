<?php

class Controller extends Component
{
	public $defaultAction = 'index';
	protected $id;
	protected $app;
	protected $module = null;
	protected $action;
	protected $view;
	protected $viewPath;
	protected $filterChain;

	public function __construct($id, $module=null) {
		$this->id = $id;
		$this->app = Php2Go::app();
		$this->module = $module;
		$this->attachBehaviors($this->behaviors());
		$this->registerEvents(array('onBeforeAction', 'onAfterAction'));
	}

	public function getId() {
		return $this->id;
	}

	public function getUniqueId() {
		return ($this->module ? $this->module->getId() . DS . $this->id : $this->id);
	}

	public function getApp() {
		return $this->app;
	}

	public function getModule() {
		return $this->module;
	}

	public function getRoute() {
		return $this->getUniqueId() . '/' . $this->action->getId();
	}

	public function getDefaultAction() {
		return $this->defaultAction;
	}

	public function getAction() {
		return $this->action;
	}

	public function getRequest() {
		return $this->app->getRequest();
	}

	public function getResponse() {
		return $this->app->getResponse();
	}

	public function getView() {
		return $this->view;
	}

	public function getViewPath() {
		if ($this->viewPath === null) {
			$owner = ($this->module ? $this->module : $this->app);
			$this->viewPath = $owner->getViewPath() . DS . $this->id;
		}
		return $this->viewPath;
	}

	public function init() {
	}

	public function actions() {
		return array();
	}

	public function authorizationRules() {
		return array();
	}

	public function behaviors() {
		return array();
	}

	public function filters() {
		return array();
	}

	public function viewHelpers() {
		return array();
	}

	public function run($actionId) {
		if (($action = $this->createAction($actionId)) !== null) {
			$prevAction = $this->action;
			$this->action = $action;
			$chain = $this->getFilterChain();
			if ($chain === null)
				$this->runAction($action);
			else
				$chain->run();
			$this->action = $prevAction;
		} else {
			$this->missingAction($actionId);
		}
	}

	public function runAction(Action $action) {
		$owner = ($this->module ? $this->module : $this->app);
		if ($owner->raiseEvent('onBeforeControllerAction', new Event($owner))) {
			if ($this->raiseEvent('onBeforeAction', new Event($this))) {
				$this->initView();
				$action->run();
				$this->raiseEvent('onAfterAction', new Event($this));
				$owner->raiseEvent('onAfterControllerAction', new Event($owner));
			}
		}
	}

	public function forward($route, $exit=true) {
		if (strpos($route, '/') === false) {
			$this->run($route);
		} else {
			if ($route[0] !== '/' && $this->module)
				$route = $this->module->getId() . '/' . $route;
			$this->app->dispatch($route);
		}
		$this->app->getResponse()->sendResponse();
		if ($exit)
			$this->app->stop();
	}

	public function redirect($url=null, $statusCode=302) {
		if ((strpos($url, '://')) === false) {
			if (is_array($url)) {
				$route = (isset($url[0]) ? array_shift($url) : '');
				$url = $this->createUrl($route, $url);
			} elseif (empty($url)) {
				$url = $this->createUrl($this->defaultAction);
			} else {
				$url = $this->createUrl($url);
			}
		}
		$this->getResponse()->redirect($url, $statusCode);
	}

	public function refresh($anchor='') {
		$this->getResponse()->redirect($this->getRequest()->getUrl() . $anchor);
	}

	public function render($view, $data=null) {
		$this->view->render($view, $data);
	}

	public function renderText($text) {
		$this->view->renderText($text);
	}

	public function renderPartial($view, $data=null, $return=false, $processOutput=false) {
		return $this->view->renderPartial($view, $data, $return, $processOutput);
	}

	public function renderJson($json, array $options=array()) {
		$this->getResponse()
				->setContentType('application/json; charset=utf-8')
				->appendBody(Json::encode($json, $options));
	}

	public function renderXml($view, $data=null) {
		$this->getResponse()
				->setContentType('text/xml; charset=' . $this->app->getCharset())
				->appendBody($this->view->renderPartial($view, $data, true));
	}

	public function createUrl($route, array $params=array(), $absolute=false, $ampersand='&') {
		if (empty($route)) {
			$route = $this->getRoute();
			$params = array_merge($_GET, $params);
		} elseif (strpos($route, '/') === false) {
			$route = $this->id . '/' . $route;
		} elseif ($route[0] !== '/' && $this->module) {
			$route = $this->module->getId() . '/' . $route;
		}
		if ($absolute)
			return $this->app->createAbsoluteUrl(trim($route, '/'), $params, $ampersand);
		else
			return $this->app->createUrl(trim($route, '/'), $params, $ampersand);
	}

	public function createAbsoluteUrl($route, array $params=array(), $ampersand='&') {
		return $this->createUrl($route, $params, true, $ampersand);
	}

	public function filterGetOnly(ActionFilterChain $chain) {
		if ($this->app->getRequest()->isGet())
			$chain->run();
		else
			throw new HttpException(400, __(PHP2GO_LANG_DOMAIN, 'Your request is not valid.'));
	}

	public function filterPostOnly(ActionFilterChain $chain) {
		if ($this->app->getRequest()->isPost())
			$chain->run();
		else
			throw new HttpException(400, __(PHP2GO_LANG_DOMAIN, 'Your request is not valid.'));
	}

	public function filterAjaxOnly(ActionFilterChain $chain) {
		if ($this->app->getRequest()->isAjax())
			$chain->run();
		else
			throw new HttpException(400, __(PHP2GO_LANG_DOMAIN, 'Your request is not valid.'));
	}

	public function filterAuthorization(ActionFilterChain $chain) {
		$filter = new AuthorizationFilter($this->authorizationRules());
		$filter->run($chain);
	}

	private function missingAction($actionId) {
		throw new HttpException(404, __(PHP2GO_LANG_DOMAIN, 'The system was enable to find the requested action "%s".', array($actionId)));
	}

	private function createAction(&$actionId) {
		if ($actionId == '')
			$actionId = $this->defaultAction;
		if (method_exists($this, 'action' . $actionId) && $actionId != 's')
			return new ActionInline($this, $actionId);
		else
			return $this->createActionFromConfig($this->actions(), $actionId);
	}

	private function createActionFromConfig(array $actions, $actionId) {
		if (isset($actions[$actionId]))
			return Php2Go::newInstance($actions[$actionId], $this, $actionId);
		return null;
	}

	private function getFilterChain() {
		$filters = $this->filters();
		if (!empty($filters))
			return ActionFilterChain::factory($this, $this->action, $filters);
		return null;
	}

	private function initView() {
		if (!$this->view) {
			$this->view = $this->app->createView();
			$this->view->setHelpers($this->viewHelpers());
		}
	}
}