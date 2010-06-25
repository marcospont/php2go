<?php

class OutputCache extends CacheProxy
{
	const ID_PREFIX = 'output-';
	
	protected $idStack = array();
	
	public function begin($id) {
		$data = $this->load($id);
		if ($data !== false) {
			echo $data;
			return false;
		}
		ob_start();
		ob_implicit_flush(false);
		$this->idStack[] = $id;
		return true;
	}
	
	public function end($lifetime=false) {
		$data = ob_get_clean();
		$id = array_pop($this->idStack);
		if ($id === null)
			throw new Exception(__(PHP2GO_LANG_DOMAIN, 'Missing call to OutputCache.start().'));
		$this->save($data, $id, $lifetime);
		echo $data;
	}

	protected function onCreateCache() {
		$this->setIdPrefix(self::ID_PREFIX);
	}	
}