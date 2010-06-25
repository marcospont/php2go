<?php

class ViewHelperPartialLoop extends ViewHelper
{
	public function partialLoop($view, $data) {
		if (!is_array($data) && !($data instanceof Traversable) && (!is_object($data) || !method_exists($data, 'toArray')))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Data must be iterable.'));
		if (is_object($data) && !($data instanceof Traversable))
			$data = $data->toArray();
		$content = '';
		foreach ($data as $item) {
			$context = (!is_object($item) && !is_array($item) ? array('content' => $item) : $item);
			$content .= $this->view->renderPartial($view, $context, true) . PHP_EOL;
		}
		echo $content;
	}
}