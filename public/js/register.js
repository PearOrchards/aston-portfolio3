/**
 * Function to handle form submission (specifically the register form)
 * @param e {Event}
 * @returns
 */
function onSubmit(e) {
    // set email and password elements
    const email = document.querySelector("#email");
    const password = document.querySelector("#password");

    // Report the validity of both, so form doesn't submit if they are invalid.
    email.reportValidity();
    password.reportValidity();
}

function checkEmails() {
    const email = document.querySelector("#email"); // Get the email element
    const email2 = document.querySelector("#email2"); // Get the second email value

    if (email.value !== email2.value) { // Check if the emails match
        email.setCustomValidity("Your emails don't match!")
    } else {
        email.setCustomValidity("");
    }
}

function checkPasswords() {
    const password = document.querySelector("#password"); // Get the password element
    const password2 = document.querySelector("#password2"); // Get the second password value

    if (password.value !== password2.value) { // Check if the passwords match
        password.setCustomValidity("Your passwords don't match!")
    } else {
        password.setCustomValidity("");
    }
}

// Add the event listener to the form on page load.
window.addEventListener('load', () => {
    document.querySelector("#email").addEventListener('input', () => checkEmails());
    document.querySelector("#email2").addEventListener('input', () => checkEmails());
    document.querySelector("#password").addEventListener('input', () => checkPasswords());
    document.querySelector("#password2").addEventListener('input', () => checkPasswords());

    document.querySelector("#registerForm").addEventListener('submit', (e) => onSubmit(e));
})