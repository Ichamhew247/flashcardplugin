<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo plugins_url('assets/css/style.css', dirname(__FILE__)); ?>">
    <title>Employee CRUD System</title>
</head>

<body>



    <div id="wp_employee_crud_plugin">
        <!-- Add Employee Layout -->
        <div class="add_employee_form hide_element">
            <button id="btn_close_add_employee_form" style="float:right;">Close Form</button>
            <h3>Add Employee</h3>

            <form action="javascript:void(0)" id="frm_add_employee" method="post" enctype="multipart/form-data">

                <input type="hidden" name="action" value="wce_add_employee">

                <!-- Name Field -->
                <label for="name">Name</label>
                <input type="text" name="name" id="name" placeholder="Employee Name" required>

                <!-- Email Field -->
                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="Employee Email" required>

                <!-- Designation Field -->
                <label for="designation">Designation</label>
                <select name="designation" id="designation" required>
                    <option value="">-- Choose Designation --</option>
                    <option value="DEV">Developer</option>
                    <option value="QA">Quality Analyst</option>
                    <option value="BA">Business Analyst</option>
                </select>

                <!-- Profile Image Field -->
                <label for="profile_image">Profile Image</label>
                <input type="file" name="profile_image" id="file">

                <!-- Submit Button -->
                <button id="btn_save_data" type="submit">Submit</button>
            </form>
        </div>



        <!-- Edit Employee Layout -->
        <div class="edit_employee_form hide_element">

            <button id="btn_close_edit_employee_form" style="float:right;">Close Edit Form</button>
            <h3>Edit Employee</h3>

            <form action="javascript:void(0)" id="frm_edit_employee" method="post" enctype="multipart/form-data">

                <input type="hidden" name="action" value="wce_edit_employee">
                <input type="hidden" name="employee_id" id="employee_id">

                <!-- Name Field -->
                <label for="employee_name">Name</label>
                <input type="text" name="employee_name" id="employee_name" placeholder="Employee Name" required>

                <!-- Email Field -->
                <label for="employee_email">Email</label>
                <input type="email" name="employee_email" id="employee_email" placeholder="Employee Email" required>

                <!-- Designation Field -->
                <label for="employee_designation">Designation</label>
                <select name="employee_designation" id="employee_designation" required>
                    <option value="">-- Choose Designation --</option>
                    <option value="DEV">Developer</option>
                    <option value="QA">Quality Analyst</option>
                    <option value="BA">Business Analyst</option>
                </select>

                <!-- Profile Image Field -->
                <label for="employee_profile_image">Profile Image</label>
                <input type="file" name="employee_profile_image" id="employee_file">
                <br>
                <img src="" id="employee_profile_icon" style="width: 100px; height: 100px;">
                <!-- Submit Button -->
                <button id="btn_update_data" type="submit">Update Employee</button>
            </form>

        </div>
        <!-- List Employee Layout -->
        <button id="btn_open_add_employee_form" style="float:right;">Add Employee</button>
        <h3>List Employees</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Designation</th>
                    <th>Profile Image</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody id="employee_data_tbody">
            </tbody>

        </table>
    </div>