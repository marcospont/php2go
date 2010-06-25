<?php

class FilterChain
{
	const APPEND = 'append';
	const PREPEND = 'prepend';
	
	protected $filters = array();
	
	public function addFilter(FilterInterface $filter, $placement=self::APPEND) {
		if ($placement == self::APPEND)
			$this->filters[] = $filter;
		else
			array_unshift($this->filters, $filter);
		return $this;
	}
	
	public function appendFilter(FilterInterface $filter) {
		$this->addFilter($filter, self::APPEND);
		return $this;
	}
	
	public function prependFilter(FilterInterface $filter) {
		$this->addFilter($filter, self::PREPEND);
		return $this;
	}
	
	public function getFilters() {
		return $this->filters;
	}
	
	public function filter($value) {
		$result = $value;
		for ($i=0,$count=sizeof($this->filters); $i<$count; $i++)
			$result = $this->filters[$i]->filter($result);
		return $result;
	}
	
	public function filterArray($array) {
		$result = $array;
		foreach ($result as &$value)
			$value = $this->filter($value);
		return $result;
	}
}