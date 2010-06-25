<?php

class ViewHelperPagination extends ViewHelper
{
	public function pagination(Paginator $paginator, $view) {		
		$this->view->renderPartial($view, array(
			'pages' => $paginator->getPages()
		));
	}
}