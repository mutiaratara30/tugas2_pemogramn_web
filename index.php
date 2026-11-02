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

function e(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

$inputNama  = ['Andi'];
$inputNim   = ['231234567'];
$inputTugas = [80];
$inputUts   = [75];
$inputUas   = [90];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputNama  = $_POST['nama']  ?? [];
    $inputNim   = $_POST['nim']   ?? [];
    $inputTugas = $_POST['tugas'] ?? [];
    $inputUts   = $_POST['uts']   ?? [];
    $inputUas   = $_POST['uas']   ?? [];
}

$errors = [];
$hasil  = [];
$jumlah = count($inputNama);

for ($i = 0; $i < $jumlah; $i++) {
    $nama  = trim($inputNama[$i]  ?? '');
    $nim   = trim($inputNim[$i]   ?? '');
    $tugas = $inputTugas[$i] ?? '';
    $uts   = $inputUts[$i]   ?? '';
    $uas   = $inputUas[$i]   ?? '';
    $no    = $i + 1;

    if ($nama === '' || $nim === '') {
        $errors[] = "Mahasiswa ke-$no: nama dan NIM wajib diisi.";
        continue;
    }
    if (!validasiNilai($tugas) || !validasiNilai($uts) || !validasiNilai($uas)) {
        $errors[] = "Mahasiswa ke-$no ($nama): nilai harus berupa angka 0 sampai 100.";
        continue;
    }

    $nilaiAkhir = hitungNilaiAkhir((float)$tugas, (float)$uts, (float)$uas);

    $hasil[] = [
        'nama'       => $nama,
        'nim'        => $nim,
        'tugas'      => (float)$tugas,
        'uts'        => (float)$uts,
        'uas'        => (float)$uas,
        'nilaiAkhir' => $nilaiAkhir,
        'grade'      => tentukanGrade($nilaiAkhir),
        'predikat'   => tentukanPredikat($nilaiAkhir),
        'status'     => tentukanStatus($nilaiAkhir),
    ];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Penghitung Nilai Mahasiswa</title>
</head>
<body>

<h2>Program Penghitung Nilai Mahasiswa</h2>
<p>Nilai Akhir = (Tugas &times; 30%) + (UTS &times; 30%) + (UAS &times; 40%)</p>

<h3>Data Mahasiswa</h3>

<?php foreach ($errors as $err): ?>
    <p style="color:red;"><?= e($err) ?></p>
<?php endforeach; ?>

<form method="post" action="">
    <table id="tabelInput" border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>Nama</th>
                <th>NIM</th>
                <th>Nilai Tugas</th>
                <th>Nilai UTS</th>
                <th>Nilai UAS</th>
                <th>Aksi</th>
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
                <td><button type="button" onclick="hapusBaris(this)">Hapus</button></td>
            </tr>
        <?php endfor; ?>
        </tbody>
    </table>
    <br>
    <button type="button" onclick="tambahBaris()">Tambah Mahasiswa</button>
    <button type="submit">Hitung Nilai</button>
</form>

<?php if (!empty($hasil)): ?>
    <hr>
    <h3>Hasil Perhitungan</h3>

    <?php foreach ($hasil as $h): ?>
        <p><b>Data mahasiswa</b></p>
        <ul>
            <li>Nama: <?= e($h['nama']) ?></li>
            <li>NIM: <?= e($h['nim']) ?></li>
            <li>Nilai Tugas: <?= formatAngka($h['tugas']) ?></li>
            <li>Nilai UTS: <?= formatAngka($h['uts']) ?></li>
            <li>Nilai UAS: <?= formatAngka($h['uas']) ?></li>
        </ul>

        <p><b>Hasil perhitungan</b></p>
        <ul>
            <li>Nilai Akhir: <?= formatAngka($h['nilaiAkhir']) ?></li>
            <li>Grade: <?= e($h['grade']) ?></li>
            <li>Predikat: <?= e($h['predikat']) ?></li>
            <li>Status: <?= e($h['status']) ?></li>
        </ul>

        <p>
            Nilai akhir diperoleh dari
            (<?= formatAngka($h['tugas']) ?> &times; 0,3) +
            (<?= formatAngka($h['uts']) ?> &times; 0,3) +
            (<?= formatAngka($h['uas']) ?> &times; 0,4) =
            <?= formatAngka($h['nilaiAkhir']) ?>.
        </p>
        <hr>
    <?php endforeach; ?>

    <?php if (count($hasil) > 1): ?>
        <h3>Rekap Seluruh Mahasiswa</h3>
        <table border="1" cellpadding="5" cellspacing="0">
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>NIM</th>
                <th>Tugas</th>
                <th>UTS</th>
                <th>UAS</th>
                <th>Nilai Akhir</th>
                <th>Grade</th>
                <th>Predikat</th>
                <th>Status</th>
            </tr>
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
                    <td><?= e($h['status']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
<?php endif; ?>

<script>
function tambahBaris() {
    var tbody = document.querySelector('#tabelInput tbody');
    var tr = document.createElement('tr');
    tr.innerHTML =
        '<td><input type="text" name="nama[]" required></td>' +
        '<td><input type="text" name="nim[]" required></td>' +
        '<td><input type="number" name="tugas[]" min="0" max="100" step="any" required></td>' +
        '<td><input type="number" name="uts[]" min="0" max="100" step="any" required></td>' +
        '<td><input type="number" name="uas[]" min="0" max="100" step="any" required></td>' +
        '<td><button type="button" onclick="hapusBaris(this)">Hapus</button></td>';
    tbody.appendChild(tr);
}

function hapusBaris(btn) {
    var tbody = document.querySelector('#tabelInput tbody');
    if (tbody.rows.length > 1) {
        btn.closest('tr').remove();
    }
}
</script>

</body>
</html>
