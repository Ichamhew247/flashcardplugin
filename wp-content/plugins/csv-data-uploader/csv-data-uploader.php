<?php



/**
 * Plugin Name: CSV Data Uploader
 * Description: This plugin will upload CSV data to DB Table
 * Author: Nattanicha Niyomchan
 * Version: 1.0
 * Plugin URI: http://example.com/csv-data-uploader
 * Author URI: http://freelanguage.com/ */


define("CDU_PLUGIN_DIR_PATH", plugin_dir_path(__FILE__));


add_shortcode("csv-data-uploader", "cdu_display_uploader_form");


function cdu_display_uploader_form()
{

    // start buffer 
    ob_start();
    include_once CDU_PLUGIN_DIR_PATH . "/template/cdu_form.php"; //Put all contents into buffer

    //Read buffer
    $template = ob_get_contents();

    //Clean buffer
    ob_end_clean();
    return  $template;
}


//DB Table on Plugin Activation
register_activation_hook(__FILE__, "cdu_create_table");

function cdu_create_table()
{
    global $wpdb;
    $table_prefix = $wpdb->prefix;
    $table_name = $table_prefix . "students_data";
    $table_collate = $wpdb->get_charset_collate();


    $sql_command = "
    CREATE TABLE `" . $table_name . "` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `age` int(5) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `photo` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) " . $table_collate . "
    ";

    require_once(ABSPATH . "/wp-admin/includes/upgrade.php");


    dbDelta($sql_command);
}


//To add Script File
function cdu_add_script_file()
{
    wp_enqueue_script("cdu-script-js", plugin_dir_url(__FILE__) . "assets/script.js", array("jquery"));
    wp_localize_script("cdu-script-js", "cdu_object", array(
        "ajax_url" => admin_url("admin-ajax.php"),
    ));
}
add_action("wp_enqueue_scripts", "cdu_add_script_file");


//Capture Ajax Request
add_action("wp_ajax_cdu_submit_form_data", "cdu_ajax_handler"); //when user is logged in //wp_ajax_your_custom_action_name
add_action("wp_ajax_nopriv_cdu_submit_form_data", "cdu_ajax_handler"); //when user is not logged in //wp_ajax_nopriv_your_custom_action_name


function cdu_ajax_handler()
{
    if ($_FILES['csv_data_file']) {
        $csvFile = $_FILES['csv_data_file']['tmp_name'];
        $handle = fopen($csvFile, "r");
        global $wpdb;
        $table_name = $wpdb->prefix . "students_data";

        if ($handle) {
            $row = 0;

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if ($row == 0) {
                    // Skip the header row
                    $row++;
                    continue;
                }

                // Insert Data into DB
                $wpdb->insert($table_name, array(
                    "name" => $data[1],
                    "email" => $data[2],
                    "age" => $data[3],
                    "phone" => $data[4],
                    "photo" => $data[5]
                ));
                $row++;
            }

            fclose($handle);

            echo json_encode([
                "status" => 1,
                "message" => "Data has been uploaded successfully"
            ]);
            exit;
        }
    } else {
        echo json_encode(array(
            "status" => 0,
            "message" => "Please upload CSV file"
        ));
        exit;
    }

    echo json_encode(array(
        "status" => 1,
        "message" => "Hello World"
    ));
    exit;
}
