<?php

/**
 * Faulh_DB_Helper.
 *
 * @link       https://github.com/faiyazalam
 * @package    User_Login_History
 * @subpackage User_Login_History/includes
 * @author     Er Faiyaz Alam
 * @access private
 */
if (!class_exists('Faulh_Tool_Helper')) {

    class Faulh_Tool_Helper {

	static public function getRoleNamesByKeys($keys = []) {
	if(empty($keys)){
	    return FALSE;
	}
	
	if(!function_exists('get_editable_roles')){
	      require_once( ABSPATH . 'wp-admin/includes/user.php' );
	}
	
	if(is_string($keys)){
	    $keys = explode(",", $keys);
	}
	
	$names = [];
	$editable_roles = array_reverse( get_editable_roles() );
	
	foreach ($keys as $key) {
	    $editable_role = $editable_roles[trim($key)];
	  $names[] = translate_user_role($editable_role['name']);
	}
	
	return implode(",", $names);
	
    }

    }

}

