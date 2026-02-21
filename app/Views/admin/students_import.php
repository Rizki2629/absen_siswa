<?= $this->extend('layouts/main') ?>

<?= $this->section('sidebar') ?>
<?= $this->include('partials/sidebar_admin') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Header -->
<div class="flex justify-between items-center mb-6">
    <div class="flex items-center gap-3">
        <a href="<?= base_url('admin/students') ?>" class="text-gray-400 hover:text-gray-600 transition-colors">
            <span class="material-symbols text-3xl">arrow_back</span>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Import Data Siswa</h2>
            <p class="text-gray-600 mt-1">Upload data siswa dari file Excel (XLSX) atau CSV</p>
        </div>
    </div>
    <a href="<?= base_url('admin/students') ?>" class="btn-secondary flex items-center space-x-2">
        <span class="material-symbols">groups</span>
        <span>Lihat Data Siswa</span>
    </a>
</div>

<!-- Step Guide -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="card border-l-4 border-primary-500">
        <div class="card-body flex items-start gap-3">
            <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center flex-shrink-0">
                <span class="text-primary-700 font-bold text-lg">1</span>
            </div>
            <div>
                <p class="font-semibold text-gray-800 mb-1">Download Template</p>
                <p class="text-sm text-gray-600">Unduh template Excel yang sudah berisi kolom yang diperlukan agar format sesuai.</p>
            </div>
        </div>
    </div>
    <div class="card border-l-4 border-yellow-400">
        <div class="card-body flex items-start gap-3">
            <div class="w-10 h-10 rounded-full bg-yellow-50 flex items-center justify-center flex-shrink-0">
                <span class="text-yellow-700 font-bold text-lg">2</span>
            </div>
            <div>
                <p class="font-semibold text-gray-800 mb-1">Isi Data Siswa</p>
                <p class="text-sm text-gray-600">Isi data siswa di template. Perhatikan format kolom terutama Jenis Kelamin dan Tanggal Lahir.</p>
            </div>
        </div>
    </div>
    <div class="card border-l-4 border-green-500">
        <div class="card-body flex items-start gap-3">
            <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center flex-shrink-0">
                <span class="text-green-700 font-bold text-lg">3</span>
            </div>
            <div>
                <p class="font-semibold text-gray-800 mb-1">Upload File</p>
                <p class="text-sm text-gray-600">Pilih file yang sudah diisi lalu klik Upload. Sistem akan memproses dan melaporkan hasilnya.</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Left: Upload Form -->
    <div class="lg:col-span-2 space-y-6">

        <!-- Template Download -->
        <div class="card">
            <div class="card-body">
                <h3 class="text-base font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <span class="material-symbols text-primary-600">download</span>
                    Download Template
                </h3>
                <p class="text-sm text-gray-600 mb-4">Gunakan template ini agar header kolom tidak salah dan proses upload berhasil.</p>
                <button onclick="downloadStudentTemplateXlsx()" class="btn-primary flex items-center gap-2">
                    <span class="material-symbols">table_view</span>
                    Download Template XLSX
                </button>
            </div>
        </div>

        <!-- Upload File -->
        <div class="card">
            <div class="card-body">
                <h3 class="text-base font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <span class="material-symbols text-primary-600">upload_file</span>
                    Upload File Data Siswa
                </h3>

                <!-- Drop Zone -->
                <div id="dropZone"
                    class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center transition-colors cursor-pointer hover:border-primary-400 hover:bg-primary-50"
                    onclick="document.getElementById('studentUploadFile').click()"
                    ondragover="handleDragOver(event)"
                    ondragleave="handleDragLeave(event)"
                    ondrop="handleDrop(event)">
                    <span class="material-symbols text-5xl text-gray-300 mb-2">upload_file</span>
                    <p class="font-medium text-gray-700 mb-1">Klik untuk pilih file atau seret ke sini</p>
                    <p class="text-sm text-gray-500">Format: XLSX, XLS, atau CSV — maksimum 10MB</p>
                    <div id="selectedFileInfo" class="hidden mt-3 bg-white border border-gray-200 rounded-lg px-4 py-2 inline-block">
                        <span class="material-symbols text-green-600 align-middle text-base mr-1">check_circle</span>
                        <span id="selectedFileName" class="text-sm text-gray-700 font-medium"></span>
                    </div>
                </div>

                <input type="file" id="studentUploadFile" accept=".xlsx,.xls,.csv" class="hidden" onchange="handleFileSelect(event)">

                <div class="mt-4 flex items-center gap-3">
                    <button onclick="uploadStudentsFile()" id="uploadBtn" disabled
                        class="btn-primary flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span class="material-symbols">cloud_upload</span>
                        Upload & Proses
                    </button>
                    <button onclick="resetFileInput()" class="btn-secondary flex items-center gap-2">
                        <span class="material-symbols">refresh</span>
                        Reset
                    </button>
                </div>
            </div>
        </div>

        <!-- Upload Result -->
        <div id="uploadResultCard" class="card hidden">
            <div class="card-body">
                <h3 class="text-base font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <span class="material-symbols text-green-600">task_alt</span>
                    Hasil Upload
                </h3>
                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div class="bg-green-50 rounded-xl p-4 text-center">
                        <p class="text-3xl font-bold text-green-700" id="resultInserted">0</p>
                        <p class="text-sm text-green-600 mt-1">Siswa Baru</p>
                    </div>
                    <div class="bg-blue-50 rounded-xl p-4 text-center">
                        <p class="text-3xl font-bold text-blue-700" id="resultUpdated">0</p>
                        <p class="text-sm text-blue-600 mt-1">Data Diperbarui</p>
                    </div>
                    <div class="bg-red-50 rounded-xl p-4 text-center">
                        <p class="text-3xl font-bold text-red-700" id="resultFailed">0</p>
                        <p class="text-sm text-red-600 mt-1">Gagal</p>
                    </div>
                </div>
                <div id="resultErrorsContainer" class="hidden">
                    <p class="text-sm font-semibold text-red-700 mb-2">Detail Error:</p>
                    <div id="resultErrors" class="bg-red-50 rounded-lg p-3 text-sm text-red-700 space-y-1 max-h-48 overflow-y-auto"></div>
                </div>
                <div class="mt-4">
                    <a href="<?= base_url('admin/students') ?>" class="btn-primary flex items-center gap-2 w-fit">
                        <span class="material-symbols">groups</span>
                        Lihat Data Siswa
                    </a>
                </div>
            </div>
        </div>

        <!-- Upload Error -->
        <div id="uploadErrorCard" class="card hidden border border-red-200">
            <div class="card-body">
                <h3 class="text-base font-semibold text-red-700 mb-2 flex items-center gap-2">
                    <span class="material-symbols">error</span>
                    Upload Gagal
                </h3>
                <p id="uploadErrorMessage" class="text-sm text-red-600"></p>
            </div>
        </div>

    </div>

    <!-- Right: Format Guide -->
    <div class="space-y-4">
        <div class="card sticky top-4">
            <div class="card-body">
                <h3 class="text-base font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <span class="material-symbols text-primary-600">help_outline</span>
                    Panduan Format
                </h3>

                <div class="space-y-3 text-sm text-gray-700">

                    <div>
                        <p class="font-semibold text-gray-800 mb-1">Kolom yang diperlukan:</p>
                        <div class="space-y-1">
                            <div class="flex gap-2">
                                <span class="text-red-500 font-bold">*</span>
                                <span><strong>Nama</strong> — Nama lengkap siswa</span>
                            </div>
                            <div class="flex gap-2">
                                <span class="text-red-500 font-bold">*</span>
                                <span><strong>NIPD</strong> — Nomor Induk Peserta Didik</span>
                            </div>
                            <div class="flex gap-2">
                                <span class="text-red-500 font-bold">*</span>
                                <span><strong>JK</strong> — Jenis Kelamin (L/P)</span>
                            </div>
                            <div class="flex gap-2">
                                <span class="text-gray-400">○</span>
                                <span><strong>NISN</strong> — Nomor Induk Siswa Nasional</span>
                            </div>
                            <div class="flex gap-2">
                                <span class="text-gray-400">○</span>
                                <span><strong>Tempat Lahir</strong></span>
                            </div>
                            <div class="flex gap-2">
                                <span class="text-gray-400">○</span>
                                <span><strong>Tanggal Lahir</strong></span>
                            </div>
                            <div class="flex gap-2">
                                <span class="text-gray-400">○</span>
                                <span><strong>NIK</strong></span>
                            </div>
                            <div class="flex gap-2">
                                <span class="text-gray-400">○</span>
                                <span><strong>Agama</strong></span>
                            </div>
                            <div class="flex gap-2">
                                <span class="text-gray-400">○</span>
                                <span><strong>Alamat, RT, RW, Kelurahan, Kecamatan</strong></span>
                            </div>
                            <div class="flex gap-2">
                                <span class="text-gray-400">○</span>
                                <span><strong>HP</strong> — Nomor HP orang tua</span>
                            </div>
                            <div class="flex gap-2">
                                <span class="text-gray-400">○</span>
                                <span><strong>Nama Ayah, Nama Ibu</strong></span>
                            </div>
                            <div class="flex gap-2">
                                <span class="text-gray-400">○</span>
                                <span><strong>Rombel Saat Ini</strong> — nama kelas</span>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2"><span class="text-red-500 font-bold">*</span> = wajib diisi</p>
                    </div>

                    <hr class="border-gray-100">

                    <div>
                        <p class="font-semibold text-gray-800 mb-1">Aturan format:</p>
                        <ul class="space-y-1.5 text-gray-600">
                            <li class="flex gap-2">
                                <span class="material-symbols text-primary-500 text-base mt-0.5">check_small</span>
                                <span>JK diisi <strong>L</strong> (Laki-laki) atau <strong>P</strong> (Perempuan)</span>
                            </li>
                            <li class="flex gap-2">
                                <span class="material-symbols text-primary-500 text-base mt-0.5">check_small</span>
                                <span>Tanggal Lahir: format Excel atau teks seperti "15 Maret 2008"</span>
                            </li>
                            <li class="flex gap-2">
                                <span class="material-symbols text-primary-500 text-base mt-0.5">check_small</span>
                                <span>Rombel dicocokkan otomatis ke kelas yang sudah ada di sistem</span>
                            </li>
                            <li class="flex gap-2">
                                <span class="material-symbols text-primary-500 text-base mt-0.5">check_small</span>
                                <span>Siswa dengan NIPD yang sama akan diperbarui datanya (update)</span>
                            </li>
                            <li class="flex gap-2">
                                <span class="material-symbols text-primary-500 text-base mt-0.5">check_small</span>
                                <span>Baris kosong dan header otomatis diabaikan</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl p-8 text-center shadow-2xl">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600 mb-4"></div>
        <p class="text-gray-700 font-medium">Memproses file...</p>
        <p class="text-sm text-gray-500 mt-1">Mohon tunggu, jangan tutup halaman ini</p>
    </div>
