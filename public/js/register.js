// Select the required elements
const loginContainer = document.getElementById('loginContainer');
const switchToRegister = document.getElementById('switchToRegister');
const switchToLogin = document.getElementById('switchToLogin');

// Add event listeners for the transitions
switchToRegister.addEventListener('click', () => {
  loginContainer.classList.add('active');
});

switchToLogin.addEventListener('click', () => {
  loginContainer.classList.remove('active');
});
