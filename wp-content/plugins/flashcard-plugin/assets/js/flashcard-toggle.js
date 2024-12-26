document.addEventListener("DOMContentLoaded", function () {
  const cards = document.querySelectorAll(".flashcard");

  cards.forEach((card) => {
    card.addEventListener("click", function () {
      card.classList.toggle("is-flipped");
    });
  });
});
