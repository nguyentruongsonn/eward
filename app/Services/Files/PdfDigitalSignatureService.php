<?php

namespace App\Services\Files;

use Exception;
use setasign\Fpdi\Tcpdf\Fpdi;
use Throwable;

class PdfDigitalSignatureService
{
    private string $certDir;

    public function __construct()
    {
        $this->certDir = storage_path('app/certs');
    }

    // Ký số và đóng dấu điện tử trực tiếp vào file PDF
    public function sign(string $rawPdfContent, string $signerName, ?string $reason = null): string
    {
        $this->ensureCertificateExists();

        $certPath = $this->certDir.'/certificate.crt';
        $keyPath = $this->certDir.'/private.key';

        // Lưu file tạm để FPDI đọc
        $tempInput = tempnam(sys_get_temp_dir(), 'pdf_in_');
        file_put_contents($tempInput, $rawPdfContent);

        try {
            $pdf = new Fpdi();
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);

            // Nạp toàn bộ các trang của file PDF gốc
            $pageCount = $pdf->setSourceFile($tempInput);
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId);

                // Chỉ đóng dấu mộc đỏ ở trang cuối cùng
                if ($pageNo === $pageCount) {
                    $this->drawDigitalSeal($pdf, $size['width'], $size['height'], $signerName);
                }
            }

            // Cấu hình chữ ký số chuẩn PKCS#7 PAdES
            $signatureInfo = [
                'Name' => $signerName,
                'Location' => 'UBND Phuong E-Ward',
                'Reason' => $reason ?: 'Phe duyet ket qua giai quyet TTHC',
                'ContactInfo' => 'dvc@eward.gov.vn',
            ];

            $pdf->setSignature(
                'file://'.$certPath,
                'file://'.$keyPath,
                '',
                '',
                2,
                $signatureInfo
            );

            $signedContent = $pdf->Output('', 'S');

            return $signedContent;
        } finally {
            if (file_exists($tempInput)) {
                @unlink($tempInput);
            }
        }
    }

    // Kiểm tra file có phải định dạng PDF không
    public function isPdf(string $path, ?string $mimeType = null): bool
    {
        if (str_ends_with(strtolower($path), '.pdf')) {
            return true;
        }

        return $mimeType === 'application/pdf';
    }

    // Vẽ con dấu mộc đỏ điện tử ở góc dưới trang giấy
    private function drawDigitalSeal(Fpdi $pdf, float $pageWidth, float $pageHeight, string $signerName): void
    {
        $stampWidth = 72;
        $stampHeight = 36;
        $marginRight = 15;
        $marginBottom = 20;

        $x = max(10, $pageWidth - $stampWidth - $marginRight);
        $y = max(10, $pageHeight - $stampHeight - $marginBottom);

        // Màu đỏ chuẩn con dấu hành chính
        $pdf->SetTextColor(200, 16, 16);
        $pdf->SetDrawColor(200, 16, 16);
        $pdf->SetLineWidth(0.6);

        // Khung viền ngoài
        $pdf->RoundedRect($x, $y, $stampWidth, $stampHeight, 2, '1111');

        // Khung viền trong
        $pdf->SetLineWidth(0.2);
        $pdf->RoundedRect($x + 1, $y + 1, $stampWidth - 2, $stampHeight - 2, 1.5, '1111');

        // Tiêu đề cơ quan
        $pdf->SetXY($x + 2, $y + 3);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell($stampWidth - 4, 4, 'UBND PHUONG E-WARD', 0, 1, 'C');

        // Nhãn xác nhận ký số
        $pdf->SetXY($x + 2, $y + 8);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell($stampWidth - 4, 5, 'DA KY DIEN TU', 0, 1, 'C');

        // Thông tin người ký và thời gian
        $pdf->SetXY($x + 2, $y + 14);
        $pdf->SetFont('helvetica', '', 7);
        $lines = "Nguoi ky: {$signerName}\nNgay ky: ".now()->format('d/m/Y H:i:s')."\nHe thong: E-Ward DVC";
        $pdf->MultiCell($stampWidth - 4, 3.5, $lines, 0, 'C');
    }

    // Tự động sinh chứng thư số nếu chưa có
    public function ensureCertificateExists(): void
    {
        $certPath = $this->certDir.'/certificate.crt';
        $keyPath = $this->certDir.'/private.key';

        if (file_exists($certPath) && file_exists($keyPath)) {
            return;
        }

        $this->generateCertificate();
    }

    // Sinh chứng thư số X.509 RSA 2048-bit bằng OpenSSL
    public function generateCertificate(): void
    {
        if (! is_dir($this->certDir)) {
            mkdir($this->certDir, 0755, true);
        }

        $opensslCnf = $this->findOpenSslConfig();
        $config = [
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ];
        if ($opensslCnf !== null) {
            $config['config'] = $opensslCnf;
        }

        $dn = [
            'countryName' => 'VN',
            'stateOrProvinceName' => 'Ha Noi',
            'localityName' => 'Ha Noi',
            'organizationName' => 'Uy Ban Nhan Dan Phuong E-Ward',
            'organizationalUnitName' => 'Bo Phan Tiep Nhan Va Tra Ket Qua',
            'commonName' => 'Chu tich UBND Phuong E-Ward',
            'emailAddress' => 'dvc@eward.gov.vn',
        ];

        $privKey = openssl_pkey_new($config);
        if ($privKey === false) {
            throw new Exception('Khong the tao khoa private key OpenSSL.');
        }

        $csr = openssl_csr_new($dn, $privKey, array_merge(['digest_alg' => 'sha256'], $config));
        if ($csr === false) {
            throw new Exception('Khong the tao CSR chung thu so.');
        }

        // Chứng thư số có hạn 3 năm (1095 ngày)
        $cert = openssl_csr_sign($csr, null, $privKey, 1095, array_merge(['digest_alg' => 'sha256'], $config));
        if ($cert === false) {
            throw new Exception('Khong the ky tao chung thu so X.509.');
        }

        openssl_x509_export($cert, $certOut);
        openssl_pkey_export($privKey, $keyOut, null, $config);

        file_put_contents($this->certDir.'/certificate.crt', $certOut);
        file_put_contents($this->certDir.'/private.key', $keyOut);
    }

    // Tìm file cấu hình openssl.cnf trên hệ điều hành
    private function findOpenSslConfig(): ?string
    {
        $candidates = [
            'C:/xampp/apache/conf/openssl.cnf',
            'C:/xampp/php/extras/ssl/openssl.cnf',
            'C:/xampp/php/extras/openssl/openssl.cnf',
            '/etc/ssl/openssl.cnf',
            '/usr/lib/ssl/openssl.cnf',
        ];

        foreach ($candidates as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }
}
