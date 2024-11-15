// auth.js

function openAuthForm(event, formId) {
    const buttons = document.querySelectorAll('.auth-toggle');
    buttons.forEach(button => button.classList.remove('active'));

    event.currentTarget.classList.add('active');

    const forms = document.querySelectorAll('.auth-form');
    forms.forEach(form => form.classList.remove('active'));

    document.getElementById(formId).classList.add('active');
}

// Attach event listeners
document.addEventListener('DOMContentLoaded', () => {
    const buttons = document.querySelectorAll('.auth-toggle');
    buttons.forEach(button => {
        const formId = button.getAttribute('data-form');
        button.addEventListener('click', (event) => openAuthForm(event, formId));
    });
});
