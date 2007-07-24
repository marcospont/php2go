<?php
/**
 * PHP2Go Web Development Framework
 *
 * Copyright (c) 2002-2007 Marcos Pont
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
 * @author Jiri Vasina <poutnik@users.sourceforge.net>
 * @copyright 2002-2007 Marcos Pont
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version $Id$
 */

$P2G_LANGUAGE['ERR_OLD_PHP_VERSION'] = "PHP2Go nem&#367;&#382;e fungovat se sou&#269;asn&#253;m nastavením serveru.<br />Va&#353;e verze PHP je: <I>%s</I>. Minimální pot&#345;ebná verze je <I>%s</I>.<br />Nainstalujte tuto nebo nov&#283;j&#353;í verzi PHP pro b&#283;&#382;nou práci.";
$P2G_LANGUAGE['ERR_UNSUPPORTED_EXTENSION'] = "Roz&#353;í&#345;ení <I>%s</I> není podporováno sou&#269;asnou instalací PHP.";
$P2G_LANGUAGE['ERR_OBJ_MESSAGE'] = "Chyba ve t&#345;íd&#283; %s:<br />%s";
$P2G_LANGUAGE['ERR_SCRIPT_MESSAGE'] = "Chyba ve skriptu:<br />%s";
$P2G_LANGUAGE['ERR_DATABASE'] = "Chyba databáze";
$P2G_LANGUAGE['ERR_INVALID_VALUE'] = "Neplatná hodnota!";
$P2G_LANGUAGE['ERR_DUPLICATED_VALUE'] = "Tato hodnota byla ji&#382; vlo&#382;ena!";
$P2G_LANGUAGE['ERR_COMM_FAILURE'] = "Selhání komunikace";
$P2G_LANGUAGE['ERR_MISSING_DATABASE_PARAMETERS'] = "Nebylo mo&#383;né nahrát parametry pro spojení s databází! [ID: %s]";
$P2G_LANGUAGE['ERR_INVALID_DATABASE_PARAMETERS'] = "Parametry pro spojení s databází nejsou platné! Polo&#383;ky USER, BASE a TYPE jsou povinné. [ID: %s]";
$P2G_LANGUAGE['ERR_DATABASE_CONNECTION_FAILED'] = "Se sou&#269;asn&#253;m nastavením nebylo mo&#382;né se p&#345;ipojit k databázi. Je toto nastavení správné?";
$P2G_LANGUAGE['ERR_INVALID_CONNECTION_CLASS_PATH'] = "Nebylo mo&#382;né na&#269;íst &#269;i inicializovat t&#345;ídu spojení %s!";
$P2G_LANGUAGE['ERR_INVALID_CONNECTION_CLASS'] = "T&#345;ída spojení %s není podt&#345;ídou php2go.db.Db!";
$P2G_LANGUAGE['ERR_TYPES'] = array(
	1 => "Fatální chyba",
	2 => "Upozorn&#283;ní",
	4 => "Chyba syntaxe",
	8 => "Poznámka",
	16 => "Fatální chyba jádra",
	32 => "Upozorn&#283;ní jádra",
	64 => "Fatální chyba p&#345;eklada&#269;e",
	128 => "Upozorn&#283;ní p&#345;eklada&#269;e",
	256 => "U&#382;ivatelská chyba",
	512 => "U&#382;ivatelské upozorn&#283;ní",
	1024 => "U&#382;ivatelská poznámka",
	4096 => "Catchable Fatal Error"
);
$P2G_LANGUAGE['ERR_ABSOLUTE_URI_NOT_FOUND'] = "Záznam 'ABSOLUTE_URI' nebyl zadán v konfigura&#269;ním poli!";
$P2G_LANGUAGE['ERR_CONFIG_ENTRY_NOT_FOUND'] = "Záznam <I>%s</I> nebyl v globálním konfigura&#269;ním poli nastaven!";
$P2G_LANGUAGE['ERR_INVALID_LANGDOMAIN_FILE'] = "Soubor text&#367; pro doménu %s, jazyk %s, je neplatný nebo neexistuje.";
$P2G_LANGUAGE['ERR_CANT_FIND_CFG_VAL'] = "Nem&#367;&#382;u najít záznam <I>%s</I> v globální konfiguraci. Byl správn&#283; nastaven p&#345;i instalaci?";
$P2G_LANGUAGE['ERR_CANT_LOAD_MODULE'] = "Nem&#367;&#382;u na&#269;íst modul <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_LOAD_DIR'] = "Nem&#367;&#382;u na&#269;íst moduly z adresá&#345;e <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_LOAD_DIR_MODULE'] = "Nem&#367;&#382;u na&#269;íst soubor <I>%s</I> na <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_CONVERT_DATE'] = "Nem&#367;&#382;u p&#345;evést datum na formát %s.";
$P2G_LANGUAGE['ERR_CANT_RUN_ON_WINDOWS'] = "T&#345;ída  %s m&#367;&#382;e b&#253;t pou&#382;ita pod  Windows!";
$P2G_LANGUAGE['ERR_UNDEFINED_FUNCTION'] = "Funkce  %s není definována!";
$P2G_LANGUAGE['ERR_UNDEFINED_METHOD'] = "Metoda %s není definována ve t&#345;íd&#283; %s!";
$P2G_LANGUAGE['ERR_ABSTRACT_CLASS'] = "T&#345;ída  <I>%s</I> je abstraktní a nem&#367;&#382;e b&#253;t vytvo&#345;ena její p&#345;ímá instance!";
$P2G_LANGUAGE['ERR_DATE_FORMAT'] = "Datum %s není v po&#382;adovaném formátu %s.";
$P2G_LANGUAGE['ERR_INVALID_RESOURCE'] = "Prom&#283;nná %s p&#345;edaná funkci nebo metod&#283; %s není pou&#382;iteln&#253;m zdrojem.";
$P2G_LANGUAGE['ERR_INVALID_AUTHENTICATOR'] = "Autentikátor %s není podt&#345;ídou php2go.auth.Auth!";
$P2G_LANGUAGE['ERR_INVALID_AUTHENTICATOR_PATH'] = "Nebylo mo&#383;né importovat nebo vytvo&#345;it autentikátor %s!";
$P2G_LANGUAGE['ERR_INVALID_USERCONTAINER'] = "Správce u&#383;ivatel&#367; %s není podt&#345;ídou php2go.auth.User!";
$P2G_LANGUAGE['ERR_INVALID_USERCONTAINER_PATH'] = "Nebylo mo&#383;né importovat nebo vytvo&#345;it správce u&#383;ivatel&#367; %s!";
$P2G_LANGUAGE['ERR_INVALID_AUTHORIZER'] = "Autorizátor %s není podt&#345;ídou php2go.auth.Authorizer!";
$P2G_LANGUAGE['ERR_INVALID_AUTHORIZER_PATH'] = "Nebylo mo&#382;né importovat nebo inicializovat autorizátor %s!";
$P2G_LANGUAGE['ERR_XML_PARSE'] = "P&#345;i procházení souboru XML se vyskytla chyba:<br />Chyba: %s<br />&#345;ádek: %s, Sloupec: %s";
$P2G_LANGUAGE['ERR_STATEMENT_EVAL'] = "P&#345;i procházení PHP kódu <I>%s</I> se vyskytla chyba";
$P2G_LANGUAGE['ERR_CANT_FIND_FILE'] = "Nebylo mo&#382;no nalézt soubor nebo adresá&#345; %s. Existuje?";
$P2G_LANGUAGE['ERR_CANT_OPEN_FILE'] = "Nebylo mo&#382;no otev&#345;ít soubor %s v módu %s. Zkontrolujte p&#345;ístupová práva a pou&#382;it&#253; mód.";
$P2G_LANGUAGE['ERR_CANT_CREATE_FILE'] = "Nebylo mo&#382;no vytvo&#345;it soubor nebo adresá&#345; %s. Zkontrolujte p&#345;ístupová práva vy&#353;&#353;ího adresá&#345;e.";
$P2G_LANGUAGE['ERR_CANT_READ_FILE'] = "Nebylo mo&#382;no otev&#345;ít soubor %s pro &#269;tení. Existuje? Je správn&#283; nastavena cesta?";
$P2G_LANGUAGE['ERR_CANT_WRITE_FILE'] = "Nebylo mo&#382;no otev&#345;ít soubor nebo adresá&#345; %s pro zápis. Máte p&#345;ístupová práva pro zápis?";
$P2G_LANGUAGE['ERR_CANT_DELETE_FILE'] = "Nebylo mo&#382;no smazat soubor %s.<br />Máte p&#345;ístupová práva v tomto adresá&#345;i? Máte p&#345;ístupová práva pro smazání tohoto souboru?";
$P2G_LANGUAGE['ERR_CANT_INCLUDE_FILE'] = "Nebylo mo&#382;no otev&#345;ít soubor %s pro p&#345;ipojení (\"inclusion\"). Existuje? Je správn&#283; nastavená cesta?";
$P2G_LANGUAGE['ERR_CANT_CHANGE_MODE'] = "Nebylo mo&#382;no nastavit mód %s pro soubor nebo adresá&#345; %s.";
$P2G_LANGUAGE['ERR_CANT_READ_DIR'] = "Nebylo mo&#382;né nalézt adresá&#345; %s. Existuje?";
$P2G_LANGUAGE['ERR_CANT_OPEN_DIR'] = "Nebylo mo&#382;né otev&#345;ít adresá&#345; %s pro &#269;tení. Zkontrolujte nastavení p&#345;ístupov&#253;ch práv.";
$P2G_LANGUAGE['ERR_CANT_FIND_SERIALIZATION_FILE'] = "Nebylo mo&#382;né otev&#345;ít soubor obsahující serializovan&#253; objekt pro &#269;tení. Je správn&#283; nastavená cesta? Byl soubor vytvo&#345;en?";
$P2G_LANGUAGE['ERR_EMPTY_INSERT_QUERY'] = "Dotaz vlo&#382;ení do databáze '%s' se zadan&#253;mi hodnotami vrátil prázdn&#253; záznam.";
$P2G_LANGUAGE['ERR_EMPTY_UPDATE_QUERY'] = "Dotaz &#219;pravy záznamu v databázi '%s' s p&#345;íkazem %s se zadan&#253;mi hodnotami vrátil prázdn&#253; záznam.";
$P2G_LANGUAGE['ERR_EMPTY_TOGLOBALS_QUERY'] = "Dotaz %s, u&#382;it&#253; pro publikování prom&#283;nn&#253;ch globáln&#283;, vrátil prázdn&#253; záznam.";
$P2G_LANGUAGE['ERR_TOGLOBALS_WRONG_USAGE'] = "Dotaz p&#345;edn&#253; metod&#283; Db->toGlobals() musí za&#269;ínat pomocí 'SELECT' (DQL).";
$P2G_LANGUAGE['ERR_EMPTY_TEMPLATE_FILE'] = "Nebylo mo&#382;no inicializovat P&#345;edlohu s prázdn&#253;m jménem souboru!";
$P2G_LANGUAGE['ERR_EMPTY_TEMPLATE_CONTENT'] = "P&#345;edloha nebyla p&#345;ipravena, proto&#382;e je prázdná. Zkontrolujte obsah p&#345;edan&#253; vytvá&#345;ecí metod&#283; (\"constructor\").";
$P2G_LANGUAGE['ERR_CANT_FIND_VARIABLE'] = "Nebylo mo&#382;né najít prom&#283;nnou %s v p&#345;edloze <I>%s</I>. <br />Pro správn&#253; v&#253;stup, vlo&#382;te {%s} do kódu p&#345;edlohy.";
$P2G_LANGUAGE['ERR_CANT_FIND_BLOCK'] = "Nebylo mo&#382;né najít blok <I>%s</I> pro vytvo&#345;ení. Byl deklarován v p&#345;edloze?";
$P2G_LANGUAGE['ERR_CANT_FIND_BLOCK_REUSE'] = "Nebylo mo&#382;né najít blok %s pro op&#283;tovn&#283; pou&#382;ití jako %s.";
$P2G_LANGUAGE['ERR_CANT_PUT_ON_PREPARED_TEMPLATE'] = "Element %s byl ji&#382; konfigurován pomocí metody DocumentElement->parse(). Není mo&#382;né p&#345;idávat elementy metodou put().";
$P2G_LANGUAGE['ERR_TEMPLATE_NOT_PREPARED'] = "P&#345;edloha %s nebyla p&#345;ipravena. Spus&#357;te metodu %s->parse(); p&#345;ed spou&#353;t&#283;ním operací na tomto objektu.";
$P2G_LANGUAGE['ERR_TEMPLATE_ALREADY_PREPARED'] = "Tato p&#345;edloha ji&#382; byla zpracována. Funkci parse() je mo&#382;né volat pouze jednou.";
$P2G_LANGUAGE['ERR_TPLPARSE_DEFINED_BLOCK'] = "The block %s was already defined!";
$P2G_LANGUAGE['ERR_TPLPARSE_UNBALANCED_BLOCKDEF'] = "Definice bloku <I>%s</I> není uzav&#345;ená!";
$P2G_LANGUAGE['ERR_TPLPARSE_INCOMPLETE_BLOCKDEF'] = "Specifikace bloku <I>%s</I> není kompletní v rámci tagu %s!";
$P2G_LANGUAGE['ERR_TPLPARSE_BLOCKINSIDELOOP'] = "Není mo&#382;né definovat dynamické bloky uvnit&#345; cykl&#367;!";
$P2G_LANGUAGE['ERR_TPLPARSE_UNBALANCED_TAG'] = "Definice tagu <I>%s</I> není uzav&#345;ená!";
$P2G_LANGUAGE['ERR_TPLPARSE_UNEXPECTED_TAG'] = "Neo&#269;ekávaný tag <I>%s</I>!";
$P2G_LANGUAGE['ERR_TPLPARSE_UNBALANCED_PARENTHESIS'] = "Neshodný po&#269;et závorek ve výrazu <I>%s</I>!";
$P2G_LANGUAGE['ERR_TPLPARSE_TAG_SYNTAX'] = "Definice tagu <I>%s</I> je neplatná: %s!";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_TOKEN'] = "Neplatný token ve výrazu <I>%s</I>: %s";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_ATTRIBUTE'] = "Chyb&#283;jící nebo neplatná hodnota prom&#283;nné!";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_ATTRIBUTENAME'] = "<I>%s</I> není platné jméno prom&#283;nné!";
$P2G_LANGUAGE['ERR_TPLPARSE_REQUIRED_ATTRIBUTE'] = "Vlastnost <I>%s</I> je po&#382;adována v definici tagu %s!";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_TAG_ATTRIBUTE'] = "Vlastnost <I>%s</I> je neplatná v definici tagu %s!";
$P2G_LANGUAGE['ERR_TPLPARSE_MISSING_ASSIGN'] = "Znak '=' nebyl nalezen po názvu prom&#283;nné!";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_IS_EXPR'] = "Neplatná operace pro 'is' (je) operaci : <I>%s</I>";
$P2G_LANGUAGE['ERR_TPLPARSE_MISSING_TAG_ARGS'] = "Tag <I>%s</I> po&#382;aduje parametry!";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_TAG_ARGS'] = "Tag <I>%s</I> nesmí obsahovat parametry!";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_MODIFIER'] = "Parametr %s není platný nebo neexistuje!";
$P2G_LANGUAGE['ERR_WIDGET_MANDATORY_PROPERTY'] = "Vlastnost <i>%s</i> is je povinná v definici prvku <i>%s</i>!";
$P2G_LANGUAGE['ERR_WIDGET_MISSING_PARENT'] = "The %s widget must be declared inside a %s widget!";
$P2G_LANGUAGE['ERR_WIDGET_MISSING_CHILDREN'] = "At least one %s widget must be declared inside the %s widget!";
$P2G_LANGUAGE['ERR_EMPTY_DOC_LAYOUT'] = "V této p&#345;edloze nejsou definovány &#382;ádné elementy.";
$P2G_LANGUAGE['ERR_CANT_REPLICATE_ROOT_BLOCK'] = "Základní blok v p&#345;edloze nem&#367;&#382;e b&#253;t p&#345;epsán.";
$P2G_LANGUAGE['ERR_MUST_BE_POSITIVE'] = "Hodnota  %s ve funkci %s musí b&#253;t v&#283;t&#353;í ne&#382; 0.";
$P2G_LANGUAGE['ERR_MUST_BE_AN_ARRAY'] = "Hodnota %s ve funkci %s musí b&#253;t pole.";
$P2G_LANGUAGE['ERR_VALUE_OUT_OF_BOUNDS'] = "Hodnota '%s' je mimo povolené hranice: %s a %s";
$P2G_LANGUAGE['ERR_URL_MALFORMED'] = "Hodnota <I>%s</I> není platnou URL!";
$P2G_LANGUAGE['ERR_HOST_INVALID'] = "<I>%s</I> není platn&#253;m hostitelem pro spojení!";
$P2G_LANGUAGE['ERR_DNS_NOT_FOUND'] = "Nebylo mo&#382;né najít platné DNS pro doménu %s.";
$P2G_LANGUAGE['ERR_SESSION_PROPERTY_NOT_FOUND'] = "Vlastnost %s prom&#283;nné sezení %s nebyla nalezena. Spus&#357;te funkci createProperty()";
$P2G_LANGUAGE['ERR_SESSION_TIMECOUNTER_NOT_FOUND'] = "&#269;íta&#269; &#269;asu %s prom&#283;nné sezení %s nebyl nalezen. Spus&#357;te funkci createTimeCounter()";
$P2G_LANGUAGE['ERR_INVALID_OBJECT'] = "Objekt p&#345;edan&#253; jako parametr není platnou instancí t&#345;ídy <I>%s</I>!";
$P2G_LANGUAGE['ERR_CALCULATOR_MISSING_PARAMETERS'] = "Nedostatek parametr&#367; pro vytvo&#345;ení kalkula&#269;ky!";
$P2G_LANGUAGE['ERR_COLORSEL_MISSING_PARAMETERS'] = "Nedostatek parametr&#367; pro vytvo&#345;ení v&#253;b&#283;ru barev!";
$P2G_LANGUAGE['ERR_INVALID_FORM_METHOD'] = "Metoda %s není platná pro odeslání formulá&#345;e '%s'";
$P2G_LANGUAGE['ERR_INVALID_VALIDATE_FUNC'] = "<I>%s</I> není platn&#253;m jménem pro valida&#269;ní funkci!";
$P2G_LANGUAGE['ERR_FORM_DUPLICATED_FIELD'] = "Pole <I>%s</I> bylo ve formulá&#345;i <I>%s</I> ji&#382; definováno!";
$P2G_LANGUAGE['ERR_FORM_DUPLICATED_BUTTON'] = "The button <I>%s</I> is already defined in the form <I>%s</I>!";
$P2G_LANGUAGE['ERR_FORM_DUPLICATED_SECTION'] = "ID sekce <I>%s</I> bylo ve formulá&#345;i <I>%s</I> ji&#382; definováno!";
$P2G_LANGUAGE['ERR_FORM_SECTION_EVALFUNC'] = "Nebylo mo&#382;né definovat viditelnost sekce <I>%s</I>, proto&#382;e funkce <I>%s</I> není definována!";
$P2G_LANGUAGE['ERR_FORM_SECTION_TPLBLOCK'] = "Sekce <I>%s</I> je podmín&#283;ná. Musíte deklarovat blok jménem <I>%s</I> v p&#345;edloze obsahující pole sekce!";
$P2G_LANGUAGE['ERR_FORM_REQUIRED_SUMMARY'] = "Následující pole formulá&#345;e jsou po&#382;adovaná:";
$P2G_LANGUAGE['ERR_FORM_ERRORS_SUMMARY'] = "Formulá&#345; obsahuje následující chyby:";
$P2G_LANGUAGE['ERR_FORM_COMPLETE_FIELDS'] = "Prosím, vlo&#382;te pole a zkuste znovu.";
$P2G_LANGUAGE['ERR_FORM_FIX_FIELDS'] = "Prosím, opravte pole a zkuste znovu.";
$P2G_LANGUAGE['ERR_FORM_FIELD_REQUIRED'] = "Pole %s je povinné.";
$P2G_LANGUAGE['ERR_FORM_FIELD_INVALID'] = "Pole %s má neplatnou hodnotu!";
$P2G_LANGUAGE['ERR_FORM_FIELD_INVALID_DATATYPE'] = "Pole %s obsahuje neplatnou hodnotu %s.";
$P2G_LANGUAGE['ERR_FORM_FIELD_INVALID_FLOAT'] = "Numerická p&#345;esnost pole %s je neplatná.\nMaximální velikost je %s pro celou &#269;ást a %s pro desetinnou &#269;ást.";
$P2G_LANGUAGE['ERR_FORM_FIELD_ALPHANUM'] = "Pole %s musí obsahovat pouze písmena abecedy a &#269;ísla.";
$P2G_LANGUAGE['ERR_FORM_FIELD_CHOICE'] = "Pole %s musí mít jednu z následujících hodnot: <I>%s</I>";
$P2G_LANGUAGE['ERR_FORM_FIELD_CREDITCARD'] = "Pole %s obsahuje neplatné &#269;íslo kreditní karty %s.";
$P2G_LANGUAGE['ERR_FORM_FIELD_MIN_LENGTH'] = "Pole %s musí mít alespo&#328; %s znak&#367;.";
$P2G_LANGUAGE['ERR_FORM_FIELD_MAX_LENGTH'] = "Pole %s musí mít maximáln&#283; %s znak&#367;.";
$P2G_LANGUAGE['ERR_FORM_FIELD_EQ'] = "Pole %s se musí rovnat poli %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_NEQ'] = "Pole %s se nesmí rovnat poli %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_GT'] = "Pole %s musí být v&#283;t&#353;í ne&#382; pole %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_LT'] = "Pole %s musí být men&#353;í ne&#382; pole %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_GOET'] = "Pole %s musí být v&#283;t&#353;í nebo rovno poli %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_LOET'] = "Pole %s musí být men&#353;í nebo rovno poli %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_EQ'] = "Pole %s musí být rovno %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_NEQ'] = "Pole %s nesmí být rovno %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_GT'] = "Pole %s musí být v&#283;t&#353;í ne&#382; %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_LT'] = "Pole %s musí být men&#353;í ne&#382; %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_GOET'] = "Pole %s musí být v&#283;t&#353;í nebo rovno %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_LOET'] = "Pole %s musí být men&#353;í nebo rovno %s!";
$P2G_LANGUAGE['ERR_FORM_INVALID_MASK'] = "Maska <I>%s</I>, pro pole <I>%s</I>, není platnou maskou!";
$P2G_LANGUAGE['ERR_FORM_INVALID_FIELDTYPE'] = "Pole typu <I>%s</I> není platné!";
$P2G_LANGUAGE['ERR_FORM_CAPTCHA'] = "Kód zadaný v poli %s nesouhlasí s obsahem p&#345;edlohy!";
$P2G_LANGUAGE['ERR_DATAGRID_INVALID_FIELDTYPE'] = "Pole typu <I>%s</I> nem&#367;&#382;e b&#253;t v rámci <I>tabulky (\"grid\")</I>!";
$P2G_LANGUAGE['ERR_DATAGRID_INVALID_CELLSIZES'] = "Hodnoty parametru <I>CELLSIZES</I> pole <I>%s</I> není platná. Musíte zadat N+1 velikostí (N=velikost FIELDSET) a sou&#269;et velikostí musí b&#253;t 100!";
$P2G_LANGUAGE['ERR_DATAGRID_INVALID_FIELDCOUNT'] = "Dotaz definovan&#253; v <I>DATASOURCE</I> pole <I>%s</I> musí vracet N+2 sloupc&#367; (N=velikost FIELDSET)";
$P2G_LANGUAGE['ERR_FORM_WRONG_LISTENER'] = "Atributy zpracovavatele událostí <I>%s</I> jsou neplatné nebo nekompletní!";
$P2G_LANGUAGE['ERR_FORM_WRONG_RULE'] = "Atributy pravidla <I>%s</I> jsou neplatné nebo nekompletní!";
$P2G_LANGUAGE['ERR_INVALID_DATASOURCE_SYNTAX'] = "Specifikace DATASOURCE v poli <I>%s</I> není platná.<br />Dce&#345;inné body KEYFIELD a LOOKUPTABLE jsou povinné!";
$P2G_LANGUAGE['ERR_MISSING_GROUPFIELD_CHILDREN'] = "Specifikace pole <I>%s</I> není kompletní. Musíte zadat alespo&#328; jeden &lt;OPTION&gt; dce&#345;inn&#253; bod!";
$P2G_LANGUAGE['ERR_MISSING_OPTION_VALUE'] = "Atribut VALUE v&#253;b&#283;ru s indexem <I>%d</I> v poli <I>%s</I> chybí!";
$P2G_LANGUAGE['ERR_MISSING_DBGROUPFIELD_DATASOURCE'] = "Není definován DATASOURCE pro %s <I>%s</I>!";
$P2G_LANGUAGE['ERR_EMPTY_DBGROUPFIELD_RESULTS'] = "Dotaz pro DATASOURCE element v %s <I>%s</I> vrátil prázdnou sadu!";
$P2G_LANGUAGE['ERR_MISSING_COMBOOPTION_VALUE'] = "Atribut VALUE v&#253;b&#283;ru s indexem %d v COMBOFIELD <I>%s</I> chybí!";
$P2G_LANGUAGE['ERR_MISSING_EDITSELECTION_CHILDREN'] = "Specifikace EDITSELECTIONFIELD <I>%s</I> není kompletní. EDITFIELD a LOOKUPFIELD dce&#345;inné body jsou povinné!";
$P2G_LANGUAGE['ERR_MISSING_LOOKUPSELECTION_CHILDREN'] = "Specifikace LOOKUPSELECTIONFIELD <I>%s</I> je &#353;patná. Bod musí obsahovat 2 potomky LOOKUPFIELD!";
$P2G_LANGUAGE['ERR_MISSING_SOURCELOOKUP_DATASOURCE'] = "DATASOURCE je definován v prvním LOOKUPFIELD potomku LOOKUPSELECTIONFIELD <I>%s</I>!";
$P2G_LANGUAGE['ERR_MISSING_QUERY_ELEMENTS'] = "\$fields a \$tables polo&#382;ky jsou povinné pro vytvo&#345;ení SQL dotazu!";
$P2G_LANGUAGE['ERR_INVALID_DATAGRID_STRUCTURE'] = "Struktura m&#345;í&#382;ky <I>%s</I> není platná. Musíte deklarovat <I>DATASOURCE</I> a <I>FIELDSET</I> obsahující alespo&#328; jedno pole!";
$P2G_LANGUAGE['ERR_MISSING_RANGEFIELD_CHILDREN'] = "Specifikace RANGEFIELD <I>%s</I> je &#353;patná. Bod musí obsahovat 2 potomky EDITFIELD, COMBOFIELD, LOOKUPFIELD or DATEPICKERFIELD!";
$P2G_LANGUAGE['ERR_EDITSEARCH_INVALID_DATAFILTER'] = "Specifikace <I>%s</I> pro DATAFILTER je neplatná.";
$P2G_LANGUAGE['ERR_EDITSEARCH_INVALID_DATAFILTER_MASK'] = "Maska <I>%s</I> p&#345;i&#345;azená pro DATAFILTER je neplatná.";
$P2G_LANGUAGE['ERR_EDITSEARCH_DUPLICATED_DATAFILTER'] = "Jiný DATAFILTER s ID <I>%s</I> ji&#383; existuje.";
$P2G_LANGUAGE['ERR_MISSING_EDITSEARCH_CHILDREN'] = "Specifikace <I>%s</I> pro EDITSEARCHFIELD není kompletní. Musíte zadat alespo&#328; jedno z polí DATAFILTER, DATASOURCE nebo LOOKUPFIELD!";
$P2G_LANGUAGE['ERR_CSV_DB_JSRS'] = "Nebylo mo&#382;né dokon&#269;it operaci!";
$P2G_LANGUAGE['ERR_SEARCHFORM_EMPTY'] = "Vyhledávání nemohlo být provedeno, proto&#382;e pole pro vyhledávání jsou prázdná.";
$P2G_LANGUAGE['ERR_SEARCHFORM_INVALID'] = "Vyhledávání nemohlo být provedeno, proto&#382;e hodnoty filtr&#367; jsou prázdné nebo krat&#353;í ne&#382; %d znak&#367;.";
$P2G_LANGUAGE['ERR_REPORT_MAX_GROUP_COLS'] = "Po&#269;et sloupc&#367; skupiny musí b&#253;t men&#353;í ne&#382; po&#269;et sloupc&#367; dotazu!";
$P2G_LANGUAGE['ERR_REPORT_UNKNOWN_GROUP_COL'] = "Skupina sloupc&#367; <I>%s</I> není definována ve v&#253;sledném záznamu dotazu!";
$P2G_LANGUAGE['ERR_REPORT_MISSING_BLOCK_VARIABLE'] = "Prom&#283;nná <I>%s</I> bloku <I>%s</I> nebyla deklarována v p&#345;edloze!";
$P2G_LANGUAGE['ERR_REPORT_INVALID_COLSIZES'] = "Hodnota <I>%s</I> není platná pro definici &#353;í&#345;ky sloupce!";
$P2G_LANGUAGE['ERR_REPORT_COL_SIZES_SUM'] = "Sou&#269;et velikostí sloupc&#367; p&#345;edan&#253;ch funkci setColumnSizes musí b&#253;t roven 100!";
$P2G_LANGUAGE['ERR_REPORT_COL_COUNT_MISMATCH'] = "Po&#269;et zadan&#253;ch velikostí sloupc&#367; (%s) není roven po&#269;tu sloupc&#367; v reportu (%s, %s skupina)";
$P2G_LANGUAGE['ERR_REPORT_MISSING_SEARCH_VARIABLE'] = "Nebylo mo&#382;né vytvo&#345;it formulá&#345; vyhledávání, proto&#382;e prom&#283;nná <I>%s</I> nebyla nalezena.<br />Deklarujte {%s} v p&#345;edloze reportupro pokra&#269;ování.";
$P2G_LANGUAGE['ERR_REPORT_SEARCH_PARS_MALFORMED'] = "Musíte poskytnout Název (\"Label\"), Název pole (\"Field Name\") a Masku (\"Mask\") p&#345;i vytvá&#345;ení parametr&#367; vyhledávání!";
$P2G_LANGUAGE['ERR_REPORT_SEARCH_INVALID_MASK'] = "Maska <I>%s</I> pro parametry vyhledávání <I>#%d</I> není platná!";
$P2G_LANGUAGE['ERR_REPORT_MIN_ALT_STYLE'] = "Funkce setAlternateStyle musí obdr&#382;et alespo&#328; 2 parametry (jména CSS)!";
$P2G_LANGUAGE['ERR_REPORT_HIDDEN_GROUP'] = "Skryt&#253; sloupec (%s) nem&#367;&#382;e b&#253;t pou&#382;it ve skupin&#283; hlavi&#269;ek!";
$P2G_LANGUAGE['ERR_REPORT_GROUP_HIDDEN'] = "Sloupec zobrazen&#253; ve skupin&#283; hlavi&#269;ek (%s) nem&#367;&#382;e b&#253;t definován jako skryt&#253;!";
$P2G_LANGUAGE['ERR_REPORT_MAX_HIDDEN_COLS'] = "Po&#269;et skryt&#253;ch sloupc&#367; musí b&#253;t men&#353;í ne&#382; po&#269;et sloupc&#367; dotazu!";
$P2G_LANGUAGE['ERR_REPORT_UNKNOWN_HIDDEN_COL'] = "Sloupec <I>%s</I> definovan&#253; jako skryt&#253; neexistuje ve v&#253;sledkovém záznamu dotazu!";
$P2G_LANGUAGE['ERR_DATASET_INVALID_TYPE'] = "Typ <I>%s</I> není platn&#253;m typem DataSet!";
$P2G_LANGUAGE['ERR_MENU_WRONG_ROOT_SQL'] = "Dotaz ulo&#382;en&#253; parametru \$rootSql metody Menu->loadFromDatabase() vrátil prázdn&#253; záznam nebo nemá minimální po&#382;adovan&#253; po&#269;et polí (CAPTION a LINK)!";
$P2G_LANGUAGE['ERR_MENU_WRONG_CHILDREN_STATEMENT'] = "Dotaz p&#345;edan&#253; parametru \$childSql metody Menu->loadFromDatabase() je neplatn&#253;.<br />Zadejte dotaz obsahující '~var~' (Sloupec vztah&#367;) a dal&#353;í 2 po&#382;adované pole (CAPTION and LINK)";
$P2G_LANGUAGE['ERR_MENU_NOT_FOUND'] = "Menu nebylo vytvo&#345;eno. Vytvo&#345;te polo&#382;ky menu pomocí Menu->loadFromDatabase() nebo Menu->loadFromXmlFile().";
$P2G_LANGUAGE['ERR_MENU_EMPTY_XML_ROOT'] = "The root node of the XML file has no children!";
$P2G_LANGUAGE['ERR_MENU_INVALID_IMAGE'] = "Obrázek <I>%s</I> není platn&#253;. Mo&#382;né hodnoty jsou: <I>%s</I>.";
$P2G_LANGUAGE['ERR_FTP_MISSING_HOST'] = "Nebylo mo&#382;né otev&#345;ít spojení. Musíte nejd&#345;ív nastavit spojení, pou&#382;ijte metody FtpClient->setServer() a FtpClient->setUserInfo()";
$P2G_LANGUAGE['ERR_FTP_MISSING_USER_OR_PASS'] = "Nebylo mo&#382;né ov&#283;&#345;it u&#382;ivatele. Jeden ze zadan&#253;ch parametr&#367; (u&#382;ivatelské jméno nebo heslo) nebylo poskytnuto";
$P2G_LANGUAGE['ERR_SPRSH_OUT_OF_BOUNDS'] = "Omezení tabulky byly p&#345;ekro&#269;eny: [%d;%d]";
$P2G_LANGUAGE['ERR_SPRSH_MAX_NOTE_CHARS'] = "Maximální velikost pro poznámku bu&#328;ky byla p&#345;ekro&#269;ena.<br />Hodnota: %s";
$P2G_LANGUAGE['ERR_SPRSH_MAX_FONTS'] = "Maximum 4 písem pro ka&#382;dou tabulku bylo p&#345;ekro&#269;eno!";
$P2G_LANGUAGE['ERR_UPLOAD_NOT_FOUND'] = "Nebylo mo&#382;né dokon&#269;it nahrání souboru. Soubor nebyl správn&#283; p&#345;enesen.";
$P2G_LANGUAGE['ERR_UPLOAD_TOO_BIG'] = "Nebylo mo&#382;né dokon&#269;it nahrání souboru. Velikost souboru je v&#283;t&#353;í ne&#382; povolené maximum.";
$P2G_LANGUAGE['ERR_UPLOAD_INVALID_NAME'] = "Nebylo mo&#382;né dokon&#269;it nahrání souboru. Jméno souboru %s není platné.";
$P2G_LANGUAGE['ERR_UPLOAD_INVALID_TYPE'] = "Nebylo mo&#382;né dokon&#269;it nahrání souboru. P&#345;ípona %s není povolena.";
$P2G_LANGUAGE['ERR_UPLOAD_FILE_EXISTS'] = "Nebylo mo&#382;né dokon&#269;it nahrání souboru. Soubor <I>%s</I> ji&#382; existuje a nebyl p&#345;epsán.";
$P2G_LANGUAGE['ERR_UPLOAD_CANT_MOVE'] = "Nebylo mo&#382;né p&#345;esunout soubor z do&#269;asného adresá&#345;e do ur&#269;eného adresá&#345;e.";
$P2G_LANGUAGE['ERR_CANT_OPEN_SOCKET'] = "Nebylo mo&#382;né otev&#345;it socket za pou&#382;ití portu %s s hostitelem %s!%s";
$P2G_LANGUAGE['ERR_SOCKET_NOT_CONNECTED'] = "Spojení za pou&#382;ití socketu není aktivní!";
$P2G_LANGUAGE['ERR_SOCKET_EOF'] = "Dosa&#382;en konec souboru, operace nemohla b&#253;t dokon&#269;ena!";
$P2G_LANGUAGE['ERR_SOCKET_TIMEOUT'] = "Vypr&#353;el &#269;as socketu. Operace nem&#367;&#382;e b&#253;t dokon&#269;ena!";
$P2G_LANGUAGE['ERR_MAIL_INVALID_TYPE'] = "Invalid message transport type!";
$P2G_LANGUAGE['ERR_MAIL_INCOMPLETE_PARAMS'] = "Insufficient parameters for transport type %s!";
$P2G_LANGUAGE['ERR_MAIL_EMPTY_RCPT'] = "Nejsou zadáni &#382;ádní adresáti pro zprávu!";
$P2G_LANGUAGE['ERR_SIGNED_MESSAGE_SIGN'] = "Nebylo mo&#382;né podepsat obsah zprávy.<br />%s";
$P2G_LANGUAGE['ERR_SMTP_CONNECTION'] = "Chyba p&#345;i spojování se serverem SMTP %s!<br />[Chyby %d] - %s";
$P2G_LANGUAGE['ERR_SMTP_COMMAND'] = "Chyba p&#345;i vykonávání p&#345;íkazu %s SMTP serverem!<br />[Chyby %d] - %s";
$P2G_LANGUAGE['ERR_SMTP_AUTHENTICATE'] = "Nebylo mo&#382;né ov&#283;&#345;it u&#382;ivatele se serverem SMTP!";
$P2G_LANGUAGE['ERR_CRYPT_MISSING_KEY'] = "Pro za&#353;ifrování nebo od&#353;ifrování dat musíte poskytnout &#353;ifrovací klí&#269; metod&#283; Crypt->setKey()!";
$P2G_LANGUAGE['ERR_CRYPT_OPEN_MODULE'] = "Nebylo mo&#382;né inicializovat &#353;ifrování <I>%s</I> v modu %s!";
$P2G_LANGUAGE['ERR_OPENSSL_PUBKEY_ENCRYPT'] = "Nebylo mo&#382;né pou&#382;ít ve&#345;ejnou &#269;ást poskytnutého klí&#269;e pro za&#353;ifrování dat. Poskytn&#283;te soubor obsahující platn&#253; certifikát metod&#283; OpenSSLCrypt->setCertificatePath()!";
$P2G_LANGUAGE['ERR_OPENSSL_PUBKEY_VERIFY'] = "Nebylo mo&#382;né pou&#382;ít ve&#345;ejnou &#269;ást poskytnutého klí&#269;e k ov&#283;&#345;ení podpisu dat. Poskytn&#283;te soubor obsahující platn&#253; certifikát metod&#283; OpenSSLCrypt->setCertificatePath()!";
$P2G_LANGUAGE['ERR_OPENSSL_PRIVKEY_DECRYPT'] = "Nebylo mo&#382;né pou&#382;ít soukromou &#269;ást poskytnutého klí&#269;e k de&#353;ifrování dat. Poskytn&#283;te soubor obsahující platn&#253; soukrom&#253; klí&#269; a ov&#283;&#345;ovací frázi, je-li pot&#345;eba.";
$P2G_LANGUAGE['ERR_OPENSSL_PRIVKEY_SIGN'] = "Nebylo mo&#382;né pou&#382;ít soukromou &#269;ást poskytnutého klí&#269;e pro vytvo&#345;ení digitálního podpisu. Poskytn&#283;te soubor obsahující platn&#253; soukrom&#253; klí&#269; a ov&#283;&#345;ovací frázi, je-lipot&#345;eba.";
$P2G_LANGUAGE['ERR_OPENSSL_CERT_PATH'] = "Cesta k certifikátu je neplatná!";
$P2G_LANGUAGE['ERR_OPENSSL_READ_CERT'] = "Nebylo mo&#382;né p&#345;e&#269;íst obsah certifikátu!%s";
$P2G_LANGUAGE['ERR_POP3_CONNECTION'] = "Chyba p&#345;i spojování se serverem POP3 %s!<br />[Chyby %d] - %s";
$P2G_LANGUAGE['ERR_POP3_COMMAND'] = "Chyba p&#345;i spou&#353;t&#283;ní p&#345;íkazu %s serverem POP3!<br />%s";
$P2G_LANGUAGE['ERR_POP3_AUTHENTICATE'] = "Nebylo mo&#382;né ov&#283;&#345;it u&#382;ivatele serverem POP3!";
$P2G_LANGUAGE['ERR_POP3_APOP'] = "Nebylo mo&#382;né provést p&#345;íkaz APOP: není ulo&#382;eno &#382;ádné ov&#283;&#345;ení!";
$P2G_LANGUAGE['ERR_HTTP_CONNECT'] = "Nebylo mo&#382;né vytvo&#345;it HTTP spojení s hostitelem <I>%s</I>!";
$P2G_LANGUAGE['ERR_HTTP_MISSING_HOST'] = "Hostitel HTTP nebyl nastaven. Spus&#357;te metodu HttpClient->setHost().";
$P2G_LANGUAGE['ERR_LDAP_CONNECT'] = "It wasn't possible to establish a connection with the LDAP server %s, port %s!";
$P2G_LANGUAGE['ERR_LDAP_MISSING_BASEDN'] = "The base DN wasn't set. Run the LdapClient->setBaseDN method.";
$P2G_LANGUAGE['ERR_LDAP_COMMAND'] = "Error while executing the command %s on the LDAP server: %s";
$P2G_LANGUAGE['ERR_LDAP_SEARCH'] = "An error occurred while trying to search LDAP using the filter %s: %s";
$P2G_LANGUAGE['ERR_LDAP_DELETE'] = "It wasn't possible to delete the entry <i>%s</i>. Try using the \$recursive parameter.";
$P2G_LANGUAGE['ERR_CANT_INSTANTIATE_VALIDATOR'] = "Nebylo mo&#382;né vytvo&#345;it ov&#283;&#345;ovací t&#345;ídu <B>%s</B>!";
$P2G_LANGUAGE['ERR_INVALID_VALIDATOR'] = "Ov&#283;&#345;ovací t&#345;ída <I>%s</I> není platná! Metoda %s->execute() nebyla nalezena.";
$P2G_LANGUAGE['ERR_ASSERTION_MESSAGE'] = "Zadan&#253;m p&#345;íkazem nebyl získán o&#269;ekávan&#253; v&#253;sledek.";
$P2G_LANGUAGE['ERR_INVALID_CALLBACK'] = "Funkce nebo metoda <I>%s</I> není platným zp&#283;tným odkazem!";
$P2G_LANGUAGE['ERR_INVALID_PROPERTIES_FILE'] = "Soubor <I>%s</I> neobsahuje platná konfigura&#269;ní data!";
$P2G_LANGUAGE['ERR_INVALID_COLOR_SPEC'] = "<I>%s</I> není platnou specifikací barvy.";
$P2G_LANGUAGE['ERR_ALLOCATE_COLOR'] = "Nebylo mo&#382;né alokovat (vytvo&#345;it) novou barvu pro tento obrázek!";
$P2G_LANGUAGE['ERR_SERVICE_MISSING_HANDLER'] = "Chyb&#283;jící jméno %s ovlada&#269;e nebo zástupce!";
$P2G_LANGUAGE['ERR_SERVICE_INVALID_HANDLER'] = "%s ovlada&#269; %s je neplatný!";
$P2G_LANGUAGE['ERR_AJAX_SUPPORT'] = "Vá&#353; prohlí&#382;e&#269; nepodporuje technologii AJAX!";
$P2G_LANGUAGE['ERR_JSON_INVALID_PROPNAME'] = "Chyba dekodování JSON: %s není platný název vlastnosti";
$P2G_LANGUAGE['ERR_JSON_MISSING_COLON'] = "Chyba dekodování JSON: chyb&#283;jící dvojte&#269;ka na pozici %d";
$P2G_LANGUAGE['ERR_JSON_INVALID_PROPVALUE'] = "Chyba dekodování JSON: %s není platnou hodnotou vlastnosti";
$P2G_LANGUAGE['ERR_JSON_MISSING_COMMA'] = "Chyba dekodování JSON: chyb&#283;jící &#269;árka na pozici %d";
$P2G_LANGUAGE['ERR_JSON_ESCAPE_SEQUENCE'] = "Chyba dekodování JSON: neplatná kontrolní sekvence na pozici %d";
$P2G_LANGUAGE['ERR_JSON_UNTERMINATED_STRING'] = "Chyba dekodování JSON: neukon&#269;ený &#345;et&#283;zec na pozici %d";
$P2G_LANGUAGE['ERR_JSON_CYCLE'] = "The JSON encoder found a cycle in an instance of the class %s!";
$P2G_LANGUAGE['ERR_JSON_ENCODE'] = "An error was found while converting type %s to a JSON string!";
$P2G_LANGUAGE['DEFAULT_BTN_VALUE'] = "Poslat";
$P2G_LANGUAGE['CALENDAR_LINK_TITLE'] = "Otev&#345;ít kalendá&#345;";
$P2G_LANGUAGE['CALCULATOR_LINK_TITLE'] = "Otev&#345;ít kalkula&#269;ku";
$P2G_LANGUAGE['CALCULATOR_WINDOW_TITLE'] = "Kalkula&#269;ka";
$P2G_LANGUAGE['CALCULATOR_RESULT_CAPTION'] = "V&#253;sledek";
$P2G_LANGUAGE['DAYS_OF_WEEK'] = array("Ned&#283;le", "Pond&#283;lí", "&#218;ter&#253;", "St&#345;eda", "&#269;tvrtek", "Pátek", "Sobota");
$P2G_LANGUAGE['DAYS_OF_WEEK_ABBR'] = array("Ne", "Po", "&#218;t", "St", "&#269;t", "Pá", "So");
$P2G_LANGUAGE['MONTHS_OF_YEAR'] = array("Leden", "&#218;nor", "B&#345;ezen", "Duben", "Kv&#283;ten", "&#269;erven", "&#269;ervenec", "Srpen", "Zá&#345;í", "&#345;íjen", "Listopad", "Prosinec");
$P2G_LANGUAGE['MONTHS_OF_YEAR_ABBR'] = array("Led", "&#218;no", "B&#345;e", "Dub", "Kv&#283;", "&#269;en", "&#269;ec", "Srp", "Zá&#345;", "&#345;íj", "Lis", "Pro");
$P2G_LANGUAGE['COMPRESS_USE_MSG'] = "<!-- Uses %s Compression -->";
$P2G_LANGUAGE['COMPRESS_DEBUG_MSG_ORIG'] = "P&#367;vodní velikost: %d";
$P2G_LANGUAGE['COMPRESS_DEBUG_MSG_COMP'] = "Pakovaná velikost: %d";
$P2G_LANGUAGE['EMPTY_SEARCH'] = "Musíte vybrat filtr a zadat &#345;et&#283;zec pro vyhledávání!";
$P2G_LANGUAGE['EMPTY_RESULTS'] = "Vyhledávání vrátilo prázdnou sadu!";
$P2G_LANGUAGE['FORM_INPUT_MASK_MESSAGES'] = array(
	'optionalCharFirst' => "Nepovinný znak musí následovat.",
	'escapeCharLast' => "The escape char indicator must preceed a literal.",
	'nestedCharClass' => "Neplatná t&#345;ída znaku.",
	'invalidLimits' => "Neplatná limity velikosti definice.");
