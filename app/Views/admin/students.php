<?= $this->extend('layouts/main') ?>

<?= $this->section('sidebar') ?>
<?= $this->include('partials/sidebar_admin') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Header -->
<div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Data Siswa</h2>
        <p class="text-gray-600 mt-1">Kelola data siswa dan informasi absensi</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="<?= base_url('admin/students-import') ?>" class="btn-secondary flex items-center justify-center space-x-2 flex-1 md:flex-none">
            <span class="material-symbols-outlined text-lg">upload_file</span>
            <span class="hidden sm:inline">Import Excel</span>
            <span class="sm:hidden">Import</span>
        </a>
        <button onclick="generateStudentAccounts()" class="btn-secondary flex items-center justify-center space-x-2 flex-1 md:flex-none">
            <span class="material-symbols-outlined text-lg">manage_accounts</span>
            <span class="hidden sm:inline">Generate Akun</span>
            <span class="sm:hidden">Akun</span>
        </button>
        <button onclick="openAddStudentModal()" class="btn-primary flex items-center justify-center space-x-2 flex-1 md:flex-none">
            <span class="material-symbols-outlined text-lg">add</span>
            <span>Tambah</span>
        </button>
    </div>
</div>

<!-- Search & Filter -->
<div class="card mb-6">
    <div class="card-body">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
            <div class="md:col-span-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <span class="material-symbols-outlined text-base align-middle mr-1">search</span>
                    Cari Siswa
                </label>
                <input type="text" id="searchStudent" placeholder="Cari berdasarkan nama atau NIS..."
                    onkeyup="filterStudents()"
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
            </div>
            <div class="md:col-span-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <span class="material-symbols-outlined text-base align-middle mr-1">filter_list</span>
                    Filter Kelas
                </label>
                <select id="filterClass" onchange="filterStudents()" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                    <option value="">Semua Kelas</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">&nbsp;</label>
                <button onclick="resetFilters()" class="w-full btn-secondary py-3">
                    <span class="material-symbols-outlined text-lg mr-2">filter_alt_off</span>
                    <span class="hidden sm:inline">Reset</span>
                    <span class="sm:hidden">Reset</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Students Table -->
