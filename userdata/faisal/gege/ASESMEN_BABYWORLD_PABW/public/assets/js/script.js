const container = document.getElementById('login-container');

// ambil tombol toggle yang BENAR
const registerBtn = document.getElementById('toggleRegister');
const loginBtn = document.getElementById('toggleLogin');

registerBtn.addEventListener('click', () => {
    container.classList.add("active");
});

loginBtn.addEventListener('click', () => {
    container.classList.remove("active");
});
