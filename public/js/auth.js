// auth.js

function openAuthForm(event, formId) {
    // Get all toggle buttons
    const buttons = document.querySelectorAll('.auth-toggle');
    if (!buttons.length) {
        console.warn('No buttons with the class "auth-toggle" found.');
        return;
    }

    // Remove "active" class from all buttons
    buttons.forEach(button => button.classList.remove('active'));

    // Add "active" class to the clicked button
    event.currentTarget.classList.add('active');

    // Get all forms and hide them
    const forms = document.querySelectorAll('.auth-form');
    if (!forms.length) {
        console.warn('No forms with the class "auth-form" found.');
        return;
    }

    forms.forEach(form => form.classList.remove('active'));

    // Show the selected form
    const targetForm = document.getElementById(formId);
    if (!targetForm) {
        console.error(`Form with ID "${formId}" not found.`);
        return;
    }

    targetForm.classList.add('active');
}

// Attach event listeners
document.addEventListener('DOMContentLoaded', () => {
    const buttons = document.querySelectorAll('.auth-toggle');
    if (!buttons.length) {
        console.warn('No buttons with the class "auth-toggle" found.');
        return;
    }

    buttons.forEach(button => {
        const formId = button.getAttribute('data-form');
        button.addEventListener('click', (event) => openAuthForm(event, formId));
    });
});
