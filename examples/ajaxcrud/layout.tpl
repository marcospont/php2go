<!-- CSS rules -->
<style type="text/css">
	label, legend, div { font-family: Verdana, Helvetica; font-size: 12px; }
	label, legend { font-weight: bold; color: #444; }
	button, input, select, textarea { font-family: Verdana, Helvetica; font-size: 11px; }
	input:focus, select:focus, textarea:focus { background-color: #ffffcc; }
	fieldset { background-color: #e0ecff; padding: 6px; height: 365px; }
	fieldset table { background-color: #e0ecff; }
	.error { display:none; margin:5px; padding:8px; border:1px solid #ff0000; background-color: #f0c2c2; color: #ff0000; }
	.error_header { color: #000; font-weight: bold; }	
	#throbber_load, #throbber_save {
		position: absolute;
		display: none;
		background-color: #fff;
		border: 1px solid gray;
		padding: 4px;
		width: 180px;
		text-align: center;
	}
	#msg {
		display: none;
		margin: 5px;
		padding: 4px;
		border: 1px solid #bbb;
		background-color: #b5edbc;
	}
	#overall {
		width: 780px;
		text-align: left;
		margin-top: 40px;
	}
	#form_layer {
		float: left;
		width: 440px;
	}
	#list_layer {
		float: right;
		width: 340px;
	}
	#list_container {
		border: 1px solid #bfbfc9;
		height: 310px;
		overflow: auto;
	}
	#list_container div, #add {
		padding : 4px;
	}
	#add {
		float: left;
		width: auto;
	}
	#operations {
		float: right;
		width: auto;
		text-align: right;
		padding: 4px;
	}
	.confDialog {
		border: 1px solid #000;
		background-color: #ff0000;
		color: #fff;
		font-weight: bold;
		padding: 6px;
	}
	.confDialog div {
		padding: 3px;
		line-height: 18px;
		text-align: center;
	}
</style>

<!-- JS code -->
<script type="text/javascript">

	/**
	 * Routine used to enable people form for adding a new record
	 */
	function prepareAddPerson() {
		// hide message
		$('msg').hide();
		// clear pk hidden field
		$('id_people').value = '';
		// reset form
		Form.reset($('people_form'));
		// focus first field
		Form.focusFirstField($('people_form'));
	}
	
	/**
	 * Performs an AJAX request to load data from the database to the form
	 */
	function loadPerson(id) {
		// hide message
		$('msg').hide();
		// run service
		var service = new AjaxService(document.location.pathname, {
			params: { id_people: id },
			handler: 'loadRecord',
			throbber: 'throbber_load'
		});
		service.send();
	}

	/**
	 * Shows delete confirmation dialog
	 */
	function showDeletePersonDialog(id) {
		confDialog.setButtonAction(0, function() { deletePerson(id); });
		confDialog.setContents('This person will be removed. Continue?');
		confDialog.open();
	}
	
	/**
	 * Routine used to request the deletion of a given person
	 */
	function deletePerson(id) {
		// hide message
		$('msg').hide();
		// create/send request
		var service = new AjaxService(document.location.pathname, {
			params: { id_people: id, current_loaded: $('id_people').value },
			handler: 'deleteRecord',
			throbber: 'throbber_save'
		});
		confDialog.close();		
		service.send();		
	}
	
	/**
	 * Shows delete confirmation dialog
	 */
	function showDeletePeopleDialog() {
		// verify if at least one box is checked
		var val = $V('list_form', 'chk[]');
		if (val) {
			confDialog.setButtonAction(0, function() { deletePeople(val); });
			confDialog.setContents('You\'re about to delete ' + val.length + ' person record(s).<br/>Click Ok to proceed. Otherwise, click Cancel.');
			confDialog.open();
		}
    }
    
    /**
     * Executes an AJAX service to delete the selected people
     */
    function deletePeople(val) {
		// reset form if we're deleting the record being edited
		if (val.indexOf($('id_people').value) != -1) {
			$('id_people').value = '';
			Form.reset($('people_form'));
		}
		var service = new AjaxService(document.location.pathname, {
			form: 'list_form',
			handler: 'multiple',
			throbber: 'throbber_save'
		});
		confDialog.close();
		service.send();		
    }
    

	/**
	 * Setup confirmation dialog
	 */
	var confDialog = null;
    Event.addLoadListener(function() {
    	confDialog = new ModalDialog({
    		contentsClass: 'confDialog',    		
    		opacity: 0.4,
    		overlayColor: '#333',
    		buttons: [
    			['Ok'],
    			['Cancel', function() { this.close(); }, true]
    		]
    	});
    	confDialog.setup();
    });
	
</script>

<!-- throbbers -->
<div id="throbber_save">
  <p class="sample_simple_text"><img src="{$p2g.const.PHP2GO_ICON_PATH}indicator.gif" border="0" align="top" alt="" />&nbsp;Sending data...</p>
</div>
<div id="throbber_load">
  <p class="sample_simple_text"><img src="{$p2g.const.PHP2GO_ICON_PATH}indicator.gif" border="0" align="top" alt="" />&nbsp;Loading data...</p>
</div>

<!-- page layout -->
<center>
<div id="overall">
  <div id="msg"></div>
  <div id="form_layer">{$form}</div>
  <div id="list_layer">
    <form id="list_form" name="list_form" action="" method="post" style="display:inline">
    <fieldset>
      <legend>People List</legend>
      <div id="add">
	    <button id="btn_add" name="btn_add" type="button" onclick="prepareAddPerson()">New</button>&nbsp;
	  </div>
      <div id="operations">
        <select id="operation" name="operation">
          <option value="delete" selected="selected">Delete selected</option>
        </select>&nbsp;<button id="btn_operation" name="btn_operation" type="button" onclick="showDeletePeopleDialog();">Ok</button>
      </div><br style="clear:both;" />
      <div id="list_container">
        {$list}
      </div>
    </fieldset>
    </form>
  </div>
</div>
</center>