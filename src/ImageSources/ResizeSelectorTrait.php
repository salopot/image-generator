<?php
namespace Salopot\ImageGenerator\ImageSources;

use Salopot\ImageGenerator\ImageProvider;
use Intervention\Image\Image;

/**
 * Local storage selector
 * Warning: use resize
 * for getting same image with different dimensions
 * Use only if can't implement SourceSelectorTrait functions
 */
trait ResizeSelectorTrait
{
    /** @var resource[] */
    protected $selectorMap;

    /** @var ImageProvider */
    protected $imageProvider;

    /**
     * @param ImageProvider $imageProvider
     */
    public function __construct(ImageProvider $imageProvider)
    {
        $this->imageProvider = $imageProvider;
    }

    /**
     * Return random image from source
     * @param int $width
     * @param int $height
     * @return Image
     */
    abstract protected function getRandomImage(int $width, int $height): Image;

    /**
     * Return image by storing selector
     * @param int $width
     * @param int $height
     * @param string $selectorName
     * @return Image
     */
    protected function getSelectedImage(int $width, int $height, string $selectorName): Image
    {
        if (isset($this->selectorMap[$selectorName])) {
            $file = $this->selectorMap[$selectorName];
            rewind($file);
            $image = $this->imageProvider->getImageManager()
                ->make(stream_get_contents($file));
            if ($image->getWidth() !== $width || $image->getHeight() !== $height) {
                // Warning: use image resize
                $image->resize($width, $height);
            }
            return $image;
        } else {
            $image = $this->getRandomImage($width, $height);
            $content = (string) $image->encode('png');
            $file = tmpfile();
            fwrite($file, $content);
            $this->selectorMap[$selectorName] = $file;
            return $image;
        }
    }

    /**
     * @inheritDoc
     */
    public function getImage(int $width, int $height, ?string $selectorName = null): Image
    {
        return $selectorName
            ? $this->getSelectedImage($width, $height, $selectorName)
            : $this->getRandomImage($width, $height);
    }
}
