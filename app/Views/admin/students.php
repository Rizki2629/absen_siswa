<?= $this->extend('layouts/main') ?>

<?= $this->section('sidebar') ?>
<?= $this->include('partials/sidebar_admin') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Header -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Data Siswa</h2>
        <p class="text-gray-600 mt-1">Kelola data siswa dan informasi absensi</p>
    </div>
    <button onclick="openAddStudentModal()" class="btn-primary flex items-center space-x-2">
        <span class="material-symbols-outlined">add</span>
        <span>Tambah Siswa</span>
    </button>
</div>

<!-- Search & Filter -->
<div class="card mb-6">
    <div class="card-body">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari Siswa</label>
                <input type="text" id="searchStudent" placeholder="Cari berdasarkan nama atau NIS..."
                    onkeyup="filterStudents()"
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter Kelas</label>
                <select id="filterClass" onchange="filterStudents()" class="w-full px-4 py-2 border border-gray-300 rounded-xl">
                    <option value="">Semua Kelas</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">&nbsp;</label>
                <button onclick="resetFilters()" class="w-full btn-secondary">
                    <span class="material-symbols-outlined text-sm mr-2">filter_alt_off</span>
                    Reset Filter
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Students Table -->
<div class="card">
    <div class="card-body">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">NIS</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">Nama Siswa</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">Kelas</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">Jenis Kelamin</th>
                        <th class="text-center py-3 px-4 font-semibold text-gray-700">Status</th>
                        <th class="text-center py-3 px-4 font-semibold text-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody id="studentsTable">
                    <tr>
                        <td colspan="6" class="text-center py-12">
                            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
                            <p class="text-gray-500 mt-4">Memuat data siswa...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit Student Modal -->
