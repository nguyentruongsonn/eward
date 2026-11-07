<?php

namespace App\Services\Files;

use Exception;
use setasign\Fpdi\Tcpdf\Fpdi;
use Throwable;

class PdfDigitalSignatureService
{
    private string $certDir;
    private OfficialSealGenerator $sealGenerator;

    public function __construct(
        ?OfficialSealGenerator $sealGenerator = null,
    ) {
        $this->certDir = storage_path('app/certs');
        $this->sealGenerator = $sealGenerator ?? new OfficialSealGenerator();
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
                'Location' => 'UBND Phuong ABC',
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

    // Vẽ con dấu mộc đỏ tròn và chữ ký số ở góc dưới trang giấy chuẩn như văn bản hành chính
    private function drawDigitalSeal(Fpdi $pdf, float $pageWidth, float $pageHeight, string $signerName): void
    {
        $sealPng = $this->sealGenerator->generateSealPng($signerName);
        if ($sealPng !== null) {
            $tempSeal = tempnam(sys_get_temp_dir(), 'seal_').'.png';
            file_put_contents($tempSeal, $sealPng);
            try {
                $sealWidth = 82; // mm
                $sealHeight = 50; // mm
                $marginRight = 18;
                $marginBottom = 22;

                $x = max(10, $pageWidth - $sealWidth - $marginRight);
                $y = max(10, $pageHeight - $sealHeight - $marginBottom);

                $pdf->Image($tempSeal, $x, $y, $sealWidth, $sealHeight, 'PNG');

                return;
            } finally {
                if (file_exists($tempSeal)) {
                    @unlink($tempSeal);
                }
            }
        }
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
            'organizationName' => 'Uy Ban Nhan Dan Phuong ABC',
            'organizationalUnitName' => 'Bo Phan Tiep Nhan Va Tra Ket Qua',
            'commonName' => 'Chu tich UBND Phuong ABC',
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
