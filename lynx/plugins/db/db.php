<?php

class Db extends Plugin
{
	private $conn;

	function lynx_construct()
	{
		$dsn = 'mysql:host=' . $this->config['host'] . ';port=' . $this->config['port'] . ';dbname=' . $this->config['db'];
		$this->conn = new PDO($dsn, $this->config['user'], $this->config['pass']);
	}

	function sql($sql)
	{
		return $this->conn->query($sql);
	}

	function select($select)
	{
		if (!isset($select['FROM']))
		{
			trigger_error("FROM not set");
			return false;
		}

		$sql = 'SELECT ' . (isset($select['SELECT']) ? $select['SELECT'] : '*') . ' FROM ';

		if (is_array($select['FROM']))
		{
			foreach ($select['FROM'] as $where => $as)
			{
				$sql .= "$where AS $as, ";
			}
		}
		else
		{
			$sql .= $select['FROM'] . " ";
		}

		if (is_array($select['WHERE']))
		{
			$sql .= "WHERE ";
			foreach ($select['WHERE'] as $where => $squals)
			{
				$sql .= "$where = ? AND ";
				$where_ary[] = $equals;
			}
		}
		else if (isset($select['WHERE']))
		{
			$sql .= "WHERE " . $select['WHERE'] . " ";
		}

		$sql .= (isset($select['ORDER']) ? "ORDER BY " . $select['ORDER'] . " " : "") . (isset($select['LIMIT']) ? "LIMIT " . $select['LIMIT'] . " " : "");

		$statement = $this->conn->prepare($sql);
		$statement->execute($where_ary);
		return $statement;
	}
}
