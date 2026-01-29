<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
requireRole('academic');

$student_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$student = $conn->query("SELECT * FROM students WHERE id = $student_id")->fetch_assoc();

if (!$student) {
    die("Student not found");
}

$today = date('d F Y');
$letter_number = 'LP3I/' . date('Y') . '/' . str_pad($student_id, 4, '0', STR_PAD_LEFT);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Penerimaan - <?= htmlspecialchars($student['full_name']) ?></title>
    <style>
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.6;
            padding: 2cm;
            background: #f5f5f5;
        }
        
        .letter {
            background: white;
            max-width: 21cm;
            min-height: 29.7cm;
            margin: 0 auto;
            padding: 2cm;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        
        .header {
            text-align: center;
            border-bottom: 3px double #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            font-size: 18pt;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 10pt;
        }
        
        .letter-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        
        .letter-title {
            text-align: center;
            margin: 30px 0;
        }
        
        .letter-title h2 {
            font-size: 14pt;
            text-decoration: underline;
        }
        
        .letter-body {
            text-align: justify;
            margin-bottom: 30px;
        }
        
        .letter-body p {
            margin-bottom: 15px;
            text-indent: 40px;
        }
        
        .student-info {
            margin: 20px 0;
            margin-left: 40px;
        }
        
        .student-info table {
            border-collapse: collapse;
        }
        
        .student-info td {
            padding: 3px 10px 3px 0;
        }
        
        .signature {
            float: right;
            text-align: center;
            margin-top: 50px;
            width: 250px;
        }
        
        .signature-line {
            border-bottom: 1px solid #333;
            margin: 80px 0 5px;
        }
        
        .btn-print {
            display: inline-block;
            padding: 10px 20px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin: 20px auto;
        }
        
        .btn-print:hover {
            background: #1d4ed8;
        }
        
        .print-actions {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="print-actions no-print">
        <button class="btn-print" onclick="window.print()">🖨️ Cetak Surat</button>
        <button class="btn-print" onclick="history.back()" style="background: #6b7280;">← Kembali</button>
    </div>
    
    <div class="letter">
        <div class="header">
            <h1>POLITEKNIK LP3I JAKARTA</h1>
            <p>Jl. Kramat Raya No.7-9, Jakarta Pusat 10450</p>
            <p>Telp: (021) 3909090 | Email: info@lp3i.ac.id | Web: www.lp3i.ac.id</p>
        </div>
        
        <div class="letter-info">
            <div>
                <p>Nomor: <?= $letter_number ?></p>
                <p>Lampiran: -</p>
                <p>Perihal: <strong>Surat Penerimaan Mahasiswa Baru</strong></p>
            </div>
            <div>
                <p>Jakarta, <?= $today ?></p>
            </div>
        </div>
        
        <div class="letter-title">
            <h2>SURAT PENERIMAAN MAHASISWA BARU</h2>
        </div>
        
        <div class="letter-body">
            <p>
                Dengan ini kami menyatakan bahwa yang bersangkutan di bawah ini:
            </p>
            
            <div class="student-info">
                <table>
                    <tr>
                        <td>Nama Lengkap</td>
                        <td>:</td>
                        <td><strong><?= htmlspecialchars($student['full_name']) ?></strong></td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($student['email']) ?></td>
                    </tr>
                    <tr>
                        <td>No. Telepon</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($student['phone']) ?></td>
                    </tr>
                    <tr>
                        <td>Asal Sekolah</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($student['school_origin']) ?></td>
                    </tr>
                    <tr>
                        <td>Program Studi</td>
                        <td>:</td>
                        <td><strong><?= htmlspecialchars($student['program_choice']) ?></strong></td>
                    </tr>
                    <tr>
                        <td>Tanggal Pendaftaran</td>
                        <td>:</td>
                        <td><?= $student['registration_date'] ?></td>
                    </tr>
                </table>
            </div>
            
            <p>
                Telah <strong>DITERIMA</strong> sebagai Mahasiswa Baru Politeknik LP3I Jakarta 
                Tahun Akademik <?= date('Y') ?>/<?= date('Y') + 1 ?>.
            </p>
            
            <p>
                Demikian surat penerimaan ini dibuat untuk digunakan sebagaimana mestinya. 
                Mahasiswa diwajibkan untuk melengkapi administrasi pendaftaran dan 
                melakukan registrasi ulang sesuai jadwal yang ditentukan.
            </p>
        </div>
        
        <div class="signature">
            <p>Jakarta, <?= $today ?></p>
            <p>Kepala Bagian Akademik</p>
            <div class="signature-line"></div>
            <p><strong>Dr. Ahmad Susanto, M.Kom</strong></p>
            <p>NIP. 19750315 200312 1 001</p>
        </div>
        
        <div style="clear: both;"></div>
    </div>
</body>
</html>
