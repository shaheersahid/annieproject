<?php

$outputDir = __DIR__.'/../public/assets/images/optical/products';

if (! is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

$products = [
    ['rayban-premium-eyewear-frame.png', 'FRAME', 'RayBan Premium', [35, 35, 35], [204, 164, 74]],
    ['cartier-premium-half-frame.png', 'FRAME', 'Cartier Half Frame', [64, 54, 46], [213, 175, 88]],
    ['astra-hexagonal-metal-frame.png', 'FRAME', 'Astra Hexagonal', [92, 103, 116], [188, 197, 206]],
    ['tomford-men-eyewear.png', 'FRAME', 'TomFord Men', [28, 31, 38], [117, 79, 56]],
    ['peachmart-blue-cut-glasses.png', 'BLUE CUT', 'Aero Screen', [31, 82, 147], [76, 154, 230]],
    ['alpha-bluecut-glasses.png', 'BLUE CUT', 'Alpha Protection', [18, 96, 124], [111, 197, 212]],
    ['cartier-transition-glasses.png', 'TRANSITION', 'Transition Lens', [75, 71, 88], [151, 134, 188]],
    ['custom-prescription-lens-package.png', 'LENS', 'Prescription Lens', [37, 104, 95], [117, 196, 174]],
    ['police-polarized-sunglasses.png', 'SUNGLASSES', 'Police Polarized', [26, 31, 37], [219, 174, 73]],
    ['eyewear-cleaning-kit.png', 'ACCESSORY', 'Cleaning Kit', [38, 109, 93], [109, 190, 159]],
];

foreach ($products as [$filename, $type, $name, $primaryRgb, $accentRgb]) {
    $image = imagecreatetruecolor(900, 900);
    imageantialias($image, true);

    $bg = imagecolorallocate($image, 248, 250, 252);
    $panel = imagecolorallocate($image, 255, 255, 255);
    $line = imagecolorallocate($image, $primaryRgb[0], $primaryRgb[1], $primaryRgb[2]);
    $accent = imagecolorallocate($image, $accentRgb[0], $accentRgb[1], $accentRgb[2]);
    $muted = imagecolorallocate($image, 102, 112, 133);
    $soft = imagecolorallocatealpha($image, $accentRgb[0], $accentRgb[1], $accentRgb[2], 100);
    $shadow = imagecolorallocatealpha($image, 15, 23, 42, 108);
    $white = imagecolorallocate($image, 255, 255, 255);

    imagefilledrectangle($image, 0, 0, 900, 900, $bg);
    imagefilledellipse($image, 450, 470, 650, 430, $soft);
    imagefilledrectangle($image, 105, 105, 795, 795, $panel);
    imagerectangle($image, 105, 105, 795, 795, imagecolorallocate($image, 226, 232, 240));
    imagefilledellipse($image, 450, 640, 430, 46, $shadow);

    if ($type === 'ACCESSORY') {
        imagefilledroundrect($image, 300, 305, 600, 595, 38, $line);
        imagefilledroundrect($image, 330, 335, 570, 565, 28, $white);
        imagefilledrectangle($image, 390, 260, 510, 330, $accent);
        imagefilledrectangle($image, 410, 230, 490, 280, $line);
        imagefilledellipse($image, 450, 435, 110, 110, $accent);
        imagefilledellipse($image, 450, 435, 72, 72, $white);
        imageline($image, 355, 620, 545, 700, $accent);
        imagesetthickness($image, 18);
        imageline($image, 345, 620, 555, 708, $accent);
        imagesetthickness($image, 4);
    } elseif ($type === 'LENS') {
        imagefilledellipse($image, 370, 430, 190, 250, imagecolorallocatealpha($image, 179, 229, 229, 48));
        imagefilledellipse($image, 530, 430, 190, 250, imagecolorallocatealpha($image, 179, 229, 229, 48));
        imagesetthickness($image, 12);
        imagearc($image, 370, 430, 190, 250, 0, 360, $line);
        imagearc($image, 530, 430, 190, 250, 0, 360, $line);
        imageline($image, 462, 430, 438, 430, $line);
        imagesetthickness($image, 5);
        imageline($image, 335, 350, 405, 510, $accent);
        imageline($image, 495, 350, 565, 510, $accent);
    } else {
        $lensFill = in_array($type, ['BLUE CUT', 'TRANSITION', 'SUNGLASSES'], true)
            ? imagecolorallocatealpha($image, $accentRgb[0], $accentRgb[1], $accentRgb[2], $type === 'SUNGLASSES' ? 48 : 76)
            : imagecolorallocatealpha($image, 235, 245, 255, 80);

        imagefilledellipse($image, 350, 430, 220, 160, $lensFill);
        imagefilledellipse($image, 550, 430, 220, 160, $lensFill);
        imagesetthickness($image, 13);
        imagearc($image, 350, 430, 220, 160, 0, 360, $line);
        imagearc($image, 550, 430, 220, 160, 0, 360, $line);
        imageline($image, 460, 430, 440, 430, $line);
        imageline($image, 240, 420, 150, 365, $line);
        imageline($image, 660, 420, 750, 365, $line);
        imagesetthickness($image, 5);
        imageline($image, 302, 385, 395, 475, $accent);
        imageline($image, 502, 385, 595, 475, $accent);
    }

    $font = 5;
    imagestring($image, $font, 132, 136, 'QADIR OPTICS', $muted);
    imagestring($image, $font, 132, 162, $type, $accent);
    imagestring($image, $font, 132, 724, $name, $line);

    imagepng($image, $outputDir.'/'.$filename, 9);
    imagedestroy($image);
}

function imagefilledroundrect($image, int $x1, int $y1, int $x2, int $y2, int $radius, int $color): void
{
    imagefilledrectangle($image, $x1 + $radius, $y1, $x2 - $radius, $y2, $color);
    imagefilledrectangle($image, $x1, $y1 + $radius, $x2, $y2 - $radius, $color);
    imagefilledellipse($image, $x1 + $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
    imagefilledellipse($image, $x2 - $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
    imagefilledellipse($image, $x1 + $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
    imagefilledellipse($image, $x2 - $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
}
