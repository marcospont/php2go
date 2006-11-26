<!-- CSS rules -->
<style type="text/css">
	label, legend, div {
		font-family: Verdana, Helvetica;
		font-size: 12px;
	}
	label, legend {
		font-weight: bold;
		color: #444;
	}
	button, input, select, textarea {
		font-family: Verdana, Helvetica;
		font-size: 11px;
	}
	input:focus, select:focus, textarea:focus {
		background-color: #ffffcc;
	}
	fieldset {
		background-color: #e0ecff;
		padding: 6px;
		height: 365px;
	}
	fieldset table {
		background-color: #e0ecff;
	}
	#msg {
		display:none;
		margin:5px;
		padding:4px;
		border:1px solid #bbb;
		background-color: #b5edbc;
	}
	#people_list div, #add {
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
	.error {
		display:none;
		margin:5px;
		padding:8px;
		border:1px solid #ff0000;
		background-color: #f0c2c2;
		color: #ff0000;
	}
	.error_header {
		color: #000;
		font-weight: bold;
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
		Form.reset($('form'));
		// focus first field
		Form.focusFirstField($('form'));
	}

	/**
	 * Performs an AJAX request to load data from the database to the form
	 */
	function loadPerson(id) {
		// hide message
		$('msg').hide();
		// create/send request
		var request = new AjaxRequest(document.location.pathname, {
			params: {action:"load",id_people:id},
			throbber: "throbber_load",
			onJSONResult: function(response) {
				if (response.json.name) {
					// reset form
					Form.reset($('form'));
					// apply field values
					for (var name in response.json) {
						try {
							if (response.json[name] !== null)
								$F(name).setValue(response.json[name]);
						} catch(e) {
						}
					}
					$('id_people').value = id;
					// focus first field
					Form.focusFirstField('form');
				} else {
					$('msg').update("Error loading record");
				}
			}
		});
		request.send();
	}

	/**
	 * Routine used to request the deletion of a given person
	 */
	function deletePerson(id) {		
		// display confirmation dialog
		if (!confirm("Are you sure?"))
			return;
		var msg = $('msg');
		// hide message
		msg.hide();
		// reset form if we're deleting the record being edited
		if (id == $('id_people').value) {
			$('id_people').value = '';
			Form.reset($('form'));
		}
		// create/send request
		var request = new AjaxUpdater(document.location.pathname, {
			params: {action:"delete",id_people:id},
			container: 'people_list',
			throbber: 'throbber_save',
			onJSONResult: function(response) {
				msg.update(response.json);
				msg.show();
			}
		});
		request.send();
	}
	
    /**
     * Used to verify if at least one checkbox is checked
     */
	function verifyPersonBoxes() {
		// verify if at least one box is checked
    	var val = $V('listForm', 'chk[]');
    	if (!val) {
    		alert("Please select at least one person!");
    		return false;
    	}
    	// display confirmation dialog
    	if (!confirm("Are you sure you want to delete " + val.length + " person record(s)?"))
    		return false;
    	// reset form if we're deleting the record being edited
    	if (val.indexOf($('id_people').value) != -1) {
			$('id_people').value = '';
			Form.reset($('form'));    		
    	}
    	return true;
    }	

</script>

<!-- throbbers -->
<div id="throbber_save" style="position:absolute;display:none;background-color:#fff;border:1px solid gray;padding:4px;width:180px;text-align:center">
  <p class="sample_simple_text"><img src="{$p2g.const.PHP2GO_ICON_PATH}indicator.gif" border="0" align="top" alt="">&nbsp;Sending data...</p>
</div>
<div id="throbber_load" style="position:absolute;display:none;background-color:#fff;border:1px solid gray;padding:4px;width:180px;text-align:center">
  <p class="sample_simple_text"><img src="{$p2g.const.PHP2GO_ICON_PATH}indicator.gif" border="0" align="top" alt="">&nbsp;Loading data...</p>
</div>

<!-- page layout -->
<div id="overall" style="width:780px">
  <div id="msg"></div>
  <div id="formLayer" style="float:left;width:440px;">{$form}</div>
  <div id="listLayer" style="float:right;width:340px;">
    <form id="listForm" name="listForm" action="" method="POST" style="display:inline">
    <fieldset>
      <legend>People List</legend>
      <div id="add"><button id="btnAdd" name="add" type="button" onClick="prepareAddPerson()">New</button>&nbsp;</div>
      <div id="operations">
        <select id="operation" name="operation">
          <option value="delete">Delete selected</option>
        </select>&nbsp;<button id="btnOperate" name="operate" type="submit" onClick="return verifyPersonBoxes();">Ok</button>
      </div><br style="clear:both;">
      <div id="people_list" style="border:1px solid #bfbfc9;height:310px;overflow:auto;">
        {$list}
      </div>
    </fieldset>
    </form>
    <script type="text/javascript">
		Form.ajaxify($('listForm'), function() {
			var ajax = new AjaxUpdater('ajaxcrud.example.php', {
				container: 'people_list',
				throbber: 'throbber_save',
				params: {action: 'multiple'},
				method: 'POST',
				onJSONResult: function(response) { 
					var msg = $('msg');
					msg.update(response.json);
					msg.show();
				}
			});
			ajax.send();
		});
    </script>
  </div>
</div>