<?php
// =====================================================================
//  includes/fungsi.php — Fungsi bantu (label lokasi, badge, CSRF)
// =====================================================================

// Label ramah untuk tiap lokasi (nama scene Unity)
function labelLokasi($lokasi)
{
    $peta = [
        'VRLab'                 => 'VR Lab (Umum)',
        'VRLabSimulation'       => 'Cair & Semi Padat',
        'VRLabSimulation_Padat' => 'Padat',
    ];
    return $peta[$lokasi] ?? $lokasi;
}

// Kelas warna badge berdasarkan lokasi
function kelasLokasi($lokasi)
{
    if ($lokasi === 'VRLabSimulation')       return 'cair';
    if ($lokasi === 'VRLabSimulation_Padat') return 'padat';
    return '';
}

// Cetak input tersembunyi berisi token CSRF (untuk form)
function csrfField()
{
    $t = $_SESSION['csrf'] ?? '';
    return '<input type="hidden" name="csrf" value="' . htmlspecialchars($t) . '">';
}

// Verifikasi token CSRF dari POST
function csrfValid()
{
    return isset($_POST['csrf'], $_SESSION['csrf'])
        && hash_equals($_SESSION['csrf'], $_POST['csrf']);
}
