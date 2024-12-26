document.addEventListener("DOMContentLoaded", function () {
  // ฟังก์ชันสำหรับการพลิกการ์ด
  const cards = document.querySelectorAll(".flashcard");

  cards.forEach((card) => {
    card.addEventListener("click", function () {
      card.classList.toggle("is-flipped");
    });
  });

  // ฟังก์ชันสำหรับการเล่นเสียง
  const buttons = document.querySelectorAll(".play-audio");

  buttons.forEach((button) => {
    button.addEventListener("click", function (event) {
      event.stopPropagation(); // หยุดการกระทำคลิกพลิกการ์ด
      const audioSrc = button.getAttribute("data-audio");
      const audio = new Audio(audioSrc);

      audio.play();
    });
  });
});
