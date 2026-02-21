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
        <span class="material-symbols">add</span>
        <span>Tambah Guru</span>
    </button>
</div>

<!-- Search & Filter -->
<div class="card mb-6">
    <div class="card-body">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-3">
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari Guru</label>
                <input type="text" id="searchInput" placeholder="Cari berdasarkan nama, NIP, atau email..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">&nbsp;</label>
                <button onclick="resetTeacherFilters()" class="w-full btn-secondary">
                    <span class="material-symbols text-sm mr-2">filter_alt_off</span>
                    Reset Filter
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Teachers Table -->
<div class="card">
    <div class="card-body">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-primary-50">
                    <tr class="border-b border-primary-100">
                        <th class="text-left py-3.5 px-4 text-xs font-bold uppercase tracking-wide text-primary-700 whitespace-nowrap">No</th>
                        <th onclick="setTeacherSort('name')" class="text-left py-3.5 px-4 text-xs font-bold uppercase tracking-wide text-primary-700 whitespace-nowrap cursor-pointer select-none hover:bg-primary-100 transition-colors">Nama<span id="teacherSortIndicator_name" class="ml-1 text-primary-500"></span></th>
                        <th onclick="setTeacherSort('nip')" class="text-left py-3.5 px-4 text-xs font-bold uppercase tracking-wide text-primary-700 whitespace-nowrap cursor-pointer select-none hover:bg-primary-100 transition-colors">NIP<span id="teacherSortIndicator_nip" class="ml-1 text-primary-500"></span></th>
                        <th onclick="setTeacherSort('email')" class="text-left py-3.5 px-4 text-xs font-bold uppercase tracking-wide text-primary-700 whitespace-nowrap cursor-pointer select-none hover:bg-primary-100 transition-colors">Email<span id="teacherSortIndicator_email" class="ml-1 text-primary-500"></span></th>
                        <th onclick="setTeacherSort('phone')" class="text-left py-3.5 px-4 text-xs font-bold uppercase tracking-wide text-primary-700 whitespace-nowrap cursor-pointer select-none hover:bg-primary-100 transition-colors">No. Telepon<span id="teacherSortIndicator_phone" class="ml-1 text-primary-500"></span></th>
                        <th onclick="setTeacherSort('is_active')" class="text-center py-3.5 px-4 text-xs font-bold uppercase tracking-wide text-primary-700 whitespace-nowrap cursor-pointer select-none hover:bg-primary-100 transition-colors">Status<span id="teacherSortIndicator_is_active" class="ml-1 text-primary-500"></span></th>
                        <th class="text-center py-3.5 px-4 text-xs font-bold uppercase tracking-wide text-primary-700 whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody id="teachersTable">
                    <tr>
                        <td colspan="7" class="text-center py-12">
                            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
                            <p class="text-gray-500 mt-4">Memuat data guru...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <p id="teachersPaginationInfo" class="text-sm text-gray-600">Memuat data...</p>
            <div class="flex items-center gap-x-2">
                <button id="teachersPrevBtn" onclick="goToTeachersPage(teachersPage - 1)" disabled
                    class="h-10 px-4 rounded-xl border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-1 text-sm font-medium transition">
                    <span class="material-symbols text-base">chevron_left</span>
                    <span>Sebelumnya</span>
                </button>
                <div id="teachersPaginationNumbers" class="flex items-center gap-x-1"></div>
                <button id="teachersNextBtn" onclick="goToTeachersPage(teachersPage + 1)" disabled
                    class="h-10 px-4 rounded-xl border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-1 text-sm font-medium transition">
                    <span>Berikutnya</span>
                    <span class="material-symbols text-base">chevron_right</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Teacher Modal -->
<div id="teacherModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full">
        <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h3 class="text-xl font-bold text-gray-900" id="teacherModalTitle">Tambah Guru Baru</h3>
            <button onclick="closeTeacherModal()" class="text-gray-400 hover:text-gray-600">
                <span class="material-symbols">close</span>
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
                    <span class="material-symbols mr-2">save</span>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Action Buttons Helper -->
