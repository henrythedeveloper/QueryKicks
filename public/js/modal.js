// modal.js

// Get the modal and its elements
const modal = document.getElementById("myModal");
const products = document.getElementsByClassName("product");
const span = document.getElementsByClassName("close")[0];

// Ensure the modal is hidden on page load
document.addEventListener('DOMContentLoaded', () => {
    if (modal) {
        modal.style.display = "none"; // Ensure the modal is hidden by default
        console.log("Modal hidden on page load.");
    } else {
        console.error("Modal element not found.");
    }

    if (products.length > 0) {
        console.log(`Found ${products.length} product(s).`);
        // Open modal on product image click
        for (let i = 0; i < products.length; i++) {
            const img = products[i].getElementsByTagName("img")[0];
            if (img) {
                img.addEventListener("click", () => {
                    modal.style.display = "flex"; // Use flex for proper centering
                    console.log("Modal opened.");
                });
            } else {
                console.warn(`No image found in product #${i + 1}.`);
            }
        }
    } else {
        console.warn("No products found.");
    }

    // Close modal on 'x' click
    if (span) {
        span.addEventListener("click", () => {
            modal.style.display = "none";
            console.log("Modal closed via close button.");
        });
    } else {
        console.error("Close button not found.");
    }

    // Close modal when clicking outside of it
    window.addEventListener("click", (event) => {
        if (event.target === modal) {
            modal.style.display = "none";
            console.log("Modal closed by clicking outside.");
        }
    });
});
