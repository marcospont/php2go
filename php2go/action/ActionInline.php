<?php

class ActionInline extends Action
{
	public function run() {
		$methodName = 'action' . $this->getId();
		$method = new ReflectionMethod($this->controller, $methodName);
		if (($n = $method->getNumberOfParameters()) > 0) {
			$params = array();
			$request = $this->controller->getRequest();
			foreach ($method->getParameters() as $i=>$param) {
				$name = $param->getName();
				if (($value = $request->getQuery($name)))
					$params[$name] = $value;
				elseif ($param->isDefaultValueAvailable())
					$params[$name] = $param->getDefaultValue();
				else
					throw new HttpException(400, __(PHP2GO_LANG_DOMAIN, 'Your request is not valid.'));
			}
			$method->invokeArgs($this->controller, $params);
		} else {
			$this->controller->{$methodName}();
		}
	}
}