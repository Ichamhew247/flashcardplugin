document.addEventListener("DOMContentLoaded", () => {
  // ตั้งเวลาสำหรับซ่อนข้อความแจ้งเตือน
  setTimeout(() => {
    const notices = document.querySelectorAll(".notice"); // เลือกข้อความแจ้งเตือนทั้งหมด
    notices.forEach((notice) => (notice.style.display = "none")); // ซ่อนข้อความแจ้งเตือน
  }, 5000); // 5000ms = 5 วินาที
});
