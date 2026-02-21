<?= $this->extend('layouts/main') ?>

<?= $this->section('sidebar') ?>
<?= $this->include('partials/sidebar_guru_piket') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Input Ketidakhadiran</h2>
        <p class="text-gray-500 mt-1">Catat siswa sakit, izin, atau lupa scan</p>
    </div>
    <button onclick="showAddModal()"
        class="btn-primary flex items-center">
        <span class="material-symbols mr-2">add_circle</span>
        Input Baru
    </button>
</div>

<!-- Exception List -->
<div class="card">
    <div class="card-header flex items-center justify-between">
        <h3 class="font-bold text-gray-900 flex items-center">
            <span class="material-symbols mr-2 text-primary-600">list_alt</span>
            Daftar Ketidakhadiran Hari Ini
        </h3>
        <input type="date" id="exceptDate" value="<?= date('Y-m-d') ?>"
            onchange="loadExceptions(this.value)"
            class="text-sm border border-gray-300 rounded-lg px-3 py-1.5 outline-none focus:ring-2 focus:ring-primary-500">
    </div>
    <div class="card-body p-0">
        <div id="exceptionList">
            <div class="p-8 text-center text-gray-400">
                <span class="material-symbols text-4xl mb-2 block">hourglass_top</span>
                <p>Memuat data...</p>
            </div>
        </div>
    </div>
</div>

<!-- Add Exception Modal -->
<div id="addModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <div class="p-6 border-b">
            <h3 class="text-lg font-bold text-gray-900">Input Ketidakhadiran</h3>
        </div>
        <form id="exceptionForm" onsubmit="submitException(event)" class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">NIS Siswa</label>
                <input type="text" name="nis" placeholder="Masukkan NIS siswa"
                    class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                <input type="date" name="date" value="<?= date('Y-m-d') ?>"
                    class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Ketidakhadiran</label>
                <select name="exception_type"
                    class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 outline-none" required>
                    <option value="sakit">Sakit</option>
                    <option value="izin">Izin</option>
                    <option value="alpha">Alpha</option>
                    <option value="lupa_scan">Lupa Scan</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan (opsional)</label>
                <textarea name="notes" rows="2" placeholder="Keterangan tambahan..."
                    class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 outline-none resize-none"></textarea>
            </div>
            <div id="formMessage" class="hidden text-sm rounded-lg px-4 py-2"></div>
            <div class="flex space-x-3 pt-2">
                <button type="button" onclick="closeModal()"
                    class="flex-1 btn-secondary">Batal</button>
                <button type="submit" class="flex-1 btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function showAddModal() {
        document.getElementById('addModal').classList.remove('hidden');
        document.getElementById('formMessage').classList.add('hidden');
        document.getElementById('exceptionForm').reset();
        document.querySelector('[name=date]').value = new Date().toISOString().split('T')[0];
    }

    function closeModal() {
        document.getElementById('addModal').classList.add('hidden');
    }

    function submitException(e) {
        e.preventDefault();
        const form = e.target;
        const msg = document.getElementById('formMessage');
        const data = Object.fromEntries(new FormData(form));

        fetch('/api/guru-piket/exceptions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(res => res.json())
            .then(resp => {
                if (resp.status === 'success' || resp.success) {
                    closeModal();
                    loadExceptions(document.getElementById('exceptDate').value);
                } else {
                    msg.textContent = resp.message ?? 'Gagal menyimpan data.';
                    msg.className = 'text-sm rounded-lg px-4 py-2 bg-danger-50 text-danger-700';
                    msg.classList.remove('hidden');
                }
            })
            .catch(() => {
                msg.textContent = 'Terjadi kesalahan koneksi.';
                msg.className = 'text-sm rounded-lg px-4 py-2 bg-danger-50 text-danger-700';
                msg.classList.remove('hidden');
            });
    }

    function deleteException(id) {
        if (!confirm('Hapus data ketidakhadiran ini?')) return;
        fetch(`/api/guru-piket/exceptions/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(() => loadExceptions(document.getElementById('exceptDate').value))
            .catch(() => {});
    }

    function loadExceptions(date) {
        const list = document.getElementById('exceptionList');
        list.innerHTML = '<div class="p-8 text-center text-gray-400"><span class="material-symbols text-4xl mb-2 block">hourglass_top</span><p>Memuat data...</p></div>';

        fetch(`/api/guru-piket/exceptions?date=${date}`, {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                const rows = data.data ?? [];
                if (rows.length === 0) {
                    list.innerHTML = '<div class="p-8 text-center text-gray-400"><span class="material-symbols text-4xl mb-2 block">inbox</span><p>Tidak ada data ketidakhadiran untuk tanggal ini.</p></div>';
                    return;
                }

                const typeBadge = t => {
                    const map = {
                        sakit: ['warning', 'medication'],
                        izin: ['primary', 'mail'],
                        alpha: ['danger', 'cancel'],
                        lupa_scan: ['gray', 'edit']
                    };
                    const [c, icon] = map[t] ?? ['gray', 'info'];
                    return `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-${c}-100 text-${c}-700 capitalize">
                        <span class="material-symbols text-xs">${icon}</span>${t.replace('_', ' ')}
                    </span>`;
                };

                list.innerHTML = `
            <table class="table">
                <thead class="table-header">
                    <tr>
                        <th class="table-header-cell">Siswa</th>
                        <th class="table-header-cell">Kelas</th>
                        <th class="table-header-cell">Jenis</th>
                        <th class="table-header-cell">Keterangan</th>
                        <th class="table-header-cell">Dicatat</th>
                        <th class="table-header-cell">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-body">
                    ${rows.map(r => `
                        <tr class="hover:bg-gray-50">
                            <td class="table-cell">
                                <p class="font-medium text-gray-900">${r.student_name ?? '-'}</p>
                                <p class="text-xs text-gray-500 font-mono">${r.nis ?? ''}</p>
                            </td>
                            <td class="table-cell text-gray-600">${r.class_name ?? '-'}</td>
                            <td class="table-cell">${typeBadge(r.exception_type)}</td>
                            <td class="table-cell text-gray-600 text-sm">${r.notes ?? '-'}</td>
                            <td class="table-cell text-xs text-gray-500">${r.recorded_by_name ?? '-'}</td>
                            <td class="table-cell">
                                <button onclick="deleteException(${r.id})"
                                    class="p-1.5 text-danger-600 hover:bg-danger-50 rounded transition-colors" title="Hapus">
                                    <span class="material-symbols text-sm">delete</span>
                                </button>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>`;
            })
            .catch(() => {
                list.innerHTML = '<div class="p-8 text-center text-danger-400"><span class="material-symbols text-4xl mb-2 block">error</span><p>Gagal memuat data.</p></div>';
            });
    }

    loadExceptions(document.getElementById('exceptDate').value);
</script>

<?= $this->endSection() ?>