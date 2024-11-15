let activeTab = null;

function openTab(event, tabName) {
    // Hide all tab-content
    const tabContents = document.querySelectorAll(".tab-content");
    tabContents.forEach(tab => tab.classList.remove("active"));

    // Show the selected tab content
    const selectedTab = document.getElementById(tabName);
    if (selectedTab) {
        selectedTab.classList.add("active");
    }

    // Remove "active" from all buttons and add to the clicked button
    const tabButtons = document.querySelectorAll(".tab-button");
    tabButtons.forEach(button => button.classList.remove("active"));
    event.currentTarget.classList.add("active");

    // Update the activeTab variable
    activeTab = tabName;
}

document.addEventListener("DOMContentLoaded", () => {
    // Default to showing the first tab
    const defaultTab = document.querySelector(".tab-button");
    if (defaultTab) {
        defaultTab.click();
    }
});

window.openTab = openTab; // Ensure function is accessible globally
