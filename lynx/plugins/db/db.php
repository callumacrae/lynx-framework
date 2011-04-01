<?php

/**
 * @package lynx-framework
 * @version $Id$
 * @copyright (c) lynxphp
 * @license http://creativecommons.org/licenses/by-sa/3.0/ CC by-sa
 */

namespace lynx\Plugins;

/**
 * @ignore
 */
if (!defined('IN_LYNX'))
{
        exit;
}

class Db extends \lynx\Core\Plugin
{
	private $conn;

	/**
	 * Sets the DSN and creates the connection
	 */
	public function lynx_construct()
	{
		$dsn = 'mysql:host=' . $this->config['host'] . ';port=' . $this->config['port'] . ';dbname=' . $this->config['db'];
		$this->conn = new \PDO($dsn, $this->config['user'], $this->config['pass']);
	}

	/**
	 * Allows you to query the database in SQL
	 *
	 * It is not recommended that you use this function, use
	 * one of the other methods instead.
	 */
	public function sql($sql)
	{
		return $this->conn->query($sql);
	}

	/**
	 * Selects some data from the database.
	 *
	 * The $select array must be as follows:
	 *
	 * $select = array(
	 * 	'FROM'		=> 'The table(s) to select the data from. May be an
	 * 					array, where the key is the table name and
	 * 			    		the value is the AS value.',
	 * 	'SELECT'		=> 'The columns to select. If NULL then defaults to *',
	 * 	'WHERE'		=> 'It is recommended that you use an array:
	 * 					WHERE key = value AND key = value',
	 * 	'ORDER'		=> 'The order to select from the database',
	 * 	'LIMIT'		=> 'The amount to limit it to (syntax: 20, 40)',
	 * );
	 *
	 * @param array $select The array telling the method what to select.
	 */
	public function select($select)
	{
		if (!isset($select['FROM']))
		{
			//you nub
			trigger_error('FROM not set');
			return false;
		}

		//if SELECT is unset, default to *
		$sql = 'SELECT ' . (isset($select['SELECT']) ? $select['SELECT'] : '*') . ' FROM ';

		//can be either an array or a string, depending on what you need
		if (is_array($select['FROM']))
		{
			foreach ($select['FROM'] as $where => $as)
			{
				$sql .= "$where AS $as, ";
			}
		}
		else
		{
			$sql .= $select['FROM'] . ' ';
		}

		$where_ary = null;
		if (isset($select['WHERE']) && is_array($select['WHERE']))
		{
			$sql .= 'WHERE ';
			foreach (array_keys($select['WHERE']) as $where)
			{
				//prepared statement fun!
				$where_ary[] = $where . ' = ?';
			}
			$sql .= implode(' AND ', $where_ary);
		}
		else if (isset($select['WHERE']))
		{
			//it CAN be a string, but isn't recommended
			$sql .= 'WHERE ' . $select['WHERE'] . ' ';
		}

		//too many ternaries! Appends the ORDER BY and LIMIT stuff to the SQL
		$sql .= (isset($select['ORDER']) ? 'ORDER BY ' . $select['ORDER'] . ' ' : null) . (isset($select['LIMIT']) ? 'LIMIT ' . $select['LIMIT'] . ' ' : null);

		$statement = $this->conn->prepare($sql);
		$statement->execute(is_array($select['WHERE']) ? array_values($select['WHERE']) : null);
		return $statement;
	}

	/**
	 * Inserts database into a table (or multiple tables)
	 *
	 * The $insert array uses the follow syntax:
	 * $insert = array(
	 * 	'table name'	=> array(
	 * 		'column name'	=> 'desired value',
	 * 	),
	 * );
	 *
	 * @param array $insert Tells the method what to insert and where
	 */
	public function insert($insert)
	{
		$return = array();
		foreach ($insert as $table => $values)
		{
			$columns= implode(', ', array_keys($values));

			/**
			 * The reason that we start at 1 and already have a question
			 * mark in the string already is so that we are not required to
			 * cut stuff off string afterwards - it would end up as ', ?, ?, ?',
			 * which is obviously invalid.
			 */
			for ($i = 1, $q_values = '?', $count = count($values); $i < $count; $i++)
			{
				$q_values .= ', ?';
			}

			$sql = 'INSERT INTO ' . $table . " ($columns) VALUES ($q_values);";

			$statement = $this->conn->prepare($sql);
			$statement->execute(array_values($values));
			$return[] = $statement;
		}
		return $return;
	}

