<?php

class ViewHelperGoogle extends ViewHelper
{
	protected $analyticsId;


	public function setAnalyticsId($id) {
		$this->analyticsId = $id;
	}

	public function analytics($id=null) {
		$id = ($id !== null ? $id : $this->analyticsId);
		if (!empty($id)) {
			$this->view->scriptBuffer()->add(
				"var gaJsHost = (('https:' == document.location.protocol) ? 'https://ssl.' : 'http://www.');\n" .
				"document.write(unescape('%3Cscript src=\"' + gaJsHost + 'google-analytics.com/ga.js\" type=\"text/javascript\"%3E%3C/script%3E'));\n" .
				"try{\n" .
				"var pageTracker = _gat._getTracker('{$id}');\n" .
				"pageTracker._trackPageview();\n" .
				"} catch(err) {}"
			);
		}
	}
}