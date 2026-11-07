<?php

namespace App\Services\Files;

class OfficialSealGenerator
{
    private ?string $boldFont = null;
    private ?string $regularFont = null;

    public function __construct()
    {
        $this->resolveFonts();
    }

    private function resolveFonts(): void
    {
        $boldCandidates = [
            'C:/Windows/Fonts/timesbd.ttf',
            'C:/Windows/Fonts/arialbd.ttf',
            '/usr/share/fonts/truetype/liberation/LiberationSerif-Bold.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
        ];

        foreach ($boldCandidates as $path) {
            if (file_exists($path)) {
                $this->boldFont = $path;
                break;
            }
        }

        $regularCandidates = [
            'C:/Windows/Fonts/times.ttf',
            'C:/Windows/Fonts/arial.ttf',
            '/usr/share/fonts/truetype/liberation/LiberationSerif-Regular.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
        ];

        foreach ($regularCandidates as $path) {
            if (file_exists($path)) {
                $this->regularFont = $path;
                break;
            }
        }
    }

    public function generateSealPng(string $signerName, ?string $wardName = null): ?string
    {
        if (! extension_loaded('gd') || ! function_exists('imagettftext') || $this->boldFont === null) {
            return null;
        }

        $wardName = $wardName ?: env('WARD_NAME', 'PHƯỜNG ABC');
        $signerName = trim($signerName);
        if ($signerName === '') {
            $signerName = 'Nguyễn Thị Tuyến';
        }

        $width = 560;
        $height = 340;
        $im = imagecreatetruecolor($width, $height);
        imagesavealpha($im, true);
        $transparent = imagecolorallocatealpha($im, 0, 0, 0, 127);
        imagefill($im, 0, 0, $transparent);

        // Màu đỏ son hành chính chuẩn cho con dấu
        $red = imagecolorallocatealpha($im, 220, 32, 28, 0);
        // Màu mực bút đen tự nhiên cho chữ ký và họ tên
        $ink = imagecolorallocatealpha($im, 15, 20, 28, 0);

        $cx = 145;
        $cy = 135;
        $outerRadius = 90;
        $innerRadius = 82;

        // --- BƯỚC 1: KÝ TAY (SIGNATURE KÝ TRƯỚC BẰNG MỰC ĐEN TỰ NHIÊN) ---
        // Vòng thắt bầu dục trên đỉnh: bắt đầu từ trong con dấu vọt lên đỉnh và lượn tròn
        $this->drawBezierSegment($im, [$cx + 10, $cy - 20], [$cx + 20, $cy - 95], [$cx + 85, $cy - 105], [$cx + 95, $cy - 70], $ink, 2.4);
        $this->drawBezierSegment($im, [$cx + 95, $cy - 70], [$cx + 100, $cy - 40], [$cx + 45, $cy - 35], [$cx + 35, $cy - 10], $ink, 2.0);

        // Nét lượn zíc zắc thân ký (mô phỏng nhát bút ký sống động)
        $this->drawBezierSegment($im, [$cx + 35, $cy - 10], [$cx + 50, $cy - 45], [$cx + 65, $cy - 40], [$cx + 70, $cy - 15], $ink, 2.2);
        $this->drawBezierSegment($im, [$cx + 70, $cy - 15], [$cx + 78, $cy - 35], [$cx + 88, $cy - 30], [$cx + 92, $cy + 5], $ink, 2.0);

        // Nét gạch dài phóng khoáng bay vút sang phải
        $this->drawBezierSegment($im, [$cx + 15, $cy - 8], [$cx + 110, $cy - 65], [$cx + 270, $cy - 55], [$cx + 350, $cy - 25], $ink, 2.3);

        // Nét quặt gập từ phải chém chéo xuống góc trái dưới
        $this->drawBezierSegment($im, [$cx + 350, $cy - 25], [$cx + 250, $cy + 45], [$cx + 130, $cy + 130], [$cx - 10, $cy + 185], $ink, 2.6);

        // Nét móc đuôi tự nhiên ở góc trái dưới
        $this->drawBezierSegment($im, [$cx - 10, $cy + 185], [$cx - 15, $cy + 175], [$cx - 8, $cy + 155], [$cx + 2, $cy + 145], $ink, 1.8);

        // --- BƯỚC 2: ĐÓNG DẤU MỘC ĐỎ TRÒN (TRÙM LÊN 1/3 CHỮ KÝ THEO NGHỊ ĐỊNH 30/2020) ---
        // 1. Viền ngoài con dấu (dày)
        $this->drawThickEllipse($im, $cx, $cy, $outerRadius * 2, $outerRadius * 2, $red, 3.2);

        // 2. Viền trong con dấu (mảnh)
        $this->drawThickEllipse($im, $cx, $cy, $innerRadius * 2, $innerRadius * 2, $red, 1.4);

        // 3. Vòng cung chữ phía trên: ỦY BAN NHÂN DÂN
        $this->drawCurvedText($im, $cx, $cy, 68, "ỦY BAN NHÂN DÂN", $this->boldFont, 11, $red, -152, -28);

        // 4. Biểu tượng ngôi sao năm cánh ở tâm trên
        $this->drawFivePointStar($im, $cx, $cy - 20, 9, 4.2, $red);

        // 5. Chữ trung tâm con dấu:
        // Dòng 1: UBND
        $t1 = "UBND";
        $b1 = imagettfbbox(13, 0, $this->boldFont, $t1);
        $w1 = abs($b1[2] - $b1[0]);
        imagettftext($im, 13, 0, (int)($cx - $w1 / 2), $cy + 5, $red, $this->boldFont, $t1);

        // Dòng 2: PHƯỜNG ABC (hoặc tên phường cấu hình)
        $t2 = mb_strtoupper($wardName, 'UTF-8');
        $b2 = imagettfbbox(14, 0, $this->boldFont, $t2);
        $w2 = abs($b2[2] - $b2[0]);
        imagettftext($im, 14, 0, (int)($cx - $w2 / 2), $cy + 26, $red, $this->boldFont, $t2);

        // 6. Ngôi sao nhỏ ở đáy con dấu
        $this->drawFivePointStar($im, $cx, $cy + 68, 6, 2.7, $red);

        // --- BƯỚC 3: HỌ TÊN NGƯỜI KÝ (TIMES NEW ROMAN BOLD, KHÔNG CHỮ THỪA) ---
        $nameX = (int)($cx + 15);
        $nameY = $cy + 175;
        imagettftext($im, 16, 0, $nameX, $nameY, $ink, $this->boldFont, $signerName);

        ob_start();
        imagepng($im);
        $png = ob_get_clean();
        imagedestroy($im);

        return $png ?: null;
    }

