<style type="text/css">
	div {
		font-family: Verdana;
		font-size: 11px;
		color: #000;
	}
	input, select, button {
		font-family: Verdana;
		font-size: 11px;
		color: #000;
	}
	div.test {
		color: white;
		background-color: black;
	}
	div.myclass {
		color: yellow;
		background-color: blue;
	}
	#output {
		width:550px;
		height:120px;
		border:1px solid #000;
		padding:4px;
		line-height: 20px;
		background-color: #f2f2f2;
		overflow:auto;
	}
	#buffer {
		width: 550px;
		height: 80px;
		border: 1px solid #000;
		padding: 4px;
	}
	#absolute {
		position: absolute;
		left: 5px;
		top: 5px;
		border: 1px solid #ff0000;
		background-color: #fff000;
		width: 100px;
		height: 100px;
		z-index: 1000;
	}
	#tglcls {
		height: 50px;
		width: 550px;
		padding: 4px;
	}
</style>
<script type="text/javascript">
	/**
	 * This function is called by the first button,
	 * at the right side of the first combo box
	 */
	function loadExample(ex) {
		// retrieve the element where output will be displayed
		var elm = $('output');
		switch (ex) {

			/**
			 * Array.walk example
			 */
			case 'array_iterate' :
				elm.clear();
				// iterate through the array elements
				// the iterator function receives the item and its index
				[1,2,3,4,5,6,7,8,9,10].walk(function(item, idx) {
					elm.insert("This is item idx " + idx + " : " + item + '<br />', 'bottom');
				});
				break;

			/**
			 * Array.map example
			 */
			case 'array_map' :
				elm.clear();
				// apply a function in each array element
				['a', 'b', 'c'].map(function(item, idx) {
					return item.toUpperCase();
				}).walk(function(item, idx) {
					elm.insert(item + '<br />', 'bottom');
				});
				break;

			/**
			 * Use cases of the Collection singleton
			 */
			case 'collection_functions' :
				var buf = "Base: [1, 2, 3, 4, 5]<br />";
				var base = [1, 2, 3, 4, 5];
				buf +=
					// apply a function, collecting true return values
					"Accept (>2 == true) : " + base.accept(function(item, idx) { return (item>2); }) + "<br />" +
					// apply a function, collecting false return values
					"Reject (>2 == false) : " + base.reject(function(item, idx) { return (item>2); }) + "<br />" +
					// apply a function, ignoring null return values
					"Valid (>2 == null) : " + base.valid(function(item, idx) { return (item>2?null:item); }) + "<br />" +
					// filter array values by a given regexp
					"Grep ^[345]$ : " + base.grep("^[345]$").serialize() + "<br />" +
					// search for a value in the collection
					"Contains 5? : " + (base.contains(5) ? 1 : 0);
				elm.update(buf);
				break;

			/**
			 * Use cases of the Array prototype extensions
			 */
			case 'array_functions' :
				var buf = "Array: [1, 2, 3, 4]<br />";
				var arr = [1, 2, 3, 4];
				buf +=
					// get first element
					"First: " + arr.first() + "<br />" +
					// get last element
					"Last: " + arr.last() + "<br />" +
					// check if the array is empty
					"Empty?: " + (arr.empty() ? 1 : 0) + "<br />" +
					// find the position of an element in the array
					"Index of '2': " + arr.indexOf(2) + "<br />" +
					// serialize the array
					"Serialize: " + arr.serialize();
				elm.update(buf);
				break;

			/**
			 * Demonstration of methods of Hash class
			 */
			case 'hash_functions' :
				var buf = "Hash: {'apples': 10, 'bananas' : 12, flag: true, position : 10, extra : null}<br />";
				// Create a hash object
				var hash = $H({'apples': 10, 'bananas' : 12, flag: true, position : 10, extra : null});
				buf +=
					// get hash keys
					"Keys: " + Object.serialize(hash.getKeys()) + "<br />" +
					// get hash values
					"Values: " + Object.serialize(hash.getValues()) + "<br />" +
					// find the value of a given hash property
					"Find value for 'bananas': " + hash.findValue('bananas') + "<br />" +
					// Convert the hash into a query string
					"Convert into query string: " + hash.toQueryString() + "<br />" +
					// serialize the hash
					"Serialize: " + hash.serialize();
				elm.update(buf);
				break;

			/**
			 * Use cases of the String prototype extensions
			 */
			case 'string_functions' :
				var buf = "Base: <b>php2go</b> javascript <i>framework</i><br />";
				var str = "<b>php2go</b> javascript <i>framework</i>";
				buf +=
					// encode the string to be used in a query string
					"Encoded: " + str.urlEncode() + "<br />" +
					// search for a given substring
					"Find (using indexOf): " + (str.find("java") ? 1 : 0) + "<br />" +
					// capitalize
					"Capitalized: " + str.stripTags().capitalize() + "<br />" +
					// padding
					"Pad left: " + str.pad('-', 50, 'left') + "<br />" +
					// escape HTML special chars
					"Escape HTML: " + str.escapeHTML() + "<br />" +
					// strip HTML tags
					"Strip tags: " + str.stripTags() + "<br />" +
					// strip additional spaces
					"Strip spaces: " + str.stripSpaces() + "<br />" +
					// serialize
					"Serialize : " + str.serialize();
				elm.update(buf);
		}
	}
	/**
	 * This function is called by the second button,
	 * at the right side of the second combo box
	 */
	function executeDHTML(val) {
		// retrieve the element where output will be displayed
		var elm = $('buffer');
		switch (val) {
			/**
			 * find elements by class name
			 */
			case "elements_by_class" :
				elm.clear();
				elm.update($('overall').getElementsByClassName('test'));
				break;
			/**
			 * find a parent element by tag name
			 */
			case "parent_by_tag" :
				alert(elm.getParentByTagName('body'));
				break;
			/**
			 * DOM insertAfter implementation
			 */
			case "insert_after" :
				// create a new DIV element, define inner HTML
				// contents and insert it after another element
				var div = $N('div');
				div.update("I'm a new element. Now is " + (new Date()).toGMTString());
				div.insertAfter(elm);
				break;
			/**
			 * get absolute position and dimensions
			 */
			case "position_dimension" :
				var abs = $('absolute');
				elm.clear();
				elm.update(
					Object.serialize(abs.getPosition()) + '<br />' +
					Object.serialize(abs.getDimensions())
				);
				break;
			/**
			 * get and set style properties
			 */
			case "get_style" :
				// get the element and read some style properties
				var abs = $('absolute');
				elm.clear();
				elm.update(
					"Display: " + abs.getStyle('display') + '<br />' +
					"Float: " + abs.getStyle('border-right-width') + '<br />' +
					"Opacity: " + abs.getOpacity()
				);
				break;
			case "set_style" :
				// get the element and change some style properties
				var abs = $('absolute');
				abs.setStyle('background-color', 'blue');
				abs.setStyle('border-right-width', '10px');
				abs.setOpacity(0.2);
				break;
			/**
			 * toggle display
			 */
			case "toggle_display" :
				// toggle display status of an element
				$('absolute').toggleDisplay();
				break;
			/**
			 * move
			 */
			case "move" :
				// move an element
				var abs = $('absolute');
				var pos = abs.getPosition();
				abs.moveTo(pos.x+30, pos.y+20);
				break;
			/**
			 * update, insert HTML
			 */
			case "add_html" :
				// clear inner HTML contents
				elm.clear();
				// add 2 HTML blocks in different positions
				elm.insert("<pre>loading...</pre>", "top");
				elm.insert("<b>please wait</b>", "bottom");
				// redefine HTML contents after 1 second
				setTimeout(function() {
					elm.update("done.");
				}, 1000);
				break;
		}
	}