$P2G_LANGUAGE['FORM_MASKS'] = array(
	'CPFCNPJ' => 'CPF/CNPJ',
	'CURRENCY' => 'M&#283;na',
	'DATE' => 'Datum',
	'TIME' => '&#268;as',
	'EMAIL' => 'E-Mail',
	'FLOAT' => 'Desetinné &#269;íslo',
	'INTEGER' => 'Celé &#269;íslo',
	'DIGIT' => '&#269;íslo',
	'IP' => 'IP adresa',
	'WORD' => 'Textový &#345;et&#283;zec',
	'URL' => 'URL/Internetová adresa',
	'ZIP' => 'PS&#269;'
);
$P2G_LANGUAGE['EDITSEARCH_BTN_VALUE'] = "Vyhledávám...";
$P2G_LANGUAGE['EDITOR_VARS'] = array(
	'formatBlock' => 'Odstavec',
	'formatBlockOptions' => array(
		'h1' => 'Nadpis 1 &lt;H1&gt;',
		'h2' => 'Nadpis 2 &lt;H2&gt;',
		'h3' => 'Nadpis 3 &lt;H3&gt;',
		'h4' => 'Nadpis 4 &lt;H4&gt;',
		'h5' => 'Nadpis 5 &lt;H5&gt;',
		'h6' => 'Nadpis 6 &lt;H6&gt;',
		'address' => 'Adresa &lt;ADDR&gt;',
		'pre' => 'Formátováno &lt;PRE&gt;',
		'removeformat' => 'Odstra&#328; formát'
	),
	'topButtons' => array(
		'bold' => 'Tu&#269;n&#283;',
		'italic' => 'Kurzívou',
		'underline' => 'Podtr&#382;en&#283;',
		'strikethrough' => 'P&#345;e&#353;krtnuté písmo',
		'cut' => 'Vyst&#345;ihnout',
		'copy' => 'Kopírovat',
		'paste' => 'Vlo&#382;it',
		'pickforecolor' => 'Zm&#283;nit barvu pop&#345;edí',
		'pickbackcolor' => 'Zm&#283;nit barvu pozadí',
		'addemoticon' => 'Vlo&#382;it smajlíka',
		'selectall' => 'Ozna&#269;it v&#353;e',
		'removeformat' => 'Odstra&#328; formát'
	),
	'font' => 'Písmo',
	'fontSize' => 'Velikost',
	'bottomButtons' => array(
		'justifyleft' => 'Zarovnat vlevo',
		'justifycenter' => 'Vyst&#345;edit text',
		'justifyright' => 'Zarovnat vpravo',
		'justifyfull' => 'Zarovnej text',
		'insertorderedlist' => 'Vlo&#382;/Odstra&#328; &#269;íslovan&#253; seznam',
		'insertunorderedlist' => 'Vlo&#382;/Odstra&#328; jednoduch&#253; seznam',
		'indent' => 'Zv&#253;&#353;it odsazení',
		'outdent' => 'Sní&#382;it odsazení',
		'createlink' => 'Vlo&#382;it odkaz',
		'unlink' => 'Odpojit',
		'insertimage' => 'Vlo&#382;it obrázek',
		'inserthorizontalrule' => 'Vlo&#382;it horizontální pravítko'
	),
	'editMode' => 'Upravit v Roz&#353;í&#345;eném módu',
	'emoticon' => 'Vybrat smajlíka',
	'validateMode' => 'Pro u&#382;ití pokro&#269;il&#253;ch nástroj&#367; editoru, ozna&#269;te polí&#269;ko \'Roz&#353;íné &#219;pravy\'',
	'createLink' => 'Zadejte adresu odkazu (P&#345;íklad: http://www.domain.com):',
	'insertImage' => 'Zadejte cestu obrázku:'
);
$P2G_LANGUAGE['COLOR_PICKER_VARS'] = array(
	'popupTitle' => 'Vyberte barvu kterou chcete',
	'colorTitle' => 'Vybrat tuto barvu'
);
$P2G_LANGUAGE['LOOKUP_CHOICE_FILTER_TIP'] = "--- Filtrovat seznam ---";
$P2G_LANGUAGE['LOOKUP_SELECTION_DEFAULT_SELFIRST'] = "--- Vlo&#382;it hodnoty ---";
$P2G_LANGUAGE['LOOKUP_SELECTION_BUTTON_TITLES'] = array(
	'add' => 'P&#345;idat vybrané polo&#382;ky',
	'addall' => 'P&#345;idat v&#353;echny polo&#382;ky',
	'rem' => 'Odstranit vybrané polo&#382;ky',
	'remall' => 'Odstranit v&#353;echny polo&#382;ky'
);
$P2G_LANGUAGE['EDIT_SELECTION_BUTTON_TITLES'] = array(
	'add' => 'P&#345;idat',
	'rem' => 'Odstranit vybrané polo&#382;ky',
	'remall' => 'Odstranit v&#353;echny polo&#382;ky'
);
$P2G_LANGUAGE['MEMO_COUNT_LABEL'] = "Znaky";
$P2G_LANGUAGE['SEL_AVAILABLE_VALUES_LABEL'] = "Mo&#382;né:";
$P2G_LANGUAGE['SEL_INSERTED_VALUES_LABEL'] = "Vlo&#382;ené:";
$P2G_LANGUAGE['FORM_DATA_BIND_MESSAGES'] = array(
	'insertSuccess' => "Data byla &#219;sp&#283;&#353;n&#283; vlo&#382;ena!",
	'updateEmpty' => "Nejsou &#382;ádné záznamy pro &#219;pravu!",
	'updateSuccess' => "Data byla &#219;sp&#283;&#353;n&#283; aktualizována!",
	'deleteEmpty' => "Nejsou &#382;ádné záznamy pro smazání!",
	'deleteSuccess' => "Data byla &#219;sp&#283;&#353;n&#283; smazána!",
	'deleteConfirm' => "Opravdu chcete smazat tento záznam?",
	'sortInvalid' => "Musíte vybrat pole pro t&#345;íd&#283;ní!",
	'gotoEmpty' => "Musíte zadat &#269;íslo záznamu pro p&#345;echod na n&#283;j!",
	'gotoInvalid' => "Po&#382;adovan&#253; záznam je neplatn&#253;!"
);
$P2G_LANGUAGE['FORM_DATA_BIND_TOOLBAR_VALUES'] = array(
	'navFirstTip' => 'Jdi na první záznam',
	'navPrevTip' => 'Jdi na p&#345;edchozí záznam',
	'navNextTip' => 'Jdi na dal&#353;í záznam',
	'navLastTip' => 'Jdi na poslední záznam',
	'actNew' => 'Nov&#253;',
	'actNewTip' => 'Vytvo&#345;it nov&#253; záznam',
	'actEdit' => 'Edit',
	'actEditTip' => 'Uprav sou&#269;asn&#253; záznam',
	'actDel' => 'Sma&#382;',
	'actDelTip' => 'Sma&#382; sou&#269;asn&#253; záznam',
	'actSave' => 'Ulo&#382;',
	'actSaveTip' => 'Ulo&#382; sou&#269;asn&#253; záznam',
	'actCancel' => 'Zru&#353;',
	'actCancelTip' => 'Zru&#353; provád&#283;nou akci',
	'sortAsc' => 'Vzestupné po&#345;adí',
	'sortDesc' => 'Sestupné po&#345;adí',
	'sortChoose' => 'Vyberte pole pro t&#345;íd&#283;ní',
	'sortTit' => 'T&#345;íd&#283;ní',
	'sortFirst' => 'Pole pro t&#345;íd&#283;ní',
	'gotoTip' => 'Zadejte &#269;íslo záznamu a zmá&#269;kn&#283;te Jdi na',
	'goto' => 'Jdi na',
	'gotoBtnTip' => 'Zmá&#269;kn&#283;te pro p&#345;echod na zadan&#253; záznam',
	'gotoTit' => 'Jdi na',
	'filter' => 'Filtr',
	'filterTip' => 'Vyberte filtr',
	'filterVTip' => 'Vyberte hodnotu pro filtr',
	'filterBtnTip' => 'Zmá&#269;kn&#283;te zde pro filtrování',
	'filterTit' => 'Filtr',
	'filterFirst' => 'Filtrované pole'
);
$P2G_LANGUAGE['REPORT_INVALID_PAGE'] = "Neplatné &#269;íslo stránky!";
$P2G_LANGUAGE['REPORT_EMPTY_VALUES'] = array(
	'emptyMsg' => 'Dotaz nebo zadan&#253; filtr pro vyhledávání vrátil prázdn&#253; seznam',
	'backLink' => 'Zp&#283;t na Report'
);
$P2G_LANGUAGE['REPORT_ORDER_TIP'] = "T&#345;ídit podle %s";
$P2G_LANGUAGE['REPORT_ORDER_OPTIONS_LABEL'] = "T&#345;ídit podle:";
$P2G_LANGUAGE['REPORT_FUNCTION_MESSAGES'] = array(
	'firstTit' => 'První',
	'firstTip' => 'Jdi na první stránku',
	'prevTit' => 'P&#345;edchozí',
	'prevTip' => 'Jdi na p&#345;edchozí stránku',
	'prevScrTit' => '%d zp&#283;t',
	'prevScrTip' => '%d stránek zp&#283;t ve výstupu',
	'pageTip' => 'Jdi na stánku &#269;.%d z %d',
	'nextTit' => 'Dal&#353;í',
	'nextTip' => 'Jdi na dal&#353;í stránku',
	'nextScrTit' => '%d dop&#345;edu',
	'nextScrTip' => '%d stránek dop&#345;edu ve výstupu',
	'lastTit' => 'Poslední',
	'lastTip' => 'Jdi na poslední stránku',
	'rowCount' => 'nalezeno %d záznam&#367;',
	'rowsPerPage' => '%d záznam&#367; na stránku',
	'thisPage' => 'Stránka %d z %d',
	'rowInterval' => 'Vypisuji %d-%d z %d záznam&#367;',
	'goTo' => 'Jdi na stránku'
);
$P2G_LANGUAGE['REPORT_SEARCH_VALUES'] = array(
	'searchTitle' => 'Hledat',
	'filtersTitle' => 'Vybrat pole',
	'addBtn' => 'P&#345;idat',
	'mainOpOr' => 'Jak&#253;koliv filtr',
	'mainOpAnd' => 'V&#353;echny filtry',
	'clearBtn' => 'Sma&#382;',
	'viewBtn' => 'Zobraz',
	'sendBtn' => 'Hledej',
	'emptyFilters' => 'Nebyly p&#345;idány &#382;ádné filtry',
	'closeFilters' => 'Zav&#345;i',
	'removeFilter' => 'Odstra&#328;',
	'addedFilter' => 'Filtr byl p&#345;idán!',
	'resendConfirmation' => 'Opravdu chcete odeslat vyhledání se zadan&#253;mi poli?'
);
$P2G_LANGUAGE['REPORT_NUMBER_OPERATORS'] = array(
	'=' => 'Rovno',
	'!=' => 'Není rovno',
	'>' => 'V&#283;t&#353;í',
	'<' => 'Men&#353;í',
	'>=' => 'V&#283;t&#353;í nebo rovno',
	'<=' => 'Men&#353;í nebo rovno'
);
$P2G_LANGUAGE['REPORT_STRING_OPERATORS'] = array(
	'=' => 'Rovno',
	'!=' => 'Není rovno',
	'LIKE' => 'Obsahuje',
	'NOT LIKE' => 'Neobsahuje',
	'LIKEI' => 'Za&#269;íná s',
	'LIKEF' => 'Kon&#269;í s'
);
$P2G_LANGUAGE['OPERATORS'] = array(
	'EQ' => 'rovno',
	'NEQ' => 'není rovno',
	'GT' => 'v&#283;t&#353;í',
	'LT' => 'men&#353;í',
	'GOET' => 'v&#283;t&#353;í nebo rovno',
	'LOET' => 'men&#353;í nebo rovno',
	'CONTAINING' => 'obsahuje',
	'STARTING' => 'za&#269;íná s',
	'ENDING' => 'kon&#269;í s',
	'BETWEEN' => 'mezi',
	'IN' => 'je v seznamu',
	'NOTIN' => 'není v seznamu',
	'AND' => 'a',
	'OR' => 'nebo'
);
$P2G_LANGUAGE['CHECKBOX_DESCRIPTIONS'] = array(
	'T' => '%s je ozna&#269;eno',
	'F' => '%s není ozna&#269;eno'
);
$P2G_LANGUAGE['CHECKGROUP_SHORTCUTS'] = array(
	'all' => 'All',
	'none' => 'None',
	'invert' => 'Invert'
);
return $P2G_LANGUAGE;
?>