<script src="<?= base_url('assets/js/action-buttons-helper.js') ?>"></script>

<script>
    let allTeachers = [];
    let teachersPage = 1;
    let teachersTotalPages = 1;
    let teachersPerPage = 25;
    let teacherSortCol = '';
    let teacherSortDir = '';
    let teacherSearchDebounce = null;

    document.addEventListener('DOMContentLoaded', function() {
        loadTeachers();
        document.getElementById('searchInput').addEventListener('input', function() {
            clearTimeout(teacherSearchDebounce);
            teacherSearchDebounce = setTimeout(() => {
                teachersPage = 1;
                applyTeacherFilterAndRender();
            }, 300);
        });
    });

    async function loadTeachers() {
        try {
            const response = await fetch('<?= base_url('api/admin/teachers') ?>', {
                credentials: 'same-origin',
                cache: 'no-store'
            });
            const data = await response.json();
            if (data.status === 'success') {
                allTeachers = data.data;
                teachersPage = 1;
                applyTeacherFilterAndRender();
            } else {
                showTeacherError('Gagal memuat data guru');
            }
        } catch (error) {
            showTeacherError('Gagal memuat data guru');
        }
    }

    function applyTeacherFilterAndRender() {
        const term = (document.getElementById('searchInput').value || '').toLowerCase();
        let list = allTeachers.filter(t =>
            (t.full_name || t.username || '').toLowerCase().includes(term) ||
            (t.nip || '').toLowerCase().includes(term) ||
            (t.email || '').toLowerCase().includes(term)
        );

        if (teacherSortCol) {
            list = [...list].sort((a, b) => {
                let va = String(a[teacherSortCol] ?? '').toLowerCase();
                let vb = String(b[teacherSortCol] ?? '').toLowerCase();
                if (teacherSortCol === 'full_name') {
                    va = String(a.full_name || a.username || '').toLowerCase();
                    vb = String(b.full_name || b.username || '').toLowerCase();
                }
                if (teacherSortCol === 'is_active') {
                    va = Number(a.is_active);
                    vb = Number(b.is_active);
                    return teacherSortDir === 'asc' ? va - vb : vb - va;
                }
                if (va < vb) return teacherSortDir === 'asc' ? -1 : 1;
                if (va > vb) return teacherSortDir === 'asc' ? 1 : -1;
                return 0;
            });
        }

        const total = list.length;
        teachersTotalPages = Math.max(1, Math.ceil(total / teachersPerPage));
        if (teachersPage > teachersTotalPages) teachersPage = teachersTotalPages;

        const offset = (teachersPage - 1) * teachersPerPage;
        const pageList = list.slice(offset, offset + teachersPerPage);
        renderTeachers(pageList, offset);
        updateTeachersPagination(total, offset);
    }

    function renderTeachers(teachers, offset) {
        const tbody = document.getElementById('teachersTable');

        if (!teachers || teachers.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-12 text-gray-500">
                        <span class="material-symbols text-5xl text-gray-300 block mb-2">person</span>
                        <p>Belum ada data guru</p>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = teachers.map((teacher, index) => {
            const rowNumber = offset + index + 1;
            const name = teacher.full_name || teacher.username || '-';
            return `
            <tr class="bg-white border-b border-gray-100 hover:bg-gray-50">
                <td class="py-3 px-4 text-gray-500 font-medium">${rowNumber}</td>
                <td class="py-3 px-4 font-medium text-gray-900">${name}</td>
                <td class="py-3 px-4 text-gray-700">${teacher.nip || '-'}</td>
                <td class="py-3 px-4 text-gray-700">${teacher.email || '-'}</td>
                <td class="py-3 px-4 text-gray-700">${teacher.phone || '-'}</td>
                <td class="py-3 px-4 text-center">
                    <span class="badge-${teacher.is_active ? 'success' : 'danger'}">${teacher.is_active ? 'Aktif' : 'Nonaktif'}</span>
                </td>
                <td class="py-3 px-4 text-center">
                    ${renderActionButtons(teacher.id, 'teacher', {
                        editTooltip: 'Edit data guru',
                        deleteTooltip: 'Hapus guru'
                    })}
                </td>
            </tr>
        `;
        }).join('');
    }

    function setTeacherSort(col) {
        const colMap = {
            name: 'full_name',
            nip: 'nip',
            email: 'email',
            phone: 'phone',
            is_active: 'is_active'
        };
        const actualCol = colMap[col] || col;
        if (teacherSortCol === actualCol) {
            if (teacherSortDir === 'asc') teacherSortDir = 'desc';
            else if (teacherSortDir === 'desc') {
                teacherSortCol = '';
                teacherSortDir = '';
            } else teacherSortDir = 'asc';
        } else {
            teacherSortCol = actualCol;
            teacherSortDir = 'asc';
        }
        updateTeacherSortIndicators(col);
        teachersPage = 1;
        applyTeacherFilterAndRender();
    }

    function updateTeacherSortIndicators(activeCol) {
        ['name', 'nip', 'email', 'phone', 'is_active'].forEach(col => {
            const el = document.getElementById('teacherSortIndicator_' + col);
            if (!el) return;
            const colMap = {
                name: 'full_name',
                nip: 'nip',
                email: 'email',
                phone: 'phone',
                is_active: 'is_active'
            };
            const actual = colMap[col] || col;
            if (teacherSortCol === actual) {
                el.textContent = teacherSortDir === 'asc' ? '↑' : '↓';
            } else {
                el.textContent = '';
            }
        });
    }

    function resetTeacherFilters() {
        document.getElementById('searchInput').value = '';
        teacherSortCol = '';
        teacherSortDir = '';
        updateTeacherSortIndicators('');
        teachersPage = 1;
        applyTeacherFilterAndRender();
    }

    function updateTeachersPagination(total, offset) {
        const start = total === 0 ? 0 : offset + 1;
        const end = Math.min(offset + teachersPerPage, total);
        document.getElementById('teachersPaginationInfo').textContent = `Menampilkan ${start}–${end} dari ${total} guru`;
        document.getElementById('teachersPrevBtn').disabled = teachersPage <= 1;
        document.getElementById('teachersNextBtn').disabled = teachersPage >= teachersTotalPages;
        renderTeachersPaginationNumbers();
    }

    function renderTeachersPaginationNumbers() {
        const container = document.getElementById('teachersPaginationNumbers');
        const pages = [];
        if (teachersTotalPages <= 7) {
            for (let i = 1; i <= teachersTotalPages; i++) pages.push(i);
        } else {
            pages.push(1);
            if (teachersPage > 3) pages.push('ellipsis-start');
            const s = Math.max(2, teachersPage - 1);
            const e = Math.min(teachersTotalPages - 1, teachersPage + 1);
            for (let i = s; i <= e; i++) pages.push(i);
            if (teachersPage < teachersTotalPages - 2) pages.push('ellipsis-end');
            pages.push(teachersTotalPages);
        }
        container.innerHTML = pages.map(item => {
            if (typeof item !== 'number') {
                const jp = item === 'ellipsis-start' ? Math.max(1, teachersPage - 5) : Math.min(teachersTotalPages, teachersPage + 5);
                return `<button type="button" onclick="goToTeachersPage(${jp})" class="w-10 h-10 rounded-xl border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 flex items-center justify-center"><span class="material-symbols text-base">more_horiz</span></button>`;
            }
            const isCurrent = item === teachersPage;
            const cls = isCurrent ? 'bg-primary-600 text-white border-primary-600' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50';
            return `<button type="button" onclick="goToTeachersPage(${item})" class="w-10 h-10 rounded-xl border text-sm font-semibold transition ${cls}">${item}</button>`;
        }).join('');
    }

    function goToTeachersPage(page) {
        if (page < 1 || page > teachersTotalPages || page === teachersPage) return;
        teachersPage = page;
        applyTeacherFilterAndRender();
    }

    function showTeacherError(message) {
        document.getElementById('teachersTable').innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-12 text-red-500">
                    <span class="material-symbols text-5xl block mb-2">error</span>
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