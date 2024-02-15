<?php

/**
 * MVC_Form
 *
 * @version 1.0
 *
 * @author Sandi Winter
 * @link https://github.com/sandiwinter/winter_mvc
 */
if ( ! class_exists( 'MVC_Form' ) ):

class MVC_Form {

    /**
     * messages array
     *
     * @var array
     */
    protected $messages = array();

    protected $error_messages = array();

    /**
     * rules array
     *
     * @var array
     */
    protected $rules = array();

    public function __construct()
    {
    }
    
    public function add_error_message($validation, $message)
    {
        $this->error_messages[$validation] = $message;
    }

    public function add_manual_error_message($message)
    {
        $this->messages[] = $message;
    }

    public function run($rules)
    {
        if(!isset($_POST))return FALSE;
        if(count($_POST)==0)return FALSE;

        $this->rules = $rules;

        foreach($rules as $key=>$rule)
        {
            $field_rules = explode('|', $rule['rules']);

            if(isset($_POST[$rule['field']]) && !empty($_POST[$rule['field']]))
            {
                foreach($field_rules as $one_rule)
                {
                    if(empty($one_rule)) continue;

                    $wmvc_rule = '';
                    $wmvc_rule_parameter = '';

                    sscanf($one_rule, '%[^[][', $wmvc_rule);
                    if((bool)preg_match_all('/\[(.*?)\]/', $one_rule, $matches) === TRUE) {
                        $wmvc_rule_parameter=trim($matches[1][0]);
                    }

                    if(function_exists('is_'.$one_rule))
                    {
                        if(call_user_func('is_'.$one_rule, $_POST[$rule['field']]) === FALSE)
                        {
                            if(isset($this->error_messages[$one_rule]))
                            {
                                $this->messages[] = $this->error_messages[$one_rule];
                            }
                            else
                            {
                                $this->messages[] = __('Field', 'wmvc_win').' '.$rule['label'].' '.__('must be', 'wmvc_win').' '.__($one_rule, 'wmvc_win');
                            }
                        }
                    }
                    elseif(function_exists('wmvc_validation_'.$wmvc_rule))
                    {
                        
                      
                       
                        if(call_user_func('wmvc_validation_'.$wmvc_rule, $this, $rule['label'], $_POST[$rule['field']], $wmvc_rule_parameter) === FALSE)
                        {
                            if(isset($this->error_messages[$wmvc_rule]))
                            {
                                $this->messages[] = $this->error_messages[$wmvc_rule];
                            }
                            else
                            {    
                                $this->messages[] = __('Field', 'wmvc_win').' '.$rule['label'].' '.__('must be', 'wmvc_win').' '.__($wmvc_rule, 'wmvc_win');
                            }
                        }
                    }
                    else
                    {
                        $this->messages[] = __('Missing function for rule:', 'wmvc_win').' is_'.$one_rule;
                    }
                }
            }
            elseif(in_array('required', $field_rules))
            {
                $this->messages[] = __('Field is required:', 'wmvc_win').' '.$rule['label'];
            }
        }

        if(count($this->messages) == 0)return TRUE;

        return FALSE;
    }

    public function run_json($rules)
    {
        $postBody = file_get_contents('php://input');
        $data_json = json_decode($postBody);
        $__POST = (array) $data_json;

        if(!isset($__POST))return FALSE;
        if(count($__POST)==0)return FALSE;

        $this->rules = $rules;

        foreach($rules as $key=>$rule)
        {
            $field_rules = explode('|', $rule['rules']);

            if(isset($__POST[$rule['field']]) && !empty($__POST[$rule['field']]))
            {
                foreach($field_rules as $one_rule)
                {
                    if(!empty($one_rule))
                    if(function_exists('is_'.$one_rule))
                    {
                        if(call_user_func('is_'.$one_rule, $__POST[$rule['field']]) === FALSE)
                        {
                            if(isset($this->error_messages[$one_rule]))
                            {
                                $this->messages[] = $this->error_messages[$one_rule];
                            }
                            else
                            {
                                $this->messages[] = __('Field', 'wmvc_win').' '.$rule['label'].' '.__('must be', 'wmvc_win').' '.__($one_rule, 'wmvc_win');
                            }
                        }
                    }
                    else
                    {
                        $this->messages[] = __('Missing function for rule:', 'wmvc_win').' is_'.$one_rule;
                    }
                }
            }
            elseif(in_array('required', $field_rules))
            {
                $this->messages[] = __('Field is required:', 'wmvc_win').' '.$rule['label'];
            }
        }

        if(count($this->messages) == 0)return TRUE;

        return FALSE;
    }

    public function messages($extra = 'class="alert alert-danger"', $success_message = NULL, $success_extra = 'class="alert alert-success"')
    {
        if(!isset($_GET['is_updated'])){
            if(!isset($_POST))return FALSE;
            if(count($_POST)==0)return FALSE;
        }

        if($success_message === NULL)
            $success_message = __('Successfully saved', 'wmvc_win');

        if(count($this->messages) == 0)
        {
            echo '<p '.$success_extra.'>'.$success_message.'</p>';
        }

        foreach($this->messages as $key=>$message)
        {
            echo '<p '.$extra.'>'.$message.'</p>';
        }
    }

