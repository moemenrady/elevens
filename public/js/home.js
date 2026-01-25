const cards = document.querySelectorAll(".card");

/* stagger animation */
cards.forEach((card, index) => {
    card.style.animationDelay = `${index * 0.1}s`;
});

/* mouse interaction */
cards.forEach(card => {

    card.addEventListener("mousemove", e => {
        const rect = card.getBoundingClientRect();

        const x = e.clientX - rect.left - rect.width / 2;
        const y = e.clientY - rect.top - rect.height / 2;

        const moveX = x / 10;
        const moveY = y / 10;

        card.style.transform = `
            translate(${moveX}px, ${moveY - 5}px)
        `;
        card.style.boxShadow = "0 30px 60px rgba(0,0,0,.4)";
    });

    card.addEventListener("mouseleave", () => {
        card.style.transform = "translate(0,0)";
        card.style.boxShadow = "0 20px 40px rgba(0,0,0,.3)";
    });

    /* routing */
    card.addEventListener("click", () => {
        window.location.href = card.dataset.route;
    });
});
