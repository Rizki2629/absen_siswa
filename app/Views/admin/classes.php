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

<!-- Search & Filter -->
<div class="card mb-6">
    <div class="card-body">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-3">
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari Kelas</label>
                <input type="text" id="searchClass" placeholder="Cari berdasarkan nama kelas atau wali kelas..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">&nbsp;</label>
                <button onclick="resetClassFilters()" class="w-full btn-secondary">
                    <span class="material-symbols-outlined text-sm mr-2">filter_alt_off</span>
                    Reset Filter
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Classes Table -->
<div class="card" id="classesContainer">
    <div class="card-body">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-primary-50">
                    <tr class="border-b border-primary-100">
                        <th class="text-left py-3.5 px-4 text-xs font-bold uppercase tracking-wide text-primary-700 whitespace-nowrap">No</th>
                        <th onclick="setClassSort('name')" class="text-left py-3.5 px-4 text-xs font-bold uppercase tracking-wide text-primary-700 whitespace-nowrap cursor-pointer select-none hover:bg-primary-100 transition-colors">Nama Kelas<span id="classSortIndicator_name" class="ml-1 text-primary-500"></span></th>
                        <th onclick="setClassSort('teacher')" class="text-left py-3.5 px-4 text-xs font-bold uppercase tracking-wide text-primary-700 whitespace-nowrap cursor-pointer select-none hover:bg-primary-100 transition-colors">Wali Kelas<span id="classSortIndicator_teacher" class="ml-1 text-primary-500"></span></th>
                        <th onclick="setClassSort('student_count')" class="text-center py-3.5 px-4 text-xs font-bold uppercase tracking-wide text-primary-700 whitespace-nowrap cursor-pointer select-none hover:bg-primary-100 transition-colors">Jumlah Siswa<span id="classSortIndicator_student_count" class="ml-1 text-primary-500"></span></th>
                        <th onclick="setClassSort('year')" class="text-left py-3.5 px-4 text-xs font-bold uppercase tracking-wide text-primary-700 whitespace-nowrap cursor-pointer select-none hover:bg-primary-100 transition-colors">Tahun Ajaran<span id="classSortIndicator_year" class="ml-1 text-primary-500"></span></th>
                        <th class="text-center py-3.5 px-4 text-xs font-bold uppercase tracking-wide text-primary-700 whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody id="classesTableBody">
                    <tr>
                        <td colspan="6" class="text-center py-12">
                            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
                            <p class="text-gray-500 mt-4">Memuat data kelas...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <p id="classesPaginationInfo" class="text-sm text-gray-600">Memuat data...</p>
            <div class="flex items-center gap-x-2">
                <button id="classesPrevBtn" onclick="goToClassesPage(classesPage - 1)" disabled
                    class="h-10 px-4 rounded-xl border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-1 text-sm font-medium transition">
                    <span class="material-symbols-outlined text-base">chevron_left</span>
                    <span>Sebelumnya</span>
                </button>
                <div id="classesPaginationNumbers" class="flex items-center gap-x-1"></div>
                <button id="classesNextBtn" onclick="goToClassesPage(classesPage + 1)" disabled
                    class="h-10 px-4 rounded-xl border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-1 text-sm font-medium transition">
                    <span>Berikutnya</span>
                    <span class="material-symbols-outlined text-base">chevron_right</span>
                </button>
            </div>
        </div>
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
    let allClasses = [];
    let allTeachersForClass = [];
    let classesPage = 1;
    let classesTotalPages = 1;
    let classesPerPage = 25;
    let classSortCol = '';
    let classSortDir = '';
    let classSearchDebounce = null;

    document.addEventListener('DOMContentLoaded', function() {
        loadClasses();
        loadTeachersForDropdown();
        document.getElementById('searchClass').addEventListener('input', function() {
            clearTimeout(classSearchDebounce);
            classSearchDebounce = setTimeout(() => {
                classesPage = 1;
                applyClassFilterAndRender();
            }, 300);
        });
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
            if (teacher.nip) option.textContent += ` (NIP: ${teacher.nip})`;
            if (selectedId && teacher.id == selectedId) option.selected = true;
            select.appendChild(option);
        });
    }

    function loadClasses() {
        fetch('<?= base_url('api/admin/classes') ?>', {
                credentials: 'same-origin',
                cache: 'no-store'
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    allClasses = data.data;
                    classesPage = 1;
                    applyClassFilterAndRender();
                }
            })
            .catch(() => {
                document.getElementById('classesTableBody').innerHTML = `
                    <tr><td colspan="6" class="text-center py-12 text-red-500">Gagal memuat data kelas</td></tr>
                `;
            });
    }

    function applyClassFilterAndRender() {
        const term = (document.getElementById('searchClass').value || '').toLowerCase();
        let list = allClasses.filter(c =>
            (c.name || '').toLowerCase().includes(term) ||
            (c.teacher_name || c.homeroom_teacher || '').toLowerCase().includes(term)
        );

        if (classSortCol) {
            list = [...list].sort((a, b) => {
                let va, vb;
                if (classSortCol === 'teacher') {
                    va = String(a.teacher_name || a.homeroom_teacher || '').toLowerCase();
                    vb = String(b.teacher_name || b.homeroom_teacher || '').toLowerCase();
                } else if (classSortCol === 'student_count') {
                    return classSortDir === 'asc' ? Number(a.student_count || 0) - Number(b.student_count || 0) : Number(b.student_count || 0) - Number(a.student_count || 0);
                } else {
                    va = String(a[classSortCol] || '').toLowerCase();
                    vb = String(b[classSortCol] || '').toLowerCase();
                }
                if (va < vb) return classSortDir === 'asc' ? -1 : 1;
                if (va > vb) return classSortDir === 'asc' ? 1 : -1;
                return 0;
            });
        }

        const total = list.length;
        classesTotalPages = Math.max(1, Math.ceil(total / classesPerPage));
        if (classesPage > classesTotalPages) classesPage = classesTotalPages;

        const offset = (classesPage - 1) * classesPerPage;
        renderClasses(list.slice(offset, offset + classesPerPage), offset);
        updateClassesPagination(total, offset);
    }

    function renderClasses(classes, offset) {
        const tbody = document.getElementById('classesTableBody');

        if (!classes || classes.length === 0) {
            tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-12 text-gray-500">
                    <span class="material-symbols-outlined text-5xl text-gray-300 block mb-2">class</span>
                    <p>Belum ada data kelas</p>
                    <button onclick="openAddClassModal()" class="btn-primary mt-4">Tambah Kelas Pertama</button>
                </td>
            </tr>
        `;
            return;
        }

        tbody.innerHTML = classes.map((cls, index) => {
            const rowNumber = offset + index + 1;
            const waliKelas = cls.teacher_name || cls.homeroom_teacher || '-';
            return `
            <tr class="bg-white border-b border-gray-100 hover:bg-gray-50">
                <td class="py-3 px-4 text-gray-500 font-medium">${rowNumber}</td>
                <td class="py-3 px-4 font-medium text-gray-900">${cls.name}</td>
                <td class="py-3 px-4 text-gray-700">${waliKelas}</td>
                <td class="py-3 px-4 text-center">
                    <span class="inline-flex items-center gap-1 text-gray-700">
                        <span class="material-symbols-outlined text-sm text-primary-500">groups</span>
                        ${cls.student_count || 0} Siswa
                    </span>
                </td>
                <td class="py-3 px-4 text-gray-700">${cls.year || cls.academic_year || '-'}</td>
                <td class="py-3 px-4 text-center">
                    <button onclick="editClass(${cls.id})" class="text-primary-600 hover:text-primary-800 mr-2 p-1 rounded focus:outline-none" style="border:none;background:none;box-shadow:none;">
                        <span class="material-symbols-outlined">edit</span>
                    </button>
                    <button onclick="deleteClass(${cls.id})" class="text-danger-600 hover:text-danger-800 p-1 rounded focus:outline-none" style="border:none;background:none;box-shadow:none;">
                        <span class="material-symbols-outlined">delete</span>
                    </button>
                </td>
            </tr>
        `;
        }).join('');
    }

    function setClassSort(col) {
        if (classSortCol === col) {
            if (classSortDir === 'asc') classSortDir = 'desc';
            else if (classSortDir === 'desc') {
                classSortCol = '';
                classSortDir = '';
            } else classSortDir = 'asc';
        } else {
            classSortCol = col;
            classSortDir = 'asc';
        }
        updateClassSortIndicators();
        classesPage = 1;
        applyClassFilterAndRender();
    }

    function updateClassSortIndicators() {
        ['name', 'teacher', 'student_count', 'year'].forEach(col => {
            const el = document.getElementById('classSortIndicator_' + col);
            if (!el) return;
            if (classSortCol === col) {
                el.textContent = classSortDir === 'asc' ? '↑' : '↓';
            } else {
                el.textContent = '';
            }
        });
    }

    function resetClassFilters() {
        document.getElementById('searchClass').value = '';
        classSortCol = '';
        classSortDir = '';
        updateClassSortIndicators();
        classesPage = 1;
        applyClassFilterAndRender();
    }

    function updateClassesPagination(total, offset) {
        const start = total === 0 ? 0 : offset + 1;
        const end = Math.min(offset + classesPerPage, total);
        document.getElementById('classesPaginationInfo').textContent = `Menampilkan ${start}–${end} dari ${total} kelas`;
        document.getElementById('classesPrevBtn').disabled = classesPage <= 1;
        document.getElementById('classesNextBtn').disabled = classesPage >= classesTotalPages;
        renderClassesPaginationNumbers();
    }

    function renderClassesPaginationNumbers() {
        const container = document.getElementById('classesPaginationNumbers');
        const pages = [];
        if (classesTotalPages <= 7) {
            for (let i = 1; i <= classesTotalPages; i++) pages.push(i);
        } else {
            pages.push(1);
            if (classesPage > 3) pages.push('ellipsis-start');
            const s = Math.max(2, classesPage - 1);
            const e = Math.min(classesTotalPages - 1, classesPage + 1);
            for (let i = s; i <= e; i++) pages.push(i);
            if (classesPage < classesTotalPages - 2) pages.push('ellipsis-end');
            pages.push(classesTotalPages);
        }
        container.innerHTML = pages.map(item => {
            if (typeof item !== 'number') {
                const jp = item === 'ellipsis-start' ? Math.max(1, classesPage - 5) : Math.min(classesTotalPages, classesPage + 5);
                return `<button type="button" onclick="goToClassesPage(${jp})" class="w-10 h-10 rounded-xl border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 flex items-center justify-center"><span class="material-symbols-outlined text-base">more_horiz</span></button>`;
            }
            const isCurrent = item === classesPage;
            const cls = isCurrent ? 'bg-primary-600 text-white border-primary-600' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50';
            return `<button type="button" onclick="goToClassesPage(${item})" class="w-10 h-10 rounded-xl border text-sm font-semibold transition ${cls}">${item}</button>`;
        }).join('');
    }

    function goToClassesPage(page) {
        if (page < 1 || page > classesTotalPages || page === classesPage) return;
        classesPage = page;
        applyClassFilterAndRender();
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
        const cls = allClasses.find(c => c.id == id);
        if (!cls) {
            alert('Data kelas tidak ditemukan');
            return;
        }
        document.getElementById('classModalTitle').textContent = 'Edit Kelas';
        document.getElementById('classId').value = cls.id;
        document.getElementById('className').value = cls.name || '';
        document.getElementById('classLevel').value = cls.grade || '';
        populateTeacherDropdown(cls.teacher_id);
        document.getElementById('academicYear').value = cls.year || cls.academic_year || '';
        document.getElementById('classModal').style.display = 'flex';
    }

    async function deleteClass(id) {
        if (!confirm('Apakah Anda yakin ingin menghapus kelas ini?')) return;
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
            alert('Terjadi kesalahan saat menghapus kelas');
        }
    }

    document.getElementById('classForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const id = document.getElementById('classId').value;
        const payload = {
            name: document.getElementById('className').value,
            level: document.getElementById('classLevel').value,
            teacher_id: document.getElementById('classTeacher').value || null,
            academic_year: document.getElementById('academicYear').value
        };

        let url = '<?= base_url('api/admin/classes') ?>';
        let method = 'POST';
        if (id) {
            url += '/' + id;
            method = 'PUT';
        }

        try {
            const resp = await fetch(url, {
                method,
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