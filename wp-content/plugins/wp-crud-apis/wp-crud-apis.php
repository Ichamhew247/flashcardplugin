<?php

/**
 * Plugin Name: WP CRUD APIs
 * Description: This plugin is used to create, read, update and delete data from the database using REST
 * Version: 1.0
 * Author: Nattanicha Niyomchan
 * Plugin URI: http://example.com/wp-crud-apis
 */


if (!defined('WPINC')) {
    exit;
}


register_activation_hook(__FILE__, 'wcp_create_student_table');

function wcp_create_student_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'students_table';
    $collate = $wpdb->get_charset_collate();


    $student_table = "CREATE TABLE $table_name (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    name varchar(50) NOT NULL,
    email varchar(50) NOT NULL,
    phone varchar(15) NOT NULL,
    PRIMARY KEY  (id)
  ) $collate;";

    include_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($student_table);
}

//Action hook for APIs registration
add_action('rest_api_init',   function () {

    //Registration of API Routes

    //List Students
    register_rest_route('students/v1', 'students', array(
        'methods' => 'GET',
        'callback' => 'wcp_handle_get_students_routes',
    ));

    //Add Student   (POST)
    register_rest_route("student/v1", "student", array(
        'methods' => 'POST',
        'callback' => 'wcp_handle_post_student',
        'args' => array(
            'name' => array(
                "type" => "string",
                "required" => true
            ),
            'email' => array(
                "type" => "string",
                "required" => true

            ),
            'phone' => array(
                "type" => "string",
                "required" => false

            )
        )
    ));

    //Update Student (PUT)
    register_rest_route("student/v1", "/student/(?P<id>\d+)", array(
        'methods' => 'PUT',
        'callback' => 'wcp_handle_put_update_student',
        'args' => array(
            'name' => array(
                "type" => "string",
                "required" => true
            ),
            'email' => array(
                "type" => "string",
                "required" => true

            ),
            'phone' => array(
                "type" => "string",
                "required" => false

            )
        )
    ));

    //Delete Student (DELETE)
    register_rest_route("student/v1", "/student/(?P<id>\d+)", array(
        'methods' => 'DELETE',
        'callback' => 'wcp_handle_delete_student',
    ));
});



//List all students
function wcp_handle_get_students_routes()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'students_table';

    $students = $wpdb->get_results("SELECT * FROM {$table_name}", ARRAY_A);

    return rest_ensure_response([
        "status" => true,
        "message" => "Student List",
        "data" => $students
    ]);
}


//Add Student
function wcp_handle_post_student($request)
{

    global $wpdb;
    $table_name = $wpdb->prefix . 'students_table';

    $name = $request->get_param('name');
    $email = $request->get_param('email');
    $phone = $request->get_param('phone');

    $wpdb->insert($table_name, array(
        'name' => $name,
        'email' => $email,
        'phone' => $phone
    ));

    if ($wpdb->insert_id > 0) {
        return rest_ensure_response([
            "status" => true,
            "message" => "Student Created Successfully",
            "data" => [$request->get_params()]
        ]);
    } else {
        return rest_ensure_response([
            "status" => false,
            "message" => "Failed to create student",
            "data" => [$request->get_params()]
        ]);
    }
}


//Update Student
function wcp_handle_put_update_student($request)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'students_table';

    $id = $request['id'];

    $student = $wpdb->get_row(
        "SELECT * FROM $table_name WHERE id = {$id}
    "
    );

    if (!empty($student)) {
        $wpdb->update($table_name, array(
            "name" => $request->get_param('name'),
            "email" => $request->get_param('email'),
        ), array('id' => $id));
        return rest_ensure_response([
            "status" => true,
            "message" => "Student data Updated Successfully",
        ]);
    } else {
        return rest_ensure_response([
            "status" => false,
            "message" => "Student does not exist",
        ]);
    }
}


//Delete Student
function wcp_handle_delete_student($request)
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'students_table';

    $id = $request['id'];
    $student = $wpdb->get_row(
        "SELECT * FROM $table_name WHERE id = {$id}"
    );

    if (!empty($student)) {
        $wpdb->delete($table_name, array('id' => $id));
        return rest_ensure_response([
            "status" => true,
            "message" => "Student Deleted Successfully",
        ]);
    } else {
        return rest_ensure_response([
            "status" => false,
            "message" => "Student does not exist",
        ]);
    }
}