<div class="card">
    <div class="card-body">
        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-primary-50 to-primary-100">
                    <tr class="border-b-2 border-primary-200">
                        <th class="text-left py-4 px-4 text-xs font-bold uppercase tracking-wide text-primary-800 whitespace-nowrap">No</th>
                        <th onclick="setSortColumn('nis')" class="text-left py-4 px-4 text-xs font-bold uppercase tracking-wide text-primary-800 whitespace-nowrap cursor-pointer select-none hover:bg-primary-200 transition-colors rounded-lg">
                            NIS<span id="sortIndicator_nis" class="ml-1 text-primary-600 text-sm"></span>
                        </th>
                        <th onclick="setSortColumn('name')" class="text-left py-4 px-4 text-xs font-bold uppercase tracking-wide text-primary-800 whitespace-nowrap cursor-pointer select-none hover:bg-primary-200 transition-colors rounded-lg">
                            Nama Siswa<span id="sortIndicator_name" class="ml-1 text-primary-600 text-sm"></span>
                        </th>
                        <th onclick="setSortColumn('gender')" class="text-center py-4 px-4 text-xs font-bold uppercase tracking-wide text-primary-800 whitespace-nowrap cursor-pointer select-none hover:bg-primary-200 transition-colors rounded-lg">
                            L/P<span id="sortIndicator_gender" class="ml-1 text-primary-600 text-sm"></span>
                        </th>
                        <th onclick="setSortColumn('class')" class="text-left py-4 px-4 text-xs font-bold uppercase tracking-wide text-primary-800 whitespace-nowrap cursor-pointer select-none hover:bg-primary-200 transition-colors rounded-lg">
                            Kelas<span id="sortIndicator_class" class="ml-1 text-primary-600 text-sm"></span>
                        </th>
                        <th class="text-center py-4 px-4 text-xs font-bold uppercase tracking-wide text-primary-800 whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody id="studentsTable">
                    <tr>
                        <td colspan="6" class="text-center py-16">
                            <div class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-primary-200 border-t-primary-600"></div>
                            <p class="text-gray-500 mt-4 font-medium">Memuat data siswa...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="md:hidden space-y-3" id="studentsMobile">
            <div class="text-center py-12">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-primary-200 border-t-primary-600"></div>
                <p class="text-gray-500 mt-4">Memuat data...</p>
            </div>
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
<div id="studentModal" class="fixed inset-0 bg-black bg-opacity-60 z-50 items-center justify-center p-4 backdrop-blur-sm" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-hidden flex flex-col animate-fade-in">
        <!-- Modal Header -->
        <div class="sticky top-0 bg-gradient-to-r from-primary-600 to-primary-700 px-6 py-5 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-white">person_add</span>
                </div>
                <h3 class="text-xl font-bold text-white" id="studentModalTitle">Tambah Siswa Baru</h3>
            </div>
            <button onclick="closeStudentModal()" class="text-white hover:bg-white hover:bg-opacity-20 rounded-lg p-2 transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="studentForm" class="p-6 space-y-5 overflow-y-auto flex-1">
            <input type="hidden" id="studentId" name="student_id">

            <!-- Identitas Section -->
            <div class="space-y-4">
                <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wide flex items-center">
                    <span class="material-symbols-outlined text-primary-600 mr-2">badge</span>
                    Identitas Siswa
                </h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            NIS <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="studentNis" name="nis" required
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all"
                            placeholder="Nomor Induk Siswa">
                        <p class="text-xs text-gray-500 mt-1">Contoh: 2024001</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            NISN <span class="text-gray-400">(opsional)</span>
                        </label>
                        <input type="text" id="studentNisn" name="nisn"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all"
                            placeholder="Nomor Induk Siswa Nasional">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="studentName" name="name" required
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all"
                        placeholder="Nama lengkap siswa">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Jenis Kelamin <span class="text-red-500">*</span>
                        </label>
                        <select id="studentGender" name="gender" required
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                            <option value="">Pilih jenis kelamin</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Kelas <span class="text-red-500">*</span>
                        </label>
                        <select id="studentClass" name="class_id" required
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                            <option value="">Pilih kelas</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Kontak Section -->
            <div class="space-y-4 pt-4 border-t-2 border-gray-100">
                <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wide flex items-center">
                    <span class="material-symbols-outlined text-primary-600 mr-2">contacts</span>
                    Informasi Kontak
                </h4>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        No. HP Orang Tua <span class="text-gray-400">(opsional)</span>
                    </label>
                    <input type="text" id="parentPhone" name="parent_phone"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all"
                        placeholder="08xxxxxxxxxx">
                    <p class="text-xs text-gray-500 mt-1">Untuk notifikasi absensi</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Alamat <span class="text-gray-400">(opsional)</span>
                    </label>
                    <textarea id="studentAddress" name="address" rows="3"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all"
                        placeholder="Alamat lengkap siswa"></textarea>
                </div>
            </div>

            <!-- Status Section -->
            <div class="pt-4 border-t-2 border-gray-100">
                <label class="flex items-center space-x-3 cursor-pointer group">
                    <input type="checkbox" id="studentActive" name="is_active" checked
                        class="w-5 h-5 text-primary-600 rounded-lg focus:ring-2 focus:ring-primary-500 border-2 border-gray-300">
                    <div>
                        <span class="text-sm font-semibold text-gray-700 group-hover:text-primary-600 transition-colors">Siswa Aktif</span>
                        <p class="text-xs text-gray-500">Siswa dapat melakukan absensi</p>
                    </div>
                </label>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col-reverse md:flex-row justify-end gap-3 pt-6 border-t-2 border-gray-100">
                <button type="button" onclick="closeStudentModal()" class="btn-secondary py-3 px-6">
                    <span class="material-symbols-outlined mr-2">close</span>
                    Batal
                </button>
                <button type="submit" class="btn-primary py-3 px-6">
                    <span class="material-symbols-outlined mr-2">save</span>
                    Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    @keyframes fade-in {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }
    .animate-fade-in {
        animation: fade-in 0.2s ease-out;
    }