<div id="studentModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center rounded-t-2xl">
            <h3 class="text-xl font-bold text-gray-900" id="studentModalTitle">Tambah Siswa Baru</h3>
            <button onclick="closeStudentModal()" class="text-gray-400 hover:text-gray-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form id="studentForm" class="p-6 space-y-4">
            <input type="hidden" id="studentId" name="student_id">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">NIS *</label>
                    <input type="text" id="studentNis" name="nis" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500"
                        placeholder="Nomor Induk Siswa">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap *</label>
                    <input type="text" id="studentName" name="name" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500"
                        placeholder="Nama lengkap siswa">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kelas *</label>
                    <select id="studentClass" name="class_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500">
                        <option value="">Pilih Kelas</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin *</label>
                    <select id="studentGender" name="gender" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500">
                        <option value="">Pilih</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">No. HP Orang Tua</label>
                <input type="text" id="parentPhone" name="parent_phone"
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500"
                    placeholder="08xxxxxxxxxx">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                <textarea id="studentAddress" name="address" rows="2"
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500"
                    placeholder="Alamat lengkap siswa"></textarea>
            </div>

            <div class="flex items-center">
                <input type="checkbox" id="studentActive" name="is_active" checked
                    class="w-4 h-4 text-primary-600 rounded focus:ring-primary-500">
                <label for="studentActive" class="ml-2 text-sm text-gray-700">Siswa Aktif</label>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closeStudentModal()" class="btn-secondary">Batal</button>
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
        loadStudents();
        loadClasses();
    });

    function loadStudents() {
        fetch('<?= base_url('api/admin/students') ?>', {
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    renderStudents(data.data);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('studentsTable').innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-12 text-red-500">
                        Gagal memuat data siswa
                    </td>
                </tr>
            `;
            });
    }

    function loadClasses() {
        fetch('<?= base_url('api/admin/classes') ?>', {
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const filterSelect = document.getElementById('filterClass');
                    const formSelect = document.getElementById('studentClass');

                    data.data.forEach(cls => {
                        filterSelect.innerHTML += `<option value="${cls.id}">${cls.name}</option>`;
                        formSelect.innerHTML += `<option value="${cls.id}">${cls.name}</option>`;
                    });
                }
            });
    }

    function renderStudents(students) {
        const tbody = document.getElementById('studentsTable');

        if (!students || students.length === 0) {
            tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-12 text-gray-500">
                    <span class="material-symbols-outlined text-5xl text-gray-300 mb-2">groups</span>
                    <p>Belum ada data siswa</p>
                    <button onclick="openAddStudentModal()" class="btn-primary mt-4">Tambah Siswa Pertama</button>
                </td>
            </tr>
        `;
            return;
        }

        tbody.innerHTML = students.map(student => `
        <tr class="border-b border-gray-100 hover:bg-gray-50">
            <td class="py-3 px-4 font-medium text-gray-900">${student.nis}</td>
            <td class="py-3 px-4">${student.name}</td>
            <td class="py-3 px-4">${student.class_name || '-'}</td>
            <td class="py-3 px-4">${student.gender === 'L' ? 'Laki-laki' : 'Perempuan'}</td>
            <td class="py-3 px-4 text-center">
                <span class="badge-${student.is_active ? 'success' : 'danger'}">
                    ${student.is_active ? 'Aktif' : 'Non-aktif'}
                </span>
            </td>
            <td class="py-3 px-4 text-center">
                <button onclick="editStudent(${student.id})" class="text-primary-600 hover:text-primary-800 mr-2 p-1 rounded focus:outline-none" style="border:none;background:none;box-shadow:none;">
                    <span class="material-symbols-outlined">edit</span>
                </button>
                <button onclick="deleteStudent(${student.id})" class="text-danger-600 hover:text-danger-800 p-1 rounded focus:outline-none" style="border:none;background:none;box-shadow:none;">
                    <span class="material-symbols-outlined">delete</span>
                </button>
            </td>
        </tr>
    `).join('');
    }

    function filterStudents() {
        // TODO: Implement client-side filtering
    }

    function resetFilters() {
        document.getElementById('searchStudent').value = '';
        document.getElementById('filterClass').value = '';
        loadStudents();
    }

    function openAddStudentModal() {
        document.getElementById('studentModalTitle').textContent = 'Tambah Siswa Baru';
        document.getElementById('studentForm').reset();
        document.getElementById('studentId').value = '';
        document.getElementById('studentModal').style.display = 'flex';
    }

    function closeStudentModal() {
        document.getElementById('studentModal').style.display = 'none';
    }

    async function editStudent(id) {
        try {
            const response = await fetch(`/api/admin/students/${id}`);
            const result = await response.json();

            if (result.status === 'success') {
                const student = result.data;

                // Fill form with student data
                document.getElementById('studentModalTitle').textContent = 'Edit Data Siswa';
                document.getElementById('studentId').value = student.id;
                document.getElementById('studentNis').value = student.nis;
                document.getElementById('studentName').value = student.name;
                document.getElementById('studentClass').value = student.class_id;
                document.getElementById('studentGender').value = student.gender;
                document.getElementById('parentPhone').value = student.parent_phone || '';
                document.getElementById('studentAddress').value = student.address || '';
                document.getElementById('studentActive').checked = student.active == 1;

                // Show modal
                document.getElementById('studentModal').style.display = 'flex';
            } else {
                alert('Gagal mengambil data siswa: ' + result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengambil data siswa');
        }
    }

    async function deleteStudent(id) {
        if (!confirm('Apakah Anda yakin ingin menghapus siswa ini?')) {
            return;
        }

        try {
            const response = await fetch(`/api/admin/students/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (result.status === 'success') {
                alert('Siswa berhasil dihapus');
                loadStudents();
            } else {
                alert('Gagal menghapus siswa: ' + result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus siswa');
        }
    }

    document.getElementById('studentForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const studentId = document.getElementById('studentId').value;
        const formData = {
            nis: document.getElementById('studentNis').value,
            name: document.getElementById('studentName').value,
            class_id: document.getElementById('studentClass').value,
            gender: document.getElementById('studentGender').value,
            parent_phone: document.getElementById('parentPhone').value,
            address: document.getElementById('studentAddress').value,
            is_active: document.getElementById('studentActive').checked
        };

        try {
            const url = studentId ? `/api/admin/students/${studentId}` : '/api/admin/students';
            const method = studentId ? 'PUT' : 'POST';

            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(formData)
            });

            const result = await response.json();

            if (result.status === 'success') {
                alert(studentId ? 'Data siswa berhasil diperbarui' : 'Siswa berhasil ditambahkan');
                closeStudentModal();
                loadStudents();
            } else {
                alert('Gagal menyimpan data: ' + result.message);
                if (result.errors) {
                    console.error('Validation errors:', result.errors);
                }
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menyimpan data siswa');
        }
    });
</script>

<?= $this->endSection() ?>