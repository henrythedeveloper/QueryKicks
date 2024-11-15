// modal.js

// Get the modal
var modal = document.getElementById("myModal");
var products = document.getElementsByClassName("product");
var span = document.getElementsByClassName("close")[0];

// Open modal on product image click
for (var i = 0; i < products.length; i++) {
    var img = products[i].getElementsByTagName("img")[0];
    img.onclick = function() {
        modal.style.display = "block";
    }
}

// Close modal on 'x' click
span.onclick = function() {
    modal.style.display = "none";
}

// Close modal when clicking outside of it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
