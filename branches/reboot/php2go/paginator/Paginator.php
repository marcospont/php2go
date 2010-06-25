<?php

class Paginator extends Component implements Countable, IteratorAggregate
{
	const DEFAULT_PAGE_SIZE = 10;
	const DEFAULT_PAGE_RANGE = 10;
	const SCROLLING_STYLE_ALL = 'all';
	const SCROLLING_STYLE_JUMPING = 'jumping';

	protected $pageNumber = 1;
	protected $pageCount;
	protected $pageSize;
	protected $pageRange;
	protected $pageVar = 'page';
	protected $scrollingStyle = self::SCROLLING_STYLE_ALL;
	protected $pages;
	protected $items;
	protected $itemCount;
	protected $adapter;

	public function __construct($data, array $options=array()) {
		$this->adapter = PaginatorAdapter::factory($data, $options);
		$this->pageSize = self::DEFAULT_PAGE_SIZE;
		$this->pageRange = self::DEFAULT_PAGE_RANGE;
		$this->loadOptions($options);
	}

	protected function loadOptions(array $options) {
		$request = Php2Go::app()->getRequest();
		if (isset($options['pageRange']))
			$this->setPageRange($options['pageRange']);
		if (isset($options['pageSize']))
			$this->setPageSize($options['pageSize']);
		if (isset($options['pageNumber']))
			$this->setPageNumber($options['pageNumber']);
		elseif (($page = $request->getQuery($this->pageVar)))
			$this->setPageNumber($page);
		if (isset($options['scrollingStyle']))
			$this->setScrollingStyle($options['scrollingStyle']);
	}

	public function getPageNumber() {
		return $this->pageNumber;
	}

	public function setPageNumber($page) {
		$this->pageNumber = $this->normalizePageNumber($page);
		$this->items = null;
		$this->itemCount = null;
	}

	public function count() {
		return $this->getPageCount();
	}

	public function getPageCount() {
		if (!$this->pageCount)
			$this->pageCount = $this->calculatePageCount();
		return $this->pageCount;
	}

	public function getPageSize() {
		return $this->pageSize;
	}

	public function setPageSize($size) {
		$this->pageSize = $this->normalizePageSize($size);
		$this->pageCount = $this->getPageCount();
		$this->items = null;
		$this->itemCount = null;
	}

	public function getPageRange() {
		return $this->pageRange;
	}

	public function setPageRange($range) {
		$this->pageRange = $range;
	}

	public function setScrollingStyle($style) {
		if (!in_array($style, array(self::SCROLLING_STYLE_ALL, self::SCROLLING_STYLE_JUMPING)))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid paginator scrolling style.'));
		$this->scrollingStyle = $style;
	}

	public function getPages() {
		if (!$this->pages)
			$this->pages = $this->createPages();
		return $this->pages;
	}

	public function getItems() {
		if (!$this->items)
			$this->items = $this->getItemsByPage($this->pageNumber);
		return $this->items;
	}

	public function getIterator() {
		return $this->getItems();
	}

	public function getAbsoluteItemNumber($item, $page=null) {
		$item = $this->normalizeItemNumber($item);
		if ($page === null)
			$page = $this->pageNumber;
		else
			$page = $this->normalizePageNumber($page);
		return (($page - 1) * $this->pageSize) + $item;
	}

	public function getItem($item, $page=null) {
		if ($page === null)
			$page = $this->pageNumber;
		elseif ($page < 0)
			$page = ($this->getPageCount() + 1) + $page;
		$items = $this->getItemsByPage($page);
		$itemCount = $this->calculateItemCount($items);
		if ($itemCount == 0)
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'The page %d does not exist.', array($page)));
		if ($item < 0)
			$item = ($itemCount + 1) + $item;
		else
			$item = $this->normalizeItemNumber($item);
		if ($item > $itemCount)
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'The item %d does not exist on page %d.', array($item, $page)));
		return $items[$item-1];
	}

	public function getItemCount() {
		return $this->calculateItemCount($this->getItems());
	}

	public function getTotalItemCount() {
		return count($this->adapter);
	}

	public function getItemsByPage($page) {
		$page = $this->normalizePageNumber($page);
		$offset = ($page - 1) * $this->pageSize;
		$items = $this->adapter->getItems($offset, $this->pageSize);
		if (!$items instanceof Traversable)
			$items = new ArrayIterator($items);
		return $items;
	}

	public function toJson() {
		return Json::encode($this->getItems());
	}

	protected function normalizePageNumber($number) {
		if ($number < 1)
			$number = 1;
		$count = $this->getPageCount();
		if ($count == 0)
			$number = 1;
		elseif ($number > $count)
			$number = $count;
		return $number;
	}

	protected function normalizePageSize($size) {
		if ($size < 1)
			$size = 1;
		$count = count($this->adapter);
		if ($size > $count)
			$size = $count;
		return $size;
	}

	protected function normalizeItemNumber($number) {
		if ($number < 1)
			$number = 1;
		if ($number > $this->pageSize)
			$number = $this->pageSize;
		return $number;
	}

	protected function calculatePageCount() {
		return intval(ceil(count($this->adapter) / $this->pageSize));
	}

	protected function calculateItemCount($items) {
		if (is_array($items) || $items instanceof Countable)
			return count($items);
		else
			return iterator_count($items);
	}

	protected function createPages() {
		$pageNumber = $this->getPageNumber();
		$pageCount = $this->getPageCount();
		$pages = new stdClass();
		$pages->pageCount = $pageCount;
		$pages->pageSize = $this->getPageSize();
		$pages->first = 1;
		$pages->current = $pageNumber;
		$pages->last = $pageCount;
		if (($pageNumber - 1) > 0)
			$pages->previous = $pageNumber - 1;
		if (($pageNumber + 1) <= $pageCount)
			$pages->next = $pageNumber + 1;
		$pages->pageRange = $this->applyScrollingStyle();
		$pages->firstPageInRange = min($pages->pageRange);
		$pages->lastPageInRange = max($pages->pageRange);
		$pages->itemCount = $this->getItemCount();
		$pages->totalItemCount = $this->getTotalItemCount();
		$pages->firstItemNumber = (($pageNumber - 1) * $pages->itemCount) + 1;
		$pages->lastItemNumber = ($pages->firstItemNumber + $pages->itemCount - 1);
		return $pages;
	}

	protected function applyScrollingStyle() {
		$pages = array();
		switch ($this->scrollingStyle) {
			case 'all' :
				$lower = 1;
				$upper = $this->getPageCount();
				break;
			case 'jumping' :
				$delta = ($this->pageNumber % $this->pageRange);
				if ($delta == 0)
					$delta = $this->pageRange;
				$lower = $this->normalizePageNumber(($this->pageNumber - $delta) + 1);
				$upper = $this->normalizePageNumber($lower + $this->pageRange - 1);
				break;
		}
		$pages = array();
		for ($i=$lower; $i<=$upper; $i++)
			$pages[$i] = $i;
		return $pages;
	}
}