    public function stampImage(string $rawImageContent, ?string $mimeType, string $signerName, ?string $wardName = null): ?string
    {
        $im = @imagecreatefromstring($rawImageContent);
        if ($im === false) {
            return null;
        }

        $sealPng = $this->generateSealPng($signerName, $wardName);
        if ($sealPng === null) {
            imagedestroy($im);
            return null;
        }

        $seal = @imagecreatefromstring($sealPng);
        if ($seal === false) {
            imagedestroy($im);
            return null;
        }

        $imgW = imagesx($im);
        $imgH = imagesy($im);
        $sealW = imagesx($seal);
        $sealH = imagesy($seal);

        // Tỷ lệ dấu phù hợp với kích thước ảnh gốc
        $targetW = (int) min(max($imgW * 0.40, 240), min(480, $sealW));
        $targetH = (int) ($sealH * ($targetW / $sealW));

        $posX = max(10, $imgW - $targetW - 25);
        $posY = max(10, $imgH - $targetH - 25);

        imagealphablending($im, true);
        imagecopyresampled($im, $seal, $posX, $posY, 0, 0, $targetW, $targetH, $sealW, $sealH);
        imagedestroy($seal);

        ob_start();
        $isJpeg = $mimeType === 'image/jpeg' || str_contains((string) $mimeType, 'jpg');
        if ($isJpeg) {
            imagejpeg($im, null, 95);
        } else {
            imagepng($im);
        }
        $output = ob_get_clean();
        imagedestroy($im);

        return $output ?: null;
    }