    public function messages_api($success_message = NULL)
    {
        $postBody = file_get_contents('php://input');
        $data_json = json_decode($postBody);
        $__POST = (array) $data_json;

        if(!isset($_GET['is_updated'])){
            if(!isset($__POST))return FALSE;
            if(count($__POST)==0)return FALSE;
        }

        if($success_message === NULL)
            $success_message = __('Successfully saved', 'wmvc_win');


        if(count($this->messages) == 0)
        {
            return $success_message;
        }

        return join("\n", $this->messages);
    }

}

// depracticated
if(!function_exists('is_valid_email'))
{
    function is_valid_email($email)
    {
        $regex = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,10})$/";
        $email = strtolower($email);

        if (!preg_match($regex, $email)) {
            return FALSE;
        }
    
        return TRUE;
    }
}

// depracticated
if(!function_exists('is_required'))
{
    function is_required($string)
    {
        if(empty($string))
            return FALSE;
    
        return TRUE;
    }
}

// depracticated
if(!function_exists('is_intval'))
{
    function is_intval($string)
    {
        if(!is_numeric($string))
            return FALSE;
    
        return TRUE;
    }
}

// depracticated
if(!function_exists('is_sockets_enabled'))
{
    function is_sockets_enabled($string)
    {
        return wmvc_is_sockets_enabled();
    }
}

if(!function_exists('wmvc_is_valid_email'))
{
    function wmvc_is_valid_email($email)
    {
        $regex = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,10})$/";
        $email = strtolower($email);

        if (!preg_match($regex, $email)) {
            return FALSE;
        }
    
        return TRUE;
    }
}

if(!function_exists('wmvc_is_required'))
{
    function wmvc_is_required($string)
    {
        if(empty($string))
            return FALSE;
    
        return TRUE;
    }
}

if(!function_exists('wmvc_is_intval'))
{
    function wmvc_is_intval($string)
    {
        if(!is_numeric($string))
            return FALSE;
    
        return TRUE;
    }
}

// depracticated
if(!function_exists('is_valid_date'))
{
    function is_valid_date($string)
    {
        return wmvc_is_valid_date($string);
    }
}

if(!function_exists('wmvc_is_valid_date'))
{
    function wmvc_is_valid_date($string)
    {
        return strtotime($string) !== FALSE;
    }
}

if(!function_exists('wmvc_is_sockets_enabled'))
{
    function wmvc_is_sockets_enabled()
    {
        return function_exists('socket_create');
    }
}

/* validation rules */

/*
Rules List:

is_numerical - is number field
is_phone - is phone field
is_email - is email field
min_length[n] - min length (characters), where n is number
max_length[n] - max length (characters), where n is number
min_number[n] - min length (number), where n is number
max_number[n] - max length (number), where n is number

*/

if(!function_exists('wmvc_validation_is_numerical'))
{
    function wmvc_validation_is_numerical($form = NULL, $label = NULL, $param = NULL)
    {
        $form->add_error_message('is_numerical', sprintf(__('Field %1$s: Numerical format required', 'wmvc_win'), $label));

        return wmvc_is_intval($param);
    }
}

if(!function_exists('wmvc_validation_is_phone'))
{
    function wmvc_validation_is_phone($form = NULL, $label = NULL, $param = NULL)
    {
        $form->add_error_message('is_phone', sprintf(__('Field %1$s: Wrong phone number format', 'wmvc_win'), $label));
        
        return wmvc_is_phone($param);
    }
}

if(!function_exists('wmvc_validation_is_email'))
{
    function wmvc_validation_is_email($form = NULL, $label = NULL, $param = NULL)
    {
        $form->add_error_message('is_email', sprintf(__('Field %1$s: Wrong email format', 'wmvc_win'), $label));
        
        return wmvc_is_valid_email($param);
    }
}

if(!function_exists('wmvc_validation_min_length'))
{
    function wmvc_validation_min_length($form = NULL, $label = NULL, $param = NULL, $arg='')
    {

        $form->add_error_message('min_length', sprintf(__('Field %1$s: Minimal length: %2$s', 'wmvc_win'), $label, $arg));
       
        if ( ! wmvc_is_intval($arg))
		{
			return FALSE;
		}
        
		return ($arg <= strlen($param));
        
    }
}

if(!function_exists('wmvc_validation_max_length'))
{
    function wmvc_validation_max_length($form = NULL, $label = NULL, $param = NULL, $arg='')
    {

        $form->add_error_message('max_length', sprintf(__('Field %1$s: Maximal length:%2$s', 'wmvc_win'), $label, $arg));
       
        if ( ! wmvc_is_intval($arg))
		{
			return FALSE;
		}
        
		return ($arg >= strlen($param));
        
    }
}

if(!function_exists('wmvc_validation_min_number'))
{
    function wmvc_validation_min_number($form = NULL, $label = NULL, $param = NULL, $arg='')
    {

        $form->add_error_message('min_number', sprintf(__('Field %1$s: Minimal number:%2$s', 'wmvc_win'), $label, $arg));
       
        if ( ! wmvc_is_intval($arg))
		{
			return FALSE;
		}
        
        return intval($param) ? ($param >= $arg) : FALSE;
    }
}

if(!function_exists('wmvc_validation_max_number'))
{
    function wmvc_validation_max_number($form = NULL, $label = NULL, $param = NULL, $arg='')
    {

        $form->add_error_message('max_number', sprintf(__('Field %1$s: Maximal number:%2$s', 'wmvc_win'), $label, $arg));
       
        if ( ! wmvc_is_intval($arg))
		{
			return FALSE;
		}
        
        return intval($param) ? ($param <= $arg) : FALSE;
    }
}

endif;

?>