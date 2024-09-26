
// Get the modal
var modal = document.getElementById("myModal");

// Get the button that opens the modal
var products = document.getElementsByClassName("product");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks on the button, open the modal
for (var i = 0; i < products.length; i++) {
    var img = products[i].getElementsByTagName("img")[0]; // Get the first img inside the product
    img.onclick = function() {
        modal.style.display = "block";
    }
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
} 

var activeTab = null;

function openTab(event, tabName) {
    // Get the specific tab content
    var selectedTab = document.getElementById(tabName);

    // If the clicked tab is already active, collapse it
    if (activeTab === tabName) {
        selectedTab.style.display = "none";
        activeTab = null;
    } else {
        // Hide all elements with class="tab-content" by default
        var tabContent = document.getElementsByClassName("tab-content");
        for (var i = 0; i < tabContent.length; i++) {
            tabContent[i].style.display = "none";
        }

        // Show the specific tab content
        selectedTab.style.display = "block";
        activeTab = tabName;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const buttons = document.querySelectorAll('.navbar button');

    buttons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            buttons.forEach(btn => btn.classList.remove('active'));
            // Add active class to the clicked button
            this.classList.add('active');
        });
    });
});
  