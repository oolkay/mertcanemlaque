<?php


if ( ! class_exists( 'Winter_MVC_Model' ) ):
    #[AllowDynamicProperties]
    class Winter_MVC_Model {
    
        public $_table_name = 'my_table_name';
        public $_order_by = 'ID DESC';
        public $_primary_key = 'ID';
        public $_own_columns = array();
        public $_timestamps = TRUE;
        protected $_primary_filter = 'intval';

        protected $load = NULL;

        /**
         * db object
         *
         * @var object
         */
        protected $db = NULL;
        
        public function __construct(){
            global $Winter_MVC;

            $this->load = &$Winter_MVC;

            $this->db = MVC_Database::instance();

            $this->_table_name = $this->db->prefix.$this->_table_name;

        }

        public function set_loader($load)
        {
            if(!empty($load))
                $this->load = &$load;
        }

        public function get($id = NULL, $single = FALSE)
        {
            if($id === NULL)
            {
                //return NULL;
            }
            elseif($id != NULL && is_numeric($id))
            {
                $filter = $this->_primary_filter;
                $id = intval($id);
                $this->db->where($this->_primary_key, $id);
                $method = 'row';
            }
            
            if($single == TRUE)
            {
                $method = 'row';
            }
            else
            {
                $method = 'results';
            }
    
            $this->db->order_by($this->_order_by);
            
            $this->db->get($this->_table_name);
            
            $result = $this->db->$method();
    
            return $result;
        }

        public function get_by($where, $single = FALSE)
        {
            $this->db->where($where);       
            return $this->get(NULL, $single);
        }

        public function delete($id)
        {
            $filter = $this->_primary_filter;
            $id = $filter($id); 
            
            if(!$id)
            {
                return FALSE;
            }
            
            $this->db->where($this->_primary_key, $id);
            $this->db->limit(1);
            $this->db->delete($this->_table_name);
        }

        public function update($data = array(), $id=NULL)
        {
            return $this->db->update($this->_table_name, $data, $id, $this->_primary_key);
        }

        public function insert($data, $id = NULL)
        {
            if(!is_array($data))
            {
                echo 'Missing data for insert in model';
                return NULL;
            }

            if($this->_timestamps === TRUE && !isset($data['date']))
                $data['date'] = current_time( 'mysql' );

            if(!empty($id))
            {
                return $this->db->update($this->_table_name, $data, $id, $this->_primary_key);
            }

            return $this->db->insert($this->_table_name, $data);
        }

        public function prepare_data($data, $insert_fields, $xss_clean = TRUE)
        {
            if(!is_array($data))return array();
            if(!is_array($insert_fields))return array();

            $prepared_data = array();

            if($xss_clean)
                $data = wmvc_xss_clean_array($data);

            foreach($insert_fields as $key=>$val)
            {
                if(is_array($val))$val = $val['field'];

                if(isset($data[$val]))
                {
                    if($data[$val] === '')
                    {
                        $prepared_data[$val] = NULL;
                    }
                    else
                    {
                        $prepared_data[$val] = $data[$val];
                    }
                }
                else
                {
                    $prepared_data[$val] = NULL;
                }
            }

            //wmvc_dump($prepared_data);

            return $prepared_data;
        }

        public function max_order($parent_id = null)
        {
            
            if(empty($parent_id))
            {
                // get max order
                $this->db->select('MAX(`order_index`) as `order`', FALSE);
            }
            else
            {
                // get max order
                $this->db->select('MAX(`order_index`) as `order`', FALSE);
                $this->db->where('parent_id', $parent_id);

                if(!empty($parent_id))
                    $this->db->or_where($this->_primary_key.' = '.$parent_id, NULL);
            }
    
            $query = $this->db->get($this->_table_name);

            if ($this->db->num_rows() > 0)
            {
                $row = $this->db->row();
            }
            else
            {
                //echo 'SQL problem in get max_order:';
                //echo $this->db->last_query();
                //exit();

                return 0;
            }

            return (int) $row->order;
        }
    
    }
    
endif;







?>