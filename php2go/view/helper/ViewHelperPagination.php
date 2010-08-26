<?php

class ViewHelperPagination extends ViewHelper
{
	public function pagination(Paginator $paginator, $view, array $data=array()) {
		$data = array_merge($data, array(
			'pages' => $paginator->getPages()
		));
		echo $this->view->renderPartial($view, $data);
	}
}