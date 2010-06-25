<?php

Php2Go::import('php2go.paginator.adapter.*');

abstract class PaginatorAdapter implements Countable
{
	public static function factory($data, array $options=array()) {
		if ($data instanceof PaginatorAdapter)
			return $data;
		if ($data instanceof ActiveRecord)
			return new PaginatorAdapterActiveRecord($data, $options);
		if (is_array($data))
			return new PaginatorAdapterArray($data, $options);
		if ($data instanceof Iterator)
			return new PaginatorAdapterIterator($data, $options);
		if ($data instanceof IteratorAggregate)
			return new PaginatorAdapterIterator($data->getIterator(), $options);
		$type = (is_object($data) ? get_class($data) : gettype($data));
		throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'No paginator adapter available for "%s".', array($type)));			
	}
	
	abstract public function getItems($offset, $pageSize);
}