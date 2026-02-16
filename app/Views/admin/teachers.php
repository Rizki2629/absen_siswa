<?= $this->extend('layouts/main') ?>

<?= $this->section('sidebar') ?>
<?= $this->include('partials/sidebar_admin') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Header -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Data Guru</h2>
        <p class="text-gray-600 mt-1">Kelola data guru dan wali kelas</p>
    </div>
    <button onclick="openAddTeacherModal()" class="btn-primary flex items-center space-x-2">
        <span class="material-symbols-outlined">add</span>
        <span>Tambah Guru</span>
    </button>
</div>

<!-- Search and Filter -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">search</span>
                <input type="text" id="searchInput" placeholder="Cari nama guru atau email..."
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500">
            </div>
        </div>
    </div>
</div>

<!-- Teachers Table -->
<div>
    <div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">Nama</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">NIP</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">Email</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">No. Telepon</th>
                        <th class="text-center py-3 px-4 font-semibold text-gray-700">Status</th>
                        <th class="text-center py-3 px-4 font-semibold text-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody id="teachersTable">
                    <tr>
                        <td colspan="6" class="text-center py-12">
                            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
                            <p class="text-gray-500 mt-4">Memuat data guru...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit Teacher Modal -->
<div id="teacherModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full">
        <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h3 class="text-xl font-bold text-gray-900" id="teacherModalTitle">Tambah Guru Baru</h3>
            <button onclick="closeTeacherModal()" class="text-gray-400 hover:text-gray-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form id="teacherForm" class="p-6 space-y-4">
            <input type="hidden" id="teacherId" name="teacher_id">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap *</label>
                <input type="text" id="teacherName" name="name" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500"
                    placeholder="Nama lengkap guru">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">NIP</label>
                <input type="text" id="teacherNip" name="nip"
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500"
                    placeholder="Nomor Induk Pegawai">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Username *</label>
                <input type="text" id="teacherUsername" name="username" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500"
                    placeholder="Username untuk login">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                <input type="email" id="teacherEmail" name="email" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500"
                    placeholder="email@contoh.com">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">No. Telepon</label>
                <input type="tel" id="teacherPhone" name="phone"
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500"
                    placeholder="08xx-xxxx-xxxx">
            </div>

            <div id="passwordField">
                <label class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
                <input type="password" id="teacherPassword" name="password"
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500"
                    placeholder="Minimal 6 karakter">
            </div>

            <div class="flex items-center">
                <input type="checkbox" id="teacherActive" name="is_active" checked
                    class="w-4 h-4 text-primary-600 rounded focus:ring-primary-500">
                <label for="teacherActive" class="ml-2 text-sm text-gray-700">Guru Aktif</label>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closeTeacherModal()" class="btn-secondary">Batal</button>
                <button type="submit" class="btn-primary">
                    <span class="material-symbols-outlined mr-2">save</span>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let allTeachers = [];

    document.addEventListener('DOMContentLoaded', function() {
        loadTeachers();

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const filtered = allTeachers.filter(teacher =>
                (teacher.full_name || teacher.username).toLowerCase().includes(searchTerm) ||
                (teacher.email || '').toLowerCase().includes(searchTerm)
            );
            renderTeachers(filtered);
        });
    });

    async function loadTeachers() {
        try {
            console.log('🔄 Loading teachers from API...');
            const response = await fetch('<?= base_url('api/admin/teachers') ?>');
            console.log('📡 Response status:', response.status, response.ok);

            const data = await response.json();
            console.log('📦 Teachers data received:', data);
            console.log('📊 Number of teachers:', data.data ? data.data.length : 0);

            if (data.status === 'success') {
                allTeachers = data.data;
                console.log('✅ Setting allTeachers:', allTeachers);
                renderTeachers(allTeachers);
            } else {
                console.error('❌ API returned error status');
                showError('Gagal memuat data guru');
            }
        } catch (error) {
            console.error('❌ Error loading teachers:', error);
            showError('Gagal memuat data guru');
        }
    }

    function renderTeachers(teachers) {
        console.log('🎨 Rendering teachers:', teachers);
        const tbody = document.getElementById('teachersTable');

        if (!teachers || teachers.length === 0) {
            console.log('⚠️ No teachers to render');
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-12 text-gray-500">
                        <span class="material-symbols-outlined text-5xl text-gray-300 mb-2">person</span>
                        <p>Belum ada data guru</p>
                    </td>
                </tr>
            `;
            return;
        }

        console.log('✅ Rendering ' + teachers.length + ' teachers');
        tbody.innerHTML = teachers.map(teacher => `
            <tr class="border-b border-gray-100 hover:bg-gray-50">
                <td class="py-3 px-4">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center mr-3">
                            <span class="text-primary-600 font-bold">${(teacher.full_name || teacher.username).charAt(0).toUpperCase()}</span>
                        </div>
                        <span class="font-medium text-gray-900">${teacher.full_name || teacher.username}</span>
                    </div>
                </td>
                <td class="py-3 px-4 text-gray-600">${teacher.nip || '-'}</td>
                <td class="py-3 px-4 text-gray-600">${teacher.email || '-'}</td>
                <td class="py-3 px-4 text-gray-600">${teacher.phone || '-'}</td>
                <td class="py-3 px-4 text-center">
                    <span class="badge-${teacher.is_active ? 'success' : 'danger'}">
                        ${teacher.is_active ? 'Aktif' : 'Nonaktif'}
                    </span>
                </td>
                <td class="py-3 px-4 text-center">
                    <button onclick="editTeacher(${teacher.id})" class="text-primary-600 hover:text-primary-800 mr-2">
                        <span class="material-symbols-outlined">edit</span>
                    </button>
                    <button onclick="deleteTeacher(${teacher.id})" class="text-danger-600 hover:text-danger-800">
                        <span class="material-symbols-outlined">delete</span>
                    </button>
                </td>
            </tr>
        `).join('');
    }

    function showError(message) {
        document.getElementById('teachersTable').innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-12 text-red-500">
                    <span class="material-symbols-outlined text-5xl mb-2">error</span>
                    <p>${message}</p>
                </td>
            </tr>
        `;
    }

    function openAddTeacherModal() {
        document.getElementById('teacherModalTitle').textContent = 'Tambah Guru Baru';
        document.getElementById('teacherForm').reset();
        document.getElementById('teacherId').value = '';
        document.getElementById('passwordField').style.display = 'block';
        document.getElementById('teacherPassword').required = true;
        document.getElementById('teacherModal').style.display = 'flex';
    }

    function closeTeacherModal() {
        document.getElementById('teacherModal').style.display = 'none';
    }

    async function editTeacher(id) {
        try {
            const response = await fetch(`<?= base_url('api/admin/teachers') ?>/${id}`);
            const result = await response.json();

            if (result.status === 'success') {
                const teacher = result.data;
                document.getElementById('teacherModalTitle').textContent = 'Edit Data Guru';
                document.getElementById('teacherId').value = teacher.id;
                document.getElementById('teacherName').value = teacher.full_name || '';
                document.getElementById('teacherNip').value = teacher.nip || '';
                document.getElementById('teacherUsername').value = teacher.username;
                document.getElementById('teacherEmail').value = teacher.email || '';
                document.getElementById('teacherPhone').value = teacher.phone || '';
                document.getElementById('teacherActive').checked = teacher.is_active == 1;
                document.getElementById('passwordField').style.display = 'none';
                document.getElementById('teacherPassword').required = false;
                document.getElementById('teacherModal').style.display = 'flex';
            } else {
                alert('Gagal memuat data guru: ' + result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Gagal memuat data guru');
        }
    }

    async function deleteTeacher(id) {
        if (!confirm('Apakah Anda yakin ingin menghapus data guru ini?')) {
            return;
        }

        try {
            const response = await fetch(`<?= base_url('api/admin/teachers') ?>/${id}`, {
                method: 'DELETE'
            });
            const result = await response.json();

            if (result.status === 'success') {
                alert('Data guru berhasil dihapus');
                loadTeachers();
            } else {
                alert('Gagal menghapus data guru: ' + result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Gagal menghapus data guru');
        }
    }

    document.getElementById('teacherForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const teacherId = document.getElementById('teacherId').value;
        const formData = {
            name: document.getElementById('teacherName').value,
            nip: document.getElementById('teacherNip').value,
            username: document.getElementById('teacherUsername').value,
            email: document.getElementById('teacherEmail').value,
            phone: document.getElementById('teacherPhone').value,
            role: 'teacher',
            is_active: document.getElementById('teacherActive').checked ? 1 : 0
        };

        // Only include password for new teacher or if field is visible
        if (!teacherId || document.getElementById('passwordField').style.display !== 'none') {
            const password = document.getElementById('teacherPassword').value;
            if (password) {
                formData.password = password;
            }
        }

        try {
            let url = '<?= base_url('api/admin/teachers') ?>';
            let method = 'POST';

            if (teacherId) {
                url += '/' + teacherId;
                method = 'PUT';
            }

            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            const result = await response.json();

            if (response.ok && result.status === 'success') {
                alert(result.message || (teacherId ? 'Data guru berhasil diupdate' : 'Data guru berhasil ditambahkan'));
                closeTeacherModal();
                loadTeachers();
            } else {
                alert(result.message || 'Gagal menyimpan data guru');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Gagal menyimpan data guru');
        }
    });
</script>

<?= $this->endSection() ?>