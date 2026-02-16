<?= $this->extend('layouts/main') ?>

<?= $this->section('sidebar') ?>
<?= $this->include('partials/sidebar_student') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<style>
    body {
        background: linear-gradient(135deg, #89CFF0 0%, #B0E0E6 100%);
    }

    .adventure-bg {
        background: linear-gradient(135deg, #89CFF0 0%, #B0E0E6 100%);
        position: relative;
    }

    .adventure-bg::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400" opacity="0.1"><path d="M100 100 L120 80 L140 100 Z" fill="%23000"/><circle cx="250" cy="150" r="20" fill="%23000"/><path d="M300 300 Q320 280 340 300" stroke="%23000" fill="none"/></svg>');
        opacity: 0.05;
        pointer-events: none;
    }

    .poster-card {
        background: white;
        border-radius: 24px;
        overflow: hidden;
        border: 1px solid rgba(229, 231, 235, 0.9);
        box-shadow: 0 10px 22px rgba(15, 23, 42, 0.12);
        transition: all 0.3s ease;
        position: relative;
        display: flex;
        flex-direction: column;
        height: 400px;
        min-width: 24px;
        max-width: 75%;
    }

    .poster-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.18);
    }

    .poster-image {
        width: 100%;
        height: 340px;
        object-fit: contain;
        display: block;
        border-radius: 24px;
        background: #e2e8f0;
        margin: 0 auto;
        z-index: 1;
    }

    .poster-image-wrap {
        position: relative;
        width: 100%;
        min-height: 420px;
        background: #e2e8f0;
        border-radius: 24px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
    }

    .poster-title {
        position: absolute;
        top: 24px;
        left: 18px;
        right: 18px;
        color: #fff;
        font-size: 18px;
        font-weight: 800;
        text-shadow: 0 2px 6px rgba(0, 0, 0, 0.35);
        line-height: 1.2;
        z-index: 2;
    }

    .time-badge {
        position: absolute;
        top: 24px;
        right: 18px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(8px);
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        color: #555;
        display: flex;
        align-items: center;
        gap: 4px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        z-index: 2;
    }
    }

    .poster-body {
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(0deg, rgba(255, 255, 255, 0.95) 80%, rgba(255, 255, 255, 0.7) 100%, rgba(255, 255, 255, 0.0) 100%);
        padding: 32px 24px 24px;
        z-index: 3;
        display: flex;
        flex-direction: column;
        align-items: stretch;
        justify-content: flex-end;
    }

    .poster-desc {
        color: #6b7280;
        font-size: 12px;
        line-height: 1.4;
        margin-bottom: 10px;
        min-height: 34px;
    }

    .check-button {
        width: 100%;
        padding: 8px 12px;
        border: none;
        border-radius: 9999px;
        font-weight: 700;
        font-size: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .check-button:hover {
        transform: scale(1.02);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
    }

    .check-button:active {
        transform: scale(0.98);
    }

    .sidebar-achievement {
        background: white;
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    }

    .badge-item {
        background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
        border-radius: 16px;
        padding: 16px;
        text-align: center;
        box-shadow: 0 4px 12px rgba(255, 165, 0, 0.3);
    }

    .progress-bar-custom {
        height: 24px;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 12px;
        overflow: hidden;
        position: relative;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #FFD700 0%, #FFA500 100%);
        transition: width 1s ease;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding-right: 8px;
        font-weight: 700;
        font-size: 11px;
        color: white;
    }
</style>

<div class="adventure-bg min-h-screen -m-6 p-6">

    <!-- Header with Character -->
    <header class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-4">
                <div class="text-6xl">🏴‍☠️</div>
                <div>
                    <h1 class="text-3xl font-black text-gray-800">
                        Jelajahi Hari-Hari Penuh Petualangan
                    </h1>
                    <p class="text-gray-600 text-sm mt-1" id="currentDate">Loading...</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl px-6 py-3 shadow-lg">
                <div class="text-center">
                    <div class="text-3xl mb-1">🧭</div>
                    <div class="text-sm font-bold text-gray-700">Petualang Cilik Rio</div>
                </div>
            </div>
        </div>

        <!-- Stats Bar -->
        <div class="bg-white rounded-2xl p-4 shadow-lg">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-black text-blue-600"><span id="completedCount">0</span>/<span id="totalCount">7</span></div>
                    <div class="text-xs text-gray-600 mt-1">Kebiasaan</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-black text-yellow-600" id="xpAmount">0</div>
                    <div class="text-xs text-gray-600 mt-1">XP Hari Ini</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-black text-green-600"><span id="streakAmount">0</span> 🔥</div>
                    <div class="text-xs text-gray-600 mt-1">Hari Berturut</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-black text-purple-600" id="progressText">0%</div>
                    <div class="text-xs text-gray-600 mt-1">Progress</div>
                </div>
            </div>
            <!-- Progress Bar -->
            <div class="mt-4 h-3 bg-gray-200 rounded-full overflow-hidden">
                <div id="progressBar" class="h-full bg-gradient-to-r from-blue-500 to-purple-500 transition-all duration-500" style="width: 0%"></div>
            </div>
        </div>
    </header>

    <div class="grid grid-cols-12 gap-6">

        <!-- Main Content: Habit Posters -->
        <div class="col-span-12 lg:col-span-9">

            <!-- Habits Grid -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5" id="habitsGrid">
                <!-- Cards will be loaded by JavaScript -->
            </div>

        </div>

        <!-- Sidebar: Achievements -->
        <div class="col-span-12 lg:col-span-3">

            <div class="sidebar-achievement mb-6">
                <div class="flex items-center gap-2 mb-4">
                    <div class="text-2xl">🧭</div>
                    <h3 class="font-black text-lg text-gray-800">Petualangan Ku</h3>
                </div>

                <div class="bg-yellow-50 rounded-xl p-4 mb-4 border-2 border-yellow-200">
                    <div class="text-sm font-bold text-gray-700 mb-2">Poin Petualang</div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl font-black text-yellow-600" id="sidebarXP">0</span>
                        <span class="text-sm text-gray-500">/ 500</span>
                    </div>
                    <div class="progress-bar-custom mt-3">
                        <div class="progress-fill" id="sidebarProgress" style="width: 0%"></div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="text-sm font-bold text-gray-700 mb-3">Lencana yang Diraih</div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-yellow-100 rounded-xl p-3 text-center border-2 border-yellow-300">
                            <div class="text-3xl mb-1">🌤️</div>
                            <div class="text-xs font-bold text-gray-700">Early Bird</div>
                        </div>
                        <div class="bg-red-100 rounded-xl p-3 text-center border-2 border-red-300">
                            <div class="text-3xl mb-1">❤️</div>
                            <div class="text-xs font-bold text-gray-700">Super Healthy</div>
                        </div>
                        <div class="bg-purple-100 rounded-xl p-3 text-center border-2 border-purple-300">
                            <div class="text-3xl mb-1">⭐</div>
                            <div class="text-xs font-bold text-gray-700">Learning Star</div>
                        </div>
                        <div class="bg-blue-100 rounded-xl p-3 text-center border-2 border-blue-300">
                            <div class="text-3xl mb-1">🦸</div>
                            <div class="text-xs font-bold text-gray-700">Social Hero</div>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl p-4 text-white">
                    <div class="text-sm font-bold mb-3">Tantangan Minggu Ini</div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs">Check-in 7 hari berturut-turut</span>
                        <span class="text-2xl">✅</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs">Raih 500 Poin</span>
                        <span class="text-2xl">💰</span>
                    </div>
                </div>

            </div>

        </div>

    </div>

</div>

<script>
    // Habit definitions with poster-style properties
    const habitDefinitions = [{
            key: 'bangun_pagi',
            title: 'Bangun Pagi',
            description: 'Mulai hari dengan segar dan tepat waktu.',
            icon: 'wb_sunny',
            image: 'Bagun pagi.png',
            time: '04:00 - 06:00',
            buttonColor: 'bg-blue-500',
            buttonText: 'Check-in & Raih Hadiah! 🎉',
            iconColor: 'text-blue-500'
        },
        {
            key: 'beribadah',
            title: 'Ibadah Tepat Waktu',
            description: 'Menjaga kedisiplinan spiritual setiap hari.',
            icon: 'auto_awesome',
            image: 'Beribadah.png',
            time: 'LIMA WAKTU',
            buttonColor: 'bg-purple-600',
            buttonText: 'Check-in & Bersinar! ✨',
            iconColor: 'text-purple-500'
        },
        {
            key: 'berolahraga',
            title: 'Olahraga',
            description: 'Tubuh sehat, jiwa kuat dan berenergi.',
            icon: 'fitness_center',
            image: 'Berolahraga.png',
            time: '15-30 MENIT',
            buttonColor: 'bg-green-500',
            buttonText: 'Check-in & Melompat! 🦘',
            iconColor: 'text-green-500'
        },
        {
            key: 'makan_sehat',
            title: 'Makan Sehat',
            description: 'Pilih buah dan sayur untuk nutrisi otak.',
            icon: 'restaurant',
            image: 'Makan Bergizi.png',
            time: 'GIZI SEIMBANG',
            buttonColor: 'bg-yellow-500',
            buttonText: 'Check-in & Kuat! 💪',
            iconColor: 'text-yellow-600'
        },
        {
            key: 'gemar_belajar',
            title: 'Semangat Belajar',
            description: 'Ulas pelajaran hari ini dengan ceria.',
            icon: 'psychology',
            image: 'Gemar Belajar.png',
            time: 'REVIEW & TUGAS',
            buttonColor: 'bg-red-500',
            buttonText: 'Check-in & Jelajah! 🔍',
            iconColor: 'text-pink-500'
        },
        {
            key: 'bermasyarakat',
            title: 'Interaksi Sosial',
            description: 'Berbicara baik pada teman atau keluarga.',
            icon: 'groups',
            image: 'Bermasyarakat.png',
            time: 'BANTU TEMAN',
            buttonColor: 'bg-orange-500',
            buttonText: 'Check-in & Teman! 🤝',
            iconColor: 'text-red-500'
        },
        {
            key: 'tidur_cepat',
            title: 'Tidur Awal',
            description: 'Istirahat yang cukup untuk esok hari.',
            icon: 'bedtime',
            image: 'Tidur Cepat.png',
            time: 'MAKS 21:30',
            buttonColor: 'bg-indigo-600',
            buttonText: 'Check-in & Mimpi! 😴',
            iconColor: 'text-indigo-500'
        }
    ];

    let currentHabits = {};

    document.addEventListener('DOMContentLoaded', function() {
        updateDate();
        loadHabits();
        loadStats();
    });

    function updateDate() {
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        const now = new Date();
        const dayName = days[now.getDay()];
        const day = now.getDate();
        const monthName = months[now.getMonth()];
        const year = now.getFullYear();

        document.getElementById('currentDate').textContent = `${dayName}, ${day} ${monthName} ${year}`;
    }

    async function loadHabits() {
        try {
            const response = await fetch('<?= base_url('student/api/habits/today') ?>');
            const result = await response.json();

            if (result.success) {
                currentHabits = result.data.habit;
                renderHabits(result.data.habit, result.data.stats);
            }
        } catch (error) {
            console.error('Error loading habits:', error);
        }
    }

    async function loadStats() {
        try {
            const response = await fetch('<?= base_url('student/api/habits/stats') ?>');
            const result = await response.json();

            if (result.success) {
                // Update sidebar stats if elements exist
                const perfectDaysEl = document.getElementById('perfectDays');
                const totalDaysEl = document.getElementById('totalDays');

                if (perfectDaysEl) perfectDaysEl.textContent = result.data.perfectDays;
                if (totalDaysEl) totalDaysEl.textContent = result.data.totalDays;
            }
        } catch (error) {
            console.error('Error loading stats:', error);
        }
    }

    function renderHabits(habit, stats) {
        const grid = document.getElementById('habitsGrid');
        grid.innerHTML = '';

        habitDefinitions.forEach(def => {
            const isCompleted = habit[def.key] == 1;

            const card = document.createElement('div');
            card.className = 'poster-card';

            card.innerHTML = `
                <div class="poster-image-wrap">
                    <img src="<?= base_url('images/habits/') ?>${def.image}" 
                         alt="${def.title}" 
                         class="poster-image"
                         onerror="this.src='<?= base_url('images/habits/placeholder.png') ?>';">

                    <div class="poster-title">${def.title}</div>
                    <div class="time-badge">
                        <span class="material-symbols-outlined text-[14px]">schedule</span>
                        ${def.time}
                    </div>

                    ${isCompleted ? `
                    <div class="absolute bottom-3 right-3 bg-white rounded-full p-2 shadow-lg">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    ` : ''}
                </div>

                <div class="poster-body">
                    <p class="poster-desc">${def.description}</p>
                    <button onclick="toggleHabit('${def.key}')" 
                            class="check-button ${isCompleted ? 'bg-green-500 hover:bg-green-600' : def.buttonColor + ' hover:opacity-90'}">
                        <span class="material-symbols-outlined text-base">
                            ${isCompleted ? 'check_circle' : 'radio_button_unchecked'}
                        </span>
                        <span>${isCompleted ? 'Sudah Check-in' : def.buttonText}</span>
                    </button>
                </div>
            `;

            grid.appendChild(card);
        });

        updateProgress(stats);
        updateSidebarStats(stats);
    }

    function updateProgress(stats) {
        const percentage = Math.round((stats.completed / stats.total) * 100);

        document.getElementById('completedCount').textContent = stats.completed;
        document.getElementById('totalCount').textContent = stats.total;
        document.getElementById('progressBar').style.width = percentage + '%';
        document.getElementById('progressText').textContent = percentage + '%';
        document.getElementById('xpAmount').textContent = stats.xp;
        document.getElementById('streakAmount').textContent = stats.streak;
    }

    function updateSidebarStats(stats) {
        // Update sidebar XP and progress if elements exist
        const sidebarXP = document.getElementById('sidebarXP');
        const sidebarProgress = document.getElementById('sidebarProgress');

        if (sidebarXP) {
            sidebarXP.textContent = stats.xp || 0;
        }

        if (sidebarProgress) {
            const percentage = Math.round((stats.completed / stats.total) * 100);
            sidebarProgress.style.width = percentage + '%';
        }
    }

    async function toggleHabit(habitKey) {
        try {
            const response = await fetch('<?= base_url('student/api/habits/toggle') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    habit: habitKey
                })
            });

            const result = await response.json();

            if (result.success) {
                currentHabits = result.data.habit;
                const stats = {
                    completed: result.data.completed,
                    total: 7,
                    xp: result.data.xp,
                    streak: document.getElementById('streakAmount').textContent // Keep current streak
                };
                renderHabits(result.data.habit, stats);

                // Reload stats
                loadStats();

                // Show celebration animation if all completed
                if (result.data.completed === 7) {
                    showCelebration();
                }
            }
        } catch (error) {
            console.error('Error toggling habit:', error);
        }
    }

    function showCelebration() {
        // Confetti celebration effect when all habits completed
        const duration = 3000;
        const animationEnd = Date.now() + duration;
        const defaults = {
            startVelocity: 30,
            spread: 360,
            ticks: 60,
            zIndex: 9999
        };

        function randomInRange(min, max) {
            return Math.random() * (max - min) + min;
        }

        const interval = setInterval(function() {
            const timeLeft = animationEnd - Date.now();

            if (timeLeft <= 0) {
                return clearInterval(interval);
            }

            const particleCount = 50 * (timeLeft / duration);

            // Create confetti particles
            for (let i = 0; i < particleCount; i++) {
                const confetti = document.createElement('div');
                confetti.className = 'animate-ping';
                confetti.style.cssText = `
                    position: fixed;
                    left: ${Math.random() * 100}%;
                    top: ${Math.random() * 100}%;
                    width: 10px;
                    height: 10px;
                    background: ${['#ff0000', '#00ff00', '#0000ff', '#ffff00', '#ff00ff'][Math.floor(Math.random() * 5)]};
                    border-radius: 50%;
                    pointer-events: none;
                    z-index: 9999;
                `;
                document.body.appendChild(confetti);

                setTimeout(() => {
                    confetti.remove();
                }, 1000);
            }
        }, 250);

        // Show success message
        const message = document.createElement('div');
        message.className = 'fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl shadow-2xl p-8 text-center z-[10000] animate-bounce';
        message.innerHTML = `
            <div class="text-6xl mb-4">🎉🏆🎉</div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Luar Biasa!</h2>
            <p class="text-gray-600">Kamu telah menyelesaikan semua kebiasaan baik hari ini!</p>
            <div class="mt-4 text-4xl font-bold text-yellow-500">+140 XP</div>
        `;
        document.body.appendChild(message);

        setTimeout(() => {
            message.remove();
        }, 4000);
    }
</script>

<?= $this->endSection() ?>