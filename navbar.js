document.addEventListener('DOMContentLoaded', function () {
    const loginBtn = document.getElementById('loginBtn');
    const registerBtn = document.getElementById('registerBtn');
    const loginPopup = document.getElementById('login-popup');
    const registerPopup = document.getElementById('register-popup');
    const closeLoginBtn = document.getElementById('closePopup');
    const closeRegisterBtn = document.getElementById('closeRegisterPopup');
    const overlay = document.getElementById('overlay');
    loginBtn.addEventListener('click', function () {
        loginPopup.style.display = 'block';
        overlay.style.display = 'block';
        registerPopup.style.display='none';
    });
    function showModal(type) {
        document.getElementById('content').classList.add('blur');
        document.getElementById('modal-overlay').classList.add('active');
        if (type === 'login') {
            document.getElementById('login-modal').classList.add('active');
        } else if (type === 'register') {
            document.getElementById('register-modal').classList.add('active');
        }
    }
    
    function closeModal() {
        document.getElementById('content').classList.remove('blur');
        document.getElementById('modal-overlay').classList.remove('active');
        document.getElementById('login-modal').classList.remove('active');
        document.getElementById('register-modal').classList.remove('active');
    }

    registerBtn.addEventListener('click', function () {
        loginPopup.style.display = 'none';
        registerPopup.style.display = 'block';
        overlay.style.display = 'block';
    });
    closeLoginBtn.addEventListener('click', function () {
        loginPopup.style.display = 'none';
        overlay.style.display = 'none';
    });
    closeRegisterBtn.addEventListener('click', function () {
        registerPopup.style.display = 'none';
        overlay.style.display = 'none';
    });
    window.addEventListener('click', function (event) {
        if (event.target === overlay) {
            loginPopup.style.display = 'none';
            registerPopup.style.display = 'none';
            overlay.style.display = 'none';
        }
    });
});
