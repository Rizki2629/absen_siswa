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
    <div class="flex gap-2">
        <button onclick="generateStudentAccounts()" class="btn-secondary flex items-center space-x-2">
            <span class="material-symbols-outlined">manage_accounts</span>
            <span>Generate Akun Siswa</span>
        </button>
        <button onclick="openAddStudentModal()" class="btn-primary flex items-center space-x-2">
            <span class="material-symbols-outlined">add</span>
            <span>Tambah Siswa</span>
        </button>
    </div>
</div>

<!-- Search & Filter -->
<div class="card mb-6">
    <div class="card-body">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari Siswa</label>
                <input type="text" id="searchStudent" placeholder="Cari berdasarkan nama, NIS, atau NISN..."
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

<div class="card mb-6">
    <div class="card-body">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Data Siswa (XLSX / CSV)</label>
                <input type="file" id="studentUploadFile" accept=".xlsx,.xls,.csv"
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 bg-white">
                <p class="text-xs text-gray-500 mt-2">Gunakan template agar header sesuai: Nama, NIPD, JK, NISN, Tempat Lahir, Tanggal Lahir, NIK, Agama, Alamat, RT, RW, Kelurahan, Kecamatan, HP, Nama Ayah, Nama Ibu, Rombel Saat Ini.</p>
                <p class="text-xs text-gray-500 mt-1">Format data: JK = L/P, Tanggal Lahir bisa format tanggal Excel atau teks (contoh: 15 Maret 2018), Rombel Saat Ini otomatis dipetakan ke kelas.</p>
            </div>
            <div class="flex gap-2">
                <button onclick="downloadStudentTemplateXlsx()" class="btn-secondary w-full">
                    Template XLSX
                </button>
                <button onclick="uploadStudentsFile()" class="btn-primary w-full">
                    Upload File
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
                <thead class="bg-primary-50">
                    <tr class="border-b border-primary-100">
                        <th class="text-left py-3.5 px-4 text-xs font-bold uppercase tracking-wide text-primary-700 whitespace-nowrap">No</th>
                        <th onclick="setSortColumn('nis')" class="text-left py-3.5 px-4 text-xs font-bold uppercase tracking-wide text-primary-700 whitespace-nowrap cursor-pointer select-none hover:bg-primary-100 transition-colors">NIS<span id="sortIndicator_nis" class="ml-1 text-primary-500"></span></th>
                        <th onclick="setSortColumn('nisn')" class="text-left py-3.5 px-4 text-xs font-bold uppercase tracking-wide text-primary-700 whitespace-nowrap cursor-pointer select-none hover:bg-primary-100 transition-colors">NISN<span id="sortIndicator_nisn" class="ml-1 text-primary-500"></span></th>
                        <th onclick="setSortColumn('name')" class="text-left py-3.5 px-4 text-xs font-bold uppercase tracking-wide text-primary-700 whitespace-nowrap cursor-pointer select-none hover:bg-primary-100 transition-colors">Nama Siswa<span id="sortIndicator_name" class="ml-1 text-primary-500"></span></th>
                        <th onclick="setSortColumn('birth_date')" class="text-left py-3.5 px-4 text-xs font-bold uppercase tracking-wide text-primary-700 whitespace-nowrap cursor-pointer select-none hover:bg-primary-100 transition-colors">Tempat, Tanggal Lahir<span id="sortIndicator_birth_date" class="ml-1 text-primary-500"></span></th>
                        <th onclick="setSortColumn('gender')" class="text-left py-3.5 px-4 text-xs font-bold uppercase tracking-wide text-primary-700 whitespace-nowrap cursor-pointer select-none hover:bg-primary-100 transition-colors">Jenis Kelamin<span id="sortIndicator_gender" class="ml-1 text-primary-500"></span></th>
                        <th onclick="setSortColumn('class')" class="text-left py-3.5 px-4 text-xs font-bold uppercase tracking-wide text-primary-700 whitespace-nowrap cursor-pointer select-none hover:bg-primary-100 transition-colors">Kelas<span id="sortIndicator_class" class="ml-1 text-primary-500"></span></th>
                        <th class="text-center py-3.5 px-4 text-xs font-bold uppercase tracking-wide text-primary-700 whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody id="studentsTable">
                    <tr>
                        <td colspan="8" class="text-center py-12">
                            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
                            <p class="text-gray-500 mt-4">Memuat data siswa...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <?= view('partials/pagination_soft', [
            'ariaLabel' => 'Students pagination',
            'infoId' => 'studentsPaginationInfo',
            'numbersId' => 'studentsPaginationNumbers',
            'prevId' => 'studentsPrevBtn',
            'nextId' => 'studentsNextBtn',
            'prevHandler' => 'goToStudentsPage(studentsPage - 1)',
            'nextHandler' => 'goToStudentsPage(studentsPage + 1)',
            'infoText' => 'Memuat data...',
            'containerClass' => 'mt-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3',
        ]) ?>
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
    let studentsPage = 1;
    let studentsTotalPages = 1;
    let studentsPerPage = 25;
    let studentsTotal = 0;
    let studentsSearchDebounce = null;
    let classesById = {};
    let sortColumn = '';
    let sortDirection = '';

    document.addEventListener('DOMContentLoaded', function() {
        loadClasses().finally(() => {
            loadStudents();
        });

        const searchInput = document.getElementById('searchStudent');
        searchInput.addEventListener('input', function() {
            clearTimeout(studentsSearchDebounce);
            studentsSearchDebounce = setTimeout(() => {
                studentsPage = 1;
                loadStudents();
            }, 350);
        });
    });

    function loadStudents() {
        const searchValue = document.getElementById('searchStudent').value.trim();
        const classValue = document.getElementById('filterClass').value;
        const params = new URLSearchParams({
            page: String(studentsPage),
            per_page: String(studentsPerPage),
        });

        if (searchValue !== '') {
            params.set('search', searchValue);
        }

        if (classValue !== '') {
            params.set('class_id', classValue);
        }

        if (sortColumn && sortDirection) {
            params.set('sort_by', sortColumn);
            params.set('sort_dir', sortDirection);
        }

        fetch(`<?= base_url('api/admin/students') ?>?${params.toString()}`, {
                credentials: 'same-origin',
                cache: 'no-store'
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    renderStudents(data.data);
                    updateStudentsPagination(data.meta || {});
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
                updateStudentsPagination({
                    page: 1,
                    per_page: studentsPerPage,
                    total: 0,
                    total_pages: 1,
                });
            });
    }

    function loadClasses() {
        return fetch('<?= base_url('api/admin/classes') ?>', {
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const filterSelect = document.getElementById('filterClass');
                    const formSelect = document.getElementById('studentClass');

                    data.data.forEach(cls => {
                        classesById[String(cls.id)] = String(cls.name || '').trim();
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
                <td colspan="8" class="text-center py-12 text-gray-500">
                    <span class="material-symbols-outlined text-5xl text-gray-300 mb-2">groups</span>
                    <p>Belum ada data siswa</p>
                    <button onclick="openAddStudentModal()" class="btn-primary mt-4">Tambah Siswa Pertama</button>
                </td>
            </tr>
        `;
            return;
        }

        const toTitleCase = (text) => {
            return String(text || '')
                .toLowerCase()
                .replace(/\b\w/g, char => char.toUpperCase())
                .trim();
        };

        const formatBirthInfo = (student) => {
            const place = String(student.birth_place || '').trim();
            const birthDateRaw = String(student.birth_date || '').trim();

            let birthDateText = '';
            if (birthDateRaw) {
                const parsed = new Date(birthDateRaw);
                if (!Number.isNaN(parsed.getTime())) {
                    birthDateText = parsed.toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'long',
                        year: 'numeric'
                    });
                } else {
                    birthDateText = birthDateRaw;
                }
            }

            if (place && birthDateText) {
                return `${toTitleCase(place)}, ${birthDateText}`;
            }
            if (place) {
                return toTitleCase(place);
            }
            if (birthDateText) {
                return birthDateText;
            }
            return '-';
        };

        const formatClassLabel = (value) => {
            const raw = String(value || '').trim();
            if (raw === '') {
                return '';
            }

            return raw
                .replace(/^kelas\s*/i, '')
                .replace(/\s+/g, ' ')
                .trim();
        };

        const getClassDisplay = (student) => {
            const classNameFromApi = String(student.class_name || '').trim();
            const classNameFromMap = classesById[String(student.class_id || '')] || '';
            const normalized = formatClassLabel(classNameFromApi || classNameFromMap);

            if (normalized !== '') {
                return `Kelas ${normalized}`;
            }
            return 'Belum diatur';
        };

        tbody.innerHTML = students.map((student, index) => {
            const rowNumber = ((studentsPage - 1) * studentsPerPage) + index + 1;
            return `
        <tr class="bg-white border-b border-gray-100 hover:bg-gray-50">
            <td class="py-3 px-4 text-gray-500 font-medium">${rowNumber}</td>
            <td class="py-3 px-4 font-medium text-gray-900">${student.nis}</td>
            <td class="py-3 px-4 text-gray-700">${student.nisn || '-'}</td>
            <td class="py-3 px-4 font-medium text-gray-900">${toTitleCase(student.name)}</td>
            <td class="py-3 px-4 text-gray-700">${formatBirthInfo(student)}</td>
            <td class="py-3 px-4">${student.gender === 'L' ? 'Laki-laki' : 'Perempuan'}</td>
            <td class="py-3 px-4 text-gray-700 font-medium">${getClassDisplay(student)}</td>
            <td class="py-3 px-4 text-center">
                <button onclick="editStudent(${student.id})" class="text-primary-600 hover:text-primary-800 mr-2 p-1 rounded focus:outline-none" style="border:none;background:none;box-shadow:none;">
                    <span class="material-symbols-outlined">edit</span>
                </button>
                <button onclick="deleteStudent(${student.id})" class="text-danger-600 hover:text-danger-800 p-1 rounded focus:outline-none" style="border:none;background:none;box-shadow:none;">
                    <span class="material-symbols-outlined">delete</span>
                </button>
            </td>
        </tr>
    `;
        }).join('');
    }

    function setSortColumn(col) {
        if (sortColumn === col) {
            if (sortDirection === 'asc') {
                sortDirection = 'desc';
            } else if (sortDirection === 'desc') {
                sortColumn = '';
                sortDirection = '';
            } else {
                sortDirection = 'asc';
            }
        } else {
            sortColumn = col;
            sortDirection = 'asc';
        }
        updateSortIndicators();
        studentsPage = 1;
        loadStudents();
    }

    function updateSortIndicators() {
        ['nis', 'nisn', 'name', 'birth_date', 'gender', 'class'].forEach(col => {
            const el = document.getElementById('sortIndicator_' + col);
            if (!el) return;
            if (sortColumn === col) {
                el.textContent = sortDirection === 'asc' ? '↑' : '↓';
            } else {
                el.textContent = '';
            }
        });
    }

    function filterStudents() {
        studentsPage = 1;
        loadStudents();
    }

    function resetFilters() {
        document.getElementById('searchStudent').value = '';
        document.getElementById('filterClass').value = '';
        sortColumn = '';
        sortDirection = '';
        updateSortIndicators();
        studentsPage = 1;
        loadStudents();
    }

    function goToStudentsPage(page) {
        if (page < 1 || page > studentsTotalPages || page === studentsPage) {
            return;
        }
        studentsPage = page;
        loadStudents();
    }

    function updateStudentsPagination(meta) {
        studentsPage = Number(meta.page || 1);
        studentsPerPage = Number(meta.per_page || 25);
        studentsTotal = Number(meta.total || 0);
        studentsTotalPages = Number(meta.total_pages || 1);

        const start = studentsTotal === 0 ? 0 : ((studentsPage - 1) * studentsPerPage) + 1;
        const end = Math.min(studentsPage * studentsPerPage, studentsTotal);

        document.getElementById('studentsPaginationInfo').textContent = `Menampilkan ${start}-${end} dari ${studentsTotal} siswa`;
        document.getElementById('studentsPrevBtn').disabled = studentsPage <= 1;
        document.getElementById('studentsNextBtn').disabled = studentsPage >= studentsTotalPages;

        renderStudentsPaginationNumbers();
    }

    function renderStudentsPaginationNumbers() {
        const container = document.getElementById('studentsPaginationNumbers');
        const pages = [];

        if (studentsTotalPages <= 7) {
            for (let i = 1; i <= studentsTotalPages; i++) {
                pages.push(i);
            }
        } else {
            pages.push(1);
            if (studentsPage > 3) {
                pages.push('ellipsis-start');
            }

            const start = Math.max(2, studentsPage - 1);
            const end = Math.min(studentsTotalPages - 1, studentsPage + 1);
            for (let i = start; i <= end; i++) {
                pages.push(i);
            }

            if (studentsPage < studentsTotalPages - 2) {
                pages.push('ellipsis-end');
            }
            pages.push(studentsTotalPages);
        }

        container.innerHTML = pages.map(item => {
            if (typeof item !== 'number') {
                const jumpPage = item === 'ellipsis-start' ? Math.max(1, studentsPage - 5) : Math.min(studentsTotalPages, studentsPage + 5);
                const tooltip = item === 'ellipsis-start' ? '5 halaman sebelumnya' : '5 halaman berikutnya';
                return `
                    <button type="button" onclick="goToStudentsPage(${jumpPage})" title="${tooltip}"
                        class="w-10 h-10 rounded-xl border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 flex items-center justify-center">
                        <span class="material-symbols-outlined text-base">more_horiz</span>
                    </button>
                `;
            }

            const isCurrent = item === studentsPage;
            const activeClass = isCurrent ?
                'bg-primary-600 text-white border-primary-600' :
                'bg-white text-gray-700 border-gray-200 hover:bg-gray-50';
            const ariaCurrent = isCurrent ? 'aria-current="page"' : '';

            return `
                <button type="button" onclick="goToStudentsPage(${item})"
                    ${ariaCurrent}
                    class="w-10 h-10 rounded-xl border text-sm font-semibold transition ${activeClass}">
                    ${item}
                </button>
            `;
        }).join('');
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

    function downloadStudentTemplateXlsx() {
        window.location.href = '<?= base_url('api/admin/students/upload-template?format=xlsx') ?>';
    }

    async function uploadStudentsFile() {
        const input = document.getElementById('studentUploadFile');
        if (!input.files || !input.files.length) {
            alert('Pilih file XLSX/CSV terlebih dahulu');
            return;
        }

        const formData = new FormData();
        formData.append('file', input.files[0]);

        try {
            const response = await fetch('<?= base_url('api/admin/students/import') ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (result.status !== 'success') {
                alert(result.message || 'Gagal upload data siswa');
                return;
            }

            const summary = result.data || {};
            let message = `Upload selesai\nBerhasil tambah: ${summary.inserted || 0}\nBerhasil update: ${summary.updated || 0}\nGagal: ${summary.failed || 0}`;

            if (summary.failed > 0 && Array.isArray(summary.errors) && summary.errors.length > 0) {
                const firstErrors = summary.errors.slice(0, 3)
                    .map(err => `Baris ${err.line}: ${err.message}`)
                    .join('\n');
                message += `\n\nContoh error:\n${firstErrors}`;
            }

            alert(message);
            input.value = '';
            loadStudents();
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat upload data siswa');
        }
    }

    async function generateStudentAccounts() {
        const classId = document.getElementById('filterClass').value || null;
        const defaultPassword = prompt('Masukkan password default akun siswa (minimal 6 karakter):', 'siswa123');

        if (defaultPassword === null) {
            return;
        }

        if (defaultPassword.trim().length < 6) {
            alert('Password default minimal 6 karakter');
            return;
        }

        if (!confirm('Lanjut generate akun siswa otomatis? Akun yang sudah ada tidak akan ditimpa.')) {
            return;
        }

        try {
            const response = await fetch('<?= base_url('api/admin/students/generate-accounts') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    class_id: classId,
                    default_password: defaultPassword.trim()
                })
            });

            const result = await response.json();
            if (result.status !== 'success') {
                alert(result.message || 'Gagal generate akun siswa');
                return;
            }

            const summary = result.data || {};
            let message = `Generate akun selesai\nDiproses: ${summary.processed || 0}\nAkun baru: ${summary.created || 0}\nAkun dipulihkan: ${summary.restored || 0}\nDilewati (sudah ada): ${summary.skipped || 0}`;

            if (Array.isArray(summary.credentials) && summary.credentials.length > 0) {
                const preview = summary.credentials.slice(0, 5)
                    .map(item => `${item.name} -> ${item.username}`)
                    .join('\n');
                message += `\n\nContoh akun:\n${preview}`;
            }

            if (Array.isArray(summary.errors) && summary.errors.length > 0) {
                const firstErrors = summary.errors.slice(0, 3)
                    .map(err => `${err.name}: ${err.message}`)
                    .join('\n');
                message += `\n\nContoh error:\n${firstErrors}`;
            }

            alert(message);
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat generate akun siswa');
        }
    }
</script>

<?= $this->endSection() ?>