	/**
	 * Selects in individual row from the database
	 *
	 * It simple calls the select method and results the first row. The syntax
	 * of the $select array should be the same as the syntax for the select
	 * method itself.
	 *
	 * @param array $select The array to be passed to the select method
	 */
	public function select_row($select)
	{
		$select['LIMIT'] = '0, 1';

		$statement = $this->select($select);
		while ($result = $statement->fetch())
		{
			return $result;
		}
		return false;
	}

	/**
	 * Updates one or more rows in the database according to the array in
	 * the parameter. The syntax of the array should be as follows:
	 *
	 * $update = array(
	 * 	'TABLE'		=> 'The table to update',
	 * 	'VALUES'		=> 'The columns (keys) to set as values (values)',
	 * 	'WHERE'		=> 'The where array, uses same syntax as select method',
	 * );
	 *
	 * @param array $update Tells the method what to update
	 */
	public function update($update)
	{
		$sql = 'UPDATE ' . $update['TABLE'] . ' SET ';

		/**
		 * This can be a string, but is recommended that you use an array
		 * wherever possible.
		 */
		if (is_array($update['VALUES']))
		{
			$i = 0;
			foreach(array_keys($update['VALUES']) as $value)
			{
				$sql .= $value . ' = ?';
				if ($i < count($update['VALUES'])-1)
				{
					$sql .= ', ';
				}
				$i++;
			}
			$update_ary = array_values($update['VALUES']);
		}
		else
		{
			$sql .= $update['VALUES'] . ' ';
		}

		$sql .= ' WHERE ';

		/**
		 * Again, this can be a string, but it is recommended that you use
		 * an array wherever possible.
		 */
		if (is_array($update['WHERE']))
		{
			$i = 0;
			foreach(array_keys($update['WHERE']) as $value)
			{
				$sql .= $value . ' = ?';
				if ($i < count($update['WHERE'])-1)
				{
					$sql .= ', ';
				}
				$i++;
			}
			$update_ary = array_merge($update_ary, array_values($update['WHERE']));
		}
		else
		{
			$sql .= $update['WHERE'] . ' ';
		}

		$statement = $this->conn->prepare($sql);
		$statement->execute($update_ary);
		return $statement;
	}

	/**
	 * Deletes one or more rows.
	 *
	 * The $select array should use the following syntax:
	 *
	 * $select = array(
	 * 	'FROM'	=> 'The table to delete from',
	 * 	'WHERE'	=> 'Uses the same syntax as everywhere else',
	 * );
	 *
	 * @param array $delete Tells the method what to delete and where from
	 */
	public function delete($delete)
	{
		$sql = 'DELETE FROM ' . $delete['FROM'] . ' WHERE ';

		if (is_array($delete['WHERE']))
		{
			$i = 0;
			foreach(array_keys($delete['WHERE']) as $value)
			{
				$sql .= $value . ' = ?';
				if ($i < count($delete['WHERE']) - 1)
				{
					$sql .= ', ';
				}
				$i++;
			}
			$values_ary = array_values($delete['WHERE']);

			$statement = $this->conn->prepare($sql);
			$statement->execute($values_ary);
			return ($statement->rowCount()) ? true : false;
		}
		$sql .= $delete['WHERE'];
		return $this->sql($sql);
	}

	/**
	 * Deletes everything from the specifies table.
	 * BE CAREFUL!
	 *
	 * You can disable this function by uncommenting the return.
	 *
	 * @param string $table The table to delete from
	 */
	public function clean($table)
	{
		//return false;
		$this->sql('DELETE FROM ' . $table);
	}

	/**
	 * Drops the specified table
	 * BE CAREFUL!
	 *
	 * You can disable this function by uncommenting the return.
	 *
	 * @param string $table The table to drop
	 */
	public function drop($table)
	{
		//return false;
		$this->sql('DROP TABLE ' . $table);
	}
}
