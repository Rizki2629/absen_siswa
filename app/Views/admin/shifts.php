<?= $this->extend('layouts/main') ?>

<?= $this->section('sidebar') ?>
<?= $this->include('partials/sidebar_admin') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Header -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Pengaturan Shift</h2>
        <p class="text-gray-600 mt-1">Kelola jam shift masuk dan pulang, serta kelas yang menggunakan shift</p>
    </div>
    <button onclick="openAddShiftModal()" class="btn-primary flex items-center space-x-2">
        <span class="material-symbols">add</span>
        <span>Tambah Shift</span>
    </button>
</div>

<!-- Shifts List -->
<div id="shiftsContainer">
    <div class="text-center py-12 text-gray-500">
        <svg class="animate-spin h-8 w-8 text-primary-600 mx-auto mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
        <p>Memuat data shift...</p>
    </div>
</div>

<!-- Add/Edit Shift Modal -->
<div id="shiftModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h3 class="text-xl font-bold text-gray-900" id="shiftModalTitle">Tambah Shift Baru</h3>
            <button onclick="closeShiftModal()" class="text-gray-400 hover:text-gray-600">
                <span class="material-symbols">close</span>
            </button>
        </div>

        <form id="shiftForm" class="p-6 space-y-4">
            <input type="hidden" id="shiftId">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Shift *</label>
                <input type="text" id="shiftName" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500"
                    placeholder="Contoh: Shift Pagi">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jam Masuk *</label>
                    <input type="time" id="checkInStart" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jam Pulang *</label>
                    <input type="time" id="checkOutStart" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Toleransi Telat (menit)</label>
                <input type="number" id="lateTolerance" value="15" min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kelas yang Menggunakan Shift Ini</label>
                <div id="classCheckboxes" class="space-y-2 max-h-40 overflow-y-auto border border-gray-200 rounded-xl p-3">
                    <p class="text-gray-400 text-sm">Memuat kelas...</p>
                </div>
                <p class="text-xs text-gray-500 mt-1">Centang kelas yang akan menggunakan shift ini</p>
            </div>

            <div class="flex items-center">
                <input type="checkbox" id="isActive" checked
                    class="w-4 h-4 text-primary-600 rounded focus:ring-primary-500">
                <label for="isActive" class="ml-2 text-sm text-gray-700">Shift Aktif</label>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closeShiftModal()" class="btn-secondary">Batal</button>
                <button type="submit" class="btn-primary" id="saveShiftBtn">
                    <span class="material-symbols mr-2">save</span>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Toast -->
<div id="toast" class="fixed bottom-6 right-6 z-50 hidden">
    <div class="bg-gray-900 text-white px-6 py-3 rounded-xl shadow-lg flex items-center gap-3">
        <span class="material-symbols text-xl" id="toastIcon">check_circle</span>
        <span id="toastMessage"></span>
    </div>
</div>

