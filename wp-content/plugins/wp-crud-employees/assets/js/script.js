jQuery(document).ready(function () {
  console.log("Welcome to WP CRUD Employees Plugin");

  // Add form validation
  jQuery("#frm_add_employee").validate({});

  // Form submit
  jQuery("#frm_add_employee").submit(function (e) {
    e.preventDefault();

    var formdata = new FormData(this);

    jQuery.ajax({
      url: wce_object.ajax_url, // ส่งไปยัง admin-ajax.php
      data: formdata,
      method: "POST",
      dataType: "json",
      contentType: false,
      processData: false,
      success: function (response) {
        console.log(response); // Debug Response
        if (response.success) {
          alert(response.data.message); // แสดงข้อความสำเร็จ
          setTimeout(function () {
            loadEmployeeData(); // โหลดข้อมูลใหม่หลังบันทึกสำเร็จ
          }, 1500);
          jQuery("#frm_add_employee")[0].reset(); // รีเซ็ตฟอร์ม
        } else {
          alert("Error: " + response.data.message);
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error:", error);
      },
    });
  });

  // Render Employee
  loadEmployeeData();

  // Delete Function
  jQuery(document).on("click", ".btn_delete_employee", function () {
    var employeeId = jQuery(this).data("id");

    if (confirm("Are you sure want to delete")) {
      jQuery.ajax({
        url: wce_object.ajax_url,
        data: {
          action: "wce_delete_employee",
          empId: employeeId,
        },
        method: "GET",
        dataType: "json",
        success: function (response) {
          console.log(response); // Debug Response
          if (response.success) {
            alert("Employee deleted successfully!");
            loadEmployeeData(); // โหลดข้อมูลใหม่หลังลบสำเร็จ
          } else {
            alert("Error: Unable to delete employee.");
          }
        },
        error: function (xhr, status, error) {
          console.error("AJAX Error:", error);
        },
      });
    } // ปิด if(confirm)
  }); // ปิด jQuery(document).on

  //Open Add Employee Form
  jQuery(document).on("click", "#btn_open_add_employee_form", function () {
    jQuery(".add_employee_form").toggleClass("hide_element");
    jQuery(this).addClass("hide_element");
  });

  //Close Add Employee Form
  jQuery(document).on("click", "#btn_close_add_employee_form", function () {
    jQuery(".add_employee_form").toggleClass("hide_element");
    jQuery("#btn_open_add_employee_form").removeClass("hide_element");
  });

  // Open Edit Employee Form
  jQuery(document).on("click", ".btn_edit_employee", function () {
    jQuery(".edit_employee_form").removeClass("hide_element");
    jQuery("#btn_open_edit_employee_form").addClass("hide_element");
    // Get Existing Employee Data by ID
    var employeeId = jQuery(this).data("id"); // or jQuery(this).attr("data-id");
    jQuery.ajax({
      url: wce_object.ajax_url,
      data: { action: "wce_get_employee_data", empId: employeeId },
      method: "GET",
      dataType: "json",
      success: function (response) {
        console.log(response); // Debug Response
        if (response.success) {
          jQuery("#employee_name").val(response?.data?.name);
          jQuery("#employee_email").val(response?.data?.email);
          jQuery("#employee_designation").val(response?.data?.designation);
          jQuery("#employee_id").val(response?.data?.id);
          jQuery("#employee_profile_icon").attr(
            "src",
            response?.data?.profile_image
          );
        } else {
          alert("Error: Unable to get employee data.");
        }
      },
    }); // ส่ง AJAX ไปยัง admin-ajax.php โดยส่งข้อมูลไปด้วย
  });

  // Close Edit Employee Form
  jQuery(document).on("click", "#btn_close_edit_employee_form", function () {
    jQuery(".edit_employee_form").toggleClass("hide_element");
    jQuery("#btn_open_edit_employee_form").removeClass("hide_element");
  });

  //Submit Edit Employee Form
  jQuery(document).on("submit", "#frm_edit_employee", function (e) {
    e.preventDefault();
    var formdata = new FormData(this);
    jQuery.ajax({
      url: wce_object.ajax_url,
      data: formdata,
      method: "POST",
      dataType: "json",
      contentType: false,
      processData: false,
      success: function (response) {
        console.log(response);
        if (response) {
          alert(response?.message);
          setTimeout(function () {
            location.reload();
          }, 1500);
        }
      },
    });
  });
});

// Load all employees from DB Table
function loadEmployeeData() {
  jQuery.ajax({
    url: wce_object.ajax_url, // ส่งไปยัง admin-ajax.php
    data: { action: "wce_load_employee_data" },
    method: "GET",
    dataType: "json",
    success: function (response) {
      console.log(response); // Debug Response
      var employeesDataHTML = "";

      if (response.success && response.data.length > 0) {
        // วนลูปข้อมูลพนักงาน
        jQuery.each(response.data, function (index, employee) {
          let employeeProfileImage = "--";

          if (employee.profile_image) {
            employeeProfileImage = `<img src="${employee.profile_image}" width="50" height="50" />`;
          }

          employeesDataHTML += `
              <tr>
                  <td>${employee.id}</td>
                  <td>${employee.name}</td>
                  <td>${employee.email}</td>
                  <td>${employee.designation}</td>
                  <td>${employeeProfileImage}</td>
                  <td>
                      <button data-id="${employee.id}" class="btn_edit_employee">Edit</button>
                      <button data-id="${employee.id}" class="btn_delete_employee">Delete</button>
                  </td>
              </tr>`;
        });
      } else {
        // กรณีไม่มีข้อมูล
        employeesDataHTML = `
            <tr>
              <td colspan="6" style="text-align: center;">No Employees Found</td>
            </tr>`;
      }

      // Bind ข้อมูลเข้ากับ Table
      console.log("sssw");
      jQuery("#employee_data_tbody").html(employeesDataHTML);
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error:", error);
    },
  });
}
