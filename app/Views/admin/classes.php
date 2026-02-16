<?= $this->extend('layouts/main') ?>

<?= $this->section('sidebar') ?>
<?= $this->include('partials/sidebar_admin') ?>
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
                <label class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
                <select id="classLevel" name="level"
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500">
                    <option value="">Pilih Kelas</option>
                    <option value="1">Kelas 1</option>
                    <option value="2">Kelas 2</option>
                    <option value="3">Kelas 3</option>
                    <option value="4">Kelas 4</option>
                    <option value="5">Kelas 5</option>
                    <option value="6">Kelas 6</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Wali Kelas</label>
                <select id="classTeacher" name="teacher_id"
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500">
                    <option value="">-- Pilih Wali Kelas --</option>
                </select>
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
    let allTeachersForClass = [];

    document.addEventListener('DOMContentLoaded', function() {
        loadClasses();
        loadTeachersForDropdown();
    });

    async function loadTeachersForDropdown() {
        try {
            const response = await fetch('<?= base_url('api/admin/teachers') ?>', {
                credentials: 'same-origin'
            });
            const data = await response.json();
            if (data.status === 'success') {
                allTeachersForClass = data.data;
                populateTeacherDropdown();
            }
        } catch (error) {
            console.error('Error loading teachers:', error);
        }
    }

    function populateTeacherDropdown(selectedId = null) {
        const select = document.getElementById('classTeacher');
        select.innerHTML = '<option value="">-- Pilih Wali Kelas --</option>';
        allTeachersForClass.forEach(teacher => {
            const option = document.createElement('option');
            option.value = teacher.id;
            option.textContent = teacher.full_name || teacher.username;
            if (teacher.nip) {
                option.textContent += ` (NIP: ${teacher.nip})`;
            }
            if (selectedId && teacher.id == selectedId) {
                option.selected = true;
            }
            select.appendChild(option);
        });
    }

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
        <div class="bg-white rounded-2xl shadow hover:shadow-lg transition-shadow p-4">
            <div>
                <div class="flex justify-between items-start mb-4">
                    <div class="bg-primary-100 rounded-full p-3">
                        <span class="material-symbols-outlined text-primary-600 text-2xl">class</span>
                    </div>
                    <div class="flex space-x-2">
                        <button onclick="editClass(${cls.id})" class="text-primary-600 hover:text-primary-800 p-1 rounded focus:outline-none" style="border:none;background:none;box-shadow:none;">
                            <span class="material-symbols-outlined">edit</span>
                        </button>
                        <button onclick="deleteClass(${cls.id})" class="text-danger-600 hover:text-danger-800 p-1 rounded focus:outline-none" style="border:none;background:none;box-shadow:none;">
                            <span class="material-symbols-outlined">delete</span>
                        </button>
                    </div>
                </div>
                <h3 class="text-lg font-bold text-gray-900">${cls.name}</h3>
                <p class="text-sm text-gray-500 mt-1">${cls.teacher_name || cls.homeroom_teacher || 'Wali kelas belum ditentukan'}</p>
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
        populateTeacherDropdown();
        document.getElementById('classModal').style.display = 'flex';
    }

    function closeClassModal() {
        document.getElementById('classModal').style.display = 'none';
    }


    function editClass(id) {
        fetch(`<?= base_url('api/admin/classes') ?>`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const cls = data.data.find(c => c.id == id);
                    if (!cls) {
                        alert('Data kelas tidak ditemukan');
                        return;
                    }
                    document.getElementById('classModalTitle').textContent = 'Edit Kelas';
                    document.getElementById('classId').value = cls.id;
                    document.getElementById('className').value = cls.name || '';
                    document.getElementById('classLevel').value = cls.grade || '';
                    populateTeacherDropdown(cls.teacher_id);
                    document.getElementById('academicYear').value = cls.year || '';
                    document.getElementById('classModal').style.display = 'flex';
                } else {
                    alert('Gagal mengambil data kelas');
                }
            })
            .catch(() => alert('Gagal mengambil data kelas'));
    }

    async function deleteClass(id) {
        if (!confirm('Apakah Anda yakin ingin menghapus kelas ini?')) {
            return;
        }

        try {
            const response = await fetch(`<?= base_url('api/admin/classes') ?>/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            const data = await response.json();

            if (data.status === 'success') {
                alert('Kelas berhasil dihapus');
                loadClasses();
            } else {
                alert(data.message || 'Gagal menghapus kelas');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus kelas');
        }
    }

    document.getElementById('classForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const id = document.getElementById('classId').value;
        const name = document.getElementById('className').value;
        const level = document.getElementById('classLevel').value;
        const teacher_id = document.getElementById('classTeacher').value;
        const academic_year = document.getElementById('academicYear').value;

        const payload = {
            name,
            level,
            teacher_id: teacher_id || null,
            academic_year
        };

        let url = '<?= base_url('api/admin/classes') ?>';
        let method = 'POST';
        if (id) {
            url += '/' + id;
            method = 'PUT';
        }

        try {
            const resp = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: JSON.stringify(payload)
            });
            const data = await resp.json();
            if ((data.status === 'success') || (data.success === true)) {
                closeClassModal();
                loadClasses();
                alert('Kelas berhasil disimpan');
            } else {
                alert(data.message || 'Gagal menyimpan kelas');
            }
        } catch (err) {
            alert('Terjadi kesalahan saat menyimpan kelas');
        }
    });
</script>

<?= $this->endSection() ?>