</script>

<table id="overall" width="779" align="center" cellpadding="4" cellspacing="0" border="0">
  <tr><td>
  <form id="frm" method="post" action="">
    <h3>Base Classes &amp; Core extensions</h3>
    <select id="example" name="example" class="test">
	  <option value="">Choose an example</option>
	  <option value="array_iterate">Iterators: iterating a simple array</option>
	  <option value="array_map">Iterators: mapping a function to an array</option>
	  <option value="collection_functions">Collection methods</option>
	  <option value="array_functions">Array prototypes</option>
	  <option value="hash_functions">Hash methods</option>
	  <option value="string_functions">String prototypes</option>
	</select>&nbsp;
    <button id="go" type="button" onclick="loadExample($V('frm', 'example'))">Execute</button><br /><br />
	<div id="output"></div>
	<h3>DOM Manipulation</h3>
	<select id="dhtml" name="dhtml" class="test">
	  <option value="">Choose an option</option>
	  <option value="elements_by_class">getElementsByClassName</option>
	  <option value="parent_by_tag">getParentByTagName</option>
	  <option value="insert_after">insertAfter</option>
	  <option value="position_dimension">getPosition, getDimensions</option>
	  <option value="get_style">getStyle</option>
	  <option value="set_style">setStyle</option>
	  <option value="toggle_display">toggleDisplay</option>
	  <option value="move">move</option>
	  <option value="add_html">update, insert</option>
	</select>&nbsp;
	<button id="exec" type="button" onclick="executeDHTML($V('frm', 'dhtml'))">Execute</button><br /><br />
	<div id="buffer"></div><br />
	<div id="tglcls" class="test"
		onmouseover="$(this).toggleClass('myclass')"
		onmouseout="$(this).toggleClass('myclass')"
	>Hover me to toggle my CSS class</div>
	<div id="absolute">I'm absolute!</div>
  </form>
  </td></tr>
</table>