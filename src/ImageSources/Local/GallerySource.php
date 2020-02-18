<?php
declare(strict_types=1);

namespace Salopot\ImageGenerator\ImageSources\Local;

use Salopot\ImageGenerator\ImageProvider;
use Salopot\ImageGenerator\ImageSources\ImageSourceInterface;
use Salopot\ImageGenerator\ImageSources\SourceSelectorTrait;
use FilesystemIterator;
use Intervention\Image\Image;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use SplFileInfo;

class GallerySource implements ImageSourceInterface
{
    use SourceSelectorTrait;

    public const NAME = 'Gallery';

    public const RESIZE_MODE_RESIZE = 'resize';
    public const RESIZE_MODE_FIT = 'fit';

    /** @var string */
    protected $galleryPath;

    /** @var string[] */
    protected $imagesList;

    /** @var string */
    protected $resizeMode;

    /**
     * GallerySource constructor.
     * @param ImageProvider $imageProvider
     * @param string $galleryPath
     * @param string $resizeMode
     */
    public function __construct(
        ImageProvider $imageProvider,
        string $galleryPath,
        string $resizeMode = self::RESIZE_MODE_FIT
    ) {
        $this->imageProvider = $imageProvider;
        $this->galleryPath = $galleryPath;
        $this->resizeMode = $resizeMode;
    }

    public function getName(): string
    {
        return static::NAME;
    }

    /**
     * Return image list ( with lazy loading )
     * @return string[]
     */
    protected function getImageList(): array
    {
        if ($this->imagesList === null) {
            $extensions = $this->imageProvider->getImageManager()->getSupportedExtensions();
            $fsIterator = new RecursiveDirectoryIterator($this->galleryPath, FilesystemIterator::SKIP_DOTS);
            /** @var SplFileInfo $item */
            foreach (new RecursiveIteratorIterator($fsIterator) as $item) {
                if (in_array($item->getExtension(), $extensions, true)) {
                    $this->imagesList[] = (string) $item;
                }
            }
            if (empty($this->imagesList)) {
                $extensionsEnum = implode(' ,', $extensions);
                throw new RuntimeException("Do not found any images ($extensionsEnum) in path {$this->galleryPath}");
            }
        }
        return $this->imagesList;
    }

    protected function getRandomSelector()
    {
        $imageList = $this->getImageList();
        $index = mt_rand(0, count($imageList) - 1);
        return $imageList[$index];
    }

    protected function getImageBySelector(int $width, int $height, $selector): Image
    {
        $image = $this->imageProvider->getImageManager()->make($selector);
        switch ($this->resizeMode) {
            case self::RESIZE_MODE_FIT:
                $image->fit($width, $height);
                break;
            case self::RESIZE_MODE_RESIZE:
                $image->resize($width, $height);
                break;
            default:
                throw new RuntimeException("Unsupported resize mode {$this->resizeMode}");
        }
        return $image;
    }

    public function getRandomImage(int $width, int $height): Image
    {
        return $this->getImageBySelector($width, $height, $this->getRandomSelector());
    }
}
