// tabs.js

let activeTab = null;

export function openTab(event, tabName) {
    // Hide all elements with class="tab-content" by default
    const tabContents = document.getElementsByClassName("tab-content");
    for (let i = 0; i < tabContents.length; i++) {
        tabContents[i].style.display = "none";
    }

    // Show the specific tab content
    const selectedTab = document.getElementById(tabName);
    if (selectedTab) {
        selectedTab.style.display = "block";
    }

    // Remove "active" class from all buttons and add to the clicked button
    const tabButtons = document.getElementsByClassName("tab-button");
    for (let button of tabButtons) {
        button.classList.remove("active");
    }
    event.currentTarget.classList.add("active");

    // Update the activeTab variable
    activeTab = tabName;
}
