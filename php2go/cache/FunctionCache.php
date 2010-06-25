<?php

class FunctionCache extends CacheProxy
{
	const ID_PREFIX = 'function-';
	
	public function call($function, array $params=array(), $lifetime=false) {
		if (($data = $this->load($this->getId($function, $params))) && isset($data[0]) && isset($data[1])) {
			$output = $data[0];
			$return = $data[1];
		} else {
			ob_start();
			ob_implicit_flush(false);
			$return = call_user_func_array($function, $params);
			$output = ob_get_clean();
			$this->save(array($output, $return), $this->getId($function, $params), $lifetime);
		}
		echo $output;
		return $return;
	}
	
	protected function onCreateCache() {
		$this->setAutoSerialization(true);
		$this->setIdPrefix(self::ID_PREFIX);
	}
	
	protected function getId($function, array $params) {
		$id = '';
		if (is_string($function)) {
			$id .= $function;
		} elseif (is_array($function)) {
			if (is_object($function[0]))
				$id .= strtolower(get_class($function[0])) . '-' . $function[1];
			elseif (is_string($function[0]) && is_string($function[1]))
				$id .= strtolower($function[0]) . '-' . $function[1];
			elseif (is_string($function[0]) && get_class($function[1]) == 'Closure')
				$id .= 'closure-' . $function[0];
			else
				throw new Exception(__(PHP2GO_LANG_DOMAIN, 'This function can not be cached.'));
		}
		if (!empty($params))
			$id .= '-' . md5(serialize($params));
		return $id;
	}
}