<?= $this->extend('layouts/main') ?>

<?= $this->section('sidebar') ?>
<a href="<?= base_url('admin/dashboard') ?>" class="sidebar-item">
    <span class="material-symbols-outlined mr-3">dashboard</span>
    <span>Dashboard</span>
</a>
<a href="<?= base_url('admin/devices') ?>" class="sidebar-item">
    <span class="material-symbols-outlined mr-3">devices</span>
    <span>Mesin Fingerprint</span>
</a>
<a href="<?= base_url('admin/device-mapping') ?>" class="sidebar-item">
    <span class="material-symbols-outlined mr-3">link</span>
    <span>Mapping ID Mesin</span>
</a>
<a href="<?= base_url('admin/attendance-logs') ?>" class="sidebar-item">
    <span class="material-symbols-outlined mr-3">description</span>
    <span>Log Absensi</span>
</a>
<a href="<?= base_url('admin/attendance') ?>" class="sidebar-item">
    <span class="material-symbols-outlined mr-3">how_to_reg</span>
    <span>Daftar Hadir</span>
</a>
<a href="<?= base_url('admin/shifts') ?>" class="sidebar-item">
    <span class="material-symbols-outlined mr-3">schedule</span>
    <span>Pengaturan Shift</span>
</a>
<a href="<?= base_url('admin/students') ?>" class="sidebar-item">
    <span class="material-symbols-outlined mr-3">groups</span>
    <span>Data Siswa</span>
</a>
<a href="<?= base_url('admin/classes') ?>" class="sidebar-item-active">
    <span class="material-symbols-outlined mr-3">class</span>
    <span>Data Kelas</span>
</a>
<a href="<?= base_url('admin/users') ?>" class="sidebar-item">
    <span class="material-symbols-outlined mr-3">manage_accounts</span>
    <span>Manajemen User</span>
</a>
<a href="<?= base_url('admin/reports') ?>" class="sidebar-item">
    <span class="material-symbols-outlined mr-3">assessment</span>
    <span>Laporan</span>
</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Header -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Data Kelas</h2>
        <p class="text-gray-600 mt-1">Kelola data kelas dan jumlah siswa</p>
    </div>
    <button onclick="openAddClassModal()" class="btn-primary flex items-center space-x-2">
        <span class="material-symbols-outlined">add</span>
        <span>Tambah Kelas</span>
    </button>
</div>

<!-- Classes Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="classesContainer">
    <div class="text-center col-span-3 py-12">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
        <p class="text-gray-500 mt-4">Memuat data kelas...</p>
    </div>
</div>

<!-- Add/Edit Class Modal -->
<div id="classModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full">
        <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h3 class="text-xl font-bold text-gray-900" id="classModalTitle">Tambah Kelas Baru</h3>
            <button onclick="closeClassModal()" class="text-gray-400 hover:text-gray-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form id="classForm" class="p-6 space-y-4">
            <input type="hidden" id="classId" name="class_id">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Kelas *</label>
                <input type="text" id="className" name="name" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500"
                    placeholder="Contoh: X IPA 1">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tingkat</label>
                <select id="classLevel" name="level"
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500">
                    <option value="">Pilih Tingkat</option>
                    <option value="X">X (Sepuluh)</option>
                    <option value="XI">XI (Sebelas)</option>
                    <option value="XII">XII (Dua Belas)</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Wali Kelas</label>
                <input type="text" id="classTeacher" name="homeroom_teacher"
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500"
                    placeholder="Nama wali kelas">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Ajaran</label>
                <input type="text" id="academicYear" name="academic_year" value="<?= date('Y') ?>/<?= date('Y') + 1 ?>"
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500"
                    placeholder="2025/2026">
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closeClassModal()" class="btn-secondary">Batal</button>
                <button type="submit" class="btn-primary">
                    <span class="material-symbols-outlined mr-2">save</span>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        loadClasses();
    });

    function loadClasses() {
        fetch('<?= base_url('api/admin/classes') ?>', {
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    renderClasses(data.data);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('classesContainer').innerHTML = `
                <div class="text-center col-span-3 py-12 text-red-500">
                    Gagal memuat data kelas
                </div>
            `;
            });
    }

    function renderClasses(classes) {
        const container = document.getElementById('classesContainer');

        if (!classes || classes.length === 0) {
            container.innerHTML = `
            <div class="card col-span-3 text-center py-12">
                <span class="material-symbols-outlined text-5xl text-gray-300 mb-2">class</span>
                <p class="text-gray-500">Belum ada data kelas</p>
                <button onclick="openAddClassModal()" class="btn-primary mt-4">Tambah Kelas Pertama</button>
            </div>
        `;
            return;
        }

        container.innerHTML = classes.map(cls => `
        <div class="card hover:shadow-lg transition-shadow">
            <div class="card-body">
                <div class="flex justify-between items-start mb-4">
                    <div class="bg-primary-100 rounded-full p-3">
                        <span class="material-symbols-outlined text-primary-600 text-2xl">class</span>
                    </div>
                    <div class="flex space-x-2">
                        <button onclick="editClass(${cls.id})" class="text-primary-600 hover:text-primary-800">
                            <span class="material-symbols-outlined">edit</span>
                        </button>
                        <button onclick="deleteClass(${cls.id})" class="text-danger-600 hover:text-danger-800">
                            <span class="material-symbols-outlined">delete</span>
                        </button>
                    </div>
                </div>
                <h3 class="text-lg font-bold text-gray-900">${cls.name}</h3>
                <p class="text-sm text-gray-500 mt-1">${cls.homeroom_teacher || 'Wali kelas belum ditentukan'}</p>
                <div class="flex items-center mt-4 text-sm text-gray-600">
                    <span class="material-symbols-outlined text-sm mr-1">groups</span>
                    <span>${cls.student_count || 0} Siswa</span>
                </div>
            </div>
        </div>
    `).join('');
    }

    function openAddClassModal() {
        document.getElementById('classModalTitle').textContent = 'Tambah Kelas Baru';
        document.getElementById('classForm').reset();
        document.getElementById('classId').value = '';
        document.getElementById('classModal').style.display = 'flex';
    }

    function closeClassModal() {
        document.getElementById('classModal').style.display = 'none';
    }

    function editClass(id) {
        alert('Edit class ' + id);
    }

    function deleteClass(id) {
        if (confirm('Apakah Anda yakin ingin menghapus kelas ini?')) {
            alert('Delete class ' + id);
        }
    }

    document.getElementById('classForm').addEventListener('submit', function(e) {
        e.preventDefault();
        alert('Save class');
        closeClassModal();
    });
</script>

<?= $this->endSection() ?>