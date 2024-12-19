<?php

/**
 * Plugin Name: CSV DATA Backup
 * Description: This plugin will backup your table data to a CSV file.
 * Version: 1.0
 * Author: Nattanicha Niyomchan
 * Plugin URI: https://www.example.com
 * Author URI: https://freelanguageschool.com 
 */



//Plugin Menu in Admin Dashboard
//We have to create a page - a button export
//Export All Table Data to CSV


add_action('admin_menu', 'tdcb_create_admin_menu');


//Admin Menu
function tdcb_create_admin_menu()
{
    add_menu_page('CSV Data Backup Plugin', 'CSV Data Backup ', 'manage_options', 'csv_data_backup', 'tdcd_export_form', 'dashicons-database-export', 8);
}



//Form Layout
function tdcd_export_form()
{
    ob_start();
    include_once plugin_dir_path(__FILE__) . 'template/table_data_backup.php';


    $layout = ob_get_contents();

    ob_end_clean();
    echo $layout;
}


add_action("admin_init", "tdcb_handle_form_export");

function tdcb_handle_form_export()
{
    if (isset($_POST['tdcb_export_button'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . "students_data";
        $students = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);  //get all the data from the table

        if (empty($students)) {   //if there is no data in the table  
            //Error message

        }

        // echo "<pre>";
        // print_r($students);

        // print_r(array_keys($students[0]));  //print the column names
        // die; //print the data

        $filename = "students_data_" . time() . ".csv";    //create a file name with timestamp
        header("Content-Type: text/csv; charset=utf-8");    //set the header
        header("Content-Disposition: attachment; filename=$filename");  //download the file

        $output = fopen("php://output", "w");  //open the file in write mode    

        fputcsv($output, array_keys($students[0]));  //write the column names to the file

        foreach ($students as $student) {  //loop through the data
            fputcsv($output, $student);  //write the data to the file
        }
        fclose($output);  //close the file
        die();  //stop the execution    

    }
}
