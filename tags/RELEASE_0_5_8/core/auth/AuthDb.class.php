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
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @copyright 2002-2007 Marcos Pont
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version $Id$
 */

import('php2go.auth.Auth');
import('php2go.db.QueryBuilder');

/**
 * Default authentication table
 */
define('AUTH_DB_DEFAULT_TABLE', 'auth');

/**
 * Authentication driver based on a database
 *
 * Based on the local properties {@link $tableName}, {@link $dbFields},
 * {@link $extraClause} and the fetched login credentials, this authenticator
 * will build and execute a database query to verify if the user is valid.
 *
 * If AUTH.AUTHENTICATOR_PATH is missing in the global configuration, this will be used
 * as the default authenticator returned from calls to {@link Auth::getInstance()}.
 *
 * @package auth
 * @uses Db
 * @uses QueryBuilder
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class AuthDb extends Auth
{
	/**
	 * Database connection ID
	 *
	 * @var string
	 */
	var $connectionId = NULL;

	/**
	 * Name of the table that must be used in the authentication query
	 *
	 * Defaults to {@link AUTH_DB_DEFAULT_TABLE}.
	 *
	 * @var string
	 */
	var $tableName;

	/**
	 * Database fields that must be used in the authentication query
	 *
	 * Defaults to '*' (all table fields)
	 *
	 * @var string
	 */
	var $dbFields = '';

	/**
	 * Extra condition clause to be used in the authentication query
	 *
	 * @var string
	 */
	var $extraClause = '';

	/**
	 * Crypt function that must be applied in the user password
	 * when building the authentication query
	 *
	 * If ommited, plain text comparison will be used.
	 *
	 * @var string
	 */
	var $cryptFunction = '';

	/**
	 * Class constructor
	 *
	 * @param string $sessionName Session name
	 * @return AuthDb
	 */
	function AuthDb($sessionName=NULL) {
		parent::Auth($sessionName);
		$this->tableName = AUTH_DB_DEFAULT_TABLE;
	}

	/**
	 * Define the database connection ID to be used
	 *
	 * The ID must be one of the connection IDs defined inside
	 * the DATABASE.CONNECTIONS configuration setting.
	 *
	 * This method should only be called when the connection ID
	 * is not the default one (DATABASE.DEFAULT_CONNECTION)
	 *
	 * @param string $id Connection ID
	 */
	function setConnectionId($id) {
		$this->connectionId = $id;
	}

	/**
	 * Set the table name that must be used in the authentication query
	 *
	 * @param string $tableName Table name
	 * @see setDbFields
	 * @see setExtraClause
	 */
	function setTableName($name) {
		$this->tableName = $name;
	}

	/**
	 * Set the field names that must be used in the authentication query
	 *
	 * Array or comma separated list of field names
	 * <code>
	 * $auth->setDbFields(array('name', 'address', 'phone', 'status', 'role'));
	 * $auth->setDbFields('name,address,phone,status,role');
	 * </code>
	 *
	 * @param string $dbFields DB fields
	 * @see setTableName
	 * @see setExtraClause
	 */
	function setDbFields($fields) {
		if (is_array($fields)) {
			$fields = array_unique($fields);
			$this->dbFields = implode(', ', $fields);
		} else {
			$fields = trim($fields);
			if ($fields[0] == ',')
				$fields = substr($fields, 1);
			$this->dbFields = $fields;
		}
	}

	/**
	 * Set a condition clause to be used in the authentication query
	 *
	 * <code>
	 * $auth->setTableName('users');
	 * $auth->setDbFields('cod_user,name,role');
	 * // without bind params
	 * $auth->setExtraClause('active = 1');
	 * // with bind params
	 * $auth->setExtraClause('active = ?', array(1));
	 * </code>
	 *
	 * @param string $extraClause Extra clause
	 * @see setTableName
	 * @see setDbFields
	 */
	function setExtraClause($clause, $params=array()) {
		$this->extraClause = array(
			'clause' => $clause,
			'params' => $params
		);
	}

	/**
	 * Define a function or method that must be used to crypt the
	 * user password before running the authentication query.
	 *
	 * The function can be a standard PHP function like md5 or crypt,
	 * a user-defined function or the name of a method of the authenticator.
	 *
	 * Setting $dbFunction to true, you could use a native or user-defined
	 * database function as your crypt function, as shown in the third example.
	 *
	 * <code>
	 * $auth->setCryptFunction('md5'); // standard PHP function
	 * $auth->setCryptFunction('myCryptFunction'); // user-defined function or method
	 * $auth->setCryptFunction('md5', true); // using database implementation of md5
	 * </code>
	 *
	 * @param string $cryptFunction Function or method name
	 * @param bool $dbFunction Is this a database function?
	 */
	function setCryptFunction($cryptFunction, $dbFunction=false) {
		$cryptFunction = trim($cryptFunction);
		if (!empty($cryptFunction)) {
			if ($dbFunction) {
				$this->cryptFunction = array(
					'type' => 'db',
					'func' => $cryptFunction
				);
			} elseif (function_exists($cryptFunction)) {
				$this->cryptFunction = array(
					'type' => 'php',
					'func' => $cryptFunction
				);
			} elseif (method_exists($this, $cryptFunction)) {
				$this->cryptFunction = array(
					'type' => 'php',
					'func' => array($this, $cryptFunction)
				);
			}
		}
	}

	/**
	 * Performs the authentication attempt against the database
	 *
	 * Builds and runs the authentication query. If a valid result
	 * set is returned from the database, the first row is returned.
	 * Otherwise, returns false.
	 *
	 * @return array|false
	 */
	function authenticate() {
		$Db =& Db::getInstance($this->connectionId);
		$queryParams = array();
		$Query = new QueryBuilder();
		$Query->addTable($this->tableName);
		if ($this->dbFields == '*') {
			$Query->setFields('*');
		} else {
			$Query->setFields($this->loginFieldName);
			if (!empty($this->dbFields))
				$Query->addFields($this->dbFields);
		}
		$Query->setClause("{$this->loginFieldName} = ?");
		$queryParams[] = $this->_login;
		if (is_array($this->cryptFunction)) {
			if ($this->cryptFunction['type'] == 'db') {
				$Query->addClause("{$this->passwordFieldName} = {$this->cryptFunction['func']}(?)");
				$queryParams[] = $this->_password;
			} else {
				$Query->addClause("{$this->passwordFieldName} = ?");
				$queryParams[] = call_user_func($this->cryptFunction['func'], $this->_password);
			}
		} else {
			$Query->addClause("{$this->passwordFieldName} = ?");
			$queryParams[] = $this->_password;
		}
		if (!empty($this->extraClause)) {
			$Query->addClause($this->extraClause['clause']);
			if (!empty($this->extraClause['params'])) {
				for ($i=0; $i<sizeof($this->extraClause['params']); $i++)
					$queryParams[] = $this->extraClause['params'][$i];
			}
		}
		$oldMode = $Db->setFetchMode(ADODB_FETCH_ASSOC);
		$Rs =& $Db->query($Query->getQuery(), TRUE, $queryParams);
		$Db->setFetchMode($oldMode);
		if ($Rs->recordCount() == 0)
			return FALSE;
		return $Rs->fields;
	}
}
?>