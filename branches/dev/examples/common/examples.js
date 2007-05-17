function testFunction() {
	alert('It worked!!!');
}

function jsrsTestReturn(returnString, request) {
	/**
	 * The callback function is called by the JSRS client, sending the return produced by the remote function
	 * and the jsrsContextObj, which represents the request. This object has some useful properties, like the
	 * requested file (url), requested remote function (func), parameters (params) and debug visibility (visible)
	 */
	alert("Request URL: " + request.url + "\nRequested function: " + request.func + "\nParameters: " + request.params + "\nReturn string: " + returnString);
}

function jsrsTest2Return(returnString, request) {
	/**
	 * This example illustrates how to process a formatted string containing a result set.
	 * The field object is retrieved using getElementById, the options collection is reduced to size 1
	 * and the string is loaded into the input using the createOptionsFromString function: an utility
	 * function that is able to process the serialized result set, splitting lines and columns using
	 * the proper separators and transforming them in HTML options
	 */
	var sel = new SelectField($('lookup_field'));
	sel.importOptions(returnString, '|', '~', 1);
	sel.fld.options[1].selected = true;
}