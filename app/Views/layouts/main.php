<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? esc($title) . ' - ' : '' ?>Absensi Siswa</title>
    
    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
    
    <!-- Material Symbols (Full Color) -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            user-select: none;
        }
        .material-symbols-outlined.outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
    
    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
    
    <?= $this->renderSection('styles') ?>
</head>
<body class="bg-gray-50 min-h-screen">
    
    <!-- Sidebar Overlay -->
    <div id="sidebarOverlay" class="sidebar-overlay" onclick="toggleSidebar()"></div>
    
    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar">
        <div class="flex flex-col h-full">
            <!-- Logo -->
            <div class="px-4 py-6 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="bg-gradient-to-br from-primary-500 to-primary-700 rounded-xl p-2 shadow-lg">
                        <span class="material-symbols-outlined text-white text-3xl">fingerprint</span>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-gray-900">Absensi Siswa</h1>
                        <p class="text-xs text-gray-500">Fingerprint System</p>
                    </div>
                </div>
            </div>
            
            <!-- User Info -->
            <div class="px-4 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center space-x-3">
                    <div class="bg-primary-100 rounded-full p-2">
                        <span class="material-symbols-outlined text-primary-600 text-2xl">account_circle</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate"><?= esc($user['name'] ?? 'User') ?></p>
                        <p class="text-xs text-gray-500 truncate"><?= esc($user['role'] ?? 'Role') ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto py-4">
                <?= $this->renderSection('sidebar') ?>
            </nav>
            
            <!-- Logout -->
            <div class="border-t border-gray-200 p-4">
                <a href="<?= base_url('logout') ?>" class="sidebar-item text-danger-600 hover:bg-danger-50 rounded-lg">
                    <span class="material-symbols-outlined mr-3">logout</span>
                    <span class="font-medium">Keluar</span>
                </a>
            </div>
        </div>
    </aside>
    
    <!-- Main Content -->
    <div class="ml-0 md:ml-64 min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-white border-b border-gray-200 sticky top-0 z-20">
            <div class="px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <!-- Mobile Hamburger Button -->
                        <button 
                            id="hamburgerBtn" 
                            class="md:hidden p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-all" 
                            onclick="toggleSidebar()"
                            aria-controls="sidebar"
                            aria-expanded="false"
                            aria-label="Toggle sidebar">
                            <span class="material-symbols-outlined text-2xl">menu</span>
                        </button>
                        
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900"><?= esc($pageTitle ?? 'Dashboard') ?></h2>
                            <p class="text-sm text-gray-500 mt-1"><?= esc($pageDescription ?? '') ?></p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <button class="relative p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-all">
                            <span class="material-symbols-outlined text-2xl">notifications</span>
                            <?php if (isset($unreadNotifications) && $unreadNotifications > 0): ?>
                            <span class="notification-badge"><?= $unreadNotifications ?></span>
                            <?php endif; ?>
                        </button>
                        
                        <!-- Time -->
                        <div class="hidden md:flex items-center space-x-2 text-gray-600">
                            <span class="material-symbols-outlined">schedule</span>
                            <span class="text-sm font-medium" id="currentTime"></span>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Page Content -->
        <main class="flex-1 p-6">
            <?= $this->renderSection('content') ?>
        </main>
        
        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 px-6 py-4">
            <div class="flex items-center justify-between text-sm text-gray-500">
                <p>&copy; <?= date('Y') ?> Absensi Siswa. All rights reserved.</p>
                <p>v1.0.0</p>
            </div>
        </footer>
    </div>
    
    <!-- Scripts -->
    <script>
        // Toggle sidebar for mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const hamburgerBtn = document.getElementById('hamburgerBtn');
            const isOpen = sidebar.classList.toggle('open');
            overlay.classList.toggle('active');
            
            // Update aria-expanded for accessibility
            if (hamburgerBtn) {
                hamburgerBtn.setAttribute('aria-expanded', isOpen);
            }
        }
        
        // Update current time
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', { 
                hour: '2-digit', 
                minute: '2-digit',
                second: '2-digit'
            });
            const dateString = now.toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            const timeElement = document.getElementById('currentTime');
            if (timeElement) {
                timeElement.textContent = timeString;
            }
        }
        updateTime();
        setInterval(updateTime, 1000);
        
        // Auto-hide alerts
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.classList.add('animate-fade-out');
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);
    </script>
    
    <?= $this->renderSection('scripts') ?>
</body>
</html>
