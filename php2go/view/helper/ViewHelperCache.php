<?php

class ViewHelperCache extends ViewHelper
{
	protected $cache;
	protected $cacheOptions;
	protected $defaultVaryBy = array(
		'route' => true,
		'session' => false,
		'user' => false,
		'params' => array()
	);

	public function loadOptions(array $options) {
		$this->cacheOptions = $options;
	}

	public function begin($id, array $varyBy=array()) {
		return $this->getCache()->begin($this->getId($id, $varyBy));
	}

	public function end($lifetime=false) {
		$this->getCache()->end($lifetime);
	}

	public function delete($id) {
		$this->getCache()->clean(Cache::CLEANING_MODE_PATTERN, $id);
	}

	private function getId($id, array $varyBy) {
		$ext = '';
		$criteria = array_merge($this->defaultVaryBy, $varyBy);
		if ($criteria['route'])
			$ext .= $this->view->getController()->getRoute();
		if (Session::isStarted()) {
			if ($criteria['session'])
				$ext .= session_id();
			if ($criteria['user']) {
				$auth = $this->view->app->getAuthenticator();
				if ($auth->valid)
					$ext .= $auth->getUser()->getName();
			}
		}
		if (is_string($criteria['params']))
			$criteria['params'] = explode(',', $criteria['params']);
		if (is_array($criteria['params'])) {
			foreach ($criteria['params'] as $param) {
				if (isset($_REQUEST[$param]))
					$ext .= $name . '=' . $_REQUEST[$param];
			}
		}
		return $id . (!empty($ext) ? '-' . md5($ext) : '');
	}

	private function getCache() {
		if (!$this->cache)
			$this->cache = new OutputCache($this->cacheOptions);
		return $this->cache;
	}
}