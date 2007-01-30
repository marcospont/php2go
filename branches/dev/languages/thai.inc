<?php
/**
 * PHP2Go Web Development Framework
 *
 * Copyright (c) 2002-2006 Marcos Pont
 *
 * LICENSE:
 *
 * This library is free software; you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General
 * Public License as published by the Free Software Foundation;
 * either version 2.1 of the License, or (at your option) any
 * later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @author Trirat Petchsingh <rosskouk@gmail.com>
 * @copyright 2002-2006 Marcos Pont
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version $Id$
 */

$P2G_LANGUAGE['ERR_OLD_PHP_VERSION'] = "PHP2Go can't run with the current settings of the server.<BR>You PHP version is <I>%s</I>. The minimum requirement of the framework is <I>%s</I>.<BR>Install an equal or greater version to run it normally.";
$P2G_LANGUAGE['ERR_UNSUPPORTED_EXTENSION'] = "The extension <I>%s</I> is not supported by the current PHP installation.";
$P2G_LANGUAGE['ERR_OBJ_MESSAGE'] = "Error in class %s:<BR>%s";
$P2G_LANGUAGE['ERR_SCRIPT_MESSAGE'] = "Error in script:<BR>%s";
$P2G_LANGUAGE['ERR_DATABASE'] = "Database Error";
$P2G_LANGUAGE['ERR_INVALID_VALUE'] = "Invalid Value!";
$P2G_LANGUAGE['ERR_DUPLICATED_VALUE'] = "This value was already inserted!";
$P2G_LANGUAGE['ERR_COMM_FAILURE'] = "Communication failure";
$P2G_LANGUAGE['ERR_MISSING_DATABASE_PARAMETERS'] = "It wasn't possible to load the database connection parameters! [ID: %s]";
$P2G_LANGUAGE['ERR_INVALID_DATABASE_PARAMETERS'] = "The database connection parameters are invalid! The entries USER, BASE and TYPE are mandatory. [ID: %s]";
$P2G_LANGUAGE['ERR_DATABASE_CONNECTION_FAILED'] = "It wasn't possible to connect to the database with the current access settings. Are they correct?";
$P2G_LANGUAGE['ERR_INVALID_CONNECTION_CLASS_PATH'] = "It wasn't possible to import and/or instantiate the connection class %s!";
$P2G_LANGUAGE['ERR_INVALID_CONNECTION_CLASS'] = "The connection class %s isn't a subclass of php2go.db.Db!";
$P2G_LANGUAGE['ERR_TYPES'] = array(
	1 => "Fatal Error",
	2 => "Warning",
	4 => "Syntax Error",
	8 => "Notice",
	16 => "Core Fatal Error",
	32 => "Core Warning",
	64 => "Compiler Fatal Error",
	128 => "Compiler Warning",
	256 => "User Error",
	512 => "User Warning",
	1024 => "User Notice"
);
$P2G_LANGUAGE['ERR_ABSOLUTE_URI_NOT_FOUND'] = "The 'ABSOLUTE_URI' entry wasn't set in the global configuration array!";
$P2G_LANGUAGE['ERR_CONFIG_ENTRY_NOT_FOUND'] = "The <I>%s</I> entry wasn't set in the global configuration array!";
$P2G_LANGUAGE['ERR_INVALID_LANGDOMAIN_FILE'] = "The messages file for the %s domain, language %s, is invalid or doesn't exist.";
$P2G_LANGUAGE['ERR_CANT_FIND_CFG_VAL'] = "It wasn't possible to find the entry <I>%s</I> in the global configuration array. Was it correctly set in the installation?";
$P2G_LANGUAGE['ERR_CANT_LOAD_MODULE'] = "It wasn't possible to load the module <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_LOAD_DIR'] = "It wasn't possible to load modules from a valid folder at <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_LOAD_DIR_MODULE'] = "It wasn't possible to load the file <I>%s</I> at <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_CONVERT_DATE'] = "It wasn't possible to convert the date to the format %s.";
$P2G_LANGUAGE['ERR_CANT_RUN_ON_WINDOWS'] = "The class %s can be used under Windows!";
$P2G_LANGUAGE['ERR_UNDEFINED_FUNCTION'] = "The function %s is not defined!";
$P2G_LANGUAGE['ERR_UNDEFINED_METHOD'] = "The method %s is not defined in the %s class!";
$P2G_LANGUAGE['ERR_ABSTRACT_CLASS'] = "The class <I>%s</I> is abstract and can't be instantiated directly!";
$P2G_LANGUAGE['ERR_DATE_FORMAT'] = "The date %s is not in the required format %s.";
$P2G_LANGUAGE['ERR_INVALID_RESOURCE'] = "The variable %s passed to the function or method %s is not a valid resource.";
$P2G_LANGUAGE['ERR_INVALID_AUTHENTICATOR'] = "The authenticator %s isn't a subclass of php2go.auth.Auth!";
$P2G_LANGUAGE['ERR_INVALID_AUTHENTICATOR_PATH'] = "It wasn't possible to import and/or instantiate the authenticator %s!";
$P2G_LANGUAGE['ERR_INVALID_USERCONTAINER'] = "The user container %s isn't a subclass of php2go.auth.User!";
$P2G_LANGUAGE['ERR_INVALID_USERCONTAINER_PATH'] = "It wasn't possible to import and/or instantiate the user container %s!";
$P2G_LANGUAGE['ERR_INVALID_AUTHORIZER'] = "The authorizer %s isn't a subclass of php2go.auth.Authorizer!";
$P2G_LANGUAGE['ERR_INVALID_AUTHORIZER_PATH'] = "It wasn't possible to import and/or instantiate the authorizer %s!";
$P2G_LANGUAGE['ERR_XML_PARSE'] = "An error ocurred in the parsing of the XML file:<BR>Error: %s<BR>Line: %s, Column: %s";
$P2G_LANGUAGE['ERR_STATEMENT_EVAL'] = "An error occurred while parsing the PHP code <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_FIND_FILE'] = "It wasn't possible to find the file or folder %s. Does it exist?";
$P2G_LANGUAGE['ERR_CANT_OPEN_FILE'] = "It wasn't possible to open the file %s with the mode %s. Check the file permissions and the used mode.";
$P2G_LANGUAGE['ERR_CANT_CREATE_FILE'] = "It wasn't possible to create the file or folder %s. Check the parent folder permissions.";
$P2G_LANGUAGE['ERR_CANT_READ_FILE'] = "It wasn't possible to open the file %s for reading. Does it exist? Is the path correct?";
$P2G_LANGUAGE['ERR_CANT_WRITE_FILE'] = "It wasn't possible to open the file or folder %s for writing. Do you have permissions to overwrite it?";
$P2G_LANGUAGE['ERR_CANT_DELETE_FILE'] = "It wasn't possible to delete the file %s.<BR>Do you have write permissions in this folder? Does the file have permissions to be deleted?";
$P2G_LANGUAGE['ERR_CANT_INCLUDE_FILE'] = "It wasn't possible to open the file %s for inclusion. Does it exists? Is the path correct?";
$P2G_LANGUAGE['ERR_CANT_CHANGE_MODE'] = "It wasn't possible to apply the mode %s to the file or folder %s.";
$P2G_LANGUAGE['ERR_CANT_READ_DIR'] = "It wasn't possible to find the directory %s. Does it exist?";
$P2G_LANGUAGE['ERR_CANT_OPEN_DIR'] = "It wasn't possible to open the folder %s for reading. Check its permissions.";
$P2G_LANGUAGE['ERR_CANT_FIND_SERIALIZATION_FILE'] = "It wasn't possible to open the file containing the serialized object for reading. Is the path correct? Was the file created?";
$P2G_LANGUAGE['ERR_EMPTY_INSERT_QUERY'] = "The insert query in the table '%s' returned an empty set using the provided values.";
$P2G_LANGUAGE['ERR_EMPTY_UPDATE_QUERY'] = "The update query in the table '%s' with the clause %s returned an empty set using the provided values.";
$P2G_LANGUAGE['ERR_EMPTY_TOGLOBALS_QUERY'] = "The query %s, used to publish variables in the global scope, returned an empty set.";
$P2G_LANGUAGE['ERR_TOGLOBALS_WRONG_USAGE'] = "The query passed to the method Db::toGlobals() must start with 'SELECT' (DQL).";
$P2G_LANGUAGE['ERR_EMPTY_TEMPLATE_FILE'] = "It wasn't possible to initialize the Template with an empty file name!";
$P2G_LANGUAGE['ERR_EMPTY_TEMPLATE_CONTENT'] = "The template wasn't prepared, because its content is empty. Check the content passed to the constructor.";
$P2G_LANGUAGE['ERR_CANT_FIND_VARIABLE'] = "It wasn't possible to find the variable %s in the template <I>%s</I>. <BR>To get the correct results from this tool, insert {%s} in the template code.";
$P2G_LANGUAGE['ERR_CANT_FIND_BLOCK'] = "It wasn't possible to find the block <I>%s</I> for creating. Is it declared in the template?";
$P2G_LANGUAGE['ERR_CANT_FIND_BLOCK_REUSE'] = "It wasn't possible to find the block %s to reuse it as %s.";
$P2G_LANGUAGE['ERR_CANT_PUT_ON_PREPARED_TEMPLATE'] = "The element %s was already configured with the method DocumentElement::parse(). It's not possible to add elements with the method put().";
$P2G_LANGUAGE['ERR_TEMPLATE_NOT_PREPARED'] = "The template %s wasn't prepared. Run the method %s::parse(); before executing operations with the object.";
$P2G_LANGUAGE['ERR_TEMPLATE_ALREADY_PREPARED'] = "This template has already been prepared. You must call parse() method only once.";
$P2G_LANGUAGE['ERR_TPLPARSE_UNBALANCED_BLOCKDEF'] = "The definition of the block <I>%s</I> is unbalanced!";
$P2G_LANGUAGE['ERR_TPLPARSE_INCOMPLETE_BLOCKDEF'] = "The specification of the block <I>%s</I> is incomplete inside the tag %s!";
$P2G_LANGUAGE['ERR_TPLPARSE_BLOCKINSIDELOOP'] = "It's not possible to define dynamic blocks inside loops!";
$P2G_LANGUAGE['ERR_TPLPARSE_UNBALANCED_TAG'] = "The definition of the tag <I>%s</I> is unbalanced!";
$P2G_LANGUAGE['ERR_TPLPARSE_UNEXPECTED_TAG'] = "Unexpected tag <I>%s</I>!";
$P2G_LANGUAGE['ERR_TPLPARSE_UNBALANCED_PARENTHESIS'] = "Unbalanced parenthesis inside the expression <I>%s</I>!";
$P2G_LANGUAGE['ERR_TPLPARSE_TAG_SYNTAX'] = "The definition of the tag <I>%s</I> is invalid : %s!";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_TOKEN'] = "Invalid token inside the expression <I>%s</I> : %s";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_ATTRIBUTE'] = "Missing or invalid attribute value!";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_ATTRIBUTENAME'] = "<I>%s</I> isn't a valid attribute name!";
$P2G_LANGUAGE['ERR_TPLPARSE_REQUIRED_ATTRIBUTE'] = "The property <I>%s</I> is required in the definition of %s tag!";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_TAG_ATTRIBUTE'] = "The property <I>%s</I> is invalid in the definition of %s tag!";
$P2G_LANGUAGE['ERR_TPLPARSE_MISSING_ASSIGN'] = "The character '=' wasn't found after the attribute name!";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_IS_EXPR'] = "Invalid operation for an 'is' operation : <I>%s</I>";
$P2G_LANGUAGE['ERR_TPLPARSE_MISSING_TAG_ARGS'] = "The tag <I>%s</I> requires arguments!";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_TAG_ARGS'] = "The tag <I>%s</I> can't contain arguments!";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_MODIFIER'] = "The modifier <I>%s</I> is invalid or inexistent!";
$P2G_LANGUAGE['ERR_WIDGET_INCLUDE'] = "The widget <i>%s</i> can only be used in INCLUDE WIDGET tags!";
$P2G_LANGUAGE['ERR_WIDGET_MANDATORY_PROPERTY'] = "The property <i>%s</i> is mandatory in the definition of the <i>%s</i> widget!";
$P2G_LANGUAGE['ERR_EMPTY_DOC_LAYOUT'] = "There are no elements defined in the provided document layout.";
$P2G_LANGUAGE['ERR_CANT_REPLICATE_ROOT_BLOCK'] = "The base block of a template can't be overwritten.";
$P2G_LANGUAGE['ERR_MUST_BE_POSITIVE'] = "The value of %s must be greater than zero in the function %s.";
$P2G_LANGUAGE['ERR_MUST_BE_AN_ARRAY'] = "The value of %s must be an array in the function %s";
$P2G_LANGUAGE['ERR_VALUE_OUT_OF_BOUNDS'] = "The value of '%s' is out of the allowed bounds: %s and %s";
$P2G_LANGUAGE['ERR_URL_MALFORMED'] = "The value of <I>%s</I> is not a valid URL!";
$P2G_LANGUAGE['ERR_HOST_INVALID'] = "<I>%s</I> is not a valid connection host!";
$P2G_LANGUAGE['ERR_DNS_NOT_FOUND'] = "It wasn't possible to find a valid DNS to the domain %s.";
$P2G_LANGUAGE['ERR_SESSION_PROPERTY_NOT_FOUND'] = "The property %s of the session variable %s wasn't found. Execute the createProperty() function";
$P2G_LANGUAGE['ERR_SESSION_TIMECOUNTER_NOT_FOUND'] = "The time counter %s of the session variable %s wasn't found. Execute the createTimeCounter() function";
$P2G_LANGUAGE['ERR_INVALID_OBJECT'] = "The object passed as a parameter is not a valid instance of the class <I>%s</I>!";
$P2G_LANGUAGE['ERR_CALCULATOR_MISSING_PARAMETERS'] = "Insufficient parameters to build the calculator!";
$P2G_LANGUAGE['ERR_COLORSEL_MISSING_PARAMETERS'] = "Insufficient parameters to build the color selection!";
$P2G_LANGUAGE['ERR_INVALID_FORM_METHOD'] = "The method %s is invalid to submit the form '%s'";
$P2G_LANGUAGE['ERR_INVALID_VALIDATE_FUNC'] = "<I>%s</I> is not a valid name for a validation function!";
$P2G_LANGUAGE['ERR_FORM_DUPLICATED_FIELD'] = "The field <I>%s</I> is already defined in the form <I>%s</I>!";
$P2G_LANGUAGE['ERR_FORM_DUPLICATED_BUTTON'] = "The button <I>%s</I> is already defined in the form <I>%s</I>!";
$P2G_LANGUAGE['ERR_FORM_DUPLICATED_SECTION'] = "The section ID <I>%s</I> was already defined in the form <I>%s</I>!";
$P2G_LANGUAGE['ERR_FORM_SECTION_EVALFUNC'] = "It wasn't possible to define the visibility of the section <I>%s</I>, because the <I>%s</I> function is not defined!";
$P2G_LANGUAGE['ERR_FORM_SECTION_TPLBLOCK'] = "The section <I>%s</I> is conditional. You must declare a block called <I>%s</I> in the template containing the section fields!";
$P2G_LANGUAGE['ERR_FORM_REQUIRED_SUMMARY'] = "ต้อง�?ารฟีลด์ในฟอร์มนี้:";
$P2G_LANGUAGE['ERR_FORM_COMPLETE_FIELDS'] = "โปรดเติมฟีลด์�?ละลองทำอี�?ครั้ง";
$P2G_LANGUAGE['ERR_FORM_FIX_FIELDS'] = "โปรด�?�?้ไขฟีลด์�?ล้วลองอี�?ครั้ง";
$P2G_LANGUAGE['ERR_FORM_ERRORS_SUMMARY'] = "ฟอร์มนี้มี errors ดังนี้:";
$P2G_LANGUAGE['ERR_FORM_FIELD_REQUIRED'] = "ฟีลด์ %s ต้องใส่";
$P2G_LANGUAGE['ERR_FORM_FIELD_INVALID'] = "ฟีลด์ %s มีค่าไม่ถู�?ต้อง!";
$P2G_LANGUAGE['ERR_FORM_FIELD_INVALID_DATATYPE'] = "ฟีลด์ %s มีค่า %s ไม่ valid.";
$P2G_LANGUAGE['ERR_FORM_FIELD_INVALID_FLOAT'] = "The numeric precision of the field %s is invalid.\nThe maximum sizes are %s for the integer part and %s for the decimal part.";
$P2G_LANGUAGE['ERR_FORM_FIELD_ALPHANUM'] = "ฟีลด์ %s ต้องเป็นตัวอั�?ขระ";
$P2G_LANGUAGE['ERR_FORM_FIELD_CHOICE'] = "พีลด์ %s ต้องมีค่าดังต่อไปนี้: <I>%s</I>";
$P2G_LANGUAGE['ERR_FORM_FIELD_CREDITCARD'] = "ฟีลด์ %s มี %s เลขบัตรเครดิตที่ไม่ valid";
$P2G_LANGUAGE['ERR_FORM_FIELD_MIN_LENGTH'] = "ฟีลด์ %s ต้องมีอย่างน้อย %s อั�?ขระ.";
$P2G_LANGUAGE['ERR_FORM_FIELD_MAX_LENGTH'] = "ฟีลด์ %s ต้องมีจน�?ว่า %s อั�?ขระ.";
$P2G_LANGUAGE['ERR_FORM_FIELD_EQ'] = "ฟีลด์ %s ต้องเท่า�?ับฟีลด์ %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_NEQ'] = "ฟีลด์ %s ไม่สามารถเท่า�?ับฟีลด์ %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_GT'] = "ฟีลด์ %s ต้องเท่า�?ับฟีลด์ %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_LT'] = "ฟีลด์ %s ต้องน้อย�?ว่าฟีลด์ %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_GOET'] = "ฟีลด์ %s ต้องให�?่�?ว่าหรือเท่า�?ับฟีลด์ %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_LOET'] = "ฟีลด์ %s ต้องน้อย�?ว่าหรือเท่า�?ับฟีลด์ %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_EQ'] = "ฟีลด์ %s ต้องเท่า�?ับฟีลด์ %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_NEQ'] = "ฟีลด์ %s ไม่สามารถเท่า�?ับ %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_GT'] = "ฟีลด์ %s ต้องให�?่�?ว่า %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_LT'] = "ฟีลด์ %s ต้องน้อย�?ว่า %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_GOET'] = "ฟีลด์ %s ต้องให�?่�?ว่าหรือเท่า�?ัย %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_LOET'] = "ฟีลด์ %s ต้องน้อย�?ว่าหรือเท่า %s!";
$P2G_LANGUAGE['ERR_FORM_INVALID_MASK'] = "The mask <I>%s</I>, ฟีลด์นี้ <I>%s</I> ไม่ valid!";
$P2G_LANGUAGE['ERR_FORM_INVALID_FIELDTYPE'] = "type ของฟีลด์<I>%s</I> ไม่ valid!";
$P2G_LANGUAGE['ERR_FORM_CAPTCHA'] = "The code entered in the field %s doesn't match the content of the image!";
$P2G_LANGUAGE['ERR_DATAGRID_INVALID_FIELDTYPE'] = "ฟีลด์ type นี้<I>%s</I> ไม่สามารถใช้ได้ใน <I>grid</I>!";
$P2G_LANGUAGE['ERR_DATAGRID_INVALID_CELLSIZES'] = "The value of the <I>CELLSIZES</I> attribute in the field <I>%s</I> is invalid. You must provide N+1 sizes (N=FIELDSET size) and the sizes sum must be 100!";
$P2G_LANGUAGE['ERR_DATAGRID_INVALID_FIELDCOUNT'] = "The query defined in the <I>DATASOURCE</I> of the field <I>%s</I> must return N+2 columns (N=FIELDSET size)";
$P2G_LANGUAGE['ERR_FORM_WRONG_LISTENER'] = "The attributes of the event handler <I>%s</I> are invalid or incomplete!";
$P2G_LANGUAGE['ERR_FORM_WRONG_RULE'] = "The attributes of the rule <I>%s</I> are invalid or incomplete!";
$P2G_LANGUAGE['ERR_INVALID_DATASOURCE_SYNTAX'] = "The specification of the tag DATASOURCE in the field <I>%s</I> is invalid.<BR>The child nodes KEYFIELD and LOOKUPTABLE are mandatory!";
$P2G_LANGUAGE['ERR_MISSING_GROUPFIELD_CHILDREN'] = "The specification of the field <I>%s</I> is incomplete. You must provide at least one &lt;OPTION&gt; child node!";
$P2G_LANGUAGE['ERR_MISSING_OPTION_VALUE'] = "The VALUE attribute of the option at index <I>%d</I> in the field <I>%s</I> is missing!";
$P2G_LANGUAGE['ERR_MISSING_DBGROUPFIELD_DATASOURCE'] = "It wasn't defined a DATASOURCE tag to the %s <I>%s</I>!";
$P2G_LANGUAGE['ERR_EMPTY_DBGROUPFIELD_RESULTS'] = "The query provided in the DATASOURCE element of the %s <I>%s</I> returned an empty set!";
$P2G_LANGUAGE['ERR_MISSING_COMBOOPTION_VALUE'] = "The VALUE attribute of the option at index %d in the COMBOFIELD <I>%s</I> is missing!";
$P2G_LANGUAGE['ERR_MISSING_EDITSELECTION_CHILDREN'] = "The specification of the EDITSELECTIONFIELD <I>%s</I> is incomplete. The EDITFIELD and LOOKUPFIELD child nodes are mandatory!";
$P2G_LANGUAGE['ERR_MISSING_LOOKUPSELECTION_CHILDREN'] = "The specification of the LOOKUPSELECTIONFIELD <I>%s</I> is wrong. The node must have two LOOKUPFIELD children!";
$P2G_LANGUAGE['ERR_MISSING_SOURCELOOKUP_DATASOURCE'] = "There's no DATASOURCE defined in the first LOOKUPFIELD child of the LOOKUPSELECTIONFIELD <I>%s</I>!";
$P2G_LANGUAGE['ERR_MISSING_QUERY_ELEMENTS'] = "The \$fields and \$tables itens are mandatory in the construction of the SQL query!";
$P2G_LANGUAGE['ERR_INVALID_DATAGRID_STRUCTURE'] = "The structure of the grid <I>%s</I> is invalid. You must declare a <I>DATASOURCE</I> and a <I>FIELDSET</I> containing at least one field!";
$P2G_LANGUAGE['ERR_MISSING_RANGEFIELD_CHILDREN'] = "The specification of the RANGEFIELD <I>%s</I> is wrong. The node must have two EDITFIELD, COMBOFIELD, LOOKUPFIELD or DATEPICKERFIELD children!";
$P2G_LANGUAGE['ERR_EDITSEARCH_INVALID_DATAFILTER'] = "The specification of the DATAFILTER <I>%s</I> is invalid.";
$P2G_LANGUAGE['ERR_EDITSEARCH_INVALID_DATAFILTER_MASK'] = "The mask assigned to the DATAFILTER <I>%s</I> is invalid.";
$P2G_LANGUAGE['ERR_EDITSEARCH_DUPLICATED_DATAFILTER'] = "Another DATAFILTER with ID <I>%s</I> already exists.";
$P2G_LANGUAGE['ERR_MISSING_EDITSEARCH_CHILDREN'] = "The specification of the EDITSEARCHFIELD <I>%s</I> is incomplete. You must provide at least one DATAFILTER, a DATASOURCE and a LOOKUPFIELD!";
$P2G_LANGUAGE['ERR_CSV_DB_JSRS'] = "ไม่สามารถทำคำสั่งนี้ได้";
$P2G_LANGUAGE['ERR_SEARCHFORM_EMPTY'] = "ไม่สามารถค้นหาเพราะฟีลด์ค้นหาว่างเปล่า";
$P2G_LANGUAGE['ERR_SEARCHFORM_INVALID'] = "ค้นหาไม่ได้เพราะ filters ว่างเปล่าหรือสั้น�?ว่า %d อั�?ขระ";
$P2G_LANGUAGE['ERR_REPORT_MAX_GROUP_COLS'] = "The grouping columns count must be smaller than the query columns count!";
$P2G_LANGUAGE['ERR_REPORT_UNKNOWN_GROUP_COL'] = "The grouping column <I>%s</I> isn't defined in the query result set!";
$P2G_LANGUAGE['ERR_REPORT_MISSING_BLOCK_VARIABLE'] = "The variable <I>%s</I> of the block <I>%s</I> wasn't declared in the template!";
$P2G_LANGUAGE['ERR_REPORT_INVALID_COLSIZES'] = "The value <I>%s</I> isn't a valid definition to the column sizes!";
$P2G_LANGUAGE['ERR_REPORT_COL_SIZES_SUM'] = "The sum of the column sizes passed to the function setColumnSizes must be equal to 100!";
$P2G_LANGUAGE['ERR_REPORT_COL_COUNT_MISMATCH'] = "The count of column sizes provided (%s) is not equal to the number of columns of the report (%s, %s grouping)";
$P2G_LANGUAGE['ERR_REPORT_MISSING_SEARCH_VARIABLE'] = "It wasn't possible to build the search form, because the variable <I>%s</I> wasn't found.<BR>Declare {%s} in the report template to proceed.";
$P2G_LANGUAGE['ERR_REPORT_SEARCH_PARS_MALFORMED'] = "You must provide the Label, the Field Name and the Mask in the construction of the search parameters!";
$P2G_LANGUAGE['ERR_REPORT_SEARCH_INVALID_MASK'] = "The mask <I>%s</I> of the search parameter <I>#%d</I> is invalid!";
$P2G_LANGUAGE['ERR_REPORT_MIN_ALT_STYLE'] = "The setAlternateStyle function must receive at least two parameters (CSS names)!";
$P2G_LANGUAGE['ERR_REPORT_HIDDEN_GROUP'] = "A hidden column (%s) can't be included in the group headers!";
$P2G_LANGUAGE['ERR_REPORT_GROUP_HIDDEN'] = "A column displayed in the group headers (%s) can't be defined as hidden!";
$P2G_LANGUAGE['ERR_REPORT_MAX_HIDDEN_COLS'] = "The hidden columns count must be smaller than the query columns count!";
$P2G_LANGUAGE['ERR_REPORT_UNKNOWN_HIDDEN_COL'] = "The column <I>%s</I> defined as hidden doesn't exist in the query result set!";
$P2G_LANGUAGE['ERR_DATASET_INVALID_TYPE'] = "The type <I>%s</I> isn't a valid DataSet type!";
$P2G_LANGUAGE['ERR_MENU_WRONG_ROOT_SQL'] = "The query stored in the \$rootSql parameter of the Menu::loadFromDatabase() method returned an empty set or doesn't have the minimum required fields (CAPTION and LINK)!";
$P2G_LANGUAGE['ERR_MENU_WRONG_CHILDREN_STATEMENT'] = "The query passed in the \$childSql parameter of the Menu::loadFromDatabase() method is invalid.<BR>Provide a statement containing the '~var~' (Relation Column) plus the two required fields (CAPTION and LINK)";
$P2G_LANGUAGE['ERR_MENU_NOT_FOUND'] = "The menu wasn't created. Build the menu itens using Menu::loadFromDatabase() or Menu::loadFromXmlFile().";
$P2G_LANGUAGE['ERR_MENU_EMPTY_XML_ROOT'] = "The root node of the XML file has no children!";
$P2G_LANGUAGE['ERR_MENU_INVALID_IMAGE'] = "The <I>%s</I> image is invalid. The possible values are: <I>%s</I>.";
$P2G_LANGUAGE['ERR_FTP_MISSING_HOST'] = "It wasn't possible to open the connection. You must setup the connection first, using the FtpClient::setServer() and FtpClient::setUserInfo() methods";
$P2G_LANGUAGE['ERR_FTP_MISSING_USER_OR_PASS'] = "It wasn't possible to authenticate. One of the user values (username or password) wasn't provided";
$P2G_LANGUAGE['ERR_SPRSH_OUT_OF_BOUNDS'] = "เ�?ินขอบเขตของ spreadsheet: [%d;%d]";
$P2G_LANGUAGE['ERR_SPRSH_MAX_NOTE_CHARS'] = "เ�?ินขนาดของ cell note <BR>Valor: %s";
$P2G_LANGUAGE['ERR_SPRSH_MAX_FONTS'] = "ใช้เ�?ิน 4 ฟอนท์สำหรับ 1 spreadsheet";
$P2G_LANGUAGE['ERR_UPLOAD_NOT_FOUND'] = "ไม่สามารถ upload ได้สำเร็จ ไม่ได้โอนไฟล์มาอย่างสมบูรณ์";
$P2G_LANGUAGE['ERR_UPLOAD_TOO_BIG'] = "ไม่สามารถ upload ได้สำเร็จ ขนาดของไฟล์ให�?่�?ว่าที่�?ำหนด";
$P2G_LANGUAGE['ERR_UPLOAD_INVALID_NAME'] = "ไม่สามารถ upload ได้สำเร็จ ชื่อไฟล์ %s ไม่ valid.";
$P2G_LANGUAGE['ERR_UPLOAD_INVALID_TYPE'] = "ไม่สามารถ upload ได้สำเร็จ The extension %s นี้ใช้ไม่ได้";
$P2G_LANGUAGE['ERR_UPLOAD_FILE_EXISTS'] = "ไม่สามารถ upload ได้สำเร็จ ไฟล์ <I>%s</I> มีอยู่�?ล้ว�?ละไม่ได้เขียนทับ";
$P2G_LANGUAGE['ERR_UPLOAD_CANT_MOVE'] = "ไม่สามารถย้าย upload temporary file ไปที่ target folder.";
$P2G_LANGUAGE['ERR_CANT_OPEN_SOCKET'] = "It wasn't possible to open the socket using the port %s with the host %s!%s";
$P2G_LANGUAGE['ERR_SOCKET_NOT_CONNECTED'] = "The connection with the socket isn't active!";
$P2G_LANGUAGE['ERR_SOCKET_EOF'] = "ถึง End of file  ไม่สามารถทำคำสั่งให้สมบูรณ์!";
$P2G_LANGUAGE['ERR_SOCKET_TIMEOUT'] = "The socket timeout was exceeded. The operation can't be completed!";
$P2G_LANGUAGE['ERR_MAIL_MESSAGE_NOT_BUILT'] = "message สร้างอย่างไม่สมบูรณ์�?ละส่งไม่ได้!<BR>Run the build() method.";
$P2G_LANGUAGE['ERR_MAIL_EMPTY_RCPT'] = "ไม่มีผู้รับ message!";
$P2G_LANGUAGE['ERR_SIGNED_MESSAGE_SIGN'] = "It wasn't possible to sign the message content.<BR>%s";
$P2G_LANGUAGE['ERR_SMTP_CONNECTION'] = "Error while connecting to the SMTP server %s!<BR>[Error %d] - %s";
$P2G_LANGUAGE['ERR_SMTP_COMMAND'] = "Error while executing the command %s in the SMTP server!<BR>[Error %d] - %s";
$P2G_LANGUAGE['ERR_SMTP_AUTHENTICATE'] = "It wasn't possible to authenticate with the SMTP server!";
$P2G_LANGUAGE['ERR_CRYPT_MISSING_KEY'] = "To encrypt or decrypt data, you must provide a crypt key through the Crypt::setKey() method!";
$P2G_LANGUAGE['ERR_CRYPT_OPEN_MODULE'] = "It wasn't possible to initialize the cipher <I>%s</I> using the mode %s!";
$P2G_LANGUAGE['ERR_OPENSSL_PUBKEY_ENCRYPT'] = "It wasn't possible to extract a public key to encrypt the provided data. Provide a file containing a valid certificate to the OpenSSLCrypt::setCertificatePath() method!";
$P2G_LANGUAGE['ERR_OPENSSL_PUBKEY_VERIFY'] = "It wasn't possible to extract a public key to verify the signature included in this piece of data. Provide a file containing a valid certificate to the OpenSSLCrypt::setCertificatePath() method!";
$P2G_LANGUAGE['ERR_OPENSSL_PRIVKEY_DECRYPT'] = "It wasn't possible to extract a private key to decrypt the provided data. Provide the file that contains the private key and the pass phrase, if the key requires one.";
$P2G_LANGUAGE['ERR_OPENSSL_PRIVKEY_SIGN'] = "It wasn't possible to extract a private key to generate the digital signature. Provide the file that contais the private key and the pass phrase, if the created key requires one.";
$P2G_LANGUAGE['ERR_OPENSSL_CERT_PATH'] = "The certificate path is invalid!";
$P2G_LANGUAGE['ERR_OPENSSL_READ_CERT'] = "It wasn't possible to read the content of the certificate %s!";
$P2G_LANGUAGE['ERR_POP3_CONNECTION'] = "Error while connecting to the POP3 server %s!<BR>[Error %d] - %s";
$P2G_LANGUAGE['ERR_POP3_COMMAND'] = "Error while executing the command %s in the POP server!<BR>%s";
$P2G_LANGUAGE['ERR_POP3_AUTHENTICATE'] = "It wasn't possible to authenticate with the POP3 server!";
$P2G_LANGUAGE['ERR_POP3_APOP'] = "It wasn't possible to run the APOP command : there's no authentication banner stored!";
$P2G_LANGUAGE['ERR_HTTP_CONNECT'] = "It wasn't possible to establish a HTTP connection with the host <I>%s</I>!";
$P2G_LANGUAGE['ERR_HTTP_MISSING_HOST'] = "The HTTP host wasn't set. Run the HttpClient::setHost() method.";
$P2G_LANGUAGE['ERR_CANT_INSTANTIATE_VALIDATOR'] = "It wasn't possible to instantiate the validation class <B>%s</B>!";
$P2G_LANGUAGE['ERR_INVALID_VALIDATOR'] = "The validation class <I>%s</I> is invalid! The %s::execute() method could not be found.";
$P2G_LANGUAGE['ERR_ASSERTION_MESSAGE'] = "The evaluated expression didn't reach the expected results.";
$P2G_LANGUAGE['ERR_INVALID_CALLBACK'] = "The function or method <I>%s</I> is not a valid callback!";
$P2G_LANGUAGE['ERR_INVALID_PROPERTIES_FILE'] = "The file <I>%s</I> doesn't contain valid configuration data!";
$P2G_LANGUAGE['ERR_INVALID_COLOR_SPEC'] = "<I>%s</I> is not a valid color specification.";
$P2G_LANGUAGE['ERR_ALLOCATE_COLOR'] = "It wasn't possible to allocate a new color for this image!";
$P2G_LANGUAGE['ERR_JSRS_MISSING_HANDLER'] = "Missing JSRS handler name or alias!";
$P2G_LANGUAGE['ERR_JSRS_INVALID_HANDLER'] = "The JSRS handler %s is invalid!";
$P2G_LANGUAGE['ERR_AJAX_SUPPORT'] = "Your browser doesn't support AJAX!";
$P2G_LANGUAGE['ERR_JSON_INVALID_PROPNAME'] = "JSON Decode Error: %s isn't a valid property name";
$P2G_LANGUAGE['ERR_JSON_MISSING_COLON'] = "JSON Decode Error: missing colon at position %d";
$P2G_LANGUAGE['ERR_JSON_INVALID_PROPVALUE'] = "JSON Decode Error: %s isn't a valid property value";
$P2G_LANGUAGE['ERR_JSON_MISSING_COMMA'] = "JSON Decode Error: missing comma at position %d";
$P2G_LANGUAGE['ERR_JSON_ESCAPE_SEQUENCE'] = "JSON Decode Error: invalid escape sequence at position %d";
$P2G_LANGUAGE['ERR_JSON_UNTERMINATED_STRING'] = "JSON Decode Error: unterminated string literal at position %d";
$P2G_LANGUAGE['ERR_JSON_CYCLE'] = "The JSON encoder found a cycle in an instance of the class %s!";
$P2G_LANGUAGE['ERR_JSON_ENCODE'] = "An error was found while converting type %s to a JSON string!";
$P2G_LANGUAGE['DEFAULT_BTN_VALUE'] = "Submit";
$P2G_LANGUAGE['CALENDAR_LINK_TITLE'] = "เปิด calendar";
$P2G_LANGUAGE['CALCULATOR_LINK_TITLE'] = "เปิดเครื่องคิดเลข";
$P2G_LANGUAGE['CALCULATOR_WINDOW_TITLE'] = "เครื่องคิดเลข";
$P2G_LANGUAGE['CALCULATOR_RESULT_CAPTION'] = "ผล";
$P2G_LANGUAGE['DAYS_OF_WEEK'] = array("อาทิตย์", "จันทร์", "อังคาร", "พุธ", "พฤหัส", "ศุ�?ร์", "เสาร์");
$P2G_LANGUAGE['DAYS_OF_WEEK_ABBR'] = array("อาทิตย์", "จันทร์", "อังคาร", "พุธ", "พฤหัส", "ศุ�?ร์", "เสาร์");
$P2G_LANGUAGE['MONTHS_OF_YEAR'] = array("ม�?ราคม", "�?ุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "�?ร�?ฎาคม", "สิงหาคม", "�?ันยายน", "ตุลาคม", "พฤศจิ�?ายน", "ธันวาคม");
$P2G_LANGUAGE['MONTHS_OF_YEAR_ABBR'] = array("มค", "�?พ", "มีค", "เมย", "พค", "มิย", "�?ค", "สค", "�?ย", "ตค", "พย", "ธค");
$P2G_LANGUAGE['COMPRESS_USE_MSG'] = "<!-- Uses %s Compression -->";
$P2G_LANGUAGE['COMPRESS_DEBUG_MSG_ORIG'] = "Original Size: %d";
$P2G_LANGUAGE['COMPRESS_DEBUG_MSG_COMP'] = "Packed Size: %d";
$P2G_LANGUAGE['EMPTY_SEARCH'] = "ท่านต้องเลือ�? filter �?ละคีย์ term ที่จะค้นหา!";
$P2G_LANGUAGE['EMPTY_RESULTS'] = "�?ารค้นหาไม่ได้ผล!";
$P2G_LANGUAGE['FORM_INPUT_MASK_MESSAGES'] = array(
	'optionalCharFirst' => 'The optional char indicator must succeed a literal.',
	'escapeCharLast' => 'The escape char indicator must preceed a literal.',
	'nestedCharClass' => 'Invalid character class.',
	'invalidLimits' => 'Invalid size limits definition.'
);
$P2G_LANGUAGE['FORM_MASKS'] = array(
	'CPFCNPJ' => 'CPF/CNPJ',
	'CURRENCY' => 'ส�?ุลเงิน',
	'DATE' => 'วันที่',
	'TIME' => 'เวลา',
	'EMAIL' => 'E-Mail',
	'FLOAT' => 'เลขมีทศนิยม',
	'INTEGER' => 'เลขจำนวนเต็ม',
	'DIGIT' => 'เล�',
	'IP' => 'IP Address',
	'WORD' => 'text �?บบอั�?ขระ',
	'URL' => 'URL/Internet Address',
	'ZIP' => 'รหัสไปรษณีย์'
);
$P2G_LANGUAGE['EDITSEARCH_BTN_VALUE'] = "Searching...";
$P2G_LANGUAGE['EDITOR_VARS'] = array(
	'formatBlock' => 'Paragraph',
	'formatBlockOptions' => array(
		'h1' => 'Heading 1 &lt;H1&gt;',
		'h2' => 'Heading 2 &lt;H2&gt;',
		'h3' => 'Heading 3 &lt;H3&gt;',
		'h4' => 'Heading 4 &lt;H4&gt;',
		'h5' => 'Heading 5 &lt;H5&gt;',
		'h6' => 'Heading 6 &lt;H6&gt;',
		'address' => 'Address &lt;ADDR&gt;',
		'pre' => 'Formatted &lt;PRE&gt;',
		'removeformat' => 'Remove Format'
	),
	'topButtons' => array(
		'bold' => 'Format ตัวหนา',
		'italic' => 'Format ตัวเอน',
		'underline' => 'Format ขีดเส้นใต้',
		'strikethrough' => 'Strikethrough',
		'cut' => 'ตัด',
		'copy' => 'คัดลอ�?',
		'paste' => 'วาง',
		'pickforecolor' => 'เปลี่ยนสี foreground',
		'pickbackcolor' => 'เปลี่ยนสี background',
		'addemoticon' => 'Insert รูปยิ้ม',
		'selectall' => 'เลือ�?ทั้งหมด',
		'removeformat' => 'ลบ format'
	),
	'font' => 'Font',
	'fontSize' => 'Size',
	'bottomButtons' => array(
		'justifyleft' => 'Align ซ้าย',
		'justifycenter' => 'จัด�?ลาง',
		'justifyright' => 'Align ขวา',
		'justifyfull' => 'Justify text',
		'insertorderedlist' => 'Insert/Remove numbered list',
		'insertunorderedlist' => 'Insert/Remove simple list',
		'indent' => 'เพิ่มย่อหน้า ',
		'outdent' => 'ลดย่อหน้า',
		'createlink' => 'Insert a Link',
		'unlink' => 'Unlink',
		'insertimage' => 'Insert รูปภาพ',
		'inserthorizontalrule' => 'Insert เส้น�?นวนอน'
	),
	'editMode' => 'Edit in Advanced Mode',
	'emoticon' => 'เลือ�? Smiley',
	'validateMode' => 'To use the advanced editor\'s tools, uncheck the \'Advanced Edit\' box',
	'createLink' => 'Enter the address of the link (Example: http://www.domain.com):',
	'insertImage' => 'ใส่ path ของรูป:'
);
$P2G_LANGUAGE['COLOR_PICKER_VARS'] = array(
	'popupTitle' => 'คลิดสีที่ท่านต้อง�?าร',
	'colorTitle' => 'เลือ�?สีนี้'
);
$P2G_LANGUAGE['LOOKUP_CHOICE_FILTER_TIP'] = "--- Filter the list ---";
$P2G_LANGUAGE['LOOKUP_SELECTION_DEFAULT_SELFIRST'] = "--- Insert Values ---";
$P2G_LANGUAGE['LOOKUP_SELECTION_BUTTON_TITLES'] = array(
	'add' => 'Add the Selected Items',
	'addall' => 'Add all the Items',
	'rem' => 'Remove the Selected Items',
	'remall' => 'Remove all the Items'
);
$P2G_LANGUAGE['EDIT_SELECTION_BUTTON_TITLES'] = array(
	'add' => 'Add',
	'rem' => 'Remove the Selected Items',
	'remall' => 'Remove all the Items'
);
$P2G_LANGUAGE['MEMO_COUNT_LABEL'] = "Chars";
$P2G_LANGUAGE['SEL_AVAILABLE_VALUES_LABEL'] = "Available:";
$P2G_LANGUAGE['SEL_INSERTED_VALUES_LABEL'] = "Inserted:";
$P2G_LANGUAGE['FORM_DATA_BIND_MESSAGES'] = array(
	'insertSuccess' => 'ข้อมูล inserted สำเร็จ!',
	'updateEmpty' => 'ไม่มีระเบียนที่จะ�?�?้ไข!',
	'updateSuccess' => 'updated ข้อมูลสำเร็จ!',
	'deleteEmpty' => 'ไม่มีระเบียนที่จะลบ!',
	'deleteSuccess' => 'ข้อมูลลบสำเร็จ!',
	'deleteConfirm' => 'ท่าน�?น่ใจว่าต้อง�?ารลบระเบียนนี้?',
	'sortInvalid' => 'ท่านต้องเลือ�?ฟีลด์เพื่อเรียงตามอั�?ษร!',
	'gotoEmpty' => 'ท่านต้องเลือ�?เลขที่ระเบียนเพื่อ continue!',
	'gotoInvalid' => 'ระเบียนที่ต้อง�?ารไม่ valid!'
);
$P2G_LANGUAGE['FORM_DATA_BIND_TOOLBAR_VALUES'] = array(
	'navFirstTip' => 'ไประเบียน�?ร�?',
	'navPrevTip' => 'ไประเบียน�?่อน',
	'navNextTip' => 'ไประเบียนถัดไป',
	'navLastTip' => 'ไประเบียนสุดท้าย',
	'actNew' => 'ใหม่',
	'actNewTip' => 'สร้างระเบียนใหม่',
	'actEdit' => 'Edit',
	'actEditTip' => 'Edit ระเบียนปัจจุบัน',
	'actDel' => 'ลบ',
	'actDelTip' => 'ลบระเบียนปัจจุบัน',
	'actSave' => 'บันทึ�?',
	'actSaveTip' => 'บันทึ�?ระเบียนปัจจุบัน',
	'actCancel' => 'ย�?เลิ�?',
	'actCancelTip' => 'ย�?เลิ�? action ปัจจุบัน',
	'sortAsc' => 'เรียงลำดับน้อยไปมา�?',
	'sortDesc' => 'เรียงลำดับมา�?ไปน้อย',
	'sortChoose' => 'เลือ�?ลำดับฟีลด์เพื่อเรียง',
	'sortTit' => 'เรียงลำดับ',
	'sortFirst' => 'เรียงลำดับฟีลด์',
	'gotoTip' => 'ใส่จำนวนระเบียง�?ละคลิ�? Go',
	'goto' => 'Go',
	'gotoBtnTip' => 'คลิ�?เพื่อไประเบียงที่ต้อง�?าร',
	'gotoTit' => 'Go to',
	'filter' => 'Filter',
	'filterTip' => 'Choose the filter field to apply',
	'filterVTip' => 'เลือ�?ค่าของ filter',
	'filterBtnTip' => 'คลิ�?ที่นี่เพื่อ filter',
	'filterTit' => 'Filter',
	'filterFirst' => 'Filter Fields'
);
$P2G_LANGUAGE['REPORT_INVALID_PAGE'] = "Invalid page number!";
$P2G_LANGUAGE['REPORT_EMPTY_VALUES'] = array(
	'emptyMsg' => 'The query or the used search filters returned an empty set',
	'backLink' => 'Back to the Report'
);
$P2G_LANGUAGE['REPORT_ORDER_TIP'] = "Order by %s";
$P2G_LANGUAGE['REPORT_ORDER_OPTIONS_LABEL'] = "Order by:";
$P2G_LANGUAGE['REPORT_FUNCTION_MESSAGES'] = array(
	'firstTit' => 'หน้า�?ร�?',
	'firstTip' => 'ไปหน้า�?ร�?',
	'prevTit' => 'หน้า�?่อน',
	'prevTip' => 'ไปหน้า�?่อน',
	'prevScrTit' => '%d Back',
	'prevScrTip' => '%d Pages Back in the report',
	'pageTip' => 'Go the the page #%d of %d',
	'nextTit' => 'Next',
	'nextTip' => 'ไปหน้าถัดไป',
	'nextScrTit' => '%d Forward',
	'nextScrTip' => '%d Pages Forward in the report',
	'lastTit' => 'หน้าสุดท้าย',
	'lastTip' => 'ไปหน้าสุดท้าย',
	'rowCount' => '%d record(s) found',
	'rowsPerPage' => '%d result(s) per page',
	'thisPage' => 'Page %d of %d',
	'rowInterval' => 'Listing %d-%d of %d results',
	'goTo' => 'ไปหน้าเลขที่'
);
$P2G_LANGUAGE['REPORT_SEARCH_VALUES'] = array(
	'searchTitle' => 'ค้นหา',
	'filtersTitle' => 'เลือ�?ฟีลด์',
	'addBtn' => 'Add',
	'mainOpOr' => 'Any filter',
	'mainOpAnd' => 'Filters ทั้งหมด',
	'clearBtn' => 'Clear',
	'viewBtn' => 'View',
	'sendBtn' => 'ค้นหา',
	'emptyFilters' => 'ไม่มี filters inserted',
	'closeFilters' => 'ปิด',
	'removeFilter' => 'เอาออ�?',
	'addedFilter' => 'เพิ่ม Filter!',
	'resendConfirmation' => 'ท่านต้อง�?ารค้นใหม่โดยใช้ current filter(s)?'
);
$P2G_LANGUAGE['REPORT_NUMBER_OPERATORS'] = array(
	'=' => 'เท่า�?ับ',
	'!=' => 'ไม่เท่า�?ับ',
	'>' => 'มา�?�?ว่า',
	'<' => 'น้อย�?ว่า',
	'>=' => 'มา�?�?ว่าหรือเท่า�?ับ',
	'<=' => 'น้อย�?ว่าหรือเท่า�?ับ'
);
$P2G_LANGUAGE['REPORT_STRING_OPERATORS'] = array(
	'=' => 'เท่า�?ับ',
	'!=' => 'ไม่เท่า�?ับ',
	'LIKE' => 'Containing',
	'NOT LIKE' => 'ไม่ Containing',
	'LIKEI' => 'Starting with',
	'LIKEF' => 'Ending with'
);
$P2G_LANGUAGE['OPERATORS'] = array(
	'EQ' => 'ไม่เท่า�?ับ',
	'NEQ' => 'ไม่เท่า�?ับ',
	'GT' => 'มา�?�?ว่า',
	'LT' => 'น้อย�?ว่า',
	'GOET' => 'มา�?�?ว่าหรือเท่า�?ับ',
	'LOET' => 'น้อย�?ว่าหรือเท่า�?ับ',
	'CONTAINING' => 'containing',
	'STARTING' => 'starting with',
	'ENDING' => 'ending with',
	'BETWEEN' => 'ระหว่าง',
	'IN' => 'is in the list',
	'NOTIN' => 'isn\'t in the list',
	'AND' => '�?ละ',
	'OR' => 'หรือ'
);
$P2G_LANGUAGE['CHECKBOX_DESCRIPTIONS'] = array(
	'T' => '%s is selected',
	'F' => '%s isn\'t selected'
);
$P2G_LANGUAGE['CHECKGROUP_SHORTCUTS'] = array(
	'all' => 'All',
	'none' => 'None',
	'invert' => 'Invert'
);
return $P2G_LANGUAGE;
?>