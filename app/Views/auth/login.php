<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Siswa - Login</title>

    <!-- Tailwind CSS -->
    <?php $cssPath = FCPATH . 'css/style.css'; ?>
    <link rel="stylesheet" href="<?= base_url('css/style.css' . (is_file($cssPath) ? '?v=' . filemtime($cssPath) : '')) ?>">

    <!-- Material Symbols -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body class="login-page">

    <div class="login-container">
        <!-- Background decoration -->
        <div class="background-gradient"></div>
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>

        <!-- Main content -->
        <div class="login-main-content">
            <!-- Header with logos -->
            <div class="login-header">
                <div style="display:flex;align-items:center;justify-content:center;gap:20px;margin-bottom:8px;">
                    <img src="<?= base_url('images/logo/logo-disdik.png') ?>" alt="Logo Disdik" style="height:72px;width:auto;object-fit:contain;">
                    <img src="<?= base_url('images/logo/logo-sekolah.png') ?>" alt="Logo Sekolah" style="height:72px;width:auto;object-fit:contain;border-radius:8px;">
                    <img src="<?= base_url('images/logo/logo-tutwuri.png') ?>" alt="Logo Tutwuri" style="height:72px;width:auto;object-fit:contain;">
                </div>
                <div class="title-section">
                    <h1>SI-HADIR</h1>
                    <p style="margin:4px 0 0;font-size:0.95rem;color:#6b7280;font-weight:500;">Sistem Informasi Habit dan Daftar Hadir Siswa</p>
                </div>
            </div>

            <!-- Login card -->
            <div class="login-card">
                <div class="welcome-section" style="text-align: left;">
                    <h2>Silahkan Masuk</h2>
                </div>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="login-alert login-alert-error">
                        <span class="material-symbols">error</span>
                        <span><?= session()->getFlashdata('error') ?></span>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="login-alert login-alert-success">
                        <span class="material-symbols">check_circle</span>
                        <span><?= session()->getFlashdata('success') ?></span>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('auth/login') ?>" method="POST" id="loginForm">
                    <?= csrf_field() ?>

                    <!-- Username field -->
                    <div class="login-form-group">
                        <label>Username</label>
                        <input
                            type="text"
                            name="username"
                            id="username"
                            placeholder="Masukan username"
                            required
                            autofocus />
                    </div>

                    <!-- Password field -->
                    <div class="login-form-group">
                        <label>Password</label>
                        <div class="password-wrapper">
                            <input
                                type="password"
                                name="password"
                                id="passwordInput"
                                placeholder="Masukan password"
                                required />
                            <button
                                type="button"
                                class="toggle-password"
                                onclick="togglePassword()">
                                <span class="material-symbols" id="eyeIcon">visibility</span>
                            </button>
                        </div>
                    </div>

                    <!-- Remember me & Forgot Password -->
                    <div class="remember-forgot-row">
                        <div class="remember-section">
                            <input
                                type="checkbox"
                                id="remember"
                                name="remember" />
                            <label for="remember">Ingat saya</label>
                        </div>
                        <a href="#" class="forgot-password">Lupa password?</a>
                    </div>

                    <!-- Login button -->
                    <button
                        type="submit"
                        class="btn-login"
                        id="loginButton">
                        Masuk
                    </button>
                </form>
            </div>

            <!-- Footer -->
            <p class="login-footer">
                © <?= date('Y') ?> Absensi Siswa. All rights reserved.
            </p>
        </div>
    </div>

    <script>
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('passwordInput');
            const eyeIcon = document.getElementById('eyeIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.textContent = 'visibility_off';
            } else {
                passwordInput.type = 'password';
                eyeIcon.textContent = 'visibility';
            }
        }

        // Auto-hide alerts
        setTimeout(() => {
            const alerts = document.querySelectorAll('.login-alert');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);
    </script>
</body>

</html>