<?php

const BOBOT_TUGAS = 0.30;
const BOBOT_UTS   = 0.30;
const BOBOT_UAS   = 0.40;
const NILAI_LULUS = 65;

function hitungNilaiAkhir(float $tugas, float $uts, float $uas): float
{
    $nilaiAkhir = ($tugas * BOBOT_TUGAS) + ($uts * BOBOT_UTS) + ($uas * BOBOT_UAS);
    return round($nilaiAkhir, 2);
}

function tentukanGrade(float $nilaiAkhir): string
{
    if ($nilaiAkhir >= 85) {
        return 'A';
    } elseif ($nilaiAkhir >= 75) {
        return 'B';
    } elseif ($nilaiAkhir >= 65) {
        return 'C';
    } elseif ($nilaiAkhir >= 50) {
        return 'D';
    }
    return 'E';
}

function tentukanStatus(float $nilaiAkhir): string
{
    return $nilaiAkhir >= NILAI_LULUS ? 'Lulus' : 'Tidak Lulus';
}

function tentukanPredikat(float $nilaiAkhir): string
{
    if ($nilaiAkhir >= 85) {
        return 'Sangat Baik';
    } elseif ($nilaiAkhir >= 75) {
        return 'Baik';
    } elseif ($nilaiAkhir >= 65) {
        return 'Cukup';
    } elseif ($nilaiAkhir >= 50) {
        return 'Kurang';
    }
    return 'Sangat Kurang';
}

function validasiNilai($nilai): bool
{
    return is_numeric($nilai) && $nilai >= 0 && $nilai <= 100;
}

function formatAngka(float $angka): string
{
    return rtrim(rtrim(number_format($angka, 2, ',', '.'), '0'), ',');
}

$nama  = 'Andi';
$nim   = '231234567';
$tugas = 80;
$uts   = 75;
$uas   = 90;

$inputNama  = [$nama];
$inputNim   = [$nim];
$inputTugas = [$tugas];
$inputUts   = [$uts];
$inputUas   = [$uas];

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputNama  = $_POST['nama']  ?? [];
    $inputNim   = $_POST['nim']   ?? [];
    $inputTugas = $_POST['tugas'] ?? [];
    $inputUts   = $_POST['uts']   ?? [];
    $inputUas   = $_POST['uas']   ?? [];
}

$hasil = [];
$jumlah = count($inputNama);

for ($i = 0; $i < $jumlah; $i++) {
    $n  = trim($inputNama[$i]  ?? '');
    $m  = trim($inputNim[$i]   ?? '');
    $t  = $inputTugas[$i] ?? '';
    $u  = $inputUts[$i]   ?? '';
    $ua = $inputUas[$i]   ?? '';
    $no = $i + 1;

    if ($n === '' || $m === '') {
        $errors[] = "Mahasiswa #$no: nama dan NIM wajib diisi.";
        continue;
    }
    if (!validasiNilai($t) || !validasiNilai($u) || !validasiNilai($ua)) {
        $errors[] = "Mahasiswa #$no ($n): nilai Tugas, UTS, dan UAS harus berupa angka 0 sampai 100.";
        continue;
    }

    $nilaiAkhir = hitungNilaiAkhir((float)$t, (float)$u, (float)$ua);
    $grade      = tentukanGrade($nilaiAkhir);
    $status     = tentukanStatus($nilaiAkhir);
    $predikat   = tentukanPredikat($nilaiAkhir);

    $hasil[] = [
        'nama' => $n, 'nim' => $m,
        'tugas' => (float)$t, 'uts' => (float)$u, 'uas' => (float)$ua,
        'nilaiAkhir' => $nilaiAkhir, 'grade' => $grade,
        'status' => $status, 'predikat' => $predikat,
    ];
}

