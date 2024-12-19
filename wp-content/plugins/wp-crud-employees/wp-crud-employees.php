<?php

/**
 * Plugin Name: WP CRUD Employees
 *  Description: A simple plugin to manage employees. Also on activation, it creates a dynamic wordpress page and it will have a shortcode
 * Author: Nattanicha Niyomchan
 */


if (!defined('ABSPATH')) {
    exit;
}

define("WCE_DIR_PATH", plugin_dir_path(__FILE__));  //C:\xampp\htdocs\wordpress\wp-content\plugins\wp-crud-employees\
define("WCE_DIR_URL", plugin_dir_url(__FILE__));  //http://localhost/wordpress/wp-content/plugins/wp-crud-employees/

include_once(WCE_DIR_PATH . 'MyEmployees.php');  //C:\xampp\htdocs\wordpress\wp-content\plugins\wp-crud-employees\MyEmployees.php

// Create Class Object
$employeesObject = new MyEmployees();

// Hook the function to the wp_enqueue_scripts action
add_action('wp_enqueue_scripts', [$employeesObject, "addAssetToPlugin"]);

// Create DB Table
register_activation_hook(__FILE__, array($employeesObject, 'callPluginActivationFunction'));

// Drop DB Table
register_deactivation_hook(__FILE__, array($employeesObject, 'dropEmployeesTable'));

// Register Shortcode
add_shortcode("wp-employee-form", [$employeesObject, 'createEmployeeForm']);


//Process AJAX Request (user logged in)
add_action("wp_ajax_wce_add_employee", [$employeesObject, "handleAddEmployeeFormData"]);
add_action("wp_ajax_wce_load_employee_data", [$employeesObject, "handleLoadEmployeeData"]);
add_action("wp_ajax_wce_delete_employee", [$employeesObject, "handleDeleteEmployeeData"]);
add_action("wp_ajax_wce_get_employee_data", [$employeesObject, "handleToGetSingleEmployeeData"]);
add_action("wp_ajax_wce_edit_employee", [$employeesObject, "handleUpdateEmployeeData"]);

//Process AJAX Request (user not logged in);
// add_action("wp_ajax_nopriv_wce_add_employee", [$employeesObject, "handleAddEmployeeFormData"]); 
// add_action("wp_ajax_nopriv_wce_load_employee_data", [$employeesObject, "handleLoadEmployeeData"]);
// add_action("wp_ajax_nopriv_wce_delete_employee", [$employeesObject, "handleDeleteEmployeeData"]);
// add_action("wp_ajax_nopriv_wce_get_employee_data", [$employeesObject, "handleToGetSingleEmployeeData"]);