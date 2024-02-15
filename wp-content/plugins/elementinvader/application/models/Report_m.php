<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Report_m extends Winter_MVC_Model {

	public $_table_name = 'wal_report';
	public $_order_by = 'idreport DESC';
    public $_primary_key = 'idreport';
    public $_own_columns = array();
    public $_timestamps = TRUE;
    protected $_primary_filter = 'intval';

    public $form_admin = array();

    public $fields_list = null;
    
	public function __construct(){
        parent::__construct();
 
        $this->form_admin = array();
	}

    /* [START] For dinamic data table */
    
    public function get_available_fields()
    {      
        $fields = $this->db->list_fields($this->_table_name);

        return $fields;
    }
    
    public function total_lang($where = array())
    {
        

        $this->db->select('*');
        $this->db->from($this->_table_name);
        $this->db->where($where);
        $this->db->order_by($this->_order_by);
        
        $query = $this->db->get();

        return $this->db->num_rows();
    }
    
    public function get_pagination_lang($limit, $offset, $where = array())
    {
        $this->db->select('*');
        $this->db->from($this->_table_name);
        $this->db->where($where);
        $this->db->limit($limit);
        $this->db->offset($offset);
        $this->db->order_by($this->_order_by);
        
        $query = $this->db->get();

        if ($this->db->num_rows() > 0)
            return $this->db->results();
        
        return array();
    }
    
    public function check_deletable($id)
    {
        return true;
    }
    
    
    /* [END] For dinamic data table */

    public function report_sendemail($report_id)
    {
        $this->load->model('log_m');
        $this->load->model('report_m');

        $report_data = $this->report_m->get($report_id, TRUE);

        if(empty($report_data->report_email))
            exit('EMAIL NOT DEFINED');

            if(!empty($report_data->by_user))
            $this->db->like('user_info', '%'.$report_data->by_user.'%');

        if(!empty($report_data->by_ip))
            $this->db->like('ip', '%'.$report_data->by_ip.'%');

        if(!empty($report_data->request_uri))
            $this->db->like('request_uri', '%'.$report_data->request_uri.'%');

        if(!empty($report_data->by_description))
            $this->db->like('description', '%'.$report_data->by_description.'%');
            
        if(!empty($report_data->date_start) && $report_data->date_start != "0000-00-00 00:00:00")
            $this->db->where('date >', date("Y-m-d H:i:s", strtotime($report_data->date_start)));

        if(!empty($report_data->date_end) && $report_data->date_end != "0000-00-00 00:00:00")
            $this->db->where('date <', date("Y-m-d H:i:s", strtotime($report_data->date_end)));

        if(!empty($report_data->level))
        {
            $level_exp = explode(',', $report_data->level);

            $q_where = '';
            foreach($level_exp as $val)
            {
                $q_where.= 'level = '.$val.' OR ';
            }

            if(!empty($q_where))
                $q_where = substr($q_where, 0, -3);

            $this->db->where('('.$q_where.')', NULL);
        }
           

        $logs  = $this->log_m->get();

        //exit($this->db->last_query());

        $data = array();
        foreach($logs as $key=>$row)
        {
            $data_row = array();

            $data_row['level'] = $row->level;
            $data_row['date'] = $row->date;
            $data_row['user_id'] = $row->user_id;
            $data_row['user_info'] = $row->user_info;
            $data_row['ip'] = $row->ip;
            $data_row['page'] = $row->page;
            $data_row['action'] = $row->action;
            $data_row['is_favourite'] = $row->is_favourite;
            $data_row['request_uri'] = $row->request_uri;
            $data_row['description'] = $row->description;

            $data_row['request_data'] = unserialize($row->request_data);
            $data_row['header_data'] = unserialize($row->header_data);
            $data_row['other_data'] = unserialize($row->other_data);

            $data_row['level_description'] = wal_generate_label_by_level($row->level);
            $data_row['date_description'] = date(get_option('date_format').' '.get_option('time_format'), strtotime($row->date));
            $data_row['user_info_description'] = get_userdata($row->user_id);

            $data[] = $data_row;
        }

        if(method_exists($this, "print_".$report_data->format))
        {
            $print_file = $this->{"print_".$report_data->format}($data, FALSE);

            if(!is_writable(WP_CONTENT_DIR . '/uploads/'))
            {
                exit('FOLDER not writable: '.WP_CONTENT_DIR . '/uploads/');
            }

            $tmpfile = WP_CONTENT_DIR . '/uploads/'.$report_data->format."_log_".date('Y-m-d-H-i-s').".".$report_data->format;
            $temp = fopen($tmpfile, "w");
            fwrite($temp, $print_file);
            fclose($temp);

            $to = $report_data->report_email;
            $subject = __('Report log', 'elementinvader').' '.date('Y-m-d H:i:s');
            $body = __('Report log attached in file', 'elementinvader').' '.$report_data->format.' '.$report_data->report_name.' '.date('Y-m-d-H-i-s');
            $headers = array('Content-Type: text/html; charset=UTF-8');
             
            $ret = wp_mail( $to, $subject, $body, $headers, array($tmpfile));

            unlink($tmpfile);

            if($ret === TRUE)
            {
                // set date sent

                $this->report_m->update(array('date_sent'=>current_time('mysql')), $report_id);

                return __('Mail sent successfuly to', 'elementinvader').' '.$report_data->report_email;
            }
            else
            {
                return __('Mail sending FAILED', 'elementinvader');
            }
        }
        else
        {
            return __('Mail sending format Method not exists', 'elementinvader');
        }
    }
    
    public function report_download($report_id)
    {
        $this->load->model('log_m');
        $this->load->model('report_m');

        $report_data = $this->report_m->get($report_id, TRUE);

        //dump($report_data);

        if(!empty($report_data->by_user))
            $this->db->like('user_info', '%'.$report_data->by_user.'%');

        if(!empty($report_data->by_ip))
            $this->db->like('ip', '%'.$report_data->by_ip.'%');

        if(!empty($report_data->by_description))
            $this->db->like('description', '%'.$report_data->by_description.'%');

        if(!empty($report_data->request_uri))
            $this->db->like('request_uri', '%'.$report_data->request_uri.'%');
            
        if(!empty($report_data->date_start) && $report_data->date_start != "0000-00-00 00:00:00")
            $this->db->where('date >', date("Y-m-d H:i:s", strtotime($report_data->date_start)));

        if(!empty($report_data->date_end) && $report_data->date_end != "0000-00-00 00:00:00")
            $this->db->where('date <', date("Y-m-d H:i:s", strtotime($report_data->date_end)));

        if(!empty($report_data->level))
        {
            $level_exp = explode(',', $report_data->level);

            $q_where = '';
            foreach($level_exp as $val)
            {
                $q_where.= 'level = '.$val.' OR ';
            }

            if(!empty($q_where))
                $q_where = substr($q_where, 0, -3);

            $this->db->where('('.$q_where.')', NULL);
        }
           

        $logs  = $this->log_m->get();

        //exit($this->db->last_query());

        $data = array();
        foreach($logs as $key=>$row)
        {
            $data_row = array();

            $data_row['level'] = $row->level;
            $data_row['date'] = $row->date;
            $data_row['user_id'] = $row->user_id;
            $data_row['user_info'] = $row->user_info;
            $data_row['ip'] = $row->ip;
            $data_row['page'] = $row->page;
            $data_row['action'] = $row->action;
            $data_row['is_favourite'] = $row->is_favourite;
            $data_row['request_uri'] = $row->request_uri;
            $data_row['description'] = $row->description;

            $data_row['request_data'] = unserialize($row->request_data);
            $data_row['header_data'] = unserialize($row->header_data);
            $data_row['other_data'] = unserialize($row->other_data);

            $data_row['level_description'] = wal_generate_label_by_level($row->level);
            $data_row['date_description'] = date(get_option('date_format').' '.get_option('time_format'), strtotime($row->date));
            $data_row['user_info_description'] = get_userdata($row->user_id);

            $data[] = $data_row;
        }

        if(method_exists($this, "print_".$report_data->format))
            $this->{"print_".$report_data->format}($data);
        else
            exit('Method not exists');
    }

    private function print_csv($logs, $echo=true)
    {
        if($echo === TRUE)
            ob_clean();
        //ob_start();

        $print_data = '';

        $counter=0;

        $skip_cols = array('request_data', 'header_data', 'other_data', 'user_info_description');
        
        foreach($logs as $key_log=>$row_log)
        {
            // print only keys if first row
            if($counter==0)
            {
                //Define CSV format for Excel
                $print_data.="sep=;\r\n";

                foreach($row_log as $key=>$val)
                {
                    if(!is_string($key) || in_array($key, $skip_cols))continue;

                    $print_data.='"'.$key.'";';    
                }
                $print_data.="\r\n";
            }

            foreach($row_log as $key=>$val)
            {
                if(!is_string($key) || in_array($key, $skip_cols))continue;

                if(is_string($val))
                {
                    $val_prepared = htmlspecialchars($val);
                    $val_prepared = '"'.$val_prepared.'"';

                    $print_data.=$val_prepared.';';
                }
                else
                {
                    $print_data.=';';
                }
            }
            $print_data.="\r\n";

            $counter++;
        }

        $print_data.= "\r\n";

        if($echo === FALSE)
            return $print_data;

        header('Content-Type: application/csv');
        header("Content-Length:".strlen($print_data));
        header("Content-Disposition: attachment; filename=csv_log_".date('Y-m-d-H-i-s').".csv");

        echo $print_data;
        
        exit();
    }

    private function print_html($logs, $echo=true)
    {
        if($echo === TRUE)
            ob_clean();
        //ob_start();

        $print_data = '';

        $counter=0;

        $skip_cols = array('request_data', 'header_data', 'other_data', 'user_info_description');
        
        $print_data.= '<html>'."\r\n";
        $print_data.= '<head>'."\r\n";
        $print_data.= '<title>'."\r\n";
        $print_data.= "HTML log ".date('Y-m-d-H-i-s')."\r\n";
        $print_data.= '</title>'."\r\n";
        $print_data.= '</head>'."\r\n";

        $print_data.= '<body>'."\r\n";

        $print_data.= '<table border="1">'."\r\n";
        foreach($logs as $key_log=>$row_log)
        {
            // print only keys if first row
            if($counter==0)
            {
                $print_data.= '<tr>'."\r\n";
                foreach($row_log as $key=>$val)
                {
                    if(!is_string($key) || in_array($key, $skip_cols))continue;

                    $print_data.= '<th>'."\r\n";
                    $print_data.=$key; 
                    $print_data.= '</th>'."\r\n";   
                }
                $print_data.= '</tr>'."\r\n";
            }

            $print_data.= '<tr>'."\r\n";
            foreach($row_log as $key=>$val)
            {
                if(!is_string($key) || in_array($key, $skip_cols))continue;

                if(is_string($val))
                {
                    $print_data.= '<td>'."\r\n";
                    $print_data.=strip_tags($val);
                    $print_data.= '</td>'."\r\n";
                }
                else
                {
                    $print_data.=';';
                }
            }
            $print_data.= '</tr>'."\r\n";

            $counter++;
        }
        $print_data.= '</table>'."\r\n";

        $print_data.= '</body>'."\r\n";
        $print_data.= '</html>'."\r\n";

        if($echo === FALSE)
            return $print_data;

        //header('Content-Type: application/html');
        //header("Content-Length:".strlen($print_data));
        //header("Content-Disposition: attachment; filename=csv_log_".date('Y-m-d-H-i-s').".html");

        echo $print_data;
        
        exit();
    }

    private function print_xml($logs, $echo=true)
    {
        if($echo === TRUE)
            ob_clean();
        //ob_start();

        $print_data = '<root>'."\r\n";

        foreach($logs as $key_log=>$row_log)
        {
            $print_data.='<log>'."\r\n";

            foreach($row_log as $key=>$val)
            {
                if(is_string($val))
                {
                    $print_data.="\t".'<'.$key.'>';
                    $print_data.=htmlspecialchars($val);
                    $print_data.='</'.$key.'>'."\r\n";
                }
                elseif(is_array($val) //&& false
                )
                {
                    $print_data.='<'.$key.'>'."\r\n";
                    foreach($val as $key_1=>$val_1)
                    {
                        if(!is_string($val_1) || is_numeric($key_1))continue;

                        $print_data.="\t"."\t".'<'.$key_1.'>';
                        $print_data.=htmlspecialchars($val_1);
                        $print_data.='</'.$key_1.'>'."\r\n";
                    }
                    $print_data.='</'.$key.'>'."\r\n";
                }

            }

            $print_data.='</log>'."\r\n";
        }
        $print_data.= '</root>'."\r\n";

        if($echo === FALSE)
            return $print_data;

        header('Content-Type: application/xml');
        header("Content-Length:".strlen($print_data));
        header("Content-Disposition: attachment; filename=csv_log_".date('Y-m-d-H-i-s').".xml");

        echo $print_data;
        
        exit();
    }

    private function print_json($logs, $echo=true)
    {
        if($echo === TRUE)
            ob_clean();

        $print_data = json_encode($logs);

        if($echo === FALSE)
            return $print_data;

        header('Content-Type: application/json');
        header("Content-Length:".strlen($print_data));
        header("Content-Disposition: attachment; filename=csv_log_".date('Y-m-d-H-i-s').".json");

        echo $print_data;
        
        exit();
    }

}













?>