<?php

abstract class ViewHelper extends Component
{
	protected $view;

	public function __construct(View $view) {
		$this->view = $view;
	}

	public function getView() {
		return $this->view;
	}

	public function __toString() {
		try {
			if (method_exists($this, 'toString'))
				return $this->toString();
			return get_class($this);
		} catch (Exception $e) {
			Php2Go::app()->handleException($e);
		}
	}

	protected function getIdByName($name) {
		return str_replace(array('_', '[]', '][', '[', ']'), array('-', '', '-', '-', ''), $name);
	}

	protected function getIdByModelAttr(Model $model, &$attr) {
		return $this->getIdByName($this->getNameByModelAttr($model, $attr));
	}

	protected function getNameByModelAttr(Model $model, &$attr) {
		if ($attr[0] === '[')
			list($modelIndex, $attr, $attrIndex) = array(strtok($attr, '[]'), strtok('['), strtok(']'));
		else
			list($attr, $attrIndex) = array(strtok($attr, '['), strtok(']'));
		// model prefix
		$name = $model->getNamePrefix();
		// tabular input index
		if (isset($modelIndex)) {
			if (($pos = strpos($name, '[')))
				$name = substr($name, 0, $pos) . '[' . $modelIndex . ']' . substr($name, $pos);
			else
				$name .= '[' . $modelIndex . ']';
		}
		// attribute name
		$name .= '[' . $attr . ']';
		// array based attribute
		($attrIndex !== false) && ($name .= '[' . $attrIndex . ']');
		return $name;
	}

	protected function defineId(array &$attrs, $namespace=null) {
		if (!isset($attrs['id']) && @$attrs['defineId'] !== false) {
			if (isset($attrs['name']))
				$attrs['id'] = $this->getIdByName($attrs['name']);
			elseif ($namespace !== null)
				$attrs['id'] = Util::id($namespace);
		}
	}

	protected function renderAttrs(array $attrs) {
		$result = '';
		$minimizedAttrs = array('compact', 'checked', 'declare', 'readonly', 'disabled', 'selected', 'defer', 'ismap', 'nohref', 'noshade', 'nowrap', 'multiple', 'noresize');
		foreach ($attrs as $name => $value) {
			if ($value === null)
				continue;
			if (is_array($value)) {
				if ($name == 'class')
					$value = implode(' ', $value);
				else
					$value = '';
			}
			if (in_array($name, $minimizedAttrs)) {
				if ($value === true || $value === 1 || $value === '1' || strtolower($value) === 'true' || $value === $name)
					$result .= " {$name}=\"{$name}\"";
			} else {
				if (strpos($name, 'on') !== 0)
					$value = $this->view->escape($value);
				if (strpos($value, '"') !== false)
					$result .= " {$name}='{$value}'";
				else
					$result .= " {$name}=\"{$value}\"";
			}
		}
		return $result;
	}
}