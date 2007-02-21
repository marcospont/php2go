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
 * @author Andrea Fazzi <andrea.fazzi@le.infn.it>
 * @copyright 2002-2006 Marcos Pont
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version $Id$
 */

$P2G_LANGUAGE['ERR_OLD_PHP_VERSION'] = "PHP2Go non puo' essere eseguito nella configurazione attuale del server.<BR>La versione di PHP in uso e' <I>%s</I>. La versione minima richiesta dal framework e' <I>%s</I>.<BR>E' necessario installare una versione successiva.";
$P2G_LANGUAGE['ERR_UNSUPPORTED_EXTENSION'] = "L'estensione <I>%s</I> non e' supportata dalla attuale installazione di PHP.";
$P2G_LANGUAGE['ERR_OBJ_MESSAGE'] = "Errore nella classe %s:<BR>%s";
$P2G_LANGUAGE['ERR_SCRIPT_MESSAGE'] = "Errore nello script:<BR>%s";
$P2G_LANGUAGE['ERR_DATABASE'] = "Errore del database";
$P2G_LANGUAGE['ERR_INVALID_VALUE'] = "Valore non valido!";
$P2G_LANGUAGE['ERR_DUPLICATED_VALUE'] = "Questo valore e' gia' stato inserito!";
$P2G_LANGUAGE['ERR_COMM_FAILURE'] = "Comunicazione fallita!";
$P2G_LANGUAGE['ERR_MISSING_DATABASE_PARAMETERS'] = "Non e' stato possibile caricare i parametri di connessione al database! [ID: %s]";
$P2G_LANGUAGE['ERR_INVALID_DATABASE_PARAMETERS'] = "I parametri di connessione al database non sono validi! I valori USER, BASE e TYPE sono obbligatori. [ID: %s]";
$P2G_LANGUAGE['ERR_DATABASE_CONNECTION_FAILED'] = "Non e' stato possibile connettersi al database utilizzando le impostazioni correnti. I parametri sono corretti?";
$P2G_LANGUAGE['ERR_INVALID_CONNECTION_CLASS_PATH'] = "Non e' stato possibile importare e/o instanziare la classe di connessione %s!";
$P2G_LANGUAGE['ERR_INVALID_CONNECTION_CLASS'] = "La classe di connessione %s non e' un classe derivata da php2go.db.Db!";
$P2G_LANGUAGE['ERR_TYPES'] = array(
	1 => "Errore fatale",
	2 => "Avviso",
	4 => "Errore di sintassi",
	8 => "Nota",
	16 => "Errore fatale del core",
	32 => "Avviso del core",
	64 => "Errore fatale del compilatore",
	128 => "Avviso del compilatore",
	256 => "Errore dell'utente",
	512 => "Avviso dell'utente",
	1024 => "Nota dell'utente"
);
$P2G_LANGUAGE['ERR_ABSOLUTE_URI_NOT_FOUND'] = "Alla chiave 'ABSOLUTE_URI' non e' stato associato alcun valore nell'array globale di configurazione!";
$P2G_LANGUAGE['ERR_CONFIG_ENTRY_NOT_FOUND'] = "La voce <I>%s</I> non e' stata impostata nell'array globale di configurazione!";
$P2G_LANGUAGE['ERR_INVALID_LANGDOMAIN_FILE'] = "Il file dei messaggi per il dominio %s, linguaggio %s, non e' valido o non esiste.";
$P2G_LANGUAGE['ERR_CANT_FIND_CFG_VAL'] = "Non e' stato possibile trovare la chiave <I>%s</I> nell'array globale di configurazione. La chiave e' stata settata correttamente nell'installazione?";
$P2G_LANGUAGE['ERR_CANT_LOAD_MODULE'] = "Impossibile caricare il modulo <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_LOAD_DIR'] = "Impossibile caricare i moduli dalla cartella <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_LOAD_DIR_MODULE'] = "Impossibile caricare il file <I>%s</I> da <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_CONVERT_DATE'] = "Non e' stato possibile convertire la data nel formato %s.";
$P2G_LANGUAGE['ERR_CANT_RUN_ON_WINDOWS'] = "La classe %s non puo' essere utilizzata sotto Windows!";
$P2G_LANGUAGE['ERR_UNDEFINED_FUNCTION'] = "La funzione %s non e' definita!";
$P2G_LANGUAGE['ERR_UNDEFINED_METHOD'] = "Il metodo %s non e' definito nella classe %s!";
$P2G_LANGUAGE['ERR_ABSTRACT_CLASS'] = "La classe <I>%s</I> e' astratta e non puo' essere istanziata direttamente!";
$P2G_LANGUAGE['ERR_DATE_FORMAT'] = "La data %s non e' nel formato richiesto %s.";
$P2G_LANGUAGE['ERR_INVALID_RESOURCE'] = "La variabile %s passata alla funzione o al metodo %s non rappresenta una risorsa valida.";
$P2G_LANGUAGE['ERR_INVALID_AUTHENTICATOR'] = "La classe di autenticazione %s non e' una sottoclasse di php2go.auth.Auth!";
$P2G_LANGUAGE['ERR_INVALID_AUTHENTICATOR_PATH'] = "Non e' stato possibile importare e/o instanziare la classe (o l'oggetto) di autenticazione %s!";
$P2G_LANGUAGE['ERR_INVALID_USERCONTAINER'] = "La classe utente container %s non e' una sottoclasse di php2go.auth.User!";
$P2G_LANGUAGE['ERR_INVALID_USERCONTAINER_PATH'] = "Non e' stato possibile instanziare e/o importare la classe (o l'oggetto) utente container %s!";
$P2G_LANGUAGE['ERR_INVALID_AUTHORIZER'] = "La classe authorizer %s non e' una classe figlia di php2go.auth.Authorizer!";
$P2G_LANGUAGE['ERR_INVALID_AUTHORIZER_PATH'] = "Non e' stato possibile importare e/o instanziare la classe authorizer %s!";
$P2G_LANGUAGE['ERR_XML_PARSE'] = "Si e' verificato un errore durante il parsing del file XML:<BR>Errore: %s<BR>Riga: %s, Colonna: %s";
$P2G_LANGUAGE['ERR_STATEMENT_EVAL'] = "Si e' verificato un errore durante il parsing del codice PHP <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_FIND_FILE'] = "Non e' stato possibile trovare il file o la cartella %s. Esiste?";
$P2G_LANGUAGE['ERR_CANT_OPEN_FILE'] = "Non e' stato possibile aprire il file %s nella modalita' %s. Si controllino i permessi del file e la modalita' utilizzata.";
$P2G_LANGUAGE['ERR_CANT_CREATE_FILE'] = "Non e' stato possibile creare il file o la cartella %s. Si controllino i permessi della cartella superiore.";
$P2G_LANGUAGE['ERR_CANT_READ_FILE'] = "Non e' stato possibile aprire il file %s per la lettura. Esiste? Il percorso e' corretto?";
$P2G_LANGUAGE['ERR_CANT_WRITE_FILE'] = "Non e' stato possible aprire il file o la cartella %s per la scrittura. Si possiedono i permessi adeguati?";
$P2G_LANGUAGE['ERR_CANT_DELETE_FILE'] = "Non e' stato possibile cancellare il file %s.<BR>Si possiedono i permessi di scrittura per questa cartella? Si possiedono i permessi per cancellare il file?";
$P2G_LANGUAGE['ERR_CANT_INCLUDE_FILE'] = "Non e' stato possibile aprire il file %s per l'inclusione. Esiste? Il percorso e' corretto?";
$P2G_LANGUAGE['ERR_CANT_CHANGE_MODE'] = "Non e' stato possibile applicare la modalita' %s al file o alla cartella %s.";
$P2G_LANGUAGE['ERR_CANT_READ_DIR'] = "Non e' stato possible trovare la cartella %s. Esiste?";
$P2G_LANGUAGE['ERR_CANT_OPEN_DIR'] = "Non e' stato possible aprire la cartella %s per la lettura. Si controllino i permessi.";
$P2G_LANGUAGE['ERR_CANT_FIND_SERIALIZATION_FILE'] = "Non e' stato possibile aprire il file contenente l'oggetto salvato. Il percorso e' corretto? Il file e' stato creato?";
$P2G_LANGUAGE['ERR_EMPTY_INSERT_QUERY'] = "La query 'INSERT' sulla tabella '%s',  utilizzando i valori dati, ha prodotto un set di valori vuoto.";
$P2G_LANGUAGE['ERR_EMPTY_UPDATE_QUERY'] = "La query 'UPDATE' sulla tabella '%s' con la clausola %s, utilizzando i valori dati, ha prodotto un set di valori vuoto.";
$P2G_LANGUAGE['ERR_EMPTY_TOGLOBALS_QUERY'] = "La query %s, usata per rendere pubbliche le variabili nello scope globale, ha prodotto un set di valori vuoto.";
$P2G_LANGUAGE['ERR_TOGLOBALS_WRONG_USAGE'] = "La query passata al metodo Db::toGlobals() deve cominciare con 'SELECT' (SQL).";
$P2G_LANGUAGE['ERR_EMPTY_TEMPLATE_FILE'] = "Non e' stato possibile inizializzare il template poiche' non si e' specificato il nome del file!";
$P2G_LANGUAGE['ERR_EMPTY_TEMPLATE_CONTENT'] = "Il template non e' stato preparato poiche' il suo contenuto e' vuoto. Si controlli il contenuto passato al costruttore.";
$P2G_LANGUAGE['ERR_CANT_FIND_VARIABLE'] = "Non e' stato possibile trovare la variabile %s nel template <I>%s</I>. <BR>Per ottenere un risultato corretto si inserisca {%s} nel codice del template.";
$P2G_LANGUAGE['ERR_CANT_FIND_BLOCK'] = "Non e' stato possibile trovare il blocco <I>%s</I> per la creazione. Lo si e' dichiarato all'interno del template?";
$P2G_LANGUAGE['ERR_CANT_FIND_BLOCK_REUSE'] = "Non e' stato possibile trovare il blocco %s per usarlo come %s.";
$P2G_LANGUAGE['ERR_CANT_PUT_ON_PREPARED_TEMPLATE'] = "L'elemento %s e' stato gia' configurato con il metodo DocumentElement::parse(). Non e' piu' possibile aggiungere elementi con il metodo put().";
$P2G_LANGUAGE['ERR_TEMPLATE_NOT_PREPARED'] = "Il template %s non e' stato preparato. Si chiami il metodo %s::parse() prima di eseguire operazioni con l'oggetto.";
$P2G_LANGUAGE['ERR_TEMPLATE_ALREADY_PREPARED'] = "Questo template e' stato gia' preparato. Si deve chiamare il metodo parse() una sola volta.";
$P2G_LANGUAGE['ERR_TPLPARSE_DEFINED_BLOCK'] = "Il blocco %s e' gia stato definito!";
$P2G_LANGUAGE['ERR_TPLPARSE_UNBALANCED_BLOCKDEF'] = "La definizione del blocco <I>%s</I> non e' bilanciata!";
$P2G_LANGUAGE['ERR_TPLPARSE_INCOMPLETE_BLOCKDEF'] = "La definizione del blocco <I>%s</I> non e' completa all'interno del tag %s!";
$P2G_LANGUAGE['ERR_TPLPARSE_BLOCKINSIDELOOP'] = "Non e' possibile definire blocchi dinamici all'interno di un ciclo!";
$P2G_LANGUAGE['ERR_TPLPARSE_UNBALANCED_TAG'] = "La definizione del tag <I>%s</I> non e' bilanciata!";
$P2G_LANGUAGE['ERR_TPLPARSE_UNEXPECTED_TAG'] = "<I>%s</I> e' un tag non atteso!";
$P2G_LANGUAGE['ERR_TPLPARSE_UNBALANCED_PARENTHESIS'] = "Parentesi non bilanciata all'interno dell'espressione <I>%s</I>!";
$P2G_LANGUAGE['ERR_TPLPARSE_TAG_SYNTAX'] = "La definizione del tag <I>%s</I> non e' valida : %s!";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_TOKEN'] = "<I>%s</I> non e' un token valido all'interno dell'espressione : %s";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_ATTRIBUTE'] = "Attributo mancante o non valido!";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_ATTRIBUTENAME'] = "<I>%s</I> non e' un nome valido per un attributo!";
$P2G_LANGUAGE['ERR_TPLPARSE_REQUIRED_ATTRIBUTE'] = "La proprieta' <I>%s</I> e' richiesta nella definizione del tag %s!";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_TAG_ATTRIBUTE'] = "La proprieta' <I>%s</I> e' non valida nella definizione del tag %s!";
$P2G_LANGUAGE['ERR_TPLPARSE_MISSING_ASSIGN'] = "Il carattere  '=' non e' stato trovato dopo il nome dell'attributo!";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_IS_EXPR'] = "Operazione non valida per un operazione Invalid 'is' : <I>%s</I>";
$P2G_LANGUAGE['ERR_TPLPARSE_MISSING_TAG_ARGS'] = "Il tag <I>%s</I> necessita di argomenti!";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_TAG_ARGS'] = "Il tag <I>%s</I> non puo' contenere argomenti!";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_MODIFIER'] = "Il modificatore %s non e' valido o e' inesistente!";
$P2G_LANGUAGE['ERR_WIDGET_INCLUDE'] = "Il widget <i>%s</i> puo' solo essere usato con i tag INCLUDE WIDGET!";
$P2G_LANGUAGE['ERR_WIDGET_MANDATORY_PROPERTY'] = "La proprieta' <i>%s</i> e' obbligatoria nella definizione del widget <i>%s</i>!";
$P2G_LANGUAGE['ERR_EMPTY_DOC_LAYOUT'] = "Non ci sono elementi definiti nel layout del documento.";
$P2G_LANGUAGE['ERR_CANT_REPLICATE_ROOT_BLOCK'] = "Il blocco base di un template non puo' essere sovrascritto.";
$P2G_LANGUAGE['ERR_MUST_BE_POSITIVE'] = "Il valore di %s deve essere maggiore di zero nella funzione %s.";
$P2G_LANGUAGE['ERR_MUST_BE_AN_ARRAY'] = "Il valore di %s deve essere un array nella funzione %s";
$P2G_LANGUAGE['ERR_VALUE_OUT_OF_BOUNDS'] = "Il valore di '%s' e' fuori dai limiti consentiti: %s and %s";
$P2G_LANGUAGE['ERR_URL_MALFORMED'] = "Il valore di <I>%s</I> non e' un valido URL!";
$P2G_LANGUAGE['ERR_HOST_INVALID'] = "<I>%s</I> non e' un host valido!";
$P2G_LANGUAGE['ERR_DNS_NOT_FOUND'] = "Non e' stato possibile trovare un DNS valido per il dominio %s.";
$P2G_LANGUAGE['ERR_SESSION_PROPERTY_NOT_FOUND'] = "La proprieta' %s della variabile di sessione %s non e' stata trovata. Si esegua la funzione createProperty()";
$P2G_LANGUAGE['ERR_SESSION_TIMECOUNTER_NOT_FOUND'] = "Il timer (time counter) %s della variabile di sessione %s non e' stato trovato. Si esegua la funzione createTimeCounter()";
$P2G_LANGUAGE['ERR_INVALID_OBJECT'] = "L'oggetto passato come parametro non e' un'instanza valida della classe <I>%s</I>!";
$P2G_LANGUAGE['ERR_CALCULATOR_MISSING_PARAMETERS'] = "Non ci sono parametri sufficienti per creare la calcolatrice!";
$P2G_LANGUAGE['ERR_COLORSEL_MISSING_PARAMETERS'] = "Non ci sono parametri sufficienti per creare la tavolozza dei colori!";
$P2G_LANGUAGE['ERR_INVALID_FORM_METHOD'] = "Il metodo %s non puo' essere utilizzato per inviare il form '%s'";
$P2G_LANGUAGE['ERR_INVALID_VALIDATE_FUNC'] = "<I>%s</I> non e' un nome valido per una funzione di convalida!";
$P2G_LANGUAGE['ERR_FORM_DUPLICATED_FIELD'] = "Il campo <I>%s</I> e' stato gia' definito nel form <I>%s</I>!";
$P2G_LANGUAGE['ERR_FORM_DUPLICATED_BUTTON'] = "Il pulsante <I>%s</I> e' gia' stato definito nel form <I>%s</I>!";
$P2G_LANGUAGE['ERR_FORM_DUPLICATED_SECTION'] = "L'ID della sezione ID <I>%s</I> e' stato gia' definito nel form <I>%s</I>!";
$P2G_LANGUAGE['ERR_FORM_SECTION_EVALFUNC'] = "Non e' stato possibile definire la visibilita' della sezione <I>%s</I>, poiche' la funzione <I>%s</I> non e' stata definita!";
$P2G_LANGUAGE['ERR_FORM_SECTION_TPLBLOCK'] = "La sezione <I>%s</I> e' di tipo 'conditional'. Occorre dichiarare un blocco chiamato <I>%s</I> nel template contenente i campi della sezione!";
$P2G_LANGUAGE['ERR_FORM_REQUIRED_SUMMARY'] = "I seguenti campi sono obbligatori:";
$P2G_LANGUAGE['ERR_FORM_ERRORS_SUMMARY'] = "Questo form contiene i seguenti errori:";
$P2G_LANGUAGE['ERR_FORM_COMPLETE_FIELDS'] = "Si inseriscano i valori nei campi e si riprovi.";
$P2G_LANGUAGE['ERR_FORM_FIX_FIELDS'] = "Si correggano i valori inseriti nei campi e si riprovi.";
$P2G_LANGUAGE['ERR_FORM_FIELD_REQUIRED'] = "Il campo %s e' obbligatorio.";
$P2G_LANGUAGE['ERR_FORM_FIELD_INVALID'] = "Il valore del campo %s non e' corretto!";
$P2G_LANGUAGE['ERR_FORM_FIELD_INVALID_DATATYPE'] = "Il campo %s contiene un valore non valido %s.";
$P2G_LANGUAGE['ERR_FORM_FIELD_INVALID_FLOAT'] = "La precisione numerica del campo %s non e' valida.\nLe dimensioni massime sono %s per la parte intera e %s per la parte decimale.";
$P2G_LANGUAGE['ERR_FORM_FIELD_ALPHANUM'] = "Il campo %s deve contenere esclusivamente caratteri alfanumerici.";
$P2G_LANGUAGE['ERR_FORM_FIELD_CHOICE'] = "Il campo %s deve contenere uno dei seguenti valori: <I>%s</I>";
$P2G_LANGUAGE['ERR_FORM_FIELD_CREDITCARD'] = "Il campo %s contiene un numero di carta di credito %s non valido.";
$P2G_LANGUAGE['ERR_FORM_FIELD_MIN_LENGTH'] = "Il campo %s deve contenere almeno %s caratteri.";
$P2G_LANGUAGE['ERR_FORM_FIELD_MAX_LENGTH'] = "Il campo %s puo' contenere al massimo %s caratteri.";
$P2G_LANGUAGE['ERR_FORM_FIELD_EQ'] = "Il valore del campo %s deve essere uguale al valore del campo %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_NEQ'] = "Il valore del campo %s non puo' essere uguale al valore del campo %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_GT'] = "Il valore del campo %s deve essere maggiore del valore del campo %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_LT'] = "Il valore del campo %s deve essere minore del valore del campo %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_GOET'] = "Il valore del campo %s deve essere maggiore o uguale al valore del campo %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_LOET'] = "Il valore del campo %s deve essere minore o uguale al valore del campo %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_EQ'] = "Il valore del campo %s deve essere uguale a %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_NEQ'] = "Il valore del campo %s non puo' essere uguale a %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_GT'] = "Il valore del campo %s deve essere maggiore di %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_LT'] = "Il valore del campo %s deve essere minore di %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_GOET'] = "Il valore del campo %s deve essere maggiore uguale a %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_LOET'] = "Il valore del campo %s deve essere minore uguale a %s!";
$P2G_LANGUAGE['ERR_FORM_INVALID_MASK'] = "La maschera <I>%s</I> sul campo <I>%s</I> non e' valida!";
$P2G_LANGUAGE['ERR_FORM_INVALID_FIELDTYPE'] = "Il tipo di campo <I>%s</I> non e' valido!";
$P2G_LANGUAGE['ERR_FORM_CAPTCHA'] = "Il codice inserito nel campo %s non coincide con il contenuto dell'immagine!";
$P2G_LANGUAGE['ERR_DATAGRID_INVALID_FIELDTYPE'] = "Il tipo di campo <I>%s</I> non puo' essere usato in una griglia <I>grid</I>!";
$P2G_LANGUAGE['ERR_DATAGRID_INVALID_CELLSIZES'] = "Il valore dell'attributo <I>CELLSIZES</I> nel campo <I>%s</I> non e' valido. Occorre fornire N+1 dimensioni (dove N e' la dimensione del FIELDSET) e la somma delle dimensioni deve essere uguale a 100!";
$P2G_LANGUAGE['ERR_DATAGRID_INVALID_FIELDCOUNT'] = "La query definita in <I>DATASOURCE</I> del campo <I>%s</I> deve  produrre N+2 colonne (dove N e' la dimensione del FIELDSET)";
$P2G_LANGUAGE['ERR_FORM_WRONG_LISTENER'] = "Gli attributi del gestore degli eventi <I>%s</I> non sono corretti o sono incompleti!";
$P2G_LANGUAGE['ERR_FORM_WRONG_RULE'] = "Gli attributi della regola <I>%s</I> non sono corretti o sono incompleti!";
$P2G_LANGUAGE['ERR_INVALID_DATASOURCE_SYNTAX'] = "La definizione del tag DATASOURCE nel campo <I>%s</I> non e' valida.<BR>I nodi figli KEYFIELD e LOOKUPTABLE sono obbligatori!";
$P2G_LANGUAGE['ERR_MISSING_GROUPFIELD_CHILDREN'] = "La definizione del campo <I>%s</I> e' incompleta. Occorre fornire almeno un nodo figlio &lt;OPTION&gt; !";
$P2G_LANGUAGE['ERR_MISSING_OPTION_VALUE'] = "Manca l'attributo VALUE dell'opzione all'indice <I>%d</I> nel campo <I>%s</I>!";
$P2G_LANGUAGE['ERR_MISSING_DBGROUPFIELD_DATASOURCE'] = "Non e' stato definito un tag DATASOURCE nel tag %s <I>%s</I>!";
$P2G_LANGUAGE['ERR_EMPTY_DBGROUPFIELD_RESULTS'] = "La query fornita nell'elemento DATASOURCE di %s <I>%s</I> non ha prodotto risultati!";
$P2G_LANGUAGE['ERR_MISSING_COMBOOPTION_VALUE'] = "Manca l'attributo VALUE attribute dell'opzione all'indice %d nel COMBOFIELD <I>%s</I>!";
$P2G_LANGUAGE['ERR_MISSING_EDITSELECTION_CHILDREN'] = "La definizione di EDITSELECTIONFIELD <I>%s</I> non e' completa.  I nodi figli EDITFIELD e LOOKUPFIELD sono obbligatori!";
$P2G_LANGUAGE['ERR_MISSING_LOOKUPSELECTION_CHILDREN'] = "La  definizione di LOOKUPSELECTIONFIELD <I>%s</I> e' incorretta. Il nodo deve avere due nodi figli LOOKUPFIELD!";
$P2G_LANGUAGE['ERR_MISSING_SOURCELOOKUP_DATASOURCE'] = "Non c'e' un DATASOURCE definito nel primo nodo figlio LOOKUPFIELD i LOOKUPSELECTIONFIELD <I>%s</I>!";
$P2G_LANGUAGE['ERR_MISSING_QUERY_ELEMENTS'] = "Gli elementi \$fields e \$tables sono obbligatori nella costruzione di una query SQL!";
$P2G_LANGUAGE['ERR_INVALID_DATAGRID_STRUCTURE'] = "La struttura della griglia <I>%s</I> non e' valida. Occorre dichiarare un <I>DATASOURCE</I> e un <I>FIELDSET</I> contenente almeno un campo!";
$P2G_LANGUAGE['ERR_MISSING_RANGEFIELD_CHILDREN'] = "La  definizione di RANGEFIELD <I>%s</I> e' incorretta. Il nodo deve avere due nodi figli EDITFIELD, COMBOFIELD, LOOKUPFIELD o DATEPICKERFIELD!";
$P2G_LANGUAGE['ERR_EDITSEARCH_INVALID_DATAFILTER'] = "Il DATAFILTER <I>%s</I> non e' stato specificato correttamente.";
$P2G_LANGUAGE['ERR_EDITSEARCH_INVALID_DATAFILTER_MASK'] = "La maschera assegnata al DATAFILTER <I>%s</I> non e' valida.";
$P2G_LANGUAGE['ERR_EDITSEARCH_DUPLICATED_DATAFILTER'] = "Esiste gia' un altro DATAFILTER con ID <I>%s</I>.";
$P2G_LANGUAGE['ERR_MISSING_EDITSEARCH_CHILDREN'] = "EDITSEARCHFIELD <I>%s</I> is non e' stato specificato completamente. Occorre fornire almeno un DATAFILTER, un DATASOURCE e un LOOKUPFIELD!";
$P2G_LANGUAGE['ERR_CSV_DB_JSRS'] = "Non e' stato possibile completare l'operazione!";
$P2G_LANGUAGE['ERR_SEARCHFORM_EMPTY'] = "La ricerca non puo' essere effettuata in quanto tutti i campi sono vuoti.";
$P2G_LANGUAGE['ERR_SEARCHFORM_INVALID'] = "La ricerca non puo' essere effettuata in quanto i valori dei filtri non sono stati definiti o sono lunghi meno di %d caratteri.";
$P2G_LANGUAGE['ERR_REPORT_MAX_GROUP_COLS'] = "Il numero di colonne raggruppate deve essere minore del numero di colonne nella query!";
$P2G_LANGUAGE['ERR_REPORT_UNKNOWN_GROUP_COL'] = "La colonna raggruppata <I>%s</I> non e' definita nel risultato della query!";
$P2G_LANGUAGE['ERR_REPORT_MISSING_BLOCK_VARIABLE'] = "La variabile <I>%s</I> del blocco <I>%s</I> non e' stata dichiarata nel template!";
$P2G_LANGUAGE['ERR_REPORT_INVALID_COLSIZES'] = "Il valore <I>%s</I> non e' una quantita' valida a definire le dimensioni delle colonne!";
$P2G_LANGUAGE['ERR_REPORT_COL_SIZES_SUM'] = "La somma delle dimensioni delle colonne passata alla funzione setColumnSizes deve essere uguale a 100!";
$P2G_LANGUAGE['ERR_REPORT_COL_COUNT_MISMATCH'] = "Il numero delle dimensioni delle colonne fornito (%s) non e' uguale al numero di colonne del report (%s, %s grouping)";
$P2G_LANGUAGE['ERR_REPORT_MISSING_SEARCH_VARIABLE'] = "Non e' stato possibile costruire il form di ricerca poiche' la variabile <I>%s</I> non e' stata trovata.<BR>Si dichiari {%s} nel template del report per procedere.";
$P2G_LANGUAGE['ERR_REPORT_SEARCH_PARS_MALFORMED'] = "Occorre fornire 'Label', 'Field Name' e 'Mask' per costruire i parametri di ricerca!";
$P2G_LANGUAGE['ERR_REPORT_SEARCH_INVALID_MASK'] = "La maschera <I>%s</I> del parametro di ricerca <I>#%d</I> non e' valida!";
$P2G_LANGUAGE['ERR_REPORT_MIN_ALT_STYLE'] = "La funzione setAlternateStyle devi ricevere almeno due parametri (i nomi dei CSS)!";
$P2G_LANGUAGE['ERR_REPORT_HIDDEN_GROUP'] = "Una colonna nascosta (%s) non puo' essere inclusa nel gruppo delle intestazioni!";
$P2G_LANGUAGE['ERR_REPORT_GROUP_HIDDEN'] = "Una colonna mostrata nelle intestazioni del gruppo (%s) non puo' essere definita come nascosta!";
$P2G_LANGUAGE['ERR_REPORT_MAX_HIDDEN_COLS'] = "Il numero di colonne nascoste deve essere minore del numero di colonne nella query!";
$P2G_LANGUAGE['ERR_REPORT_UNKNOWN_HIDDEN_COL'] = "La colonna <I>%s</I> definita come nascosta non esiste nel risultato della query!";
$P2G_LANGUAGE['ERR_DATASET_INVALID_TYPE'] = "Il tipo <I>%s</I> non e' un tipo valido di DataSet!";
$P2G_LANGUAGE['ERR_MENU_WRONG_ROOT_SQL'] = "La query passata come parametro \$rootSql del metodo Menu::loadFromDatabase() ha prodotto un set vuoto di risultati o non possiede i campi minimi richiesti (CAPTION e LINK)!";
$P2G_LANGUAGE['ERR_MENU_WRONG_CHILDREN_STATEMENT'] = "La query passata al parametro \$childSql del metodo Menu::loadFromDatabase() non e' valida.<BR>Si fornisca una linea  a statement containing the '~var~' (Relation Column) plus the two required fields (CAPTION and LINK)";
$P2G_LANGUAGE['ERR_MENU_NOT_FOUND'] = "Il menu non e' stato creato. Si costruiscano gli elementi del menu usando i metodi Menu::loadFromDatabase() o Menu::loadFromXmlFile().";
$P2G_LANGUAGE['ERR_MENU_EMPTY_XML_ROOT'] = "Il nodo padre del file XML non ha nodi figli!";
$P2G_LANGUAGE['ERR_MENU_INVALID_IMAGE'] = "L'immagine <I>%s</I> non e' valida. I valori possibili sono: <I>%s</I>.";
$P2G_LANGUAGE['ERR_FTP_MISSING_HOST'] = "Non e' stato possibile aprire la connessione. Occorre impostare la connessione usando i metodi FtpClient::setServer() e FtpClient::setUserInfo()";
$P2G_LANGUAGE['ERR_FTP_MISSING_USER_OR_PASS'] = "Non e' stato possibile eseguire l'operazione di autenticazione. Non e' stato fornito il nome utente o la password.";
$P2G_LANGUAGE['ERR_SPRSH_OUT_OF_BOUNDS'] = "Sono stati superati i limiti massimi del foglio elettronico: [%d;%d]";
$P2G_LANGUAGE['ERR_SPRSH_MAX_NOTE_CHARS'] = "E' stata superata la dimensione massima per una cella tipo nota<BR>Valore: %s";
$P2G_LANGUAGE['ERR_SPRSH_MAX_FONTS'] = "E' stato superato il numero massimo di 4 font per ogni foglio elettronico!";
$P2G_LANGUAGE['ERR_UPLOAD_NOT_FOUND'] = "Non e' stato possibile completare l'upload. Il file non e' stato trasferito correttamente.";
$P2G_LANGUAGE['ERR_UPLOAD_TOO_BIG'] = "Non e' stato possibile completare l'upload. La dimensione del file e' maggiore della dimensione massima permessa.";
$P2G_LANGUAGE['ERR_UPLOAD_INVALID_NAME'] = "Non e' stato possibile completare l'upload. Il nome del file %s non e' valido.";
$P2G_LANGUAGE['ERR_UPLOAD_INVALID_TYPE'] = "Non e' stato possibile completare l'upload. L'estensione %s non e' permessa.";
$P2G_LANGUAGE['ERR_UPLOAD_FILE_EXISTS'] = "Non e' stato possibile completare l'upload. Il file <I>%s</I> esiste gia' e non puo' essere sovrascritto.";
$P2G_LANGUAGE['ERR_UPLOAD_CANT_MOVE'] = "Non e' stato possibile spostare il file di upload temporaneo nella cartella di destinazione.";
$P2G_LANGUAGE['ERR_CANT_OPEN_SOCKET'] = "Non e' stato possibile aprire il socket usando la porta %s con l'host %s!%s";
$P2G_LANGUAGE['ERR_SOCKET_NOT_CONNECTED'] = "La connessione con il socket non e' attiva!";
$P2G_LANGUAGE['ERR_SOCKET_EOF'] = "E' stata raggiunta la fine del file, l'operazione non puo' essere completata!";
$P2G_LANGUAGE['ERR_SOCKET_TIMEOUT'] = "E' stato raggiunto il timeout del socket timeout. L'operazione non puo' essere completata!";
$P2G_LANGUAGE['ERR_MAIL_EMPTY_RCPT'] = "Non ci sono recipienti per il messaggio!";
$P2G_LANGUAGE['ERR_SIGNED_MESSAGE_SIGN'] = "Non e' stato possibile firmare il contenuto del messaggio.<BR>%s";
$P2G_LANGUAGE['ERR_SMTP_CONNECTION'] = "Si e' verificato un errore durante la connessione al server SMTP %s!<BR>[Errore %d] - %s";
$P2G_LANGUAGE['ERR_SMTP_COMMAND'] = "Si e' verificato un errore durante l'esecuzione del comando %s sul server SMTP %s!<BR>[Errore %d] - %s";
$P2G_LANGUAGE['ERR_SMTP_AUTHENTICATE'] = "Non e' stato possibile eseguire la procedura di autenticazione con il server SMTP!";
$P2G_LANGUAGE['ERR_CRYPT_MISSING_KEY'] = "Per criptare o decriptare i dati occorre fornire una chiave utilizzando il metodo Crypt::setKey()!";
$P2G_LANGUAGE['ERR_CRYPT_OPEN_MODULE'] = "Non e' stato possibile inizializzare la cifra <I>%s</I> usando la modalita' %s!";
$P2G_LANGUAGE['ERR_OPENSSL_PUBKEY_ENCRYPT'] = "Non e' stato possibile estrarre una chiave pubblica per criptare i dati forniti. Occorre indicare come parametro al metodo OpenSSLCrypt::setCertificatePath() il nome di un file contenente un certificato valido!";
$P2G_LANGUAGE['ERR_OPENSSL_PUBKEY_VERIFY'] = "Non e' stato possibile estrarre una chiave pubblica per verificare la firma inclusa nei dati. Si fornisca come parametro al metodo OpenSSLCrypt::setCertificatePath() il nome di un file contenente un certificato valido!";
$P2G_LANGUAGE['ERR_OPENSSL_PRIVKEY_DECRYPT'] = "Non e' stato possibile estrarre una chiave privata per decriptare i dati forniti. Si fornisca il file contenente la chiave privata e la 'passphrase' nel caso la chiave lo richieda.";
$P2G_LANGUAGE['ERR_OPENSSL_PRIVKEY_SIGN'] = "Non e' stato possibile estrarre una chiave privata per generare la firma digitale. Si fornisca il file contenente la chiave privata e la 'passphrase' nel caso la chiave lo richieda.";
$P2G_LANGUAGE['ERR_OPENSSL_CERT_PATH'] = "Il percorso del certificato non e' valido!";
$P2G_LANGUAGE['ERR_OPENSSL_READ_CERT'] = "Non e' stato possibile leggere il contenuto del certificato %s!";
$P2G_LANGUAGE['ERR_POP3_CONNECTION'] = "Si e' verificato un errore durante la connessione al server POP3 %s!<BR>[Errore %d] - %s";
$P2G_LANGUAGE['ERR_POP3_COMMAND'] = "Si e' verificato un errore durante l'esecuzione del comando %s sul server POP3!<BR>%s";
$P2G_LANGUAGE['ERR_POP3_AUTHENTICATE'] = "Non e' stato possibile effettuare l'autenticazione sul server POP3!";
$P2G_LANGUAGE['ERR_POP3_APOP'] = "Non e' stato possibile eseguire il comando APOP!";
$P2G_LANGUAGE['ERR_HTTP_CONNECT'] = "Non e' stato possibile stabilire una connessione HTTP con il sito (host) <I>%s</I>!";
$P2G_LANGUAGE['ERR_HTTP_MISSING_HOST'] = "Il sito (host) HTTP non e' stato impostasto. Si esegua il metodo HttpClient::setHost().";
$P2G_LANGUAGE['ERR_CANT_INSTANTIATE_VALIDATOR'] = "Non e' stato possibile istanziare la classe di convalida <B>%s</B>!";
$P2G_LANGUAGE['ERR_INVALID_VALIDATOR'] = "La classe di convalida <I>%s</I> non e' valida: non e' stato trovato il metodo %s::execute().";
$P2G_LANGUAGE['ERR_ASSERTION_MESSAGE'] = "L'espressione valutata non ha ottenuto i risultati aspettati.";
$P2G_LANGUAGE['ERR_INVALID_CALLBACK'] = "La funzione o il metodo <I>%s<I> non rappresenta una chiamata valida";
$P2G_LANGUAGE['ERR_INVALID_PROPERTIES_FILE'] = "Il file <I>%s</I> non contiene valori di configurazione validi!";
$P2G_LANGUAGE['ERR_INVALID_COLOR_SPEC'] = "<I>%s</I> non e' una specificazione di colore corretta.";
$P2G_LANGUAGE['ERR_ALLOCATE_COLOR'] = "Non e' stato possibile allocare un nuovo colore per quest'immagine!";
$P2G_LANGUAGE['ERR_JSRS_MISSING_HANDLER'] = "Manca il nome o l'alias dell'handler JSRS!";
$P2G_LANGUAGE['ERR_JSRS_INVALID_HANDLER'] = "L'handler JSRS %s non e' valido!";
$P2G_LANGUAGE['ERR_AJAX_SUPPORT'] = "Il tuo browser non supporta AJAX!";
$P2G_LANGUAGE['ERR_JSON_INVALID_PROPNAME'] = "JSON Errore di decodifica: %s non e' un nome valido di proprieta'";
$P2G_LANGUAGE['ERR_JSON_MISSING_COLON'] = "JSON Errore di decodifica: mancano i due punti alla posizione %d";
$P2G_LANGUAGE['ERR_JSON_INVALID_PROPVALUE'] = "JSON Errore di decodifica: %s non e' un valore valido per la proprieta'";
$P2G_LANGUAGE['ERR_JSON_MISSING_COMMA'] = "JSON Errore di decodifica: manca una virgola alla posizione %d";
$P2G_LANGUAGE['ERR_JSON_ESCAPE_SEQUENCE'] = "JSON Errore di decodifica: sequenza di escape non valda alla posizione %d";
$P2G_LANGUAGE['ERR_JSON_UNTERMINATED_STRING'] = "JSON Errore di decodifica: stringa non terminata alla posizione %d";
$P2G_LANGUAGE['ERR_JSON_CYCLE'] = "L'encoder JSON ha trovato un ciclo in un instanza della classe %s!";
$P2G_LANGUAGE['ERR_JSON_ENCODE'] = "Si e' verificato un errore nella conversione del tipo %s in una stringa JSON!";
$P2G_LANGUAGE['DEFAULT_BTN_VALUE'] = "Invia";
$P2G_LANGUAGE['CALENDAR_LINK_TITLE'] = "Apri il calendario";
$P2G_LANGUAGE['CALCULATOR_LINK_TITLE'] = "Apri la calcolatrice";
$P2G_LANGUAGE['CALCULATOR_WINDOW_TITLE'] = "Calcolatrice";
$P2G_LANGUAGE['CALCULATOR_RESULT_CAPTION'] = "Risultato";
$P2G_LANGUAGE['DAYS_OF_WEEK'] = array("Domenica", "Lunedi'", "Martedi'", "Mercoledi'", "Giovedi'", "Venerdi'", "Sabato");
$P2G_LANGUAGE['DAYS_OF_WEEK_ABBR'] = array("Dom", "Lun", "Mar", "Mer", "Gio", "Ven", "Sab");
$P2G_LANGUAGE['MONTHS_OF_YEAR'] = array("Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno", "Luglio", "Agosto", "Settembre", "Ottobre", "Novembre", "Dicembre");
$P2G_LANGUAGE['MONTHS_OF_YEAR_ABBR'] = array("Gen", "Feb", "Mar", "Apr", "Mag", "Giu", "Lug", "Ago", "Set", "Ott", "Nov", "Dic");
$P2G_LANGUAGE['COMPRESS_USE_MSG'] = "<!-- Usa %s Compressione -->";
$P2G_LANGUAGE['COMPRESS_DEBUG_MSG_ORIG'] = "Dimensione originale: %d";
$P2G_LANGUAGE['COMPRESS_DEBUG_MSG_COMP'] = "Dimensione compressa: %d";
$P2G_LANGUAGE['EMPTY_SEARCH'] = "Occorre scegliere un filtro ed inserire un termine da ricercare!";
$P2G_LANGUAGE['EMPTY_RESULTS'] = "La ricerca non ha prodotto risultati!";
$P2G_LANGUAGE['FORM_INPUT_MASK_MESSAGES'] = array(
	'optionalCharFirst' => "L'indicatore opzionale di carattere deve seguire un'espressione letterale.",
	'escapeCharLast' => "Il carattere di escape deve precedere un'espressione letterale.",
	'nestedCharClass' => "Classe di carattere non valida.",
	'invalidLimits' => "Definizione dei limiti di dimensione non valida."
);
$P2G_LANGUAGE['FORM_MASKS'] = array(
	'CPFCNPJ' => 'CPF/CNPJ',
	'CURRENCY' => 'Moneta',
	'DATE' => 'Data',
	'TIME' => 'Tempo',
	'EMAIL' => 'E-Mail',
	'FLOAT' => 'Numero decimale',
	'INTEGER' => 'Numero intero',
	'DIGIT' => 'Numero',
	'IP' => 'Indirizzo IP',
	'WORD' => 'Testo alfanumerico',
	'URL' => 'Indirizzo URL/Internet',
	'ZIP' => 'CAP'
);
$P2G_LANGUAGE['EDITSEARCH_BTN_VALUE'] = "Ricerca in corso...";
$P2G_LANGUAGE['EDITOR_VARS'] = array(
	'formatBlock' => 'Paragrafo',
	'formatBlockOptions' => array(
		'h1' => 'Intestazione 1 &lt;H1&gt;',
		'h2' => 'Intestazione 2 &lt;H2&gt;',
		'h3' => 'Intestazione 3 &lt;H3&gt;',
		'h4' => 'Intestazione 4 &lt;H4&gt;',
		'h5' => 'Intestazione 5 &lt;H5&gt;',
		'h6' => 'Intestazione 6 &lt;H6&gt;',
		'address' => 'Indirizzo &lt;ADDR&gt;',
		'pre' => 'Formattato &lt;PRE&gt;',
		'removeformat' => 'Elimina formattazione'
	),
	'topButtons' => array(
		'bold' => 'Grassetto',
		'italic' => 'Corsivo',
		'underline' => 'Sottolineato',
		'strikethrough' => 'Formato Barrato',
		'cut' => 'Taglia',
		'copy' => 'Copia',
		'paste' => 'Incolla',
		'pickforecolor' => 'Cambia il colore in primo piano',
		'pickbackcolor' => 'Cambia il colore di sfondo',
		'addemoticon' => 'Inserisci faccina',
		'selectall' => 'Seleziona tutto',
		'removeformat' => 'Elimina formattazione'
	),
	'font' => 'Carattere',
	'fontSize' => 'Dimens.',
	'bottomButtons' => array(
		'justifyleft' => 'Allinea a sinistra',
		'justifycenter' => 'Centra',
		'justifyright' => 'Allinea a destra',
		'justifyfull' => 'Giustifica il testo',
		'insertorderedlist' => 'Inserisci/Rimuovi lista numerata',
		'insertunorderedlist' => 'Inserisci/Rimuovi lista semplice',
		'indent' => 'Aumenta indentazione',
		'outdent' => 'Diminuisci indentazione',
		'createlink' => 'Inserisci un Link',
		'unlink' => 'Scollega',
		'insertimage' => 'Inserisci un\'immagine',
		'inserthorizontalrule' => 'Inserisci linea orizzontale'
	),
	'editMode' => 'Modifica in Modalita\' Avanzata',
	'emoticon' => 'Scegli una faccina',
	'validateMode' => 'Per usare gli strumenti avanzati dell\'editor occorre deselezionare il checkbox \'Modifiche Avanzate\'',
	'createLink' => 'Si inserisca l\'indirizzo del link (Esempio: http://www.dominio.com):',
	'insertImage' => 'Si inserisca il percorso dell\'immagine:'
);
$P2G_LANGUAGE['COLOR_PICKER_VARS'] = array(
	'popupTitle' => "Clicca sul colore che desideri",
	'colorTitle' => "Scegli questo colore"
);
$P2G_LANGUAGE['LOOKUP_CHOICE_FILTER_TIP'] = "--- Filtra la lista ---";
$P2G_LANGUAGE['LOOKUP_SELECTION_DEFAULT_SELFIRST'] = "--- Inserisci valori ---";
$P2G_LANGUAGE['LOOKUP_SELECTION_BUTTON_TITLES'] = array(
	'add' => 'Aggiungi gli elementi selezionati',
	'addall' => 'Aggiungi tutti gli elementi',
	'rem' => 'Rimuovi gli elementi selezionati',
	'remall' => 'Rimuovi tutti gli elementi'
);
$P2G_LANGUAGE['EDIT_SELECTION_BUTTON_TITLES'] = array(
	'add' => 'Aggiungi',
	'rem' => 'Rimuovi gli elementi selezionati',
	'remall' => 'Rimuovi tutti gli elementi'
);
$P2G_LANGUAGE['MEMO_COUNT_LABEL'] = "Caratteri";
$P2G_LANGUAGE['SEL_AVAILABLE_VALUES_LABEL'] = "Disponibili:";
$P2G_LANGUAGE['SEL_INSERTED_VALUES_LABEL'] = "Inseriti:";
$P2G_LANGUAGE['FORM_DATA_BIND_MESSAGES'] = array(
	'insertSuccess' => 'I dati sono stati inseriti con successo!',
	'updateEmpty' => 'Non ci sono record da modificare!',
	'updateSuccess' => 'I dati sono stati aggiornati con successo!',
	'deleteEmpty' => 'Non ci sono record da cancellare!',
	'deleteSuccess' => 'I dati sono stati cancellati con successo!',
	'deleteConfirm' => 'Si vuole davvero eliminare questo record?',
	'sortInvalid' => 'Occorre selezionare un campo da ordinare!',
	'gotoEmpty' => 'Occorre inserire un numero di record!',
	'gotoInvalid' => 'Il record richiesto non e\' valido!'
);
$P2G_LANGUAGE['FORM_DATA_BIND_TOOLBAR_VALUES'] = array(
	'navFirstTip' => 'Vai al primo record',
	'navPrevTip' => 'Vai al record precedente',
	'navNextTip' => 'Vai al record successivo',
	'navLastTip' => 'Vai all\'ultimo record',
	'actNew' => 'Nuovo',
	'actNewTip' => 'Crea un nuovo record',
	'actEdit' => 'Modifica',
	'actEditTip' => 'Modifica il record corrente',
	'actDel' => 'Cancella',
	'actDelTip' => 'Cancella il record corrente',
	'actSave' => 'Salva',
	'actSaveTip' => 'Salva il record corrente',
	'actCancel' => 'Annulla',
	'actCancelTip' => 'Annulla l\' azione corrente',
	'sortAsc' => 'Ordine ascendente',
	'sortDesc' => 'Ordine discendente',
	'sortChoose' => 'Scegli un campo da ordinare',
	'sortTit' => 'Ordina',
	'sortFirst' => 'Ordina i campi',
	'gotoTip' => 'Fornisci il numero dei record e clicca Vai',
	'goto' => 'Vai',
	'gotoBtnTip' => 'Clicca per andare sul record scelto',
	'gotoTit' => 'Vai',
	'filter' => 'Filtra',
	'filterTip' => 'Scegli il filtro di campo da applicare',
	'filterVTip' => 'Scegli il valore del filtro',
	'filterBtnTip' => 'Clicca qui per filtrare',
	'filterTit' => 'Filtra',
	'filterFirst' => 'Filtra i campi'
);
$P2G_LANGUAGE['REPORT_INVALID_PAGE'] = "Numero di pagina non corretto!";
$P2G_LANGUAGE['REPORT_EMPTY_VALUES'] = array(
	'emptyMsg' => 'La query o i filtri di ricerca utilizzati hanno prodotto un insieme vuoto di risultati',
	'backLink' => 'Torna indietro al Report'
);
$P2G_LANGUAGE['REPORT_ORDER_TIP'] = "Ordinare per %s";
$P2G_LANGUAGE['REPORT_ORDER_OPTIONS_LABEL'] = "Ordinare per:";
$P2G_LANGUAGE['REPORT_FUNCTION_MESSAGES'] = array(
	'firstTit' => 'Prima Pagina',
	'firstTip' => 'Vai alla prima pagina',
	'prevTit' => 'Precedente',
	'prevTip' => 'Vai alla pagina precedente',
	'prevScrTit' => '%d Indietro',
	'prevScrTip' => '%d Pagine Indietro nel report',
	'pageTip' => 'Vai alla pagina #%d di %d',
	'nextTit' => 'Successiva',
	'nextTip' => 'Vai alla pagina successiva',
	'nextScrTit' => '%d Avanti',
	'nextScrTip' => '%d Pagine Avanti nel report',
	'lastTit' => 'Ultima Pagina',
	'lastTip' => 'Vai all\'ultima pagina',
	'rowCount' => '%d record(s) trovati',
	'rowsPerPage' => '%d risultato(i) per pagina',
	'thisPage' => 'Pagina %d of %d',
	'rowInterval' => 'Elencati i risultati da %d a %d su un totale di %d',
	'goTo' => 'Vai alla pagina'
);
$P2G_LANGUAGE['REPORT_SEARCH_VALUES'] = array(
	'searchTitle' => 'Cerca',
	'filtersTitle' => 'Scegli un Campo',
	'addBtn' => 'Aggiungi',
	'mainOpOr' => 'Nessun Filtro',
	'mainOpAnd' => 'Tutti i Filtri',
	'clearBtn' => 'Cancella',
	'viewBtn' => 'Visualizza',
	'sendBtn' => 'Cerca',
	'emptyFilters' => 'Non ci sono filtri inseriti',
	'closeFilters' => 'Chiudi',
	'removeFilter' => 'Rimuovi',
	'addedFilter' => 'Aggiunto un filtro!',
	'resendConfirmation' => 'Si vuole effettuare nuovamente la ricerca utilizzando i filtri attuali?'
);
$P2G_LANGUAGE['REPORT_NUMBER_OPERATORS'] = array(
	'=' => 'Uguale a',
	'!=' => 'Diverso da',
	'>' => 'Maggiore di',
	'<' => 'Minore di',
	'>=' => 'Maggiore o uguale a',
	'<=' => 'Minore uguale a'
);
$P2G_LANGUAGE['REPORT_STRING_OPERATORS'] = array(
	'=' => 'Uguale a',
	'!=' => 'Non e\' uguale a',
	'LIKE' => 'Contiene',
	'NOT LIKE' => 'Non contiene',
	'LIKEI' => 'Comincia con',
	'LIKEF' => 'Finisce con'
);
$P2G_LANGUAGE['OPERATORS'] = array(
	'EQ' => 'uguale a',
	'NEQ' => 'diverso da',
	'GT' => 'maggiore di',
	'LT' => 'minore di',
	'LOET' => 'maggiore o uguale a',
	'GOET' => 'minore uguale a',
	'CONTAINING' => 'contiene',
	'STARTING' => 'comincia con',
	'ENDING' => 'finisce con',
	'BETWEEN' => 'tra',
	'IN' => 'e\' nella lista',
	'NOTIN' => 'non e\' nella lista',
	'AND' => 'e',
	'OR' => 'o'
);
$P2G_LANGUAGE['CHECKBOX_DESCRIPTIONS'] = array(
	'T' => '%s e\' selezionato',
	'F' => '%s non e\' selezionato'
);
$P2G_LANGUAGE['CHECKGROUP_SHORTCUTS'] = array(
	'all' => 'Tutti',
	'none' => 'Nessuno',
	'invert' => 'Inverti'
);
return $P2G_LANGUAGE;
?>