    private function drawFivePointStar($im, int $cx, int $cy, float $outerR, float $innerR, int $color): void
    {
        $points = [];
        for ($i = 0; $i < 10; $i++) {
            $r = ($i % 2 === 0) ? $outerR : $innerR;
            $angle = deg2rad(-90 + $i * 36);
            $points[] = (int)($cx + $r * cos($angle));
            $points[] = (int)($cy + $r * sin($angle));
        }
        imagefilledpolygon($im, $points, $color);
    }

    private function drawThickEllipse($im, int $cx, int $cy, int $w, int $h, int $color, float $thickness = 2.0): void
    {
        for ($t = -$thickness / 2; $t <= $thickness / 2; $t += 0.4) {
            imageellipse($im, $cx, $cy, (int)($w + 2 * $t), (int)($h + 2 * $t), $color);
        }
    }

    private function drawCurvedText($im, int $cx, int $cy, float $radius, string $text, string $fontFile, int $fontSize, int $color, float $startAngleDeg = -152, float $endAngleDeg = -28): void
    {
        $chars = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);
        $numChars = count($chars);
        if ($numChars === 0) return;

        $totalAngle = $endAngleDeg - $startAngleDeg;
        $stepAngle = $totalAngle / max(1, $numChars - 1);

        for ($i = 0; $i < $numChars; $i++) {
            $ch = $chars[$i];
            if ($ch === ' ') continue;

            $angleDeg = $startAngleDeg + $i * $stepAngle;
            $angleRad = deg2rad($angleDeg);

            $x = $cx + $radius * cos($angleRad);
            $y = $cy + $radius * sin($angleRad);

            $textAngle = - ($angleDeg + 90);

            $bbox = imagettfbbox($fontSize, 0, $fontFile, $ch);
            $chW = abs($bbox[2] - $bbox[0]);

            $radA = deg2rad($textAngle);
            $drawX = $x - ($chW / 2) * cos($radA);
            $drawY = $y + ($chW / 2) * sin($radA);

            imagettftext($im, $fontSize, $textAngle, (int)$drawX, (int)$drawY, $color, $fontFile, $ch);
        }
    }

    private function drawBezierSegment($im, array $p0, array $p1, array $p2, array $p3, int $color, float $thickness = 2.0): void
    {
        $steps = 70;
        $pts = [];
        for ($i = 0; $i <= $steps; $i++) {
            $t = $i / $steps;
            $u = 1 - $t;
            $x = $u * $u * $u * $p0[0] + 3 * $u * $u * $t * $p1[0] + 3 * $u * $t * $t * $p2[0] + $t * $t * $t * $p3[0];
            $y = $u * $u * $u * $p0[1] + 3 * $u * $u * $t * $p1[1] + 3 * $u * $t * $t * $p2[1] + $t * $t * $t * $p3[1];
            $pts[] = [$x, $y];
        }
        $this->drawSmoothCurve($im, $pts, $color, $thickness);
    }

    private function drawSmoothCurve($im, array $points, int $color, float $thickness = 2.0): void
    {
        $count = count($points);
        if ($count < 2) return;

        for ($i = 0; $i < $count - 1; $i++) {
            $p0 = $points[$i];
            $p1 = $points[$i + 1];

            $x0 = $p0[0]; $y0 = $p0[1];
            $x1 = $p1[0]; $y1 = $p1[1];

            $dx = $x1 - $x0;
            $dy = $y1 - $y0;
            $dist = sqrt($dx * $dx + $dy * $dy);
            $steps = max(1, (int)($dist * 2));

            $prevX = $x0;
            $prevY = $y0;
            for ($s = 1; $s <= $steps; $s++) {
                $t = $s / $steps;
                $cx = $x0 + $t * $dx;
                $cy = $y0 + $t * $dy;

                for ($tx = -$thickness / 2; $tx <= $thickness / 2; $tx += 0.5) {
                    for ($ty = -$thickness / 2; $ty <= $thickness / 2; $ty += 0.5) {
                        if ($tx * $tx + $ty * $ty <= ($thickness * $thickness) / 4) {
                            imageline($im, (int)($prevX + $tx), (int)($prevY + $ty), (int)($cx + $tx), (int)($cy + $ty), $color);
                        }
                    }
                }
                $prevX = $cx;
                $prevY = $cy;
            }
        }
    }
}
