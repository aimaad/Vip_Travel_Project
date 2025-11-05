document.addEventListener('DOMContentLoaded', function () {
    const toggleButton = document.getElementById('toggle-form');
    const authContainer = document.querySelector('.auth-container');
    const loginForm = document.getElementById('login-form');
    const signupForm = document.getElementById('signup-form');
    const leftSideHeading = document.getElementById('leftSideHeading');
    const leftSideText = document.getElementById('leftSideText');

    toggleButton.addEventListener('click', function () {
        if (authContainer.classList.contains('slide-left')) {
            // Switch back to login state
            authContainer.classList.remove('slide-left');
            loginForm.classList.add('active');
            signupForm.classList.remove('active');
            toggleButton.textContent = translations.signupButton;
            leftSideHeading.textContent = translations.loginHeading;
            leftSideText.textContent = translations.loginText;
        } else {
            // Switch to sign-up state
            authContainer.classList.add('slide-left');
            loginForm.classList.remove('active');
            signupForm.classList.add('active');
            leftSideHeading.textContent = translations.signupHeading;
            leftSideText.textContent = translations.signupText;
            toggleButton.textContent = translations.loginButton;
        }
    });
});
