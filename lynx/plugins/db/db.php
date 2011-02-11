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

	function insert($insert)
	{
		$return = array();
		foreach ($insert as $table => $values)
		{
			$columns= implode(', ', array_keys($values));

			for ($i = 1, $q_values = '?', $count = count($values); $i < $count; $i++)
			{
				$q_values .= ', ?';
			}

			$sql = "INSERT INTO " . $table . " ($columns) VALUES ($q_values);";

			$statement = $this->conn->prepare($sql);
			$statement->execute(array_values($values));
			$return[] = $statement;
		}
		return $return;
	}

	function select_row($select)
	{
		$select['LIMIT'] = '0, 1';

		$statement = $this->select($select);
		while ($result = $statement->fetch())
		{
			return $result;
		}
		return false;
	}

	function update($update)
	{
		$sql = 'UPDATE ' . $update['TABLE'] . ' SET ';

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
			$update_ary = array_values($update['VALUES'];
		}
		else
		{
			$sql .= $update['VALUES'] . ' ';
		}

		$sql .= ' WHERE ';

		if (is_array($update['WHERE']))
		{
			$i = 0;
			foreach(array_keys($update['WHERE']) as)
			{
				$sql .= $value . ' = ?';
				if ($i < count($update['WHERE'])-1)
				{
					$sql .= ', ';
				}
				$i++;
			}
			$update_ary[] = array_values($update['VALUES']);
		}
		else
		{
			$sql .= $update['WHERE'] . ' ';
		}

		$statement = $this->db->prepare($sql);
		$statement->execute($update_ary);
		return $statement;
	}
}
