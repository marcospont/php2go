<?php

/**
 * Perform the necessary imports
 */
import('php2go.base.Document');
import('php2go.datetime.Date');
import('php2go.net.HttpResponse');
import('php2go.net.Url');
import('php2go.util.System');

class MyDoc extends Document 
{
	function MyDoc() 
	{		
		/**
		 * All the HTML output must be initialized with an instance of Document class. This class
		 * generates the page skeleton (HTML, HEAD and BODY tags) and stores the references to the 
		 * page slots. In the execution chain, the user must define the content of these page slots
		 */
		Document::Document(TEMPLATE_PATH . 'doc_layout.tpl');
		Document::setCache(FALSE);
		Document::addBodyCfg('STYLE', 'margin: 0em');
		Document::addStyle(CSS_PATH . 'subscribe.css');
	}
	
	function display() 
	{
		/**
		 * Builds the slots and output the final page content.
		 * You can assign simple strings or DocumentElement instances to the entries of the array elements.
		 * The _buildMenu() method builds an instance of DocumentElement and returns a reference to it.
		 */
		$this->elements['header'] =& $this->_buildHeader();
		$this->elements['menu'] = '<center>Menu Slot</center>';
		$this->elements['footer'] = 'Footer Slot';
		Document::display();
	}
	
	function &_buildHeader() 
	{
		/**
		 * Build the header slot. This example shows how to assign date and system variables
		 * using the framework classes
		 */
		$header =& new DocumentElement();
		$header->put('<h1>PHP2Go Form Example</h1>', T_BYVAR);
		$header->put(TEMPLATE_PATH . 'header_layout.tpl', T_BYFILE);
		$header->parse();
		$header->assign('localDate', Date::localDate());
		$header->assign('systemOS', System::getOs());
		$header->assign('futureDate', Date::futureDate(Date::localDate(), 30));		
		return $header;
	}
}

?>