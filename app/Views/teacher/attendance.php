<?= $this->extend('layouts/main') ?>

<?= $this->section('sidebar') ?>
<?= $this->include('partials/sidebar_teacher') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
$classes = $classes ?? [];
$singleClass = count($classes) === 1 ? $classes[0] : null;
$defaultClassId = $singleClass ? $singleClass['id'] : '';
$defaultClassName = $singleClass ? $singleClass['name'] : '';
?>

<!-- Header -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900">Daftar Hadir</h2>
    <p class="text-gray-600 mt-1">
        <?php if ($singleClass): ?>
            Kelas <?= esc($singleClass['name']) ?>
        <?php else: ?>
            Input kehadiran siswa
        <?php endif; ?>
    </p>
</div>

<?php if (empty($classes)): ?>
<!-- No Class Assigned -->
<div class="bg-white rounded-2xl shadow p-12 text-center">
    <span class="material-symbols-outlined text-6xl text-gray-300 mb-4">school</span>
    <p class="text-gray-500 text-lg">Anda belum ditetapkan sebagai wali kelas</p>
    <p class="text-gray-400 text-sm mt-2">Hubungi administrator untuk mengatur kelas Anda</p>
</div>
<?php else: ?>

<!-- Filter Form -->
<div class="bg-white rounded-2xl shadow p-6 mb-6">
    <div class="grid grid-cols-1 <?= $singleClass ? 'md:grid-cols-3' : 'md:grid-cols-4' ?> gap-4">

        <?php if (!$singleClass): ?>
        <!-- Kelas dropdown (multiple classes) -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <span class="material-symbols-outlined text-sm align-middle">class</span>
                Kelas
            </label>
            <select id="classId" onchange="tryAutoLoad()"
                class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <option value="">Pilih Kelas</option>
                <?php foreach ($classes as $cls): ?>
                    <option value="<?= esc($cls['id']) ?>"><?= esc($cls['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php else: ?>
        <!-- Hidden input for single class -->
        <input type="hidden" id="classId" value="<?= esc($singleClass['id']) ?>">
        <?php endif; ?>

        <!-- Tanggal -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <span class="material-symbols-outlined text-sm align-middle">calendar_today</span>
                Tanggal
            </label>
            <input type="date" id="date" onchange="tryAutoLoad()"
                class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                value="<?= date('Y-m-d') ?>">
        </div>

        <!-- Shift -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <span class="material-symbols-outlined text-sm align-middle">schedule</span>
                Shift
            </label>
            <select id="shiftId" onchange="tryAutoLoad()"
                class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <option value="">Pilih Shift</option>
            </select>
        </div>

        <?php if (!$singleClass): ?>
        <!-- Button only shown when multiple classes -->
        <div class="flex items-end">
            <button onclick="loadAttendance()"
                class="w-full px-4 py-2 bg-primary-600 text-white rounded-xl hover:bg-primary-700 transition-colors font-medium">
                <span class="material-symbols-outlined text-sm align-middle mr-1">search</span>
                Tampilkan
            </button>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Attendance List -->
<div id="attendanceContainer" class="hidden">
    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <div class="px-6 py-4 bg-primary-600 text-white flex items-center justify-between">
            <h3 class="text-lg font-bold flex items-center">
                <span class="material-symbols-outlined mr-2">how_to_reg</span>
                Daftar Siswa
            </h3>
            <button onclick="saveAttendance()"
                class="px-4 py-2 bg-white text-primary-600 rounded-xl hover:bg-primary-50 transition-colors font-medium">
                <span class="material-symbols-outlined text-sm align-middle mr-1">save</span>
                Simpan
            </button>
        </div>

        <div class="p-6">
            <div id="attendanceList"></div>
        </div>
    </div>
</div>

<!-- Empty State -->
<div id="emptyState" class="bg-white rounded-2xl shadow p-12 text-center">
    <span class="material-symbols-outlined text-6xl text-gray-300 mb-4">person_search</span>
    <p class="text-gray-500"><?= $singleClass ? 'Pilih shift untuk menampilkan daftar siswa' : 'Pilih kelas dan tanggal untuk menampilkan daftar siswa' ?></p>
</div>

<?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        loadShifts();
    });

    function tryAutoLoad() {
        const classId = document.getElementById('classId').value;
        const date = document.getElementById('date').value;
        const shiftId = document.getElementById('shiftId').value;
        if (classId && date && shiftId) {
            loadAttendance();
        }
    }

    async function loadShifts() {
        try {
            const response = await fetch('<?= base_url('api/shifts') ?>');
            const result = await response.json();

            const select = document.getElementById('shiftId');
            select.innerHTML = '<option value="">Pilih Shift</option>';

            result.data.forEach(shift => {
                const option = document.createElement('option');
                option.value = shift.id;
                option.textContent = `${shift.name} (${shift.start_time} - ${shift.end_time})`;
                select.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading shifts:', error);
        }
    }

    async function loadAttendance() {
        const classId = document.getElementById('classId').value;
        const date = document.getElementById('date').value;
        const shiftId = document.getElementById('shiftId').value;

        if (!classId || !date || !shiftId) return;

        try {
            const response = await fetch(`<?= base_url('api/teacher/attendance') ?>?class_id=${classId}&date=${date}&shift_id=${shiftId}`);
            const result = await response.json();

            if (!result.success) {
                alert(result.message);
                return;
            }

            renderAttendanceList(result.data);

            document.getElementById('emptyState').classList.add('hidden');
            document.getElementById('attendanceContainer').classList.remove('hidden');
        } catch (error) {
            console.error('Error loading attendance:', error);
            alert('Gagal memuat data kehadiran');
        }
    }

    function renderAttendanceList(students) {
        const container = document.getElementById('attendanceList');

        if (students.length === 0) {
            container.innerHTML = '<p class="text-center text-gray-500 py-8">Tidak ada siswa di kelas ini</p>';
            return;
        }

        let html = '<div class="space-y-3">';

        students.forEach(student => {
            const isPresent = student.status === 'hadir';
            const isExcused = student.status === 'izin';
            const isSick = student.status === 'sakit';
            const isAbsent = student.status === 'alpha';

            html += `
            <div class="border border-gray-200 rounded-xl p-4 hover:border-primary-300 transition-all">
                <div class="flex items-center justify-between">
                    <div class="flex items-center flex-1">
                        <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                            <span class="material-symbols-outlined text-primary-600">person</span>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">${student.name}</h4>
                            <p class="text-sm text-gray-600">NIS: ${student.nis}</p>
                        </div>
                    </div>
                    
                    <div class="flex gap-2">
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" 
                                   name="status_${student.id}" 
                                   value="hadir" 
                                   class="hidden peer"
                                   ${isPresent ? 'checked' : ''}>
                            <div class="w-20 px-3 py-2 border-2 border-gray-300 rounded-lg text-center peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:text-green-700 font-medium hover:border-green-300 transition-all">
                                Hadir
                            </div>
                        </label>
                        
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" 
                                   name="status_${student.id}" 
                                   value="izin" 
                                   class="hidden peer"
                                   ${isExcused ? 'checked' : ''}>
                            <div class="w-20 px-3 py-2 border-2 border-gray-300 rounded-lg text-center peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-700 font-medium hover:border-blue-300 transition-all">
                                Izin
                            </div>
                        </label>
                        
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" 
                                   name="status_${student.id}" 
                                   value="sakit" 
                                   class="hidden peer"
                                   ${isSick ? 'checked' : ''}>
                            <div class="w-20 px-3 py-2 border-2 border-gray-300 rounded-lg text-center peer-checked:border-yellow-500 peer-checked:bg-yellow-50 peer-checked:text-yellow-700 font-medium hover:border-yellow-300 transition-all">
                                Sakit
                            </div>
                        </label>
                        
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" 
                                   name="status_${student.id}" 
                                   value="alpha" 
                                   class="hidden peer"
                                   ${isAbsent ? 'checked' : ''}>
                            <div class="w-20 px-3 py-2 border-2 border-gray-300 rounded-lg text-center peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:text-red-700 font-medium hover:border-red-300 transition-all">
                                Alpha
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        `;
        });

        html += '</div>';
        container.innerHTML = html;
    }

    async function saveAttendance() {
        const classId = document.getElementById('classId').value;
        const date = document.getElementById('date').value;
        const shiftId = document.getElementById('shiftId').value;

        const attendanceData = [];
        const radioGroups = document.querySelectorAll('[name^="status_"]');
        const studentIds = new Set();

        radioGroups.forEach(radio => {
            const studentId = radio.name.replace('status_', '');
            studentIds.add(studentId);
        });

        studentIds.forEach(studentId => {
            const selectedRadio = document.querySelector(`[name="status_${studentId}"]:checked`);
            if (selectedRadio) {
                attendanceData.push({
                    student_id: studentId,
                    status: selectedRadio.value
                });
            }
        });

        try {
            const response = await fetch('<?= base_url('api/teacher/attendance') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    class_id: classId,
                    date: date,
                    shift_id: shiftId,
                    attendance: attendanceData
                })
            });

            const result = await response.json();

            if (result.success) {
                alert('Data kehadiran berhasil disimpan');
                loadAttendance();
            } else {
                alert(result.message || 'Gagal menyimpan data');
            }
        } catch (error) {
            console.error('Error saving attendance:', error);
            alert('Gagal menyimpan data kehadiran');
        }
    }
</script>

<?= $this->endSection() ?>