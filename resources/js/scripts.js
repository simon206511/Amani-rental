// scripts.js

// Show/hide password logic (based on your provided code)
const pwShowHide = document.querySelectorAll(".showHidePw");
const pwFields = document.querySelectorAll(".password");

pwShowHide.forEach(eyeIcon => {
    eyeIcon.addEventListener("click", () => {
        pwFields.forEach(pwField => {
            if (pwField.type === "password") {
                pwField.type = "text";
                pwShowHide.forEach(icon => {
                    icon.classList.replace("uil-eye-slash", "uil-eye");
                });
            } else {
                pwField.type = "password";
                pwShowHide.forEach(icon => {
                    icon.classList.replace("uil-eye", "uil-eye-slash");
                });
            }
        });
    });
});

// Switch between signup and login forms
const container = document.querySelector(".container");
const signUp = document.querySelector(".signup-link");
const login = document.querySelector(".login-link");

signUp.addEventListener("click", () => {
    container.classList.add("active");
});

login.addEventListener("click", () => {
    container.classList.remove("active");
});

// Example error and success messages (you can customize these)
const loginForm = document.querySelector('.form.login');
const registrationForm = document.querySelector('.form.signup');
// If login fails:
document.getElementById('login-error').textContent = 'Incorrect email or password. Please try again.';

// If registration succeeds:
document.getElementById('registration-success').textContent = 'Registered successfully! You can now log in.';