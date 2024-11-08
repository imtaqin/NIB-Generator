<?php
require('fpdf.php');
require('./vendor/autoload.php'); // QR Code library

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Logo\Logo;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $companyName = $_POST['company_name'];

    class PDF extends FPDF
    {
        function Header()
        {
            // Logo Garuda
            $this->Image('garuda.png', 95, 10, 20); // Sesuaikan posisi dan ukuran logo
            $this->Ln(25);
    
            // Judul utama
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(0, 8, 'PEMERINTAH REPUBLIK INDONESIA', 0, 1, 'C');
            $this->Cell(0, 8, 'PERIZINAN BERUSAHA BERBASIS RISIKO', 0, 1, 'C');
            $this->Cell(0, 8, 'NOMOR INDUK BERUSAHA: 1608240001818', 0, 1, 'C');
            $this->Ln(10);
        }
    
        function Footer()
        {
            $this->SetY(-40);
            $this->SetFont('Arial', '', 10);
            $this->SetDrawColor(0, 0, 0);
            $this->SetLineWidth(0.2);
            $this->Rect(10, $this->GetY(), 190, 35);
            $this->SetXY(12, $this->GetY() + 2);
            $this->SetFont('Arial', '', 9);
            $footerText = [
                "1. Dokumen ini diterbitkan sistem OSS berdasarkan data dari Pelaku Usaha, tersimpan dalam sistem OSS,",
                "   yang menjadi tanggung jawab Pelaku Usaha.",
                "2. Dalam hal terjadi kekeliruan isi dokumen ini akan dilakukan perbaikan sebagaimana mestinya.",
                "3. Dokumen ini telah ditandatangani secara elektronik menggunakan sertifikat elektronik yang diterbitkan",
                "   oleh BSrE-BSSN.",
                "4. Data lengkap Perizinan Berusaha dapat diperoleh melalui sistem OSS menggunakan hak akses."
            ];
            foreach ($footerText as $line) {
                $this->Cell(130, 5, $line, 0, 1);
            }
            $this->Image('sertifikat.png', 155, $this->GetY() - 30, 40, 20); // Sesuaikan posisi dan ukuran logo
        }
    
        function Body()
        {
            $this->SetFont('Arial', '', 12);
            $this->SetMargins(20, 20, 20);
            $this->SetAutoPageBreak(true, 40);
    
            // Konten utama
            $content = [
                'Berdasarkan Undang-Undang Nomor 6 Tahun 2023 tentang Penetapan Peraturan Pemerintah Pengganti',
                'Undang-Undang Nomor 2 Tahun 2022 tentang Cipta Kerja Menjadi Undang-Undang, Pemerintah Republik',
                'Indonesia menerbitkan Nomor Induk Berusaha (NIB) kepada:',
                '',
                '1. Nama Pelaku Usaha          : ' . $companyName,
                '2. Alamat Kantor               : Jalan Lenteng Agung Raya Kancil 2 No. 46, Desa/Kelurahan Lenteng',
                '                                    Agung, Kec. Jagakarsa, Kota Adm. Jakarta Selatan, Provinsi DKI Jakarta,',
                '                                    Kode Pos: 16630',
                '   No. Telepon                 : 085705938970',
                '   Email                       : neocyberindonesia@gmail.com',
                '3. Status Penanaman Modal      : PMDN',
                '4. Kode Klasifikasi Baku Lapangan Usaha Indonesia (KBLI): Lihat Lampiran',
                '5. Skala Usaha                 : Usaha Mikro',
                '',GFXD
                'NIB ini berlaku di seluruh wilayah Republik Indonesia selama menjalankan kegiatan usaha dan berlaku',
                'sebagai hak akses kepabeanan, pendaftaran kepesertaan jaminan sosial kesehatan dan jaminan sosial',
                'ketenagakerjaan, serta bukti pemenuhan laporan pertama Wajib Lapor Ketenagakerjaan di Perusahaan',
                '(WLKP).',
                '',
                'Pelaku Usaha dengan NIB tersebut di atas dapat melaksanakan kegiatan berusaha sebagaimana terlampir',
                'dengan tetap memperhatikan ketentuan peraturan perundang-undangan.',
                '',
                'Diterbitkan di Jakarta, tanggal: 16 Agustus 2024',
                '',
                'Menteri Investasi/',
                'Kepala Badan Koordinasi Penanaman Modal,'
            ];
    
            foreach ($content as $line) {
                $this->MultiCell(0, 7, $line);
            }
    
            // QR Code
            $this->Ln(10);
            $this->Image('qrcode.png', 165, $this->GetY(), 30); // Posisi dan ukuran QR code
    
            // Tanda tangan
            $this->SetY($this->GetY() + 30);
            $this->SetFont('Arial', '', 12);
            $this->Cell(0, 10, 'Ditandatangani secara elektronik', 0, 1, 'C');
        }
    }
    
    $qrCode = QrCode::create('https://oss.go.id/nib/1608240001818')
        ->setEncoding(new Encoding('UTF-8'))
        ->setErrorCorrectionLevel(ErrorCorrectionLevel::High)
        ->setSize(300)
        ->setMargin(10);
    
    $logo = new Logo(__DIR__ . '/kemeninvest.jpg'); // Pastikan path benar
    $writer = new PngWriter();
    $result = $writer->write($qrCode, $logo);
    
    // Simpan QR Code ke file
    $result->saveToFile('qrcode.png');
    
    // Buat PDF dengan ukuran kertas A4
    $pdf = new PDF('P', 'mm', 'A4');
    $pdf->AddPage();
    $pdf->Body();
    $pdf->Output('I', 'NIB_Document.pdf');
    
    // Hapus file QR code sementara
    unlink('qrcode.png');
}
?>
