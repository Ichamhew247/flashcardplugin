<?php

class MyEmployees
{
    private $wpdb;
    private $table_name;
    private $table_prefix;

    public function __construct()
    {
        global $wpdb; // ใช้ตัวแปร $wpdb ที่เป็น global
        $this->wpdb = $wpdb; // เก็บ $wpdb ไว้ใน property ภายในคลาส
        $this->table_prefix = $this->wpdb->prefix; // wp_
        $this->table_name = $this->table_prefix . "employees_table"; // wp_employees_table
    }

    // Create Database Table + WordPress Page
    public function callPluginActivationFunction()
    {
        $collate = $this->wpdb->get_charset_collate();

        $createcommand =
            "
        CREATE TABLE `{$this->table_name}` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(50) NOT NULL,
            `email` varchar(50) DEFAULT NULL,
            `designation` varchar(5) DEFAULT NULL,
            `profile_image` varchar(220) DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) $collate;
        ";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($createcommand);

        //WP Page
        $page_title = "Employee CRUD System";
        $page_content = "[wp-employee-form]";

        if (!get_page_by_title($page_title)) {
            wp_insert_post(array(
                'post_title' => $page_title,
                'post_content' => $page_content,
                'post_type' => 'page',
                'post_status' => 'publish',
            ));
        }
    }

    // Drop Database Table
    public function dropEmployeesTable()
    {
        $delete_command = "DROP TABLE IF EXISTS {$this->table_name}";
        $this->wpdb->query($delete_command);
    }

    //Render Employee Form Layout
    public function createEmployeeForm()
    {
        ob_start();
        include_once(WCE_DIR_PATH . 'template/employee_form.php');

        $template = ob_get_contents();

        ob_end_clean();

        return $template;
    }

    //Add CSS / JS
    public function addAssetToPlugin()
    {
        //Styles
        wp_enqueue_style('employee-crud-css', WCE_DIR_URL . "assets/css/style.css");
        //Validation
        wp_enqueue_script("wce-validation", WCE_DIR_URL . "assets/js/jquery.validate.min.js", array('jquery'), '1.0', true);
        //JS
        wp_enqueue_script("employee-crud-js", WCE_DIR_URL . "assets/js/script.js", array('jquery'), '1.0', true);
        wp_localize_script('employee-crud-js', 'wce_object', array('ajax_url' => admin_url('admin-ajax.php')));
    }

    //Process AJAX Request: Add Employee Form Data
    public function handleAddEmployeeFormData()
    {
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_text_field($_POST['email']);
        $designation = sanitize_text_field($_POST['designation']);

        $profile_url = "";

        // Check if file is uploaded
        if (isset($_FILES['profile_image']['name'])) {


            $UploadFile = $_FILES['profile_image'];
            //Original File Name - CC0CB81E-C3ED-49FE-8AFE-F22069E729F1-1.jpg 
            //$UploadFile['name']
            $originalFileName = pathinfo($UploadFile['name'], PATHINFO_FILENAME);  //CC0CB81E-C3ED-49FE-8AFE-F22069E729F1-1

            //File Extension - jpg
            $file_extension = pathinfo($UploadFile['name'], PATHINFO_EXTENSION); //jpg

            //New Image Name
            $newImageName = $originalFileName . "-" . time() . "." . $file_extension; //CC0CB81E-C3ED-49FE-8AFE-F22069E729F1-1-1632345678.jpg

            $_FILES['profile_image']['name'] = $newImageName;

            $fileUploaded = wp_handle_upload($_FILES['profile_image'], array('test_form' => false));
            $profile_url = $fileUploaded['url'];
        }

        // Insert data into database
        $result = $this->wpdb->insert($this->table_name, array(
            'name' => $name,
            'email' => $email,
            'designation' => $designation,
            'profile_image' => $profile_url
        ));

        if ($result) {
            wp_send_json_success(["message" => "Data Inserted Successfully"]);
        } else {
            wp_send_json_error(["message" => "Failed to Insert Data"]);
        }
    }

    // Load DB Table Employees
    public function handleLoadEmployeeData()
    {
        $employees = $this->wpdb->get_results(
            "SELECT * FROM {$this->table_name}",
            ARRAY_A
        );

        if (!empty($employees)) {
            wp_send_json_success($employees); // ส่งข้อมูล employees ตรงๆ
        } else {
            wp_send_json_error(["message" => "No Employees Found"]);
        }
    }

    // Delete Employee Data
    public function handleDeleteEmployeeData()
    {
        $employee_id = $_GET['empId'];

        $this->wpdb->delete($this->table_name, [
            'id' => $employee_id
        ]);
        return wp_send_json([
            "status" => true,
            "message" => "Employee Deleted Successfully",
            "success" => true
        ]);
    }

    //Read Single Employee Data
    public function handleToGetSingleEmployeeData()
    {
        $employee_id = $_GET['empId'];


        if ($employee_id > 0) {

            $employeeData = $this->wpdb->get_row(
                "SELECT * FROM {$this->table_name} WHERE id = {$employee_id} ",
                ARRAY_A
            );
            return wp_send_json([
                "status" => true,
                "message" => "Employee Data Found",
                "data" => $employeeData,
                "success" => true
            ]);
        } else {
            return wp_send_json([
                "status" => false,
                "message" => "Please Pass employee ID",
                "success" => false
            ]);
        }
    }

    //Update Employee Data
    public function handleUpdateEmployeeData()
    {
        $name = sanitize_text_field($_POST['employee_name']);
        $email = sanitize_text_field($_POST['employee_email']);
        $designation = sanitize_text_field($_POST['employee_designation']);
        $id = sanitize_text_field($_POST['employee_id']);

        $employeeData = $this->getEmployeeData($id);

        $profile_image_url = "";
        if (!empty($employeeData)) {

            //Existing Profile Image
            $profile_image_url = $employeeData['profile_image'];


            //New File Image Object
            $profile_file_image = isset($_FILES['employee_profile_image']['name']) ? $_FILES['employee_profile_image']['name'] : '';
            //Check Image Exists
            if (!empty($profile_file_image)) {


                if (!empty($profile_image_url)) {
                    //http://localhost/wp/wordpress-plugin_course/wp-content/uploads/2024/12/CC0CB81E-C3ED-49FE-8AFE-F22069E729F1-1.jpg
                    $wp_site_url = get_site_url(); //http://localhost/wp/wordpress-plugin_course
                    $file_path = str_replace($wp_site_url, "/", "", $profile_image_url); // /wp-content/uploads/2024/12/CC0CB81E-C3ED-49FE-8AFE-F22069E729F1-1.jpg
                    if (file_exists(ABSPATH . $file_path)) {
                        //Remove File from uploads folder
                        unlink(ABSPATH . $file_path);
                    }
                }

                $UploadFile = $_FILES['employee_profile_image'];
                //Original File Name - CC0CB81E-C3ED-49FE-8AFE-F22069E729F1-1.jpg 
                //$UploadFile['name']
                $originalFileName = pathinfo($UploadFile['name'], PATHINFO_FILENAME);  //CC0CB81E-C3ED-49FE-8AFE-F22069E729F1-1

                //File Extension - jpg
                $file_extension = pathinfo($UploadFile['name'], PATHINFO_EXTENSION); //jpg

                //New Image Name
                $newImageName = $originalFileName . "-" . time() . "." . $file_extension; //CC0CB81E-C3ED-49FE-8AFE-F22069E729F1-1-1632345678.jpg

                $_FILES['employee_profile_image']['name'] = $newImageName;
                //Upload New Image
                $fileUploaded = wp_handle_upload($_FILES['employee_profile_image'], array('test_form' => false));
                $profile_image_url = $fileUploaded['url'];
            }

            $this->wpdb->update($this->table_name, [
                'name' => $name,
                'email' => $email,
                'designation'   => $designation,
                'profile_image' => $profile_image_url
            ], ['id' => $id]);
            return wp_send_json([
                "status" => true,
                "message" => "Employee Data Updated Successfully",
                "success" => true
            ]);
        } else {
            return wp_send_json([
                "status" => true,
                "message" => "Employee Data Found",
                "success" => true
            ]);
        }
    }


    //Get Employee Data
    private function getEmployeeData($employee_id)
    {
        $employeeData = $this->wpdb->get_row(
            "SELECT * FROM {$this->table_name} WHERE id = {$employee_id} ",
            ARRAY_A
        );
        return $employeeData;
    }
}
