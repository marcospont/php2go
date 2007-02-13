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
 * @author Herman Sandoval Manrique <hfsandovalm@gmail.com>
 * @copyright 2002-2006 Marcos Pont
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version $Id$
 */

$P2G_LANGUAGE['ERR_OLD_PHP_VERSION'] = "PHP2Go no se puede ejecutar con la configuración actual del servidor.<BR>Su versión actual de PHP es <I>%s</I>. El requerimiento mínimo del framework es <I>%s</I>.<BR>Instale una versión igual o superior para ejecutarlo normalmente.";
$P2G_LANGUAGE['ERR_UNSUPPORTED_EXTENSION'] = "La extensión <I>%s</I> no es soportada por la instalación actual de PHP.";
$P2G_LANGUAGE['ERR_OBJ_MESSAGE'] = "Error en la clase %s:<BR>%s";
$P2G_LANGUAGE['ERR_SCRIPT_MESSAGE'] = "Error en el script:<BR>%s";
$P2G_LANGUAGE['ERR_DATABASE'] = "Error de Base de Datos";
$P2G_LANGUAGE['ERR_INVALID_VALUE'] = "Valor Inválido!";
$P2G_LANGUAGE['ERR_DUPLICATED_VALUE'] = "Este valor ya fue insertado!";
$P2G_LANGUAGE['ERR_COMM_FAILURE'] = "Falla de comunicación";
$P2G_LANGUAGE['ERR_MISSING_DATABASE_PARAMETERS'] = "No fue posible cargar los parámetros de conexión de base de datos! [ID: %s]";
$P2G_LANGUAGE['ERR_INVALID_DATABASE_PARAMETERS'] = "Los parámetros de conexión de base de datos son inválidos! Las entradas USER, BASE y TYPE son obligatorias. [ID: %s]";
$P2G_LANGUAGE['ERR_DATABASE_CONNECTION_FAILED'] = "No fué posible conectarse a la base de datos con la configuración de acceso actual. Esta es correcta?";
$P2G_LANGUAGE['ERR_INVALID_CONNECTION_CLASS_PATH'] = "No fué posible importar o instanciar la clase de conexión %s!";
$P2G_LANGUAGE['ERR_INVALID_CONNECTION_CLASS'] = "La clase de conexión %s no es una subclase de php2go.db.Db!";
$P2G_LANGUAGE['ERR_TYPES'] = array(
	1 => "Error Fatal",
	2 => "Aviso",
	4 => "Error de Sintaxis",
	8 => "Nota",
	16 => "Error Fatal del Núcleo",
	32 => "Aviso del Núcleo",
	64 => "Error Fatal de Compilador",
	128 => "Aviso del Compilador",
	256 => "Error de Usuario",
	512 => "Aviso de Usuario",
	1024 => "Nota de Usuario"
);
$P2G_LANGUAGE['ERR_ABSOLUTE_URI_NOT_FOUND'] = "La entrada 'ABSOLUTE_URI' no ha sido establecida en el arreglo de configuración global!";
$P2G_LANGUAGE['ERR_CONFIG_ENTRY_NOT_FOUND'] = "The <I>%s</I> entry wasn't set in the global configuration array!";
$P2G_LANGUAGE['ERR_INVALID_LANGDOMAIN_FILE'] = "The messages file for the %s domain, language %s, is invalid or doesn't exist.";
$P2G_LANGUAGE['ERR_CANT_FIND_CFG_VAL'] = "No fué posible encontrar la variable <I>%s</I> en el arreglo de configuración global. Fué configurada correctamente en la instalación?";
$P2G_LANGUAGE['ERR_CANT_LOAD_MODULE'] = "No fue posible cargar el módulo <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_LOAD_DIR'] = "No fue posible cargar módulos desde una carpeta válida en  <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_LOAD_DIR_MODULE'] = "No fue posible cargar el archivo <I>%s</I> en <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_CONVERT_DATE'] = "No fue posible convertir la fecha al formato %s.";
$P2G_LANGUAGE['ERR_CANT_RUN_ON_WINDOWS'] = "La clase %s no se puede utilizar bajo Windows!";
$P2G_LANGUAGE['ERR_UNDEFINED_FUNCTION'] = "La función %s no está definida!";
$P2G_LANGUAGE['ERR_UNDEFINED_METHOD'] = "El método %s no está definido en la clase %s!";
$P2G_LANGUAGE['ERR_ABSTRACT_CLASS'] = "La clase <I>%s</I> es abstracta y no puede ser instanciada directamente!";
$P2G_LANGUAGE['ERR_DATE_FORMAT'] = "La fecha %s no está en el formato requerido %s.";
$P2G_LANGUAGE['ERR_INVALID_RESOURCE'] = "La variable %s pasada a la función o método %s no es un recurso válido.";
$P2G_LANGUAGE['ERR_INVALID_AUTHENTICATOR'] = "El autenticador %s no es una subclase de php2go.auth.Auth!";
$P2G_LANGUAGE['ERR_INVALID_AUTHENTICATOR_PATH'] = "No fue posible importar y/o instanciar el autenticador %s!";
$P2G_LANGUAGE['ERR_INVALID_USERCONTAINER'] = "El contenedor de usuario %s no es una subclase de php2go.auth.User!";
$P2G_LANGUAGE['ERR_INVALID_USERCONTAINER_PATH'] = "No fue posible importar y/o instanciar el contenedor de usuario %s!";
$P2G_LANGUAGE['ERR_INVALID_AUTHORIZER'] = "El autorizador %s no es una subclase de php2go.auth.Authorizer!";
$P2G_LANGUAGE['ERR_INVALID_AUTHORIZER_PATH'] = "No fue posible importar y/o instanciar el autorizador %s!";
$P2G_LANGUAGE['ERR_XML_PARSE'] = "Ocurrió un error en el parsing del archivo XML:<BR>Error: %s<BR>Línea: %s, Columna: %s";
$P2G_LANGUAGE['ERR_STATEMENT_EVAL'] = "Ocurrió un error en em parsing del código PHP <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_FIND_FILE'] = "No fue posible encontrar el archivo o carpeta %s. Este existe?";
$P2G_LANGUAGE['ERR_CANT_OPEN_FILE'] = "No fue posible abrir el archivo %s con el modo %s. Verifique los permisos del archivo y el modo usado.";
$P2G_LANGUAGE['ERR_CANT_CREATE_FILE'] = "No fue posible crear el archivo o carpeta %s. Verifique los permisos de la carpeta padre.";
$P2G_LANGUAGE['ERR_CANT_READ_FILE'] = "No fue posible abrir el archivo %s para lectura. Este existe? La ruta es correcta?";
$P2G_LANGUAGE['ERR_CANT_WRITE_FILE'] = "No fue posible abrir el archivo o carpeta %s para escritura. Tiene permisos para sobreescribirlo?";
$P2G_LANGUAGE['ERR_CANT_DELETE_FILE'] = "No fue posible borrar el archivo %s.<BR>Tiene usted permisos de escritura en esta carpeta? El archivo tiene permiso de borrado?";
$P2G_LANGUAGE['ERR_CANT_INCLUDE_FILE'] = "No fue posible abrir el archivo %s para su inclusión. Este existe? La ruta es correcta?";
$P2G_LANGUAGE['ERR_CANT_CHANGE_MODE'] = "No fue posible aplicar el modo %s al archivo o carpeta %s.";
$P2G_LANGUAGE['ERR_CANT_READ_DIR'] = "No fue posible encontrar el directorio %s. Este existe?";
$P2G_LANGUAGE['ERR_CANT_OPEN_DIR'] = "No fue posible abrir la carpeta %s para lectura. Verifique sus permisos.";
$P2G_LANGUAGE['ERR_CANT_FIND_SERIALIZATION_FILE'] = "No fue posible abrir para lectura el archivo que contiene el objeto serializado. La ruta es correcta? Se creó el archivo?";
$P2G_LANGUAGE['ERR_EMPTY_INSERT_QUERY'] = "La consulta de inserción en la tabla '%s' retornó un conjunto de resultados vacío utilizando los valores suministrados.";
$P2G_LANGUAGE['ERR_EMPTY_UPDATE_QUERY'] = "La consulta de actualización en la tabla '%s' con la condición %s retornó un conjunto de resultados vacío utilizando los valores suministrados.";
$P2G_LANGUAGE['ERR_EMPTY_TOGLOBALS_QUERY'] = "La consulta %s, utilizada para publicar variables en el ambiente retornó un conjunto de resultados vacío.";
$P2G_LANGUAGE['ERR_TOGLOBALS_WRONG_USAGE'] = "La consulta pasada al método Db::toGlobals() debería comenzar con 'SELECT' (DQL).";
$P2G_LANGUAGE['ERR_EMPTY_TEMPLATE_FILE'] = "No fue posible inicializar la plantilla con un nombre de archivo vacío!";
$P2G_LANGUAGE['ERR_EMPTY_TEMPLATE_CONTENT'] = "La plantilla no fue preparada ya que su contenido está vacío. Verifique el contenido pasado al constructor.";
$P2G_LANGUAGE['ERR_CANT_FIND_VARIABLE'] = "No fue posible encontrar la variable %s en la plantilla <I>%s</I>. <BR>Para obtener resultados correctos de esta herramienta, inserte {%s} en el código de la plantilla.";
$P2G_LANGUAGE['ERR_CANT_FIND_BLOCK'] = "No fue posible encontrar el bloque <I>%s</I> para su creación. Se declaró en la plantilla?";
$P2G_LANGUAGE['ERR_CANT_FIND_BLOCK_REUSE'] = "No fue posible encontrar el bloque %s para reusarlo como %s.";
$P2G_LANGUAGE['ERR_CANT_PUT_ON_PREPARED_TEMPLATE'] = "El elemento %s ya fue configurado con el método DocumentElement::parse(). No es posible agregar elementos con el método put().";
$P2G_LANGUAGE['ERR_TEMPLATE_NOT_PREPARED'] = "La plantilla %s no fue preparada. Ejecute el método %s::parse(); antes de ejecutar operaciones con el objeto.";
$P2G_LANGUAGE['ERR_TEMPLATE_ALREADY_PREPARED'] = "Esta plantilla ya ha sido preparada. Usted debería llamar solamente una vez el método parse().";
$P2G_LANGUAGE['ERR_TPLPARSE_DEFINED_BLOCK'] = "Ya está definido el bloque %s!";
$P2G_LANGUAGE['ERR_TPLPARSE_UNBALANCED_BLOCKDEF'] = "La definición del bloque <I>%s</I> está desbalanceada!";
$P2G_LANGUAGE['ERR_TPLPARSE_INCOMPLETE_BLOCKDEF'] = "La especificación del bloque <I>%s</I> dentro de la etiqueta %s está incompleta!";
$P2G_LANGUAGE['ERR_TPLPARSE_BLOCKINSIDELOOP'] = "No es posible definir bloques dinámicos dentro de bucles (loops)!";
$P2G_LANGUAGE['ERR_TPLPARSE_UNBALANCED_TAG'] = "La definición de la etiqueta <I>%s</I> está desbalanceada!";
$P2G_LANGUAGE['ERR_TPLPARSE_UNEXPECTED_TAG'] = "Etiqueta <I>%s</I> no esperada!";
$P2G_LANGUAGE['ERR_TPLPARSE_UNBALANCED_PARENTHESIS'] = "Paréntesis desbalanceado dentro de la expresión <I>%s</I>!";
$P2G_LANGUAGE['ERR_TPLPARSE_TAG_SYNTAX'] = "La definición de la etiqueta <I>%s</I> no es válida : %s!";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_TOKEN'] = "Símbolo (token) inválido dentro de la expresión <I>%s</I> : %s";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_ATTRIBUTE'] = "Falta, o no es válido, el valor del atributo!";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_ATTRIBUTENAME'] = "<I>%s</I> no es un nombre de atributo válido!";
$P2G_LANGUAGE['ERR_TPLPARSE_REQUIRED_ATTRIBUTE'] = "La propiedad <I>%s</I> se requiere en la definición de la etiqueta %s!";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_TAG_ATTRIBUTE'] = "La propiedad <I>%s</I> no es válida en la definición de la etiqueta %s!";
$P2G_LANGUAGE['ERR_TPLPARSE_MISSING_ASSIGN'] = "No se encontró el caracter '=' después del nombre de atributo!";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_IS_EXPR'] = "Operación inválida para una operación de tipo 'is' : <I>%s</I>";
$P2G_LANGUAGE['ERR_TPLPARSE_MISSING_TAG_ARGS'] = "La etiqueta <I>%s</I> requiere argumentos!";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_TAG_ARGS'] = "La etiqueta <I>%s</I> no puede contener argumentos!";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_MODIFIER'] = "El modificador %s no es válido o no existe!";
$P2G_LANGUAGE['ERR_WIDGET_INCLUDE'] = "El widget <i>%s</i> solo puede ser usado entre etiquetas INCLUDE WIDGET!";
$P2G_LANGUAGE['ERR_WIDGET_MANDATORY_PROPERTY'] = "La propiedad <i>%s</i> es obligatoria en la definición del widget <i>%s</i>!";
$P2G_LANGUAGE['ERR_EMPTY_DOC_LAYOUT'] = "No hay elementos definidos en la capa de documento suministrada.";
$P2G_LANGUAGE['ERR_CANT_REPLICATE_ROOT_BLOCK'] = "El bloque base de una plantilla no puede ser sobreescrito.";
$P2G_LANGUAGE['ERR_MUST_BE_POSITIVE'] = "El valor de %s debería se mayor que cero en la función %s.";
$P2G_LANGUAGE['ERR_MUST_BE_AN_ARRAY'] = "El valor de %s debería ser un arreglo en la función %s";
$P2G_LANGUAGE['ERR_VALUE_OUT_OF_BOUNDS'] = "El valor de '%s' se encuentra fuera de los límites permitidos: %s y %s";
$P2G_LANGUAGE['ERR_URL_MALFORMED'] = "El valor de <I>%s</I> no es un URL válido!";
$P2G_LANGUAGE['ERR_HOST_INVALID'] = "<I>%s</I> no es una conexión a servidor válida!";
$P2G_LANGUAGE['ERR_DNS_NOT_FOUND'] = "No fue posible encontrar un DNS válido para el dominio %s.";
$P2G_LANGUAGE['ERR_SESSION_PROPERTY_NOT_FOUND'] = "No se encontró la propiedad %s de la variable de sesión %s. Ejecute la función createProperty()";
$P2G_LANGUAGE['ERR_SESSION_TIMECOUNTER_NOT_FOUND'] = "No se encontró el contador de tiempo %s de la variable de sesión %s. Ejecute la función createTimeCounter()";
$P2G_LANGUAGE['ERR_INVALID_OBJECT'] = "El objeto pasado como parámetro no es una instancia válida de la clase <I>%s</I>!";
$P2G_LANGUAGE['ERR_CALCULATOR_MISSING_PARAMETERS'] = "Parámetros insuficientes para construir la calculadora!";
$P2G_LANGUAGE['ERR_COLORSEL_MISSING_PARAMETERS'] = "Parámetros insuficientes para construir el selector de color!";
$P2G_LANGUAGE['ERR_INVALID_FORM_METHOD'] = "El método %s no es válido para enviar el formulario '%s'";
$P2G_LANGUAGE['ERR_INVALID_VALIDATE_FUNC'] = "<I>%s</I> no es un nombre válido de función de validación!";
$P2G_LANGUAGE['ERR_FORM_DUPLICATED_FIELD'] = "El campo <I>%s</I> ya fue definido en el formulario <I>%s</I>!";
$P2G_LANGUAGE['ERR_FORM_DUPLICATED_BUTTON'] = "El botón <I>%s</I> ya ha sido definido en el formulario <I>%s</I>!";
$P2G_LANGUAGE['ERR_FORM_DUPLICATED_SECTION'] = "El ID de sección <I>%s</I> ya fue definido en el formulario <I>%s</I>!";
$P2G_LANGUAGE['ERR_FORM_SECTION_EVALFUNC'] = "No fue posible definir la visibilidad de la sección <I>%s</I>, ya que la función <I>%s</I> no ha sido definida!";
$P2G_LANGUAGE['ERR_FORM_SECTION_TPLBLOCK'] = "La sección <I>%s</I> es condicional. Usted debería declarar un bloque en la plantilla que contiene los campos de sección llamado <I>%s</I>!";
$P2G_LANGUAGE['ERR_FORM_REQUIRED_SUMMARY'] = "Los siguientes campos del formulario son obligatorios:";
$P2G_LANGUAGE['ERR_FORM_ERRORS_SUMMARY'] = "El formulario contiene los siguientes errores:";
$P2G_LANGUAGE['ERR_FORM_COMPLETE_FIELDS'] = "Por favor, llene los campos e intente nuevamente.";
$P2G_LANGUAGE['ERR_FORM_FIX_FIELDS'] = "Por favor, corrija los campos e intente nuevamente.";
$P2G_LANGUAGE['ERR_FORM_FIELD_REQUIRED'] = "El campo %s es obligatorio.";
$P2G_LANGUAGE['ERR_FORM_FIELD_INVALID'] = "El campo %s tiene un valor incorrecto!";
$P2G_LANGUAGE['ERR_FORM_FIELD_INVALID_DATATYPE'] = "El campo %s contiene un valor %s no válido.";
$P2G_LANGUAGE['ERR_FORM_FIELD_INVALID_FLOAT'] = "La precisión numérica del campo %s no es válida.\nLos tamaños máximos son %s para la parte entera y %s para la parte decimal.";
$P2G_LANGUAGE['ERR_FORM_FIELD_ALPHANUM'] = "El campo %s debería tener únicamente caracteres alfanuméricos.";
$P2G_LANGUAGE['ERR_FORM_FIELD_CHOICE'] = "El campo %s debería tener uno de los siguientes valores: <I>%s</I>.";
$P2G_LANGUAGE['ERR_FORM_FIELD_CREDITCARD'] = "El campo '%s' contiene un número de tarjeta de crédito %s inválido.";
$P2G_LANGUAGE['ERR_FORM_FIELD_MIN_LENGTH'] = "El campo %s debería tener al menos %s caracteres.";
$P2G_LANGUAGE['ERR_FORM_FIELD_MAX_LENGTH'] = "El campo %s debería tener máximo %s caracteres.";
$P2G_LANGUAGE['ERR_FORM_FIELD_EQ'] = "El campo %s debe ser igual al campo %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_NEQ'] = "El campo %s debe ser diferente al campo %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_GT'] = "El campo %s debe ser mayor que el campo %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_LT'] = "El campo %s debe ser menor que el campo %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_GOET'] = "El campo %s debe ser mayor o igual que el campo %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_LOET'] = "El campo %s debe ser menor o igual que el campo %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_EQ'] = "El campo %s debe tener un valor igual a %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_NEQ'] = "El campo %s debe tener un valor diferente a %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_GT'] = "El campo %s debe tener un valor mayor a %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_LT'] = "El campo %s debe tener un valor menor a %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_GOET'] = "El campo %s debe tener un valor mayor o igual a %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_LOET'] = "El campo %s debe tener un valor menor o igual a %s!";
$P2G_LANGUAGE['ERR_FORM_INVALID_MASK'] = "La máscara <I>%s</I>, en el campo<I>%s</I>, no es válida!";
$P2G_LANGUAGE['ERR_FORM_INVALID_FIELDTYPE'] = "El tipo de campo <I>%s</I> no es válido!";
$P2G_LANGUAGE['ERR_FORM_CAPTCHA'] = "El código indicado en el campo %s no coincide con el contenido de la imagen!";
$P2G_LANGUAGE['ERR_DATAGRID_INVALID_FIELDTYPE'] = "El tipo de campo <I>%s</I> no se puede utilizar dentro de una <I>rejilla</I>!";
$P2G_LANGUAGE['ERR_DATAGRID_INVALID_CELLSIZES'] = "El valor del atributo <I>CELLSIZES</I> en el campo <I>%s</I> no es válido. Usted debe suministrar N+1 tamaños (N=tamaño del FIELDSET) y la suma de los tamaños debería dar 100!";
$P2G_LANGUAGE['ERR_DATAGRID_INVALID_FIELDCOUNT'] = "La consulta definida en el <I>DATASOURCE</I> del campo <I>%s</I> debe retornar N+2 columnas (N=tamaño del FIELDSET)";
$P2G_LANGUAGE['ERR_FORM_WRONG_LISTENER'] = "Los atributos del manipulador de eventos (event handler) <I>%s</I> no son válidos o son incompletos!";
$P2G_LANGUAGE['ERR_FORM_WRONG_RULE'] = "Los atributos de la regla <I>%s</I> no son válidos o son incompletos!";
$P2G_LANGUAGE['ERR_INVALID_DATASOURCE_SYNTAX'] = "La especificación de la etiqueta DATASOURCE en el campo <I>%s</I> no es válida.<BR>Los nodos hijos KEYFIELD y LOOKUPTABLE son obligatorios!";
$P2G_LANGUAGE['ERR_MISSING_GROUPFIELD_CHILDREN'] = "La especificación del campo <I>%s</I> está incompleta. Usted deberí suministrar al menos un nodo hijo &lt;OPTION&gt;!";
$P2G_LANGUAGE['ERR_MISSING_OPTION_VALUE'] = "Falta el atributo VALUE de la opción en el índice <I>%d</I> del campo <I>%s</I>!";
$P2G_LANGUAGE['ERR_MISSING_DBGROUPFIELD_DATASOURCE'] = "No se definió una etiqueta DATASOURCE en el %s <I>%s</I>!";
$P2G_LANGUAGE['ERR_EMPTY_DBGROUPFIELD_RESULTS'] = "La consulta sumistrada en el elemento DATASOURCE del %s <I>%s</I> retornó un conjunto de datos vacío!";
$P2G_LANGUAGE['ERR_MISSING_COMBOOPTION_VALUE'] = "Falta el atribugo VALUE de la opción en el índice %d en el COMBOFIELD <I>%s</I>!";
$P2G_LANGUAGE['ERR_MISSING_EDITSELECTION_CHILDREN'] = "La especificación del EDITSELECTIONFIELD <I>%s</I> está incompleta. Los nodos hijos EDITFIELD y LOOKUPFIELD son obligatorios!";
$P2G_LANGUAGE['ERR_MISSING_LOOKUPSELECTION_CHILDREN'] = "La especificación del LOOKUPSELECTIONFIELD <I>%s</I> es errónea. El nodo debería tener dos hijos LOOKUPFIELD!";
$P2G_LANGUAGE['ERR_MISSING_SOURCELOOKUP_DATASOURCE'] = "No se definió DATASOURCE en el primer hijo LOOKUPFIELD del LOOKUPSELECTIONFIELD <I>%s</I>!";
$P2G_LANGUAGE['ERR_MISSING_QUERY_ELEMENTS'] = "Los elementos \$fields y \$tables son obligatorios en la construcción de la consulta SQL!";
$P2G_LANGUAGE['ERR_INVALID_DATAGRID_STRUCTURE'] = "La estructura de la rejilla <I>%s</I> no es válida. Usted debería declarar un <I>DATASOURCE</I> y un <I>FIELDSET</I> que contengan al menos un campo!";
$P2G_LANGUAGE['ERR_MISSING_RANGEFIELD_CHILDREN'] = "La especificación del RANGEFIELD <I>%s</I> es errónea. El nodo debería tener dos hijos EDITFIELD, COMBOFIELD, LOOKUPFIELD o DATEPICKERFIELD!";
$P2G_LANGUAGE['ERR_EDITSEARCH_INVALID_DATAFILTER'] = "La especificación del DATAFILTER <I>%s</I> no es válida.";
$P2G_LANGUAGE['ERR_EDITSEARCH_INVALID_DATAFILTER_MASK'] = "La máscara asignada al DATAFILTER <I>%s</I> no es válida.";
$P2G_LANGUAGE['ERR_EDITSEARCH_DUPLICATED_DATAFILTER'] = "Ya existe otro DATAFILTER con el ID <I>%s</I>.";
$P2G_LANGUAGE['ERR_MISSING_EDITSEARCH_CHILDREN'] = "La especificación del EDITSEARCHFIELD <I>%s</I> está incompleta. Usted debería proveer al menos un DATAFILTER, un DATASOURCE y un LOOKUPFIELD!";
$P2G_LANGUAGE['ERR_CSV_DB_JSRS'] = "No fue posible completar la operación!";
$P2G_LANGUAGE['ERR_SEARCHFORM_EMPTY'] = "La búsqueda no puede ser realizada debido a que todos los campos de búsqueda están vacíos.";
$P2G_LANGUAGE['ERR_SEARCHFORM_INVALID'] = "La búsqueda no puede ser realizada debido a que los valores de filtrado están vacíos o tienen menos de %d caracteres de longitud.";
$P2G_LANGUAGE['ERR_REPORT_MAX_GROUP_COLS'] = "La cantidad de columnas de agrupación debería ser menor que el número de columnas de la consulta!";
$P2G_LANGUAGE['ERR_REPORT_UNKNOWN_GROUP_COL'] = "La columna de agrupación <I>%s</I> no está definida en el conjunto de resultados de la consulta!";
$P2G_LANGUAGE['ERR_REPORT_MISSING_BLOCK_VARIABLE'] = "No se declaró en la plantilla la variable <I>%s</I> del bloque <I>%s</I>!";
$P2G_LANGUAGE['ERR_REPORT_INVALID_COLSIZES'] = "El valor <I>%s</I> no es una definición válida para los tamaños de coluna!";
$P2G_LANGUAGE['ERR_REPORT_COL_SIZES_SUM'] = "La suma de los tamaños de columna suministrados a la función setColumnSizes debería ser igual a 100!";
$P2G_LANGUAGE['ERR_REPORT_COL_COUNT_MISMATCH'] = "La cantidad de tamaños de columna suministrados (%s) no es igual al número de columnas del reporte (%s, agrupación %s )";
$P2G_LANGUAGE['ERR_REPORT_MISSING_SEARCH_VARIABLE'] = "No fue posible construir el formulario de búsqueda ya que no se encontró la variable <I>%s</I>.<BR>Para proceder declare {%s} en la plantilla de reporte.";
$P2G_LANGUAGE['ERR_REPORT_SEARCH_PARS_MALFORMED'] = "Usted debería suministrar la Etiqueta, el Nombre de Campo y la Máscara en la construcción de los parámetros de búsqueda!";
$P2G_LANGUAGE['ERR_REPORT_SEARCH_INVALID_MASK'] = "La máscara <I>%s</I> del parámetro de búsqueda <I>#%d</I> no es válida!";
$P2G_LANGUAGE['ERR_REPORT_MIN_ALT_STYLE'] = "La función setAlternateStyle debería recibir al menos dos parámetros (nombres CSS)!";
$P2G_LANGUAGE['ERR_REPORT_HIDDEN_GROUP'] = "No se puede incluir una columna oculta (%s) en los encabezados del grupo!";
$P2G_LANGUAGE['ERR_REPORT_GROUP_HIDDEN'] = "No se puede definir como oculta una columna que se muestra en los encabezados de grupo (%s)!";
$P2G_LANGUAGE['ERR_REPORT_MAX_HIDDEN_COLS'] = "La cantidad de columnas ocultas debe ser menor que la cantidad de columnas de la consulta!";
$P2G_LANGUAGE['ERR_REPORT_UNKNOWN_HIDDEN_COL'] = "La columna <I>%s</I>, definida como oculta, no existe en el conjunto de resultados de la consulta!";
$P2G_LANGUAGE['ERR_DATASET_INVALID_TYPE'] = "El tipo <I>%s</I> no es un tipo DataSet válido!";
$P2G_LANGUAGE['ERR_MENU_WRONG_ROOT_SQL'] = "La consulta almacenada en el parámetro \$rootSql del método Menu::loadFromDatabase() retornó un conjunto vacío de datos o no tiene el mínimo número de campos requerido (CAPTION y LINK)!";
$P2G_LANGUAGE['ERR_MENU_WRONG_CHILDREN_STATEMENT'] = "La consulta pasada en el parámetro \$childSql del método Menu::loadFromDatabase() no es válida.<BR>Suministre una sentencia que contenga '~var~' (Columna Relación) mas los dos campos requeridos (CAPTION y LINK)";
$P2G_LANGUAGE['ERR_MENU_NOT_FOUND'] = "No se creó el menú. Construya los elementos de menú utilizando Menu::loadFromDatabase() o Menu::loadFromXmlFile().";
$P2G_LANGUAGE['ERR_MENU_EMPTY_XML_ROOT'] = "El nodo raíz del archivo XML no tiene nodos hijos";
$P2G_LANGUAGE['ERR_MENU_INVALID_IMAGE'] = "La imagen <I>%s</I> no es válida. Los posibles valores son: <I>%s</I>.";
$P2G_LANGUAGE['ERR_FTP_MISSING_HOST'] = "No fue posible abrir la conexión. Usted primero debería configurar la conexión usando los métodos FtpClient::setServer() y FtpClient::setUserInfo()";
$P2G_LANGUAGE['ERR_FTP_MISSING_USER_OR_PASS'] = "No fue posible autenticarse. No se suministró uno de los valores de usuario (nombre de usuario o contraseña)";
$P2G_LANGUAGE['ERR_SPRSH_OUT_OF_BOUNDS'] = "Se excedieron los límites de la hoja de cálculo: [%d;%d]";
$P2G_LANGUAGE['ERR_SPRSH_MAX_NOTE_CHARS'] = "Se excedió el tamaño máximo para una nota de celda<BR>Valor: %s";
$P2G_LANGUAGE['ERR_SPRSH_MAX_FONTS'] = "Se excedió el límite de máximo 4 fuentes por cada hoja de cálculo!";
$P2G_LANGUAGE['ERR_UPLOAD_NOT_FOUND'] = "No fue posible completar la subida (upload). El archivo no se transfirió correctamente.";
$P2G_LANGUAGE['ERR_UPLOAD_TOO_BIG'] = "No fue posible completar la subida (upload). El tamaño del archivo es mayor que el máximo permitido.";
$P2G_LANGUAGE['ERR_UPLOAD_INVALID_NAME'] = "No fue posible completar la subida (upload). El nombre de archivo %s no es válido.";
$P2G_LANGUAGE['ERR_UPLOAD_INVALID_TYPE'] = "No fue posible completar la subida (upload). La extensión %s no es permitida.";
$P2G_LANGUAGE['ERR_UPLOAD_FILE_EXISTS'] = "No fue posible completar la subida (upload). El archivo <I>%s</I> ya existe y no se sobreescribió.";
$P2G_LANGUAGE['ERR_UPLOAD_CANT_MOVE'] = "No fue posible mover el archivo temporal a subir (upload) a la carpeta de destino.";
$P2G_LANGUAGE['ERR_CANT_OPEN_SOCKET'] = "No fue posible abrir el socket usando el puerto %s con el servidor %s!%s";
$P2G_LANGUAGE['ERR_SOCKET_NOT_CONNECTED'] = "No está activa la conexión con el socket!";
$P2G_LANGUAGE['ERR_SOCKET_EOF'] = "Se alcanzó el fin del archivo, no se pudo completar la operación!";
$P2G_LANGUAGE['ERR_SOCKET_TIMEOUT'] = "Se superó el tiempo máximo de ejecución del socket. No se puede completar la operación!";
$P2G_LANGUAGE['ERR_MAIL_EMPTY_RCPT'] = "No hay receptores para el mensaje!";
$P2G_LANGUAGE['ERR_SIGNED_MESSAGE_SIGN'] = "No fue posible firmar el contenido del mensaje.<BR>%s";
$P2G_LANGUAGE['ERR_SMTP_CONNECTION'] = "Error al conectarse al servidor SMTP %s!<BR>[Error %d] - %s";
$P2G_LANGUAGE['ERR_SMTP_COMMAND'] = "Error al ejecutar el comando %s en el servidor SMTP!<BR>[Error %d] - %s";
$P2G_LANGUAGE['ERR_SMTP_AUTHENTICATE'] = "No fue posible autenticar con el servidor SMTP!";
$P2G_LANGUAGE['ERR_CRYPT_MISSING_KEY'] = "Para encriptar o desencriptar datos, usted debería suministrar una llave de encripción a través del método Crypt::setKey()!";
$P2G_LANGUAGE['ERR_CRYPT_OPEN_MODULE'] = "No fue posible inicializar el cifrado <I>%s</I> utilizando el modo %s!";
$P2G_LANGUAGE['ERR_OPENSSL_PUBKEY_ENCRYPT'] = "No fue posible extraer una llave pública para encriptar los datos suministrados. Provea al método OpenSSLCrypt::setCertificatePath() el archivo que contiene un certificado válido!";
$P2G_LANGUAGE['ERR_OPENSSL_PUBKEY_VERIFY'] = "No fue posible extraer una llave pública para verificar la firma incluida en estos datos. Provea al método OpenSSLCrypt::setCertificatePath() el archivo que contiene un certificado válido.";
$P2G_LANGUAGE['ERR_OPENSSL_PRIVKEY_DECRYPT'] = "No fue posible extraer una llave privada para desencriptar los datos suministrados. Provea el archivo que contiene la llave privada y la frase clave, si la llave requiere de una.";
$P2G_LANGUAGE['ERR_OPENSSL_PRIVKEY_SIGN'] = "No fue posible extraer una llave privada para generar la firma digital. Provea el archivo que contiene la llave privada y la frase clave, si la llave creada requiere de una.";
$P2G_LANGUAGE['ERR_OPENSSL_CERT_PATH'] = "La ruta del certificado no es válida!";
$P2G_LANGUAGE['ERR_OPENSSL_READ_CERT'] = "No fue posible leer el contenido del certificado!%s";
$P2G_LANGUAGE['ERR_POP3_CONNECTION'] = "Error al conectarse al servidor POP3 %s!<BR>[Error %d] - %s";
$P2G_LANGUAGE['ERR_POP3_COMMAND'] = "Error al ejecutar el comando %s en el servidor POP!<BR>%s";
$P2G_LANGUAGE['ERR_POP3_AUTHENTICATE'] = "No fue posible autenticar con el servidor POP3!";
$P2G_LANGUAGE['ERR_POP3_APOP'] = "No fue posible ejecutar el comando APOP : no hay un mensaje de autenticación almacenado!";
$P2G_LANGUAGE['ERR_HTTP_CONNECT'] = "No fue posible establecer una conexión HTTP con el servidor <I>%s</I>!";
$P2G_LANGUAGE['ERR_HTTP_MISSING_HOST'] = "No se estableció el servidor HTTP. Ejecute el método HttpClient::setHost().";
$P2G_LANGUAGE['ERR_CANT_INSTANTIATE_VALIDATOR'] = "No fue posible instanciar la clase de validación <B>%s</B>!";
$P2G_LANGUAGE['ERR_INVALID_VALIDATOR'] = "La clase de validación <I>%s</I> no es válida! No se puede encontrar el método %s::execute().";
$P2G_LANGUAGE['ERR_ASSERTION_MESSAGE'] = "La expresión evaluada no alcanzó los resultados esperados.";
$P2G_LANGUAGE['ERR_INVALID_CALLBACK'] = "La función o método <I>%s</I> no es una retrollamada (callback) válida!";
$P2G_LANGUAGE['ERR_INVALID_PROPERTIES_FILE'] = "The file <I>%s</I> doesn't contain valid configuration data!";
$P2G_LANGUAGE['ERR_INVALID_COLOR_SPEC'] = "<I>%s</I> no es una especificación de color válida.";
$P2G_LANGUAGE['ERR_ALLOCATE_COLOR'] = "No fue posible asignar un nuevo color a esta imagen!";
$P2G_LANGUAGE['ERR_JSRS_MISSING_HANDLER'] = "El nombre o alias del manipulador (handler) JSRS es erróneo o falta!";
$P2G_LANGUAGE['ERR_JSRS_INVALID_HANDLER'] = "El manipulador (handler) JSRS %s no es válido!";
$P2G_LANGUAGE['ERR_AJAX_SUPPORT'] = "Su navegador no soporta AJAX!";
$P2G_LANGUAGE['ERR_JSON_INVALID_PROPNAME'] = "Error de decodificación JSON: %s no es un nombre de propiedad válido";
$P2G_LANGUAGE['ERR_JSON_MISSING_COLON'] = "Error de decodificación JSON: falta el caracter dos puntos en la posición %d";
$P2G_LANGUAGE['ERR_JSON_INVALID_PROPVALUE'] = "Error de decodificación JSON: %s no es un valor de propiedad válido";
$P2G_LANGUAGE['ERR_JSON_MISSING_COMMA'] = "Error de decodificación JSON: falta una coma en la posición %d";
$P2G_LANGUAGE['ERR_JSON_ESCAPE_SEQUENCE'] = "Error de decodificación JSON: secuencia de escape no válida en la posición %d";
$P2G_LANGUAGE['ERR_JSON_UNTERMINATED_STRING'] = "Error de decodificación JSON: cadena literal sin finalizar en la posición  %d";
$P2G_LANGUAGE['ERR_JSON_CYCLE'] = "El codificador JSON encontró un ciclo en una instancia de la clase %s!";
$P2G_LANGUAGE['ERR_JSON_ENCODE'] = "Ocurrió un error al convertir el tipo %s a una cadena JSON!";
$P2G_LANGUAGE['DEFAULT_BTN_VALUE'] = "Enviar";
$P2G_LANGUAGE['CALENDAR_LINK_TITLE'] = "Abrir el calendario";
$P2G_LANGUAGE['CALCULATOR_LINK_TITLE'] = "Abrir la calculadora";
$P2G_LANGUAGE['CALCULATOR_WINDOW_TITLE'] = "Calculadora";
$P2G_LANGUAGE['CALCULATOR_RESULT_CAPTION'] = "Resultado";
$P2G_LANGUAGE['DAYS_OF_WEEK'] = array("Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado");
$P2G_LANGUAGE['DAYS_OF_WEEK_ABBR'] = array("Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sab");
$P2G_LANGUAGE['MONTHS_OF_YEAR'] = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
$P2G_LANGUAGE['MONTHS_OF_YEAR_ABBR'] = array("Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic");
$P2G_LANGUAGE['COMPRESS_USE_MSG'] = "<!-- Utiliza Compresión %s -->";
$P2G_LANGUAGE['COMPRESS_DEBUG_MSG_ORIG'] = "Tamaño Original: %d";
$P2G_LANGUAGE['COMPRESS_DEBUG_MSG_COMP'] = "Tamaño Comprimido: %d";
$P2G_LANGUAGE['EMPTY_SEARCH'] = "Usted debería seleccionar un filtro e ingresar un término de búsqueda!";
$P2G_LANGUAGE['EMPTY_RESULTS'] = "La búsqueda retornó un conjunto de resultados vacío!";
$P2G_LANGUAGE['FORM_INPUT_MASK_MESSAGES'] = array(
	'optionalCharFirst' => "El indicador de caracter opcional debe ir después de un literal.",
	'escapeCharLast' => "El caracter indicador de escape debe ir antes de un literal.",
	'nestedCharClass' => "Clase de caracter no válida.",
	'invalidLimits' => "Definición de límites de tamaño no válida."
);
$P2G_LANGUAGE['FORM_MASKS'] = array(
	'CPFCNPJ' => 'CPF/CNPJ',
	'CURRENCY' => 'Moneda',
	'DATE' => 'Fecha',
	'TIME' => 'Hora',
	'EMAIL' => 'Correo-e',
	'FLOAT' => 'Número Flotante',
	'INTEGER' => 'Número Entero',
	'DIGIT' => 'Número',
	'IP' => 'Dirección IP',
	'WORD' => 'Texto alfanumérico',
	'URL' => 'Dirección URL/Internet',
	'ZIP' => 'Código Postal'
);
$P2G_LANGUAGE['EDITSEARCH_BTN_VALUE'] = "Buscando...";
$P2G_LANGUAGE['EDITOR_VARS'] = array(
	'formatBlock' => 'Párrafo',
	'formatBlockOptions' => array(
		'h1' => 'Encabezado 1 &lt;H1&gt;',
		'h2' => 'Encabezado 2 &lt;H2&gt;',
		'h3' => 'Encabezado 3 &lt;H3&gt;',
		'h4' => 'Encabezado 4 &lt;H4&gt;',
		'h5' => 'Encabezado 5 &lt;H5&gt;',
		'h6' => 'Encabezado 6 &lt;H6&gt;',
		'address' => 'Dirección &lt;ADDR&gt;',
		'pre' => 'Formateado &lt;PRE&gt;',
		'removeformat' => 'Eliminar Formato'
	),
	'topButtons' => array(
		'bold' => 'Formato Negrita',
		'italic' => 'Formato Cursiva',
		'underline' => 'Formato Subrayado',
		'strikethrough' => 'Formato Tachado',
		'cut' => 'Cortar',
		'copy' => 'Copiar',
		'paste' => 'Pegar',
		'pickforecolor' => 'Cambiar el color de Frente',
		'pickbackcolor' => 'Cambiar el color de Fondo',
		'addemoticon' => 'Insertar un Smiley',
		'selectall' => 'Seleccionar Todo',
		'removeformat' => 'Eliminar Formato'
	),
	'font' => 'Fuente',
	'fontSize' => 'Tamaño',
	'bottomButtons' => array(
		'justifyleft' => 'Alineación izquierda',
		'justifycenter' => 'Centrar el texto',
		'justifyright' => 'Alineación derecha',
		'justifyfull' => 'Justificar texto',
		'insertorderedlist' => 'Insertar/Eliminar Lista Numerada',
		'insertunorderedlist' => 'Insertar/Eliminar Lista Simple',
		'indent' => 'Aumentar la Identación',
		'outdent' => 'Reducir la Identación',
		'createlink' => 'Insertar un Vínculo',
		'unlink' => 'Desvincular',
		'insertimage' => 'Insertar una Imagen',
		'inserthorizontalrule' => 'Insertar regla horizontal'
	),
	'editMode' => 'Editar en Modo Avanzado',
	'emoticon' => 'Seleccionar un Smiley',
	'validateMode' => 'Para utilizar las herramientas del editor avanzado, desmarque la opción \'Editor Avanzado\'',
	'createLink' => 'Ingrese la dirección del vínculo (Ejemplo: http://www.domain.com):',
	'insertImage' => 'Ingrese la ruta de la imagen:'
);
$P2G_LANGUAGE['COLOR_PICKER_VARS'] = array(
	'popupTitle' => 'Presione clic sobre el color que desea',
	'colorTitle' => 'Seleccionar este color'
);
$P2G_LANGUAGE['LOOKUP_CHOICE_FILTER_TIP'] = "--- Filtrar la lista ---";
$P2G_LANGUAGE['LOOKUP_SELECTION_DEFAULT_SELFIRST'] = "--- Insertar Valores ---";
$P2G_LANGUAGE['LOOKUP_SELECTION_BUTTON_TITLES'] = array(
	'add' => 'Agregar los Elementos seleccionados',
	'addall' => 'Agregar todos los Elementos',
	'rem' => 'Eliminar los Elementos seleccionados',
	'remall' => 'Eliminar todos los Elementos'
);
$P2G_LANGUAGE['EDIT_SELECTION_BUTTON_TITLES'] = array(
	'add' => 'Agregar',
	'rem' => 'Eliminar los Elementos seleccionados',
	'remall' => 'Eliminar todos los Elementos'
);
$P2G_LANGUAGE['MEMO_COUNT_LABEL'] = "Caracteres";
$P2G_LANGUAGE['SEL_AVAILABLE_VALUES_LABEL'] = "Disponibles:";
$P2G_LANGUAGE['SEL_INSERTED_VALUES_LABEL'] = "Insertados:";
$P2G_LANGUAGE['FORM_DATA_BIND_MESSAGES'] = array(
	'insertSuccess' => 'Los datos se insertaron satisfactoriamente!',
	'updateEmpty' => 'No hay registros para editar!',
	'updateSuccess' => 'Los datos se actualizaron satisfactoriamente!',
	'deleteEmpty' => 'No hay registros para eliminar!',
	'deleteSuccess' => 'Los datos se borraron satisfactoriamente!',
	'deleteConfirm' => 'Realmente desea eliminar este registro?',
	'sortInvalid' => 'Usted debería seleccionar un campo por el cual ordenar!',
	'gotoEmpty' => 'Usted debería ingresar el número de registro al que desea ir!',
	'gotoInvalid' => 'El registro solicitado no es válido!'
);
$P2G_LANGUAGE['FORM_DATA_BIND_TOOLBAR_VALUES'] = array(
	'navFirstTip' => 'Ir al primer registro',
	'navPrevTip' => 'Ir al registro anterior',
	'navNextTip' => 'Ir al siguiente registro',
	'navLastTip' => 'Ir al último registro',
	'actNew' => 'Nuevo',
	'actNewTip' => 'Crear un nuevo registro',
	'actEdit' => 'Editar',
	'actEditTip' => 'Editar el registro actual',
	'actDel' => 'Borrar',
	'actDelTip' => 'Borrar el registro actual',
	'actSave' => 'Guardar',
	'actSaveTip' => 'Guardar el registro actual',
	'actCancel' => 'Cancelar',
	'actCancelTip' => 'Cancelar la acción actual',
	'sortAsc' => 'Orden Ascendente',
	'sortDesc' => 'Orden Descendente',
	'sortChoose' => 'Seleccione un campo por el cual ordenar',
	'sortTit' => 'Orden',
	'sortFirst' => 'Campos de Ordenamiento',
	'gotoTip' => 'Indique el número de registro y presione Ir',
	'goto' => 'Ir',
	'gotoBtnTip' => 'Presione para ir al registro seleccionado',
	'gotoTit' => 'Ir a',
	'filter' => 'Filtrar',
	'filterTip' => 'Seleccione el campo al cual se le aplicará el filtro',
	'filterVTip' => 'Seleccione el valor del filtro',
	'filterBtnTip' => 'Presione aquí para filtrar',
	'filterTit' => 'Filtrar',
	'filterFirst' => 'Campos de Filtro'
);
$P2G_LANGUAGE['REPORT_INVALID_PAGE'] = "Número de página inválido!";
$P2G_LANGUAGE['REPORT_EMPTY_VALUES'] = array(
	'emptyMsg' => 'La consulta o los filtros de búsqueda utilizados retornaron un juego de registros vacío',
	'backLink' => 'Volver al Reporte'
);
$P2G_LANGUAGE['REPORT_ORDER_TIP'] = "Ordenar por %s";
$P2G_LANGUAGE['REPORT_ORDER_OPTIONS_LABEL'] = "Ordenar por:";
$P2G_LANGUAGE['REPORT_FUNCTION_MESSAGES'] = array(
	'firstTit' => 'Primera',
	'firstTip' => 'Ir a la primera p&aacute;gina',
	'prevTit' => 'Anterior',
	'prevTip' => 'Ir a la p&aacute;gina anterior en el reporte',
	'prevScrTit' => '%d Atr&aacute;s',
	'prevScrTip' => '%d P&aacute;ginas atr&aacute;s en el reporte',
	'pageTip' => 'Ir a la p&aacute;gina #%d de %d',
	'nextTit' => 'Siguiente',
	'nextTip' => 'Ir a la p&aacute;gina siguiente en el reporte',
	'nextScrTit' => '%d Adelante',
	'nextScrTip' => '%d P&aacute;ginas adelante en el reporte',
	'lastTit' => '&Uacute;ltima',
	'lastTip' => 'Ir a la &uacute;ltima p&aacute;gina',
	'rowCount' => '%d registro(s) encontrado(s)',
	'rowsPerPage' => '%d resultado(s) por p&aacute;gina',
	'thisPage' => 'P&aacute;gina %d de %d',
	'rowInterval' => 'Mostrando los resultados %d-%d de %d ',
	'goTo' => 'Ir a la p&aacute;gina'
);
$P2G_LANGUAGE['REPORT_SEARCH_VALUES'] = array(
	'searchTitle' => 'Buscar',
	'filtersTitle' => 'Seleccione un Campo',
	'addBtn' => 'Agregar',
	'mainOpOr' => 'Cualquier Filtro',
	'mainOpAnd' => 'Todos los Filtros',
	'clearBtn' => 'Limpiar',
	'viewBtn' => 'Ver',
	'sendBtn' => 'Buscar',
	'emptyFilters' => 'No hay filtros insertados',
	'closeFilters' => 'Cerrar',
	'removeFilter' => 'Borrar',
	'addedFilter' => 'Se adicionó el Filtro!',
	'resendConfirmation' => 'Desea repetir la búsqueda nuevamente con el filtro(s) actual?'
);
$P2G_LANGUAGE['REPORT_NUMBER_OPERATORS'] = array(
	'=' => 'Igual a',
	'!=' => 'Diferente de',
	'>' => 'Mayor que',
	'<' => 'Menor que',
	'>=' => 'Mayor o igual a',
	'<=' => 'Menor o igual a'
);
$P2G_LANGUAGE['REPORT_STRING_OPERATORS'] = array(
	'=' => 'Igual a',
	'!=' => 'Diferente de',
	'LIKE' => 'Conteniendo',
	'NOT LIKE' => 'que no contenga',
	'LIKEI' => 'Iniciando con',
	'LIKEF' => 'Terminando en'
);
$P2G_LANGUAGE['OPERATORS'] = array(
	'EQ' => 'igual a',
	'NEQ' => 'diferente de',
	'GT' => 'mayor que',
	'LT' => 'menor que',
	'GOET' => 'mayor o igual a',
	'LOET' => 'menor o igual a',
	'CONTAINING' => 'conteniendo',
	'STARTING' => 'iniciando con',
	'ENDING' => 'terminando en',
	'BETWEEN' => 'entre',
	'IN' => 'está en la lista',
	'NOTIN' => 'no está en la lista',
	'AND' => 'y',
	'OR' => 'o'
);
$P2G_LANGUAGE['CHECKBOX_DESCRIPTIONS'] = array(
	'T' => '%s está seleccionado',
	'F' => '%s no está seleccionado'
);
$P2G_LANGUAGE['CHECKGROUP_SHORTCUTS'] = array(
	'all' => 'Todos',
	'none' => 'Ninguno',
	'invert' => 'Invertir'
);
return $P2G_LANGUAGE;
?>