<script>
    let allClasses = [];
    let shifts = [];
    const API_BASE = '<?= base_url('api/admin') ?>';
    const fetchOpts = {
        credentials: 'include',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        loadClasses();
        loadShifts();
    });

    async function loadClasses() {
        try {
            const resp = await fetch(`${API_BASE}/classes`, fetchOpts);
            const data = await resp.json();
            allClasses = data.data || [];
        } catch (e) {
            console.error('Gagal memuat kelas:', e);
        }
    }

    async function loadShifts() {
        try {
            const resp = await fetch(`${API_BASE}/shifts`, fetchOpts);
            const data = await resp.json();
            shifts = data.data || [];
            renderShifts();
        } catch (e) {
            console.error('Gagal memuat shift:', e);
            document.getElementById('shiftsContainer').innerHTML = `
                <div class="card p-8 text-center text-red-500">
                    <span class="material-symbols text-4xl mb-2">error</span>
                    <p>Gagal memuat data shift</p>
                </div>`;
        }
    }

    function renderShifts() {
        const container = document.getElementById('shiftsContainer');

        if (shifts.length === 0) {
            container.innerHTML = `
                <div class="card">
                    <div class="card-body">
                        <div class="text-center py-12 text-gray-500">
                            <span class="material-symbols text-5xl text-gray-300 mb-2">schedule</span>
                            <p>Belum ada data shift</p>
                            <button onclick="openAddShiftModal()" class="btn-primary mt-4">Tambah Shift Pertama</button>
                        </div>
                    </div>
                </div>`;
            return;
        }

        let html = '<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">';

        shifts.forEach(shift => {
            const classNames = (shift.classes || []).map(c => c.name).join(', ') || '<span class="text-gray-400 italic">Belum ada kelas</span>';
            const statusBadge = shift.is_active == 1 ?
                '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>' :
                '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Nonaktif</span>';

            html += `
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">${escHtml(shift.name)}</h3>
                                ${statusBadge}
                            </div>
                            <div class="flex gap-2">
                                <button onclick="editShift(${shift.id})" class="p-2 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition">
                                    <span class="material-symbols">edit</span>
                                </button>
                                <button onclick="deleteShift(${shift.id})" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                                    <span class="material-symbols">delete</span>
                                </button>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <div class="bg-blue-50 rounded-xl p-3 text-center">
                                <p class="text-xs text-gray-500 mb-1">Jam Masuk</p>
                                <p class="text-lg font-bold text-blue-700">${escHtml(shift.check_in_start || '-')}</p>
                            </div>
                            <div class="bg-orange-50 rounded-xl p-3 text-center">
                                <p class="text-xs text-gray-500 mb-1">Toleransi</p>
                                <p class="text-lg font-bold text-orange-700">${shift.late_tolerance || 0} <span class="text-xs font-normal">menit</span></p>
                            </div>
                            <div class="bg-green-50 rounded-xl p-3 text-center">
                                <p class="text-xs text-gray-500 mb-1">Jam Pulang</p>
                                <p class="text-lg font-bold text-green-700">${escHtml(shift.check_out_start || '-')}</p>
                            </div>
                        </div>
                        <div class="border-t border-gray-100 pt-3">
                            <p class="text-xs text-gray-500 mb-1">Kelas:</p>
                            <p class="text-sm text-gray-700">${classNames}</p>
                        </div>
                    </div>
                </div>`;
        });

        html += '</div>';
        container.innerHTML = html;
    }

    function openAddShiftModal() {
        document.getElementById('shiftModalTitle').textContent = 'Tambah Shift Baru';
        document.getElementById('shiftId').value = '';
        document.getElementById('shiftName').value = '';
        document.getElementById('checkInStart').value = '07:00';
        document.getElementById('checkOutStart').value = '14:00';
        document.getElementById('lateTolerance').value = '15';
        document.getElementById('isActive').checked = true;
        renderClassCheckboxes([]);
        document.getElementById('shiftModal').style.display = 'flex';
    }

    async function editShift(id) {
        try {
            const resp = await fetch(`${API_BASE}/shifts/${id}`, fetchOpts);
            const data = await resp.json();
            if (!data.success) return;

            const shift = data.data;
            document.getElementById('shiftModalTitle').textContent = 'Edit Shift';
            document.getElementById('shiftId').value = shift.id;
            document.getElementById('shiftName').value = shift.name;
            document.getElementById('checkInStart').value = shift.check_in_start || '';
            document.getElementById('checkOutStart').value = shift.check_out_start || '';
            document.getElementById('lateTolerance').value = shift.late_tolerance || 0;
            document.getElementById('isActive').checked = shift.is_active == 1;

            const assignedIds = (shift.classes || []).map(c => parseInt(c.id));
            renderClassCheckboxes(assignedIds);

            document.getElementById('shiftModal').style.display = 'flex';
        } catch (e) {
            showToast('Gagal memuat data shift', 'error');
        }
    }

    function renderClassCheckboxes(selectedIds = []) {
        const container = document.getElementById('classCheckboxes');
        if (allClasses.length === 0) {
            container.innerHTML = '<p class="text-gray-400 text-sm">Belum ada kelas terdaftar</p>';
            return;
        }

        let html = '';
        allClasses.forEach(cls => {
            const checked = selectedIds.includes(parseInt(cls.id)) ? 'checked' : '';
            html += `
                <label class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 p-1 rounded">
                    <input type="checkbox" value="${cls.id}" ${checked} class="class-checkbox w-4 h-4 text-primary-600 rounded focus:ring-primary-500">
                    <span class="text-sm text-gray-700">${escHtml(cls.name)}</span>
                </label>`;
        });
        container.innerHTML = html;
    }

    function closeShiftModal() {
        document.getElementById('shiftModal').style.display = 'none';
    }

    async function deleteShift(id) {
        if (!confirm('Apakah Anda yakin ingin menghapus shift ini? Semua kelas yang terkait akan di-unassign.')) return;

        try {
            const resp = await fetch(`${API_BASE}/shifts/${id}`, {
                method: 'DELETE',
                credentials: 'include',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const data = await resp.json();
            if (data.success) {
                showToast('Shift berhasil dihapus', 'success');
                loadShifts();
            } else {
                showToast(data.message || 'Gagal menghapus shift', 'error');
            }
        } catch (e) {
            showToast('Gagal menghapus shift', 'error');
        }
    }

    document.getElementById('shiftForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const id = document.getElementById('shiftId').value;
        const classCheckboxes = document.querySelectorAll('.class-checkbox:checked');
        const classIds = Array.from(classCheckboxes).map(cb => parseInt(cb.value));

        const payload = {
            name: document.getElementById('shiftName').value,
            check_in_start: document.getElementById('checkInStart').value,
            check_in_end: document.getElementById('checkInStart').value,
            check_out_start: document.getElementById('checkOutStart').value,
            check_out_end: document.getElementById('checkOutStart').value,
            late_tolerance: parseInt(document.getElementById('lateTolerance').value) || 0,
            is_active: document.getElementById('isActive').checked ? 1 : 0,
            class_ids: classIds
        };

        const saveBtn = document.getElementById('saveShiftBtn');
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<svg class="animate-spin h-5 w-5 text-white inline mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>Menyimpan...';

        try {
            const url = id ? `${API_BASE}/shifts/${id}` : `${API_BASE}/shifts`;
            const method = id ? 'PUT' : 'POST';

            const resp = await fetch(url, {
                method: method,
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const data = await resp.json();
            if (data.success) {
                showToast(data.message || 'Shift berhasil disimpan', 'success');
                closeShiftModal();
                loadShifts();
            } else {
                showToast(data.message || 'Gagal menyimpan shift', 'error');
            }
        } catch (e) {
            showToast('Gagal menyimpan shift', 'error');
        } finally {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<span class="material-symbols mr-2">save</span>Simpan';
        }
    });

    function escHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        const icon = document.getElementById('toastIcon');
        const msg = document.getElementById('toastMessage');
        msg.textContent = message;
        icon.textContent = type === 'success' ? 'check_circle' : 'error';
        toast.querySelector('div').className = `${type === 'success' ? 'bg-green-700' : 'bg-red-700'} text-white px-6 py-3 rounded-xl shadow-lg flex items-center gap-3`;
        toast.classList.remove('hidden');
        setTimeout(() => toast.classList.add('hidden'), 3500);
    }
</script>

<?= $this->endSection() ?>