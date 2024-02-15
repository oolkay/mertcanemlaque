<?php

/**
 * MVC_Database
 *
 * @version 1.0
 *
 * @author Sandi Winter, simplified CodeIgniter DB query builder library
 * @link https://github.com/sandiwinter/winter_mvc
 */
if ( ! class_exists( 'MVC_Database' ) ):

class MVC_Database {

    /**
     * wpdb
     *
     * @var object
     */
	protected $wpdb = NULL;
	
	protected $query_array = array();

	public $prefix = '';

    public static function instance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new MVC_Database();
        }
        return $inst;
    }

    public function __construct()
    {
        global $wpdb;

        $this->wpdb = &$wpdb;

        $this->prefix = $wpdb->prefix;
    }
    
    public function last_query()
    {
        // Print last SQL query string
        return $this->wpdb->last_query;
    }

    public function last_result()
    {
        // Print last SQL query result
        return $this->wpdb->last_result;
	}
	
    public function results()
    {
        // Print last SQL query result
        return $this->wpdb->last_result;
	}
	
    public function row()
    {
        if(!isset($this->wpdb->last_result[0]))return NULL;

        // Print last SQL query result
        return $this->wpdb->last_result[0];
    }

    public function last_error()
    {
        // Print last SQL query error
        return $this->wpdb->last_error;
	}

	/**
	 * Select
	 *
	 * Generates the SELECT portion of the query
	 *
	 * @param	string
	 * @param	mixed
	 */
	public function select($select = '', $escape = NULL)
	{
		$this->query_array['select'][$select] = $escape;
	}

	/**
	 * GROUP BY
	 *
	 * Sets a flag which tells the query string compiler to add DISTINCT
	 *
	 * @param	string	$val - column name
	 */
	public function group_by($val = TRUE)
	{
		$this->query_array['group_by'][$val] = NULL;
	}

	/**
	 * DISTINCT
	 *
	 * Sets a flag which tells the query string compiler to add DISTINCT
	 *
	 * @param	bool	$val
	 */
	public function distinct($val = TRUE)
	{
		$this->query_array['distinct'][$val] = NULL;
	}

	// --------------------------------------------------------------------

	/**
	 * From
	 *
	 * Generates the FROM portion of the query
	 *
	 * @param	mixed	$from	can be a string or array
	 */
	public function from($from)
	{
		$this->query_array['from'][$from] = $from;
	}

	// --------------------------------------------------------------------

	/**
	 * JOIN
	 *
	 * Generates the JOIN portion of the query
	 *
	 * @param	string
	 * @param	string	the join condition
	 * @param	string	the type of join
	 * @param	string	whether not to try to escape identifiers
	 */
	public function join($sql_part, $escape = NULL, $condition = '')
	{
		$this->query_array['join'][' '.$condition.' JOIN '.$sql_part] = $escape;
	}

	// --------------------------------------------------------------------

	/**
	 * WHERE
	 *
	 * Generates the WHERE portion of the query.
	 * Separates multiple calls with 'AND'.
	 *
	 * @param	mixed
	 * @param	mixed
	 */
	public function where($key, $value = NULL)
	{
		if(is_array($key))
		{
			foreach($key as $key_1=>$val_1)
			{
				$this->query_array['where'][$key_1] = $val_1;
			}

			return;
		}

		if(!is_string($key))return;

		$this->query_array['where'][$key] = $value;
	}

	// --------------------------------------------------------------------

	/**
	 * OR WHERE
	 *
	 * Generates the WHERE portion of the query.
	 * Separates multiple calls with 'OR'.
	 *
	 * @param	mixed
	 * @param	mixed
	 */
	public function or_where($sql_part, $escape = NULL)
	{
		$this->query_array['where']['OR '.$sql_part] = $escape;
	}

	/**
	 * LIKE
	 *
	 * Generates a %LIKE% portion of the query.
	 * Separates multiple calls with 'AND'.
	 *
	 * @param	mixed	$field
	 * @param	string	$match
	 * @param	string	$side
	 * @param	bool	$escape
	 */
	public function like($sql_part, $escape = NULL)
	{
		$this->query_array['like'][$sql_part] = $escape;
	}


	/**
	 * OR LIKE
	 *
	 * Generates a %LIKE% portion of the query.
	 * Separates multiple calls with 'OR'.
	 *
	 * @param	mixed	$field
	 * @param	string	$match
	 * @param	string	$side
	 * @param	bool	$escape
	 */
	public function or_like($sql_part, $escape = NULL)
	{
		$this->query_array['like']['OR '.$sql_part] = $escape;
	}

	/**
	 * LIMIT
	 *
	 * @param	int	$value	LIMIT value
	 * @param	int	$offset	OFFSET value
	 */
	public function limit($limit, $offset = 0)
	{
		$this->query_array['limit'] = $limit;
		$this->query_array['offset'] = $offset;
	}

	// --------------------------------------------------------------------

	/**
	 * Sets the OFFSET value
	 *
	 * @param	int	$offset	OFFSET value
	 */
	public function offset($offset)
	{
		$this->query_array['offset'] = $offset;
	}

	/**
	 * Sets the ORDER_BY value
	 *
	 * @param	string ORDER_BY value
	 */
	public function order_by($sql_part)
	{
		if(empty($this->query_array['order_by']))
		{
			$this->query_array['order_by'] = $sql_part;
		}
		else
		{
			$this->query_array['order_by'] .= ', '.$sql_part;
		}
		
	}


	/**
	 * Get
	 *
	 * Compiles the select statement based on the other functions called
	 * and runs the query
	 *
	 * @param	string	the table
	 * @param	string	the limit clause
	 * @param	string	the offset clause
	 * @return	CI_DB_result
	 */
	public function get($table = '', $limit = NULL, $offset = NULL)
	{
		if ($table !== '')
		{
			$this->from($table);
		}

		if ( ! empty($limit))
		{
			$this->limit($limit, $offset);
		}

		$result = $this->query($this->_compile_select());
		$this->_reset_select();
		return $result;
	}

	public function delete($table = '')
	{
		$sql_query = 'DELETE FROM '.$table.' ';

		if(isset($this->query_array['where']))
		{
			$sql_query .= 'WHERE ';

			foreach($this->query_array['where'] as $col=>$val)
			{
				if(empty($val))
				{
					$sql_query .= ' '.$col.' AND ';
				}
				else
				{
					if(substr($col, -2) == ' <')
					{
						$sql_query .= '`'.substr($col, 0, -2).'` < \''.$val.'\' AND ';
					}
					elseif(substr($col, -2) == ' >')
					{
						$sql_query .= '`'.substr($col, 0, -2).'` > \''.$val.'\' AND ';
                    }
                    elseif(substr($col, -3) == ' !=')
					{
						$sql_query .= '`'.substr($col, 0, -3).'` != \''.$val.'\' AND ';
					}
					else
					{
						$sql_query .= '`'.$col.'` = \''.$val.'\' AND ';
					}
				}
			}

			$sql_query = substr($sql_query, 0, -4);
		}

		$result = $this->query($sql_query);
		$this->_reset_select();
		return $result;
	}

	private function _compile_select()
	{
		$sql_query = '';

		if(isset($this->query_array['select']))
		{
			$values_array = array_keys($this->query_array['select']);
			$sql_query .= 'SELECT '.implode(",", $values_array).' ';
		}
		else
		{
			$sql_query .= 'SELECT * ';
		}

		if(isset($this->query_array['from']))
		{
			$values_array = array_keys($this->query_array['from']);
			$sql_query .= 'FROM '.implode(",", $values_array).' ';
		}
		else
		{
			echo 'FROM part missing';
			return;
		}

		if(isset($this->query_array['join']))
		{
			$values_array = array_keys($this->query_array['join']);
			$sql_query .= implode(" ", $values_array).' ';
		}

		if(isset($this->query_array['where']))
		{
			$sql_query .= 'WHERE ';

			foreach($this->query_array['where'] as $col=>$val)
			{
				if(empty($val))
				{
                    if(substr($col,0,3) == 'OR ') // remove AND from before if exists
                    {
                        if(substr($sql_query, -5) == ' AND ')
                        {
                            $sql_query = substr($sql_query, 0, -5);
                        }
                    }

                    $sql_query .= ' '.$col.' AND ';
				}
				else
				{
					if(substr($col, -2) == ' <')
					{
						$sql_query .= '`'.substr($col, 0, -2).'` < \''.$val.'\' AND ';
					}
					elseif(substr($col, -2) == ' >')
					{
						$sql_query .= '`'.substr($col, 0, -2).'` > \''.$val.'\' AND ';
                    }
                    elseif(substr($col, -3) == ' !=')
					{
						$sql_query .= '`'.substr($col, 0, -3).'` != \''.$val.'\' AND ';
					}
					else
					{
						$sql_query .= '`'.$col.'` = \''.$val.'\' AND ';
					}
				}
			}

			$sql_query = substr($sql_query, 0, -4);
		}

		if(isset($this->query_array['like']))
		{
			if(strpos($sql_query, 'WHERE') === FALSE)
			{
				$sql_query .= 'WHERE ';
			}
			else
			{
				$sql_query .= ' AND ';
			}

			foreach($this->query_array['like'] as $col=>$val)
			{
				if(empty($val))
				{
					$sql_query .= ' '.$col.' AND ';
				}
				else
				{
					$sql_query .= '`'.$col.'` LIKE \''.$val.'\' AND ';
				}
			}

			$sql_query = substr($sql_query, 0, -4);
		}

		if(isset($this->query_array['group_by']))
		{
			$sql_query .= 'GROUP BY '.implode(',', array_keys($this->query_array['group_by'])).' ';
		}

		if(isset($this->query_array['order_by']))
		{
			$sql_query .= 'ORDER BY '.$this->query_array['order_by'].' ';
		}

		if(isset($this->query_array['limit']))
		{
			$sql_query .= 'LIMIT '.$this->query_array['limit'].' ';
		}

		if(isset($this->query_array['offset']) && isset($this->query_array['limit']))
		{
			$sql_query .= 'OFFSET '.$this->query_array['offset'].' ';
		}

		if(isset($this->query_array['distinct'])) { 
			$pos = strpos($sql_query, 'SELECT');
			if ($pos !== false) {
				$sql_query = substr_replace($sql_query, 'SELECT DISTINCT '.implode(',', array_keys($this->query_array['distinct'])).',', $pos, strlen('SELECT'));
			}
		}
		//wmvc_dump($sql_query);

		return $sql_query;
	}

	private function _reset_select()
	{
		$this->query_array = array();
	}

	/**
	 * Determines if a query is a "write" type.
	 *
	 * @param	string	An SQL query string
	 * @return	bool
	 */
	public function is_write_type($sql)
	{
		return (bool) preg_match('/^\s*"?(SET|INSERT|UPDATE|DELETE|REPLACE|CREATE|DROP|TRUNCATE|LOAD|COPY|ALTER|RENAME|GRANT|REVOKE|LOCK|UNLOCK|REINDEX|MERGE)\s/i', $sql);
	}

	/**
	 * Query
	 *
	 * @param	string	the sql query
	 * @return	mixed
	 */
	public function query($sql)
	{
		return $this->_execute($sql);
	}

    
	/**
	 * Execute the query
	 *
	 * @param	string	$sql	an SQL query
	 * @return	mixed
	 */
	protected function _execute($sql)
	{
		global $wpdb;
		return $wpdb->query($sql);
    }
    
	/**
	 * Number of rows in the result set
	 *
	 * @return	int
	 */
	public function num_rows()
	{
        global $wpdb;
        return $wpdb->num_rows;
	}

	public function list_fields($table_name)
	{
		$sql = 'SHOW COLUMNS FROM '.$table_name.';';

		$this->_execute($sql);

		$results = $this->results();

		$fields = array();

		foreach($results as $row)
		{
			$fields[$row->Field] = $row;
		}
		
		return $fields;
	}

	//UPDATE MyGuests SET lastname='Doe' WHERE id=2
	public function update($table_name, $data = array(), $id = NULL, $primary_field = 'id')
	{
		if(count($data) == 0)return;
		if(empty($id))return;
		if(empty($table_name))return;
		if(empty($primary_field))return;

		$query = '';
		$query.= 'UPDATE '.$table_name.' SET ';

		foreach($data as $key=>$val)
		{
            if($val === NULL)
            {
                $query.= ' `'.$key.'` = NULL ,';
            }
            else
            {
                $query.= ' `'.$key.'` = \''.$val.'\' ,';
            }
		}

		$query = substr($query, 0, -1);

        if(is_array($id))
        {
            $where_part = array();
            foreach($id as $key=>$val)
            {
                $where_part[] = ' '.$key.' = \''.$val.'\' ';
            }

            $query.= 'WHERE '.join(' AND ', $where_part).';';
        }
        else
        {
            $query.= 'WHERE '.$primary_field.'='.$id.';';
        }
		

		//echo $query;exit();

		$this->query($query);
        $this->_reset_select();

		return $id;
	}

	public function insert($table_name, $data)
	{
		global $wpdb;

		if(empty($table_name))
		{
			echo 'Missing table name for insert';
			return;
		}

		if(!is_array($data) || count($data) == 0)
		{
			echo 'Missing data for insert';
			return;
		}

		$wpdb->insert( 
			$table_name, 
			$data
		);

		return $wpdb->insert_id;
	}

    /*
    
    UPDATE mytable SET title = CASE
    WHEN id = 1 THEN ‘Great Expectations’
    WHEN id = 2 THEN ‘War and Peace’
    ...
    END
    WHERE id IN (1,2,...)

    */

    public function updateBatch( $table_name, $values, $index)
	{
		$ids   = [];
		$final = [];

		foreach ($values as $val)
		{
			$ids[] = $val[$index];

			foreach (array_keys($val) as $field)
			{
				if ($field !== $index)
				{
					$final[$field][] = 'WHEN ' . $index . ' = ' . $val[$index] . ' THEN ' . $val[$field];
				}
			}
		}

		$cases = '';
		foreach ($final as $k => $v)
		{
			$cases .= $k . " = CASE \n"
					. implode("\n", $v) . "\n"
					. 'ELSE ' . $k . ' END, ';
		}

		$this->where($index . ' IN(' . implode(',', $ids) . ')', null, false);

		$query = 'UPDATE ' . '' . $table_name . ' SET ' . substr($cases, 0, -2) . '';

        $this->query($query);
        $this->_reset_select();
	}



}

endif;

?>
