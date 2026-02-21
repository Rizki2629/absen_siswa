<?= $this->extend('layouts/main') ?>

<?= $this->section('sidebar') ?>
<?= $this->include('partials/sidebar_student') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<style>
    body {
        background:
            radial-gradient(circle at 12% 18%, rgba(99, 102, 241, 0.10), transparent 34%),
            radial-gradient(circle at 88% 24%, rgba(14, 165, 233, 0.10), transparent 30%),
            linear-gradient(180deg, #f8fafc 0%, #eef2ff 52%, #f8fafc 100%);
    }

    .adventure-bg {
        background: rgba(255, 255, 255, 0.72);
        border: 1px solid rgba(255, 255, 255, 0.9);
        border-radius: 24px;
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(6px);
    }

    .adventure-bg::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400" opacity="0.1"><path d="M100 100 L120 80 L140 100 Z" fill="%236366f1"/><circle cx="250" cy="150" r="20" fill="%230ea5e9"/><path d="M300 300 Q320 280 340 300" stroke="%238b5cf6" fill="none"/></svg>');
        opacity: 0.12;
        pointer-events: none;
    }

    .adventure-bg::after {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at 0% 100%, rgba(59, 130, 246, 0.10), transparent 28%),
            radial-gradient(circle at 100% 0%, rgba(139, 92, 246, 0.10), transparent 30%);
        pointer-events: none;
    }

    .poster-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid rgba(229, 231, 235, 0.9);
        box-shadow: 0 10px 22px rgba(15, 23, 42, 0.12);
        transition: all 0.3s ease;
        position: relative;
        display: flex;
        flex-direction: column;
        width: 100%;
        max-width: 100%;
        min-width: 0;
        margin: 0 auto;
    }

    .habits-grid {
        column-gap: 10px;
        row-gap: 12px;
    }

    .poster-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.18);
    }

    .poster-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        border-radius: 20px;
        background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
        margin: 0 auto;
        z-index: 1;
        transition: opacity 0.3s ease;
    }

    .poster-image.loading {
        opacity: 0;
    }

    .poster-image.loaded {
        opacity: 1;
    }

    .poster-image-wrap {
        position: relative;
        width: 100%;
        height: 310px;
        aspect-ratio: auto;
        background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
        border-radius: 20px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
    }

    .poster-image-wrap::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 50%, #cbd5e1 100%);
        background-size: 200% 200%;
        animation: shimmer 1.5s infinite;
        z-index: 0;
    }

    .poster-image-wrap.image-loaded::before {
        display: none;
    }

    @keyframes shimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }

    .poster-title-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 38%;
        background: linear-gradient(180deg, rgba(0, 0, 0, 0.80) 0%, rgba(0, 0, 0, 0.14) 55%, rgba(0, 0, 0, 0) 100%);
        z-index: 2;
        pointer-events: none;
    }

    .poster-title {
        position: absolute;
        top: 12px;
        left: 10px;
        right: 10px;
        color: #fff;
        font-size: 13px;
        font-weight: 800;
        text-shadow: 0 2px 6px rgba(0, 0, 0, 0.35);
        line-height: 1.2;
        z-index: 2;
    }

    .time-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(8px);
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 600;
        color: #555;
        display: flex;
        align-items: center;
        gap: 4px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        z-index: 2;
    }

    .poster-body {
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        background: transparent;
        padding: 16px 10px 10px;
        z-index: 3;
        display: flex;
        flex-direction: column;
        align-items: stretch;
        justify-content: flex-end;
    }

    .poster-desc {
        color: #6b7280;
        font-size: 11px;
        line-height: 1.4;
        margin-bottom: 8px;
        min-height: 28px;
    }

    .check-button {
        width: 100%;
        padding: 6px 8px;
        border: none;
        border-radius: 9999px;
        background-color: #7c3aed;
        color: #ffffff;
        font-weight: 700;
        font-size: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .check-button:hover {
        background-color: #6d28d9;
    }

    .check-button.check-button-completed {
        background-color: #5b21b6;
    }

    .check-button.check-button-completed:hover {
        background-color: #4c1d95;
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

    .habit-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.45);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 10000;
        padding: 16px;
    }

    .habit-modal {
        width: 100%;
        max-width: 520px;
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.3);
        padding: 16px;
    }

    .habit-modal-title {
        font-size: 18px;
        font-weight: 800;
        color: #111827;
        margin-bottom: 12px;
    }

    .habit-modal-field {
        margin-bottom: 10px;
    }

    .habit-modal-label {
        display: block;
        font-size: 13px;
        font-weight: 700;
        color: #374151;
        margin-bottom: 6px;
    }

    .habit-modal-input,
    .habit-modal-textarea {
        width: 100%;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        padding: 8px 10px;
        font-size: 13px;
        color: #111827;
        outline: none;
    }

    .habit-modal-textarea {
        min-height: 88px;
        resize: vertical;
    }

    .habit-modal-input:focus,
    .habit-modal-textarea:focus {
        border-color: #7c3aed;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.2);
    }

    .habit-modal-checklist {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
    }

    .habit-modal-check-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: #374151;
    }

    .habit-time-row {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .habit-time-separator {
        font-size: 18px;
        font-weight: 800;
        color: #4b5563;
    }

    .habit-time-select {
        flex: 1;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        padding: 8px 10px;
        font-size: 13px;
        color: #111827;
        background: #ffffff;
        outline: none;
    }

    .habit-time-select:focus {
        border-color: #7c3aed;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.2);
    }

    .habit-modal-actions {
        margin-top: 14px;
        display: flex;
        justify-content: flex-end;
        gap: 8px;
    }

    .habit-modal-btn {
        border: none;
        border-radius: 9999px;
        padding: 8px 14px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
    }

    .habit-modal-btn-cancel {
        background: #e5e7eb;
        color: #1f2937;
    }

    .habit-modal-btn-submit {
        background: #7c3aed;
        color: #ffffff;
    }

    .habit-action-row {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .check-button-status {
        flex: 1;
        background-color: #5b21b6;
        color: #ffffff;
        cursor: default;
    }

    .check-button-status:hover,
    .check-button-status:active {
        transform: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        background-color: #5b21b6;
    }

    .edit-button {
        border: none;
        border-radius: 9999px;
        padding: 6px 10px;
        height: 100%;
        background: #ede9fe;
        color: #5b21b6;
        font-size: 10px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .edit-button:hover {
        background: #ddd6fe;
    }

    .achievement-toggle-btn {
        padding: 8px 14px;
        border: none;
        border-radius: 9999px;
        background: #4f46e5;
        color: #ffffff;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 4px 10px rgba(79, 70, 229, 0.25);
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
    }

    .achievement-toggle-btn:hover {
        background: #4338ca;
    }

    .habit-date-input {
        border: 1px solid #c4b5fd;
        border-radius: 10px;
        padding: 6px 10px;
        background: #ffffff;
        color: #4c1d95;
        font-size: 12px;
        font-weight: 600;
        outline: none;
    }

    .habit-date-input:focus {
        border-color: #7c3aed;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.16);
    }
</style>

<div class="adventure-bg min-h-screen -m-3 md:-m-6 p-2 md:p-4 lg:p-3">

    <!-- Header with Character -->
    <header class="mb-2 md:mb-3">
        <div class="flex items-center justify-between mb-4">
            <div>
                <div>
                    <h1 class="text-lg md:text-2xl font-black text-gray-800 leading-tight">
                        Jurnal 7 Kebiasaan Anak Indonesia Hebat
                    </h1>
                    <p class="text-gray-600 text-xs md:text-sm mt-1" id="currentDate">Loading...</p>
                </div>
            </div>
        </div>

    </header>

    <div class="rounded-2xl border border-purple-300 bg-gradient-to-r from-purple-100 to-indigo-100 p-3 shadow-sm mb-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div class="flex items-center gap-2">
                <p class="text-sm font-semibold text-slate-700">Ringkasan Pencapaian</p>
                <input type="date" id="habitDateInput" class="habit-date-input" aria-label="Pilih tanggal kebiasaan">
            </div>
            <button type="button" id="achievementToggleBtn" onclick="toggleAchievementPanel()" class="achievement-toggle-btn self-start md:self-auto">
                Lihat Pencapaian
            </button>
        </div>

        <div id="achievementPanel" class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-3" style="display: none;">
            <div class="rounded-2xl border border-indigo-200 bg-gradient-to-r from-indigo-50 to-blue-50 p-4 shadow-sm">
                <p class="text-xs text-indigo-700 font-semibold mb-1">Status Lengkap Tanggal Dipilih</p>
                <p id="todayStatusText" class="text-lg font-extrabold text-indigo-900">0/7 selesai</p>
                <p id="todayProgressText" class="text-xs text-indigo-700 mt-1">Ayo selesaikan semua kebiasaan</p>
            </div>
            <div class="rounded-2xl border border-purple-200 bg-gradient-to-r from-purple-50 to-fuchsia-50 p-4 shadow-sm">
                <p class="text-xs text-purple-700 font-semibold mb-1">Pencapaian</p>
                <div class="flex items-center gap-4 text-sm">
                    <span class="font-bold text-purple-800">🔥 Streak: <span id="streakAmount">0</span></span>
                    <span class="font-bold text-fuchsia-700">⭐ XP: <span id="xpAmount">0</span></span>
                    <span class="font-bold text-amber-700">🏅 Badge: <span id="badgeCount">0</span></span>
                </div>
            </div>
            <div class="rounded-2xl border border-emerald-200 bg-gradient-to-r from-emerald-50 to-lime-50 p-4 shadow-sm">
                <p class="text-xs text-emerald-700 font-semibold mb-2">Reminder Jam Kebiasaan</p>
                <div id="reminderList" class="text-sm text-emerald-800">Belum ada reminder aktif</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-12 gap-6">

        <!-- Main Content: Habit Posters -->
        <div class="col-span-12">

            <!-- Habits Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-7 habits-grid" id="habitsGrid">
                <!-- Cards will be loaded by JavaScript -->
            </div>

        </div>

    </div>

</div>

<div id="habitQuestionModal" class="habit-modal-overlay">
    <div class="habit-modal">
        <h3 id="habitModalTitle" class="habit-modal-title">Isi detail check-in</h3>
        <div id="habitModalFields"></div>
        <div class="habit-modal-actions">
            <button type="button" class="habit-modal-btn habit-modal-btn-cancel" onclick="closeHabitQuestionModal()">Batal</button>
            <button type="button" class="habit-modal-btn habit-modal-btn-submit" onclick="submitHabitQuestionModal()">Simpan & Check-in</button>
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
            timeWindow: ['04:00', '06:00'],
            buttonColor: 'bg-blue-500',
            buttonText: 'Check-in & Raih Hadiah! 🎉',
            iconColor: 'text-blue-500'
        },
        {
            key: 'beribadah',
            title: 'Beribadah',
            description: 'Menjaga kedisiplinan spiritual setiap hari.',
            icon: 'auto_awesome',
            image: 'Beribadah.png',
            time: 'LIMA WAKTU',
            timeWindow: ['04:00', '21:30'],
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
            timeWindow: ['15:00', '18:00'],
            buttonColor: 'bg-green-500',
            buttonText: 'Check-in & Melompat! 🦘',
            iconColor: 'text-green-500'
        },
        {
            key: 'makan_sehat',
            title: 'Makan Bergizi',
            description: 'Pilih buah dan sayur untuk nutrisi otak.',
            icon: 'restaurant',
            image: 'Makan Bergizi.png',
            time: 'GIZI SEIMBANG',
            timeWindow: ['06:00', '19:00'],
            buttonColor: 'bg-yellow-500',
            buttonText: 'Check-in & Kuat! 💪',
            iconColor: 'text-yellow-600'
        },
        {
            key: 'gemar_belajar',
            title: 'Gemar Belajar',
            description: 'Ulas pelajaran hari ini dengan ceria.',
            icon: 'psychology',
            image: 'Gemar Belajar.png',
            time: 'REVIEW & TUGAS',
            timeWindow: ['18:30', '21:00'],
            buttonColor: 'bg-red-500',
            buttonText: 'Check-in & Jelajah! 🔍',
            iconColor: 'text-pink-500'
        },
        {
            key: 'bermasyarakat',
            title: 'Bermasyarakat',
            description: 'Berbicara baik pada teman atau keluarga.',
            icon: 'groups',
            image: 'Bermasyarakat.png',
            time: 'BANTU TEMAN',
            timeWindow: ['07:00', '20:00'],
            buttonColor: 'bg-orange-500',
            buttonText: 'Check-in & Teman! 🤝',
            iconColor: 'text-red-500'
        },
        {
            key: 'tidur_cepat',
            title: 'Tidur Cepat',
            description: 'Istirahat yang cukup untuk esok hari.',
            icon: 'bedtime',
            image: 'Tidur Cepat.png',
            time: 'MAKS 21:30',
            timeWindow: ['20:00', '21:30'],
            buttonColor: 'bg-indigo-600',
            buttonText: 'Check-in & Mimpi! 😴',
            iconColor: 'text-indigo-500'
        }
    ];

    let currentHabits = {};
    let currentHabitAnswers = {};
    let modalHabitKey = null;
    let modalMode = 'checkin';
    let selectedHabitDate = '';

    const habitQuestionConfigs = {
        bangun_pagi: {
            title: 'Bangun Pagi',
            fields: [{
                type: 'time',
                name: 'jam_bangun',
                label: 'Jam berapa kamu bangun pagi?',
                required: true
            }]
        },
        beribadah: {
            title: 'Beribadah',
            fields: [{
                    type: 'checkbox-group',
                    name: 'ibadah_wajib',
                    label: 'Centang ibadah yang sudah dilakukan',
                    required: true,
                    options: ['Subuh', 'Zuhur', 'Ashar', 'Magrib', 'Isya']
                },
                {
                    type: 'text',
                    name: 'ibadah_lainnya',
                    label: 'Ibadah lainnya (opsional)',
                    placeholder: 'Contoh: Tilawah, dzikir, doa, dll'
                }
            ]
        },
        berolahraga: {
            title: 'Berolahraga',
            fields: [{
                    type: 'text',
                    name: 'kegiatan_olahraga',
                    label: 'Kegiatan olahraganya apa?',
                    required: true,
                    placeholder: 'Contoh: Lari pagi, skipping, futsal'
                },
                {
                    type: 'text',
                    name: 'durasi_olahraga',
                    label: 'Berapa lama?',
                    required: true,
                    placeholder: 'Contoh: 30 menit'
                }
            ]
        },
        makan_sehat: {
            title: 'Makan Bergizi',
            fields: [{
                type: 'textarea',
                name: 'menu_makanan',
                label: 'Menu makanannya apa?',
                required: true,
                placeholder: 'Contoh: Nasi, sayur bayam, ikan, buah'
            }]
        },
        gemar_belajar: {
            title: 'Gemar Belajar',
            fields: [{
                type: 'textarea',
                name: 'materi_belajar',
                label: 'Apa yang kamu pelajari hari ini?',
                required: true,
                placeholder: 'Tuliskan ringkas pelajaran hari ini'
            }]
        },
        bermasyarakat: {
            title: 'Bermasyarakat',
            fields: [{
                type: 'textarea',
                name: 'kegiatan_masyarakat',
                label: 'Kegiatan masyarakat apa yang telah kamu lakukan?',
                required: true,
                placeholder: 'Contoh: Membantu tetangga, gotong royong'
            }]
        },
        tidur_cepat: {
            title: 'Tidur Cepat',
            fields: [{
                type: 'time',
                name: 'jam_tidur',
                label: 'Jam berapa kamu tidur?',
                required: true
            }]
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        initHabitDatePicker();
        updateDate();
        initAchievementPanel();
        loadHabits();
        loadStats();
    });

    function getTodayISODate() {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    function initHabitDatePicker() {
        const dateInput = document.getElementById('habitDateInput');
        if (!dateInput) return;

        const today = getTodayISODate();
        selectedHabitDate = today;
        dateInput.max = today;
        dateInput.value = today;

        dateInput.addEventListener('change', function() {
            const chosenDate = this.value || today;
            const safeDate = chosenDate > today ? today : chosenDate;

            if (safeDate !== this.value) {
                this.value = safeDate;
            }

            selectedHabitDate = safeDate;
            updateDate();
            loadHabits();
            loadStats();
        });
    }

    function initAchievementPanel() {
        const panel = document.getElementById('achievementPanel');
        const toggleBtn = document.getElementById('achievementToggleBtn');
        if (!panel || !toggleBtn) return;

        panel.style.display = 'none';
        toggleBtn.textContent = 'Lihat Pencapaian';
    }

    function toggleAchievementPanel() {
        const panel = document.getElementById('achievementPanel');
        const toggleBtn = document.getElementById('achievementToggleBtn');
        if (!panel || !toggleBtn) return;

        const isHidden = panel.style.display === 'none';
        panel.style.display = isHidden ? 'grid' : 'none';
        toggleBtn.textContent = isHidden ? 'Sembunyikan Pencapaian' : 'Lihat Pencapaian';
    }

    function getHabitAnswer(habitKey) {
        return currentHabitAnswers[habitKey] || {};
    }

    function setHabitAnswer(habitKey, answers) {
        currentHabitAnswers[habitKey] = answers;
    }

    function updateDate() {
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        const sourceDate = selectedHabitDate ? new Date(`${selectedHabitDate}T00:00:00`) : new Date();
        const dayName = days[sourceDate.getDay()];
        const day = sourceDate.getDate();
        const monthName = months[sourceDate.getMonth()];
        const year = sourceDate.getFullYear();

        document.getElementById('currentDate').textContent = `${dayName}, ${day} ${monthName} ${year}`;
    }

    async function loadHabits() {
        try {
            const response = await fetch(`<?= base_url('student/api/habits/today') ?>?date=${encodeURIComponent(selectedHabitDate)}`);
            const result = await response.json();

            if (result.success) {
                currentHabits = result.data.habit;
                currentHabitAnswers = result.data.answers || {};
                renderHabits(result.data.habit, result.data.stats);
                updateHabitInsights(result.data);
            }
        } catch (error) {
            console.error('Error loading habits:', error);
        }
    }

    async function loadStats() {
        try {
            const response = await fetch(`<?= base_url('student/api/habits/stats') ?>?date=${encodeURIComponent(selectedHabitDate)}`);
            const result = await response.json();

            if (result.success) {
                // Update sidebar stats if elements exist
                const perfectDaysEl = document.getElementById('perfectDays');
                const totalDaysEl = document.getElementById('totalDays');

                if (perfectDaysEl) perfectDaysEl.textContent = result.data.perfectDays;
                if (totalDaysEl) totalDaysEl.textContent = result.data.totalDays;

                if (result.data.today) {
                    updateProgress(result.data.today);
                }
                if (result.data.badges) {
                    updateBadgeSummary(result.data.badges);
                }
            }
        } catch (error) {
            console.error('Error loading stats:', error);
        }
    }

    function handleHabitClick(habitKey, isCompleted) {
        if (isCompleted) {
            return;
        }

        openHabitQuestionModal(habitKey, 'checkin');
    }

    function openHabitQuestionModal(habitKey, mode = 'checkin') {
        const config = habitQuestionConfigs[habitKey];
        if (!config) {
            toggleHabit(habitKey);
            return;
        }

        modalHabitKey = habitKey;
        modalMode = mode;
        const existingAnswers = getHabitAnswer(habitKey);

        const modal = document.getElementById('habitQuestionModal');
        const title = document.getElementById('habitModalTitle');
        const fields = document.getElementById('habitModalFields');

        title.textContent = mode === 'edit' ? `Edit ${config.title}` : `Check-in ${config.title}`;
        fields.innerHTML = config.fields.map((field) => renderQuestionField(field, existingAnswers)).join('');
        modal.style.display = 'flex';
    }

    function closeHabitQuestionModal() {
        const modal = document.getElementById('habitQuestionModal');
        modal.style.display = 'none';
        modalHabitKey = null;
        modalMode = 'checkin';
    }

    function renderQuestionField(field, existingAnswers = {}) {
        if (field.type === 'checkbox-group') {
            const selectedValues = Array.isArray(existingAnswers[field.name]) ? existingAnswers[field.name] : [];
            const optionsHtml = field.options.map((option) => `
                <label class="habit-modal-check-item">
                    <input type="checkbox" name="${field.name}" value="${option}" ${selectedValues.includes(option) ? 'checked' : ''}>
                    <span>${option}</span>
                </label>
            `).join('');

            return `
                <div class="habit-modal-field" data-required="${field.required ? '1' : '0'}" data-type="checkbox-group" data-name="${field.name}">
                    <label class="habit-modal-label">${field.label}</label>
                    <div class="habit-modal-checklist">${optionsHtml}</div>
                </div>
            `;
        }

        if (field.type === 'textarea') {
            const currentValue = (existingAnswers[field.name] || '').toString();
            return `
                <div class="habit-modal-field">
                    <label class="habit-modal-label" for="field_${field.name}">${field.label}</label>
                    <textarea id="field_${field.name}" class="habit-modal-textarea" name="${field.name}" ${field.required ? 'required' : ''} placeholder="${field.placeholder || ''}">${currentValue}</textarea>
                </div>
            `;
        }

        if (field.type === 'time') {
            const currentValue = (existingAnswers[field.name] || '').toString();
            const [selectedHour = '', selectedMinute = ''] = currentValue.split(':');
            const hourOptions = Array.from({
                length: 24
            }, (_, idx) => String(idx).padStart(2, '0'));
            const minuteOptions = Array.from({
                length: 12
            }, (_, idx) => String(idx * 5).padStart(2, '0'));

            const hourHtml = hourOptions.map((hour) => `<option value="${hour}" ${selectedHour === hour ? 'selected' : ''}>${hour}</option>`).join('');
            const minuteHtml = minuteOptions.map((minute) => `<option value="${minute}" ${selectedMinute === minute ? 'selected' : ''}>${minute}</option>`).join('');

            return `
                <div class="habit-modal-field">
                    <label class="habit-modal-label" for="field_${field.name}_hour">${field.label}</label>
                    <div class="habit-time-row">
                        <select id="field_${field.name}_hour" class="habit-time-select" name="${field.name}_hour" ${field.required ? 'required' : ''}>
                            <option value="">Jam</option>
                            ${hourHtml}
                        </select>
                        <span class="habit-time-separator">:</span>
                        <select id="field_${field.name}_minute" class="habit-time-select" name="${field.name}_minute" ${field.required ? 'required' : ''}>
                            <option value="">Menit</option>
                            ${minuteHtml}
                        </select>
                    </div>
                </div>
            `;
        }

        const inputType = 'text';
        const currentValue = (existingAnswers[field.name] || '').toString();
        return `
            <div class="habit-modal-field">
                <label class="habit-modal-label" for="field_${field.name}">${field.label}</label>
                <input id="field_${field.name}" class="habit-modal-input" type="${inputType}" name="${field.name}" value="${currentValue}" ${field.required ? 'required' : ''} placeholder="${field.placeholder || ''}">
            </div>
        `;
    }

    function submitHabitQuestionModal() {
        if (!modalHabitKey) return;

        const config = habitQuestionConfigs[modalHabitKey];
        const answers = {};

        for (const field of config.fields) {
            if (field.type === 'checkbox-group') {
                const checked = Array.from(document.querySelectorAll(`input[name="${field.name}"]:checked`)).map((el) => el.value);
                if (field.required && checked.length === 0) {
                    alert(`Mohon isi: ${field.label}`);
                    return;
                }
                answers[field.name] = checked;
                continue;
            }

            if (field.type === 'time') {
                const hourInput = document.querySelector(`[name="${field.name}_hour"]`);
                const minuteInput = document.querySelector(`[name="${field.name}_minute"]`);
                const hourValue = hourInput ? hourInput.value : '';
                const minuteValue = minuteInput ? minuteInput.value : '';

                if (field.required && (!hourValue || !minuteValue)) {
                    alert(`Mohon isi: ${field.label}`);
                    if (hourInput && !hourValue) {
                        hourInput.focus();
                    } else if (minuteInput) {
                        minuteInput.focus();
                    }
                    return;
                }

                answers[field.name] = hourValue && minuteValue ? `${hourValue}:${minuteValue}` : '';
                continue;
            }

            const input = document.querySelector(`[name="${field.name}"]`);
            const value = input ? input.value.trim() : '';

            if (field.required && !value) {
                alert(`Mohon isi: ${field.label}`);
                if (input) input.focus();
                return;
            }

            answers[field.name] = value;
        }

        setHabitAnswer(modalHabitKey, answers);

        const habitKey = modalHabitKey;
        const mode = modalMode;
        closeHabitQuestionModal();

        if (mode === 'edit') {
            toggleHabit(habitKey, answers, true);
            return;
        }

        toggleHabit(habitKey, answers);
    }

    function renderHabits(habit, stats) {
        const grid = document.getElementById('habitsGrid');
        grid.innerHTML = '';

        habitDefinitions.forEach(def => {
            const isCompleted = habit[def.key] == 1;
            const buttonClass = isCompleted ? 'check-button-completed' : '';
            const buttonIcon = isCompleted ? 'check_circle' : 'radio_button_unchecked';
            const buttonText = isCompleted ? 'Sudah Check-in' : 'Check-in';
            const buttonAction = `onclick="handleHabitClick('${def.key}', ${isCompleted ? 'true' : 'false'})"`;

            const card = document.createElement('div');
            card.className = 'poster-card';

            card.innerHTML = `
                <div class="poster-image-wrap">
                    <img src="<?= base_url('images/habits/') ?>${def.image}" 
                         alt="${def.title}" 
                         class="poster-image loading"
                         loading="lazy"
                         onload="this.classList.remove('loading'); this.classList.add('loaded'); this.parentElement.classList.add('image-loaded');"
                         onerror="this.src='<?= base_url('images/habits/placeholder.png') ?>'; this.classList.add('loaded');">

                    <div class="poster-title-overlay"></div>
                    <div class="poster-title">${def.title}</div>
                    ${(def.key === 'bangun_pagi' || def.key === 'tidur_cepat') ? `
                    <div class="time-badge">
                        <span class="material-symbols text-[14px]">schedule</span>
                        ${def.time}
                    </div>
                    ` : ''}

                    ${isCompleted ? `
                    <div class="absolute bottom-3 right-3 bg-white rounded-full p-2 shadow-lg">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    ` : ''}
                </div>

                <div class="poster-body">
                    ${isCompleted ? `
                    <div class="habit-action-row">
                        <button type="button" class="check-button check-button-status">
                            <span class="material-symbols text-base">${buttonIcon}</span>
                            <span>${buttonText}</span>
                        </button>
                        <button type="button" class="edit-button" onclick="openHabitQuestionModal('${def.key}', 'edit')">
                            <span class="material-symbols text-sm">edit</span>
                            <span>Edit</span>
                        </button>
                    </div>
                    ` : `
                    <button ${buttonAction}
                            class="check-button ${buttonClass}">
                        <span class="material-symbols text-base">
                            ${buttonIcon}
                        </span>
                        <span>${buttonText}</span>
                    </button>
                    `}
                </div>
            `;

            grid.appendChild(card);
        });

        updateProgress(stats);
        updateSidebarStats(stats);
    }

    function updateProgress(stats) {
        const percentage = Math.round((stats.completed / stats.total) * 100);

        const completedCountEl = document.getElementById('completedCount');
        const totalCountEl = document.getElementById('totalCount');
        const progressBarEl = document.getElementById('progressBar');
        const progressTextEl = document.getElementById('progressText');
        const xpAmountEl = document.getElementById('xpAmount');
        const streakAmountEl = document.getElementById('streakAmount');
        const todayStatusTextEl = document.getElementById('todayStatusText');
        const todayProgressTextEl = document.getElementById('todayProgressText');

        if (completedCountEl) completedCountEl.textContent = stats.completed;
        if (totalCountEl) totalCountEl.textContent = stats.total;
        if (progressBarEl) progressBarEl.style.width = percentage + '%';
        if (progressTextEl) progressTextEl.textContent = percentage + '%';
        if (xpAmountEl) xpAmountEl.textContent = stats.xp;
        if (streakAmountEl) streakAmountEl.textContent = stats.streak;
        if (todayStatusTextEl) todayStatusTextEl.textContent = stats.today_status || `${stats.completed}/${stats.total} selesai`;
        if (todayProgressTextEl) {
            todayProgressTextEl.textContent = stats.is_perfect_today ? 'Keren! Semua kebiasaan lengkap hari ini 🎉' : `Progress ${percentage}%`;
        }
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

    async function toggleHabit(habitKey, answers = {}, updateOnly = false) {
        try {
            const response = await fetch('<?= base_url('student/api/habits/toggle') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    habit: habitKey,
                    answers: answers,
                    update_only: updateOnly,
                    date: selectedHabitDate
                })
            });

            const result = await response.json();

            if (result.success) {
                currentHabits = result.data.habit;
                currentHabitAnswers = result.data.answers || currentHabitAnswers;
                const stats = result.data.stats || {
                    completed: result.data.completed,
                    total: 7,
                    xp: result.data.xp,
                    streak: 0,
                    today_status: `${result.data.completed}/7 selesai`,
                    is_perfect_today: result.data.completed === 7,
                };
                renderHabits(result.data.habit, stats);
                updateHabitInsights(result.data);

                // Reload stats
                loadStats();
            }
        } catch (error) {
            console.error('Error toggling habit:', error);
        }
    }

    function updateHabitInsights(data) {
        if (data.badges) {
            updateBadgeSummary(data.badges);
        }
        if (Array.isArray(data.reminders)) {
            renderReminders(data.reminders);
        }
    }

    function updateBadgeSummary(badges) {
        const badgeCountEl = document.getElementById('badgeCount');
        if (badgeCountEl) {
            badgeCountEl.textContent = `${badges.earned_count}/${badges.total}`;
        }
    }

    function renderReminders(reminders) {
        const reminderEl = document.getElementById('reminderList');
        if (!reminderEl) return;

        if (!reminders.length) {
            reminderEl.textContent = 'Belum ada reminder aktif';
            return;
        }

        reminderEl.innerHTML = reminders.map((reminder) => `
            <div class="mb-1 text-xs md:text-sm text-primary-700 font-semibold">
                ${reminder.habit_label} (${reminder.start_time}-${reminder.end_time})
            </div>
        `).join('');
    }
</script>

<?= $this->endSection() ?>