</div>

<script>
    let selectedFile = null;

    function handleFileSelect(event) {
        const file = event.target.files[0];
        if (file) {
            setSelectedFile(file);
        }
    }

    function handleDragOver(event) {
        event.preventDefault();
        document.getElementById('dropZone').classList.add('border-primary-500', 'bg-primary-50');
    }

    function handleDragLeave(event) {
        document.getElementById('dropZone').classList.remove('border-primary-500', 'bg-primary-50');
    }

    function handleDrop(event) {
        event.preventDefault();
        document.getElementById('dropZone').classList.remove('border-primary-500', 'bg-primary-50');
        const file = event.dataTransfer.files[0];
        if (file) {
            const ext = file.name.split('.').pop().toLowerCase();
            if (!['xlsx', 'xls', 'csv'].includes(ext)) {
                alert('Format file tidak didukung. Gunakan XLSX, XLS, atau CSV.');
                return;
            }
            setSelectedFile(file);
        }
    }

    function setSelectedFile(file) {
        selectedFile = file;
        document.getElementById('selectedFileName').textContent = file.name + ' (' + formatFileSize(file.size) + ')';
        document.getElementById('selectedFileInfo').classList.remove('hidden');
        document.getElementById('uploadBtn').disabled = false;
        hideResultCards();
    }

    function resetFileInput() {
        selectedFile = null;
        document.getElementById('studentUploadFile').value = '';
        document.getElementById('selectedFileInfo').classList.add('hidden');
        document.getElementById('uploadBtn').disabled = true;
        hideResultCards();
    }

    function hideResultCards() {
        document.getElementById('uploadResultCard').classList.add('hidden');
        document.getElementById('uploadErrorCard').classList.add('hidden');
    }

    function formatFileSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    }

    function downloadStudentTemplateXlsx() {
        window.location.href = '<?= base_url('api/admin/students/upload-template?format=xlsx') ?>';
    }

    async function uploadStudentsFile() {
        if (!selectedFile) {
            alert('Pilih file XLSX/CSV terlebih dahulu');
            return;
        }

        const formData = new FormData();
        formData.append('file', selectedFile);

        document.getElementById('loadingOverlay').classList.remove('hidden');
        document.getElementById('uploadBtn').disabled = true;
        hideResultCards();

        try {
            const response = await fetch('<?= base_url('api/admin/students/import') ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            document.getElementById('loadingOverlay').classList.add('hidden');

            if (result.status !== 'success') {
                document.getElementById('uploadErrorMessage').textContent = result.message || 'Gagal upload data siswa';
                document.getElementById('uploadErrorCard').classList.remove('hidden');
                document.getElementById('uploadBtn').disabled = false;
                return;
            }

            const summary = result.data || {};
            document.getElementById('resultInserted').textContent = summary.inserted || 0;
            document.getElementById('resultUpdated').textContent = summary.updated || 0;
            document.getElementById('resultFailed').textContent = summary.failed || 0;

            if (summary.failed > 0 && Array.isArray(summary.errors) && summary.errors.length > 0) {
                const errorsHtml = summary.errors
                    .map(err => `<div class="py-0.5">Baris ${err.line}: ${err.message}</div>`)
                    .join('');
                document.getElementById('resultErrors').innerHTML = errorsHtml;
                document.getElementById('resultErrorsContainer').classList.remove('hidden');
            } else {
                document.getElementById('resultErrorsContainer').classList.add('hidden');
            }

            document.getElementById('uploadResultCard').classList.remove('hidden');
            document.getElementById('uploadBtn').disabled = false;

            // Scroll to result
            document.getElementById('uploadResultCard').scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });

        } catch (error) {
            console.error('Error:', error);
            document.getElementById('loadingOverlay').classList.add('hidden');
            document.getElementById('uploadErrorMessage').textContent = 'Terjadi kesalahan koneksi saat upload data siswa';
            document.getElementById('uploadErrorCard').classList.remove('hidden');
            document.getElementById('uploadBtn').disabled = false;
        }
    }
</script>

<?= $this->endSection() ?>