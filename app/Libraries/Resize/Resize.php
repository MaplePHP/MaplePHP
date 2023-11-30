<?php
/**
 * E.G THIS IS NOT COMPLETED
 * I REFRESHING AN OLD LIBRARY
 * THE LIBRARYS NAME CAN BE CHNAGED IN FUTURE
 */

namespace MaplePHP\Resize;

use Exception;
use Imagick;
use ImagickPixel;

class Resize
{
    protected const ALLOWE_MIME_TYPES = [
        "image/jpeg" => "jpg",
        "image/png" => "png",
        "image/gif" => "gif",
        "image/webp" => "webp"
    ];

    private $path;
    private $mime;
    private $info;
    private $filesize;
    private $savePath;
    private $ending;
    private $imagick;
    private $hex = "#FFFFFF";
    private $clone;
    private $frameCount = 0;
    private $frames = [];

    /**
     * Construct
     * @param string        $path         Path to original image
     * @param string/null   $savePath     Full path and file name WITHOUT file ending (is required to be set here or with @savePath method)
     */
    public function __construct(string $path, ?string $savePath = null)
    {
        if (is_file($path)) {
            $this->path = $path;
            $this->savePath = $savePath;

            $this->info = getimagesize($path);
            $this->mime = $this->info['mime'];
            $this->filesize = filesize($path) / 1024; // Kb
            $this->imagick = new Imagick($this->path);
            $this->ending = $this->getImgEnding();
        } else {
            throw new Exception("File do not exist", 2);
        }
    }

    /**
     * Get imagick instance
     * @return Imagick
     */
    public function getImagick(): Imagick
    {
        return $this->imagick;
    }

    /**
     * Add/change FULL save path
     * @param string      $savePath      Full path and file name WITHOUT file ending
     * @return self
     */
    public function savePath(string $savePath)
    {
        $this->savePath = $savePath;
        //$this->_saveDIR = dirname($this->savePath);
        return $this;
    }

    /**
     * Clone all image settings (change name)
     * @return Imagick
     */
    public function clone(): Imagick
    {
        $this->clone = clone $this->imagick;
        return $this->clone;
    }

    /**
     * Set quality
     * I do really recommend to set the quallity to around 80
     * @param int $quality 1-100
     * @return self
     */
    public function setQuality(int $quality): self
    {
        //$this->imagick->setImageCompression(Imagick::COMPRESSION_JPEG);
        $this->imagick->setImageCompressionQuality($quality);
        return $this;
    }

    /**
     * Optimize GIF colors for web
     * E.g. My laboration
     * @return self
     */
    public function optimizeGifColors(): self
    {
        if ($this->mime === "image/gif") {
            $this->setColors(256);
        }
        return $this;
    }

    /**
     * RECOMMENDED: Set the image resolution.
     * I do really recommend to set the resolution to 200 x and 200 y. The size of the image will be almost the same.
     * You will loose to much quality otherwise.
     * @param int $xRes
     * @param int $yRes
     * @return self
     */
    public function setResolution(int $xRes, int $yRes): self
    {
        $this->imagick->setResolution($xRes, $yRes);
        return $this;
    }

    /**
     * Turn on compression for web (Some standard values?)
     * @return self
     */
    public function webCompression()
    {
        $this->imagick->setSamplingFactors(array('2x2', '1x1', '1x1'));
        if ($this->info['mime'] === "image/jpeg") {
            $this->imagick->setInterlaceScheme(Imagick::INTERLACE_PLANE);
        }
        //$this->imagick->transformimagecolorspace(Imagick::COLORSPACE_SRGB);
        //$this->imagick->setImageType(Imagick::IMGTYPE_TRUECOLOR);
        //$this->imagick->setImageDepth(12);
        $this->imagick->stripImage();
        return $this;
    }

    /**
     * Enable lossless Compression
     * @return self
     */
    public function losslessCompression(): self
    {
        $this->imagick->setOption('webp:lossless', 'true');
        return $this;
    }

    /**
     * Enable lossless Compression (Recommended for web usage)
     * @return self
     */
    public function losslyCompression(): self
    {
        $this->imagick->setOption('webp:lossless', 'false');
        return $this;
    }

    /**
     * Set background color
     * @param string      $hex     String HEX
     * @return self
     */
    public function setBackgroundColor(string $hex): self
    {
        $this->hex = $hex;
        $color = new ImagickPixel($this->hex);
        $this->imagick->setImageBackgroundColor($color);
        $this->imagick->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);
        $this->imagick->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);
        return $this;
    }

    /**
     * Set max color count
     * @param self
     */
    public function setColors(int $count)
    {
        $this->imagick->quantizeImage($count, Imagick::COLORSPACE_YIQ, 0, false, false);
        return $this;
    }

    /**
     * Convert to file type
     * @param  string $type file type (ex webp)
     * @return self
     */
    public function convertTo(string $type)
    {
        $this->imagick->setImageFormat($type);
        //$this->imagick->setOption('webp:lossless', 'false');
        $this->ending = $type;
        $this->info['mime'] = ($type === "jpg") ? "image/jpeg" : "image/{$type}";
        return $this;
    }

    /**
     * Resize image
     * @param  int $width
     * @param  int $height
     * @return array new dimension [width, height]
     */
    public function resize(int $width, int $height): array
    {
        $newSize = $this->calcScale($width, $height);
        $this->imagick->resizeImage($newSize[0], $newSize[1], Imagick::FILTER_LANCZOS, 0.9, true);
        return $newSize;
    }

    /**
     * Scale image
     * @param  int $width
     * @param  int $height
     * @return void
     */
    public function scale(int $width, int $height): void
    {
        $this->imagick->scaleImage($width, $height, true);
    }

    /**
     * Crop image
     * @param  int   $cropW   [description]
     * @param  int   $cropH   [description]
     * @param  int|null $canvasW [description]
     * @param  int|null $canvasH [description]
     * @param  int  $xPos       [description]
     * @param  int  $yPos       [description]
     * @return void
     */
    public function crop(int $cropW, int $cropH, ?int $canvasW = null, ?int $canvasH = null, $xPos = 0, $yPos = 0): void
    {
        $autoCenter = (bool)(is_null($canvasW));
        $this->calcBound($cropW, $cropH, $canvasW, $canvasH, $xPos, $yPos, $autoCenter);
        $this->resize($canvasW, $canvasH);
        $this->imagick->cropImage($cropW, $cropH, $xPos, $yPos);
    }

    /**
     * Trim all whitespce
     * @param  float  $fuzz
     * @return self
     */
    public function trim(float $fuzz = 0.1)
    {
        $this->imagick->trimImage($fuzz * Imagick::getQuantum());
        return $this;
    }

    /**
     * Rotate image
     * @param  int $deg 0-360
     * @return void
     */
    public function rotate(int $deg): void
    {
        $this->imagick->rotateimage($this->hex, $deg);
        $this->imagick->setImageOrientation(Imagick::ORIENTATION_TOPLEFT);
    }

    /**
     * Auto rotate image back that has been rotated thorugh mobile.
     * @return  bool (has been rotated true/false)
     */
    public function autoRotate()
    {
        try {
            //exif_read_data has bug 7.1 and does not really work (Hopefylly imagick orientation works better)
            $orientation = (int)$this->imagick->getImageOrientation();
            if ($orientation !== 1) {
                $deg = 0;
                switch ($orientation) {
                    case 3:
                        $deg = -180;
                        break;
                    case 6:
                        $deg = -270;
                        break;
                    case 8:
                        $deg = -90;
                        break;
                }
                if ($deg) {
                    $this->rotate($deg, $this->hex);
                }
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Loop thorugh all image frames.
     * @param  callable   $call  callback (self, frameCount, imagick)
     * @param  array|null $match loop only matchable mime types.
     * @return self
     */
    public function frames(callable $call, ?array $match = null)
    {
        $this->frameCount = 0;
        $this->frames = array();
        if (is_null($match) || in_array($this->mime, $match)) {
            $imagick = $this->imagick->coalesceImages();
            foreach ($imagick as $frame) {
                $this->imagick = $frame;
                $call($frame);
                $this->frames[$this->frameCount] = $this->savePath."-{$this->frameCount}.{$this->ending}";
                $this->frameCount++;
            }
            $this->imagick = $imagick->deconstructImages();
        } else {
            $call($this, $this->frameCount, $this->imagick);
        }
        return $this;
    }

    /**
     * Get all image frames
     * @return array all image names
     */
    public function getFrames()
    {
        return $this->frames;
    }

    /**
     * Execute all settings and save new image
     */
    public function execute()
    {
        if (is_null($this->savePath)) {
            throw new Exception("You need to set an image path", 1);
        }

        if ($this->frameCount > 0) {
            $this->frameCount = 0;
            $this->imagick->writeImages($this->savePath.".".$this->ending, true);
        } else {
            $this->imagick->writeImage($this->savePath.".".$this->ending);
        }

        if (!is_null($this->clone)) {
            $this->imagick->setImage($this->clone);
        }
    }

    /**
     * Get new save path
     * @return string
     */
    public function getTruePath()
    {
        return $this->savePath.".".$this->ending;
    }

    /**
     * Get new basename
     * @return string
     */
    public function getTrueBasename()
    {
        return basename($this->savePath.".".$this->ending);
    }

    public function path(string $add = null)
    {
        return $this->savePath.$add.".".$this->ending;
    }

    public function basename(string $add = null)
    {
        return basename($this->path($add));
    }

    /**
     * Get image info
     * @return array
     */
    public function getImgInfo(): array
    {
        return $this->info;
    }

    /**
     * Get image mime type
     * @return string
     */
    public function getMime(): string
    {
        return $this->info['mime'];
    }

    /**
     * Check if file is image
     * @return bool
     */
    public function isImg(): bool
    {
        $mimes = array_keys(static::ALLOWE_MIME_TYPES);
        return (in_array($this->info['mime'], $mimes));
    }

    /**
     * Check if image is mime
     * @param  string  $mime
     * @return bool
     */
    public function isMime(string $mime): bool
    {
        return ($this->mime === strtolower($mime));
    }

    /**
     * Get ending
     * @return string
     */
    public function getEnding(): string
    {
        return $this->ending;
    }

    /**
     * Get current expected ending
     * @return string|null
     */
    public function getImgEnding(): ?string
    {
        return (static::ALLOWE_MIME_TYPES[$this->info['mime']] ?? null);
    }

    /**
     * Get current setted hex code
     * @return string|null
     */
    public function getHex(): ?string
    {
        return $this->hex;
    }

    /**
     * Convert rgb to hex (MOVE TO DATA TYPE CLASS)
     * @param  int    $red
     * @param  int    $green
     * @param  int    $blue
     * @return string
     */
    public function rgbToHex(int $red, int $green, int $blue): string
    {
        return "#".sprintf('%02X%02X%02X', $red, $green, $blue);
    }

    /**
     * Create luminance in color
     * @param  string $hexcolor
     * @param  float  $percent  0 = 1 (e.g. 0.1 = 10%)
     * @return string
     */
    public function getLuminance(string $hexcolor, float $percent)
    {
        if (strlen($hexcolor) < 6) {
            $hexcolor = $hexcolor[0].$hexcolor[0].$hexcolor[1].$hexcolor[1].$hexcolor[2].$hexcolor[2];
        }
        $hexcolor = array_map('hexdec', str_split(str_pad(str_replace('#', '', $hexcolor), 6, '0'), 2));
        foreach ($hexcolor as $i => $color) {
            $fromColor = $percent < 0 ? 0 : $color;
            $toColor = $percent < 0 ? $color : 255;
            $pvalue = ceil(($toColor - $fromColor) * $percent);
            $hexcolor[$i] = str_pad(dechex($color + $pvalue), 2, '0', STR_PAD_LEFT);
        }
        return '#'.implode($hexcolor);
    }


    public function getAverageColour($asHexStr = true)
    {
        $image = new Imagick($this->path);
        $image->scaleImage(1, 1);
        //getimagehistogram
        $pixels = $image->getImageHistogram();
        if (count($pixels) === 0) {
            throw new Exception("Unexpected error: Could not find ant colors in image", 1);
        }
       
        $pixel = reset($pixels);
        $rgb = $pixel->getColor(); //getcolor

        if ($asHexStr) {
            return $this->rgbToHex($rgb['r'], $rgb['g'], $rgb['b']);
        }
        return $rgb;
    }

    private function calcBound(&$cropW, &$cropH, ?int &$canvasW = null, ?int &$canvasH = null, &$xPos, &$yPos, bool $center = true)
    {
        $width = ($cropW / $this->info[0]);
        $height = ($cropH / $this->info[1]);
        $ratio = ($width > $height) ? $width : $height;

        if ($ratio > 1) {
            $cropW = round($cropW / $ratio);
            $cropH = round($cropH / $ratio);
            // Change scale
            if ($center === false) {
                $xPos = ($xPos / $ratio);
                $yPos = ($yPos / $ratio);
            }
            $ratio = 1;
        }

        $canvasW = round($ratio * $this->info[0]);
        $canvasH = round($ratio * $this->info[1]);

        if ($center === true) {
            $xPos = round(($canvasW - $cropW) / 2);
            $yPos = round(($canvasH - $cropH) / 2);
        }
    }

    private function calcScale($width, $height)
    {
        if ($width < $this->info[0] || $height < $this->info[1]) {
            $imageScale = min($width / $this->info[0], $height / $this->info[1]);
            $width = ceil($imageScale * $this->info[0]);
            $height = ceil($imageScale * $this->info[1]);
        } else {
            $width = $this->info[0];
            $height = $this->info[1];
        }
        return [$width, $height];
    }
}
