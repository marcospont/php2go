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
		display:none;
		margin:5px;
		padding:4px;
		border:1px solid #bbb;
		background-color: #b5edbc;
	}
	#overall {
		width: 780px;
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
	 * Routine used to request the deletion of a given person
	 */
	function deletePerson(id) {
		// display confirmation dialog
		if (confirm('Are you sure?')) {
			// hide message
			$('msg').hide();
			// create/send request
			var service = new AjaxService(document.location.pathname, {
				params: { id_people: id, current_loaded: $('id_people').value },
				handler: 'deleteRecord',
				throbber: 'throbber_save'
			});
			service.send();
		}
	}

    /**
     * Used to verify if at least one checkbox is checked
     */
	function verifyPersonBoxes() {
		// verify if at least one box is checked
		var val = $V('list_form', 'chk[]');
		if (!val) {
			alert('Please select at least one person!');
			return false;
		}
		// display confirmation dialog
		if (!confirm('Are you sure you want to delete ' + val.length + ' person record(s)?'))
			return false;
		// reset form if we're deleting the record being edited
		if (val.indexOf($('id_people').value) != -1) {
			$('id_people').value = '';
			Form.reset($('people_form'));
		}
		return true;
    }
	
</script>

<!-- throbbers -->
<div id="throbber_save">
  <p class="sample_simple_text"><img src="{$p2g.const.PHP2GO_ICON_PATH}indicator.gif" border="0" align="top" alt="" />&nbsp;Sending data...</p>
</div>
<div id="throbber_load">
  <p class="sample_simple_text"><img src="{$p2g.const.PHP2GO_ICON_PATH}indicator.gif" border="0" align="top" alt="" />&nbsp;Loading data...</p>
</div>

<!-- page layout -->
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
        </select>&nbsp;<button id="btn_operation" name="btn_operation" type="submit" onclick="return verifyPersonBoxes();">Ok</button>
      </div><br style="clear:both;" />
      <div id="list_container">
        {$list}
      </div>
    </fieldset>
    </form>
    <script type="text/javascript">
		/**
		 * Prepares the 'list_form' form to be submitted through an AJAX call
		 */
		Form.ajaxify($('list_form'), function() {
			var ajax = new AjaxService(document.location.pathname, {
				handler: 'multiple',
				throbber: 'throbber_save'
			});
			return ajax;
		});
    </script>
  </div>
</div>