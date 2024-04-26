import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css'


// Style pour le mot de passe inscription
// const passwordInput = document.getElementById('passwordInput');
// const progress = document.getElementById('progress');

if (window.location.href.includes("/register")){
passwordInput.addEventListener('input', function () {
    const password = passwordInput.value;
    const hasLength = password.length >= 8;
    const specialChars = ['!', '@', '#', '$', '%', '^', '&', '-', '_', ':', ';', '"', "'", '?', '*'];
    const hasSpecialChar = password.split('').some(char => specialChars.includes(char));
    const hasUppercase = /[A-Z]/.test(password);
    const lengthRequirement = document.getElementById('lengthRequirement');
    const specialCharRequirement = document.getElementById('specialCharRequirement');
    const uppercaseRequirement = document.getElementById('uppercaseRequirement');
    lengthRequirement.style.color = hasLength ? 'green' : 'red';
    specialCharRequirement.style.color = hasSpecialChar ? 'green' : 'red';
    uppercaseRequirement.style.color = hasUppercase ? 'green' : 'red';

    if (hasLength || hasSpecialChar || hasUppercase) {
        progress.style.width = '33.33%';
        progress.classList.remove('segment-orange', 'segment-green');
        progress.classList.add('segment-yellow');
    }
    if ((hasLength && hasSpecialChar) || (hasLength && hasUppercase) || (hasLength && hasUppercase)) {
        progress.style.width = '66.66%';
        progress.classList.remove('segment-yellow', 'segment-green');
        progress.classList.add('segment-orange');
    }
    if (hasLength && hasSpecialChar && hasUppercase) {
        progress.style.width = '100%';
        progress.classList.remove('segment-yellow', 'segment-orange');
        progress.classList.add('segment-green');
    }
    if (!hasLength && !hasSpecialChar && !hasUppercase) {
        progress.style.width = '0';
        progress.classList.remove('segment-yellow', 'segment-orange', 'segment-green');
    }
});
}