</style>

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
        const mobileContainer = document.getElementById('studentsMobile');

        if (!students || students.length === 0) {
            const emptyState = `
                <div class="text-center py-16 text-gray-500">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-4">
                        <span class="material-symbols-outlined text-5xl text-gray-300">groups</span>
                    </div>
                    <p class="text-lg font-semibold text-gray-700 mb-2">Belum ada data siswa</p>
                    <p class="text-sm text-gray-500 mb-6">Mulai tambahkan siswa untuk mengelola data absensi</p>
                    <button onclick="openAddStudentModal()" class="btn-primary inline-flex items-center">
                        <span class="material-symbols-outlined mr-2">add</span>
                        Tambah Siswa Pertama
                    </button>
                </div>
            `;
            tbody.innerHTML = `<tr><td colspan="6">${emptyState}</td></tr>`;
            mobileContainer.innerHTML = emptyState;
            return;
        }

        const toTitleCase = (text) => {
            return String(text || '')
                .toLowerCase()
                .replace(/\b\w/g, char => char.toUpperCase())
                .trim();
        };

        const formatClassLabel = (value) => {
            const raw = String(value || '').trim();
            if (raw === '') {
                return '';
            }
            return raw.replace(/^kelas\s*/i, '').replace(/\s+/g, ' ').trim();
        };

        const getClassDisplay = (student) => {
            const classNameFromApi = String(student.class_name || '').trim();
            const classNameFromMap = classesById[String(student.class_id || '')] || '';
            const normalized = formatClassLabel(classNameFromApi || classNameFromMap);
            return normalized !== '' ? normalized : 'Belum diatur';
        };

        const getGenderBadge = (gender) => {
            return gender === 'L' 
                ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">L</span>'
                : '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-pink-100 text-pink-800">P</span>';
        };

        // Desktop Table View
        tbody.innerHTML = students.map((student, index) => {
            const rowNumber = ((studentsPage - 1) * studentsPerPage) + index + 1;
            const classDisplay = getClassDisplay(student);
            return `
        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
            <td class="py-4 px-4 text-gray-400 font-medium text-sm">${rowNumber}</td>
            <td class="py-4 px-4">
                <span class="font-mono text-sm font-semibold text-gray-900">${student.nis}</span>
            </td>
            <td class="py-4 px-4">
                <div class="font-semibold text-gray-900">${toTitleCase(student.name)}</div>
                ${student.nisn ? `<div class="text-xs text-gray-500 mt-0.5">NISN: ${student.nisn}</div>` : ''}
            </td>
            <td class="py-4 px-4 text-center">${getGenderBadge(student.gender)}</td>
            <td class="py-4 px-4">
                <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-semibold bg-primary-50 text-primary-700">
                    ${classDisplay}
                </span>
            </td>
            <td class="py-4 px-4">
                <div class="flex items-center justify-center space-x-1">
                    <button onclick="editStudent(${student.id})" 
                        title="Edit data siswa"
                        class="p-2 text-primary-600 hover:bg-primary-50 rounded-lg transition-colors">
                        <span class="material-symbols-outlined text-xl">edit</span>
                    </button>
                    <button onclick="deleteStudent(${student.id})" 
                        title="Hapus siswa"
                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                        <span class="material-symbols-outlined text-xl">delete</span>
                    </button>
                </div>
            </td>
        </tr>
    `;
        }).join('');

        // Mobile Card View
        mobileContainer.innerHTML = students.map((student, index) => {
            const rowNumber = ((studentsPage - 1) * studentsPerPage) + index + 1;
            const classDisplay = getClassDisplay(student);
            return `
        <div class="bg-white border border-gray-200 rounded-xl p-4 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <div class="flex-1">
                    <div class="flex items-center space-x-2 mb-1">
                        <span class="text-xs font-semibold text-gray-400">#${rowNumber}</span>
                        ${getGenderBadge(student.gender)}
                    </div>
                    <h3 class="font-bold text-gray-900 text-lg">${toTitleCase(student.name)}</h3>
                    <p class="text-sm text-gray-500 font-mono mt-0.5">NIS: ${student.nis}</p>
                </div>
                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-primary-100 text-primary-700">
                    ${classDisplay}
                </span>
            </div>
            ${student.nisn ? `<p class="text-xs text-gray-500 mb-3">NISN: ${student.nisn}</p>` : ''}
            <div class="flex space-x-2 pt-3 border-t border-gray-100">
                <button onclick="editStudent(${student.id})" 
                    class="flex-1 btn-secondary text-sm py-2">
                    <span class="material-symbols-outlined text-base mr-1">edit</span>
                    Edit
                </button>
                <button onclick="deleteStudent(${student.id})" 
                    class="flex-1 bg-red-50 text-red-600 hover:bg-red-100 font-semibold rounded-xl py-2 text-sm transition-colors">
                    <span class="material-symbols-outlined text-base mr-1">delete</span>
                    Hapus
                </button>
            </div>
        </div>
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
        ['nis', 'name', 'gender', 'class'].forEach(col => {
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
                document.getElementById('studentNisn').value = student.nisn || '';
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
            nisn: document.getElementById('studentNisn').value || null,
            name: document.getElementById('studentName').value,
            class_id: document.getElementById('studentClass').value,
            gender: document.getElementById('studentGender').value,
            parent_phone: document.getElementById('parentPhone').value || null,
            address: document.getElementById('studentAddress').value || null,
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
                alert(studentId ? 'Data siswa berhasil diperbarui! ✓' : 'Siswa berhasil ditambahkan! ✓');
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