function e(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Penghitung Nilai Mahasiswa</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: "Segoe UI", Arial, sans-serif; background: #f1f5f9; margin: 0; padding: 24px; color: #1e293b; }
        .container { max-width: 960px; margin: 0 auto; }
        h1 { margin: 0 0 4px; }
        .sub { color: #64748b; margin-bottom: 20px; }
        .card { background: #fff; border-radius: 12px; padding: 20px; margin-bottom: 20px; box-shadow: 0 1px 4px rgba(0,0,0,.08); }
        .card h2 { margin-top: 0; font-size: 1.1rem; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #e2e8f0; font-size: .92rem; }
        th { background: #f8fafc; }
        input { width: 100%; padding: 7px 8px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: .9rem; }
        .btn { padding: 9px 16px; border: 0; border-radius: 8px; cursor: pointer; font-size: .9rem; }
        .btn-primary { background: #2563eb; color: #fff; }
        .btn-secondary { background: #e2e8f0; }
        .btn-danger { background: #fee2e2; color: #b91c1c; padding: 6px 10px; }
        .actions { margin-top: 14px; display: flex; gap: 10px; }
        .error { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; padding: 10px 14px; border-radius: 8px; margin-bottom: 14px; }
        .badge { padding: 3px 10px; border-radius: 999px; font-weight: 600; font-size: .8rem; }
        .lulus { background: #dcfce7; color: #166534; }
        .gagal { background: #fee2e2; color: #991b1b; }
        .detail { display: grid; grid-template-columns: 160px 1fr; gap: 4px 10px; }
        .detail dt { color: #64748b; }
        .detail dd { margin: 0; font-weight: 500; }
        .rumus { background: #eff6ff; padding: 8px 12px; border-radius: 8px; margin-top: 12px; font-size: .88rem; }
        .hasil-item { border-top: 1px dashed #cbd5e1; padding-top: 16px; margin-top: 16px; }
        .hasil-item:first-of-type { border-top: 0; padding-top: 0; margin-top: 0; }
        .table-wrap { overflow-x: auto; }
    </style>
</head>
<body>
<div class="container">
    <h1>Penghitung Nilai Mahasiswa</h1>
    <div class="sub">Nilai Akhir = (Tugas × 30%) + (UTS × 30%) + (UAS × 40%)</div>

    <div class="card">
        <h2>Input Data Mahasiswa</h2>

        <?php foreach ($errors as $err): ?>
            <div class="error"><?= e($err) ?></div>
        <?php endforeach; ?>

        <form method="post" action="">
            <div class="table-wrap">
                <table id="tabelInput">
                    <thead>
                        <tr>
                            <th>Nama</th><th>NIM</th><th>Tugas</th><th>UTS</th><th>UAS</th><th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php for ($i = 0; $i < max($jumlah, 1); $i++): ?>
                        <tr>
                            <td><input type="text" name="nama[]" value="<?= e((string)($inputNama[$i] ?? '')) ?>" required></td>
                            <td><input type="text" name="nim[]" value="<?= e((string)($inputNim[$i] ?? '')) ?>" required></td>
                            <td><input type="number" name="tugas[]" min="0" max="100" step="any" value="<?= e((string)($inputTugas[$i] ?? '')) ?>" required></td>
                            <td><input type="number" name="uts[]" min="0" max="100" step="any" value="<?= e((string)($inputUts[$i] ?? '')) ?>" required></td>
                            <td><input type="number" name="uas[]" min="0" max="100" step="any" value="<?= e((string)($inputUas[$i] ?? '')) ?>" required></td>
                            <td><button type="button" class="btn btn-danger" onclick="hapusBaris(this)">Hapus</button></td>
                        </tr>
                    <?php endfor; ?>
                    </tbody>
                </table>
            </div>
            <div class="actions">
                <button type="button" class="btn btn-secondary" onclick="tambahBaris()">+ Tambah Mahasiswa</button>
                <button type="submit" class="btn btn-primary">Hitung Nilai</button>
            </div>
        </form>
    </div>

    <?php if (!empty($hasil)): ?>
    <div class="card">
        <h2>Hasil Perhitungan</h2>

        <?php foreach ($hasil as $h): ?>
            <div class="hasil-item">
                <h3 style="margin:0 0 10px"><?= e($h['nama']) ?></h3>
                <dl class="detail">
                    <dt>Nama</dt><dd><?= e($h['nama']) ?></dd>
                    <dt>NIM</dt><dd><?= e($h['nim']) ?></dd>
                    <dt>Nilai Tugas</dt><dd><?= formatAngka($h['tugas']) ?></dd>
                    <dt>Nilai UTS</dt><dd><?= formatAngka($h['uts']) ?></dd>
                    <dt>Nilai UAS</dt><dd><?= formatAngka($h['uas']) ?></dd>
                    <dt>Nilai Akhir</dt><dd><?= formatAngka($h['nilaiAkhir']) ?></dd>
                    <dt>Grade</dt><dd><?= e($h['grade']) ?></dd>
                    <dt>Predikat</dt><dd><?= e($h['predikat']) ?></dd>
                    <dt>Status</dt>
                    <dd>
                        <span class="badge <?= $h['status'] === 'Lulus' ? 'lulus' : 'gagal' ?>">
                            <?= e($h['status']) ?>
                        </span>
                    </dd>
                </dl>
                <div class="rumus">
                    Nilai akhir diperoleh dari
                    (<?= formatAngka($h['tugas']) ?> × 0,3) + (<?= formatAngka($h['uts']) ?> × 0,3) + (<?= formatAngka($h['uas']) ?> × 0,4)
                    = <?= formatAngka($h['nilaiAkhir']) ?>.
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (count($hasil) > 1): ?>
    <div class="card">
        <h2>Rekap Seluruh Mahasiswa</h2>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>No</th><th>Nama</th><th>NIM</th><th>Tugas</th><th>UTS</th><th>UAS</th><th>Nilai Akhir</th><th>Grade</th><th>Predikat</th><th>Status</th></tr>
                </thead>
                <tbody>
                <?php foreach ($hasil as $i => $h): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= e($h['nama']) ?></td>
                        <td><?= e($h['nim']) ?></td>
                        <td><?= formatAngka($h['tugas']) ?></td>
                        <td><?= formatAngka($h['uts']) ?></td>
                        <td><?= formatAngka($h['uas']) ?></td>
                        <td><?= formatAngka($h['nilaiAkhir']) ?></td>
                        <td><?= e($h['grade']) ?></td>
                        <td><?= e($h['predikat']) ?></td>
                        <td><span class="badge <?= $h['status'] === 'Lulus' ? 'lulus' : 'gagal' ?>"><?= e($h['status']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>

<script>
function tambahBaris() {
    const tbody = document.querySelector('#tabelInput tbody');
    const tr = document.createElement('tr');
    tr.innerHTML =
        '<td><input type="text" name="nama[]" required></td>' +
        '<td><input type="text" name="nim[]" required></td>' +
        '<td><input type="number" name="tugas[]" min="0" max="100" step="any" required></td>' +
        '<td><input type="number" name="uts[]" min="0" max="100" step="any" required></td>' +
        '<td><input type="number" name="uas[]" min="0" max="100" step="any" required></td>' +
        '<td><button type="button" class="btn btn-danger" onclick="hapusBaris(this)">Hapus</button></td>';
    tbody.appendChild(tr);
}
function hapusBaris(btn) {
    const tbody = document.querySelector('#tabelInput tbody');
    if (tbody.rows.length > 1) btn.closest('tr').remove();
}
</script>
</body>
</html>
