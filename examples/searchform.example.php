<?php

	// $Header: /www/cvsroot/php2go/examples/searchform.example.php,v 1.6 2006/08/23 03:00:37 mpont Exp $
	// $Revision: 1.6 $
	// $Date: 2006/08/23 03:00:37 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('config.example.php');
	import('php2go.base.Document');
	import('php2go.form.SearchForm');

	$db =& Db::getInstance();
	$tables = $db->getTables();
	if (!in_array('products', $tables)) {
		PHP2Go::raiseError("The <i>products</i> table was not found! Please run <i>products.sql</i>, located at the <i>ROOT/examples/resources</i> folder.<br>P.S.: The creation script was designed for mySQL databases.", E_USER_ERROR, __FILE__, __LINE__);
	}

	/**
	 * Set this to TRUE to always show search form
	 */
	$keepSearchVisible = TRUE;

	/**
	 * create and configure an instance of the class document, where the form will be included
	 */
	$doc = new Document('resources/layout.example.tpl');
	$doc->setCache(FALSE);
	$doc->setCompression();
	$doc->addStyle('resources/css.example.css');
	$doc->addBodyCfg(array('bgcolor'=>'#ffffff', 'style'=>'margin:0em'));
	$doc->setTitle('PHP2Go Example : php2go.form.SearchForm');
	$main =& $doc->createElement('main');

	/**
	 * in this example, we're using a GET parameter called `action` to tell the script
	 * if it must run the search form or show the search results.
	 * this routine is just illustrative, you can implement it in a great variety of ways...
	 */
	$action = HttpRequest::get('action');
	if ($keepSearchVisible || TypeUtils::isNull($action) || $action != 'list') {

		/**
		 * create an instance of SearchForm
		 * obs: when the second parameter (template) is NULL, the Form property will be an instance of the FormBasic class; otherwise, a FormTemplate will be created
		 */
		$search = new SearchForm('resources/searchform.example.xml', NULL, 'search', $doc);
		/**
		 * set the auto redirect properties
		 * � the first parameter is the flag, indicating that this search form will redirect to another URL when the search filters are valid
		 * � the second parameter is the redirect URL that must be loaded after the search clause is built
		 * � the third parameter is the param name that must be used to carry the search clause
		 * � the forth parameter indicates that the query clause must be persisted in the session scope; A FALSE value will make the query string available via $_GET in the target URL
		 */
		$search->setAutoRedirect(TRUE, HttpRequest::basePath() . '?action=list', 'filter', TRUE);
		/**
		 * define the minimun length required in search fields that use the string operators (STARTING, ENDING, CONTAINING)
		 */
		$search->setStringMinLength(2);
		if ($keepSearchVisible) {
			/**
			 * � when action is empty (new search), filters must be removed from the session
			 * � when action is not empty (list), we must tell SearchForm to preserve the search
			 *   filters in the session. we must do it, because the default behaviour of the class
			 *   is to destroy filters stored in the session scope when the search form is displayed
			 *   in its initial state (not posted)
			 */
			if (empty($action))
				$search->clearFilterPersistence();
			else
				$search->setPreserveSession(TRUE);
			$search->setFilterPersistence(TRUE);
		}
		/**
		 * configure the form interface: form width, labels width and CSS styles
		 */
		$search->Form->setFormWidth(550);
		$search->Form->setInputStyle('input_style');
		$search->Form->setLabelWidth(0.23);
		$search->Form->setLabelStyle('label_style');
		$search->Form->setButtonStyle('button_style');
		$search->Form->setErrorStyle('error_style', FORM_ERROR_BULLET_LIST, 'Some error(s) occurred while processing the form:', 'error_header');
		/**
		 * the `run` method verifies if the SearchForm is posted and is valid. if this condition is satisfied,
		 * the class builds the query string based on the configuration stored in each form field.
		 * if this method returns FALSE, the form was not posted or is not valid
		 */
		if (($searchString = $search->run()) === FALSE) {
			/**
			 * the getContent() method is just a shortcut to the getContent() method of the internal form instance
			 */
			$main->put($search->getContent(), T_BYVAR);
		} else {
			/**
			 * when using `auto redirect`, this else block won't be executed,
			 * because HttpResponse::redirect aborts the script execution.
			 * otherwise, entering this block indicates that a valid search
			 * query is available
			 */
			dumpVariable($searchString);
		}
	}

	/**
	 * read filter from the session scope
	 */
	$filter = HttpRequest::session('filter');
	$filterDesc = HttpRequest::session('filter_description');
	if (!empty($filter)) {

		/**
		 * add search results template in the document element
		 */
		$main->put('resources/searchformresults.template.tpl', T_BYFILE);
		$main->parse();
		/**
		 * show the search filter in the results (just for debug purposes)
		 */
		$main->assign('filter', $filter);
		$main->assign('filter_description', $filterDesc);
		/**
		 * create an instance of the QueryBuilder class, defining the fields and tables of the base query
		 * in the third parameter we add the filters stored in the session by the SearchForm as the condition clause
		 */
		$query = new QueryBuilder(
			"p.code, p.short_desc, p.price, p.amount",
			"products p",
			$filter,
			"", "2"
		);
		/**
		 * create the dataset and assign it on the template
		 */
		$dataset =& $query->createDataSet();
		$main->assign('results', $dataset);

	}

	/**
	 * display the HTML document
	 */
	$doc->display();

?>