<?php
declare(strict_types=1);

namespace Salopot\ImageGenerator;

use Intervention\Image\AbstractFont as Font;
use Intervention\Image\Image;
use InvalidArgumentException;
use RuntimeException;

class ImageGenerator
{
    /** @var Image */
    protected $image;

    /** @var ImageProvider */
    protected $imageProvider;

    /** @var string Save image format */
    protected $extension;

    /**
     * ImageGenerator constructor.
     * @param ImageProvider $imageProvider
     * @param Image $image
     */
    public function __construct(ImageProvider $imageProvider, Image $image) {
        $this->imageProvider = $imageProvider;
        $this->image = $image;
        $this->extension = 'jpg';
    }

    /**
     * Set output image extension
     * @param string $extension
     * @return $this
     */
    public function setExtension(string $extension): self
    {
        if (!in_array($extension, $this->imageProvider->getImageManager()->getSupportedExtensions(), true)) {
            throw new InvalidArgumentException("Not supported image extension {$extension}");
        }
        $this->extension = $extension;
        return $this;
    }

    /**
     * Insert image: logo, watermark
     * @param $image Path, raw, base64, SplFileInfo with image
     * @param string $align horizontal text alignment [left | center | right]
     * @param string $valign vertical text alignment [top | middle | bottom]
     * @param int $margin
     * @return $this
     */
    public function insertImage(
        $image,
        string $align = 'right',
        string $valign = 'top',
        int $margin = 0
    ): self {
        if ($valign === 'middle') {
            $position = $align;
        } else if ($align === 'center') {
            $position = $valign;
        } else {
            $position = "{$valign}-{$align}";
        }
        $this->image->insert($image, $position, $margin, $margin);
        return $this;
    }

    /**
     * Render text to the image
     * @param string $text
     * @param int $size
     * @param string $align horizontal text alignment [left | center | right]
     * @param string $valign vertical text alignment [top | middle | bottom]
     * @param int $margin
     * @param int $angle
     * @return $this
     */
    public function text(
        string $text,
        int $size = 20,
        string $align = 'right',
        string $valign = 'bottom',
        int $margin = 10,
        int $angle = 0
    ): self {
        $fontFile = __DIR__ . '/fonts/OpenSans/OpenSans-Regular.ttf';
        $borderSize = 1;
        $offsets = [
            [-$borderSize, -$borderSize],
            [-$borderSize, +$borderSize],
            [+$borderSize, -$borderSize],
            [+$borderSize, +$borderSize],
        ];
        switch ($align) {
            case 'left':
                $x = 0 + $margin;
                break;
            case 'center':
                $x = intdiv($this->image->width(), 2);
                break;
            case 'right':
                $x = $this->image->width() - $margin;
                break;
            default:
                throw new RuntimeException("Invalid align value: {$align}");
        }
        switch ($valign) {
            case 'top':
                $y = 0 + $margin;
                break;
            case 'middle':
                $y = intdiv($this->image->height(), 2);
                break;
            case 'bottom':
                $y = $this->image->height() - $margin;
                break;
            default:
                throw new RuntimeException("Invalid valign value: {$valign}");
        }
        foreach ($offsets as $offset) {
            $this->image->text($text, $x+$offset[0], $y+$offset[1], function (Font $font) use (
                $fontFile, $size, $borderSize, $align, $valign, $angle
            ) {
                $font->file($fontFile);
                $font->size($size - 2*$borderSize);
                $font->color('#000');
                $font->align($align);
                $font->valign($valign);
                $font->angle($angle);
            });
        }
        $this->image->text($text, $x, $y, function (Font $font) use (
            $fontFile, $size, $borderSize, $align, $valign, $angle
        ) {
            $font->file($fontFile);
            $font->size($size - 2*$borderSize);
            $font->color('#fff');
            $font->align($align);
            $font->valign($valign);
            $font->angle($angle);
        });
        return $this;
    }

    /**
     * Make image grayscale
     * @return $this
     */
    public function grayscale(): self
    {
        $this->image->greyscale();
        return $this;
    }

    /**
     * Apply a gaussian blur
     * 0 - none
     * 100 - max
     * @param int $value
     * @return $this
     */
    public function blur(int $value): self
    {
        $this->image->blur($value);
        return $this;
    }

    /**
     * Set the opacity in percent
     * 0 - full transparency
     * 100 - none
     * @param int $value
     * @return $this
     */
    public function opacity(int $value): self
    {
        $this->image->opacity($value);
        return $this;
    }

    /**
     * Level of brightness change applied
     * -100 - min
     * 100 - max
     * @param int $value
     * @return $this
     */
    public function brightness(int $value): self
    {
        $this->image->brightness($value);
        return $this;
    }

    /**
     * Performs a gamma correction
     * @param float $value
     * @return $this
     */
    public function gamma(float $value): self
    {
        $this->image->gamma($value);
        return $this;
    }

    /**
     * Changes the contrast
     * -100 - min
     * 100 - max
     * @param int $value
     * @return $this
     */
    public function contrast(int $value): self
    {
        $this->image->contrast($value);
        return $this;
    }

    /**
     * Reverse all colors
     * @return $this
     */
    public function negative(): self
    {
        $this->image->invert();
        return $this;
    }

    /**
     * Return generated image content
     * @return string
     */
    public function getContent(): string
    {
        return (string) $this->image->encode($this->extension);
    }

    /**
     * Return data url (base64 encoded) with generated image
     * @return string
     */
    public function getDataUrl(): string
    {
        // Encode image data as external for detecting mime type
        $image = $this->imageProvider->getImageManager()->make($this->getContent());
        return (string) $image->encode('data-url');
    }

    /**
     * Return path to local file with generated image
     * @example '/path/to/dir/13b73edae8443990be1aa8f1a483bc27.jpg'
     * @param string|null $dir Directory for store files
     * @param bool $fullPath return full file path or only file name
     * @return string
     */
    public function getFilePath(?string $dir = null, bool $fullPath = true): string
    {
        $dir = $dir ?? sys_get_temp_dir();
        if (!is_dir($dir) || !is_writable($dir)) {
            throw new InvalidArgumentException("Can't write to directory {$dir}");
        }
        $dir = rtrim($dir, '/\\');
        do {
            $name = md5(uniqid('', true)) . '.' . $this->extension;
            $path = $dir . DIRECTORY_SEPARATOR . $name;
        } while (file_exists($path));

        $this->image->save($path);
        return $fullPath ? $path : $name;
    }
}
