<?= $this->extend('layouts/main') ?>

<?= $this->section('sidebar') ?>
<?= $this->include('partials/sidebar_guru_piket') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900">Monitoring Real-time</h2>
    <p class="text-gray-500 mt-1">Pantau scan kehadiran siswa secara langsung</p>
</div>

<div class="card">
    <div class="card-header flex items-center justify-between">
        <h3 class="font-bold text-gray-900 flex items-center">
            <span class="material-symbols mr-2 text-success-600 animate-pulse">sensors</span>
            Scan Terbaru
        </h3>
        <span class="inline-flex items-center px-3 py-1 rounded-full bg-success-100 text-success-800 text-xs font-medium">
            <span class="inline-block w-2 h-2 bg-success-500 rounded-full mr-2 animate-pulse"></span>
            Live
        </span>
    </div>
    <div class="card-body p-0" id="monitoringFeed">
        <div class="p-8 text-center text-gray-400">
            <span class="material-symbols text-5xl mb-3 block">wifi_tethering</span>
            <p>Menunggu data scan masuk...</p>
        </div>
    </div>
</div>

<script>
    function fetchRecentScans() {
        fetch('/api/guru-piket/recent-logs', {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                const feed = document.getElementById('monitoringFeed');
                if (!data.data || data.data.length === 0) {
                    feed.innerHTML = '<div class="p-8 text-center text-gray-400"><span class="material-symbols text-5xl mb-3 block">wifi_tethering</span><p>Belum ada scan hari ini.</p></div>';
                    return;
                }
                feed.innerHTML = data.data.map(s => `
            <div class="p-4 border-b last:border-b-0 hover:bg-gray-50 flex items-center justify-between">
                <div>
                    <p class="font-semibold text-gray-900">${s.student_name ?? '-'}</p>
                    <p class="text-xs text-gray-500">${s.nis ?? ''} · ${s.class_name ?? ''}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-mono text-gray-700">${s.scan_time ?? s.created_at ?? ''}</p>
                    <span class="text-xs px-2 py-0.5 rounded-full ${s.status === 'hadir' ? 'bg-success-100 text-success-700' : 'bg-warning-100 text-warning-700'}">${s.status ?? '-'}</span>
                </div>
            </div>
        `).join('');
            })
            .catch(() => {});
    }

    fetchRecentScans();
    setInterval(fetchRecentScans, 10000); // refresh every 10 seconds
</script>

<?= $this->endSection() ?>