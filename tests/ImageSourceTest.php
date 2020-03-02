<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Salopot\ImageGenerator\ImageSources\ImageSourceInterface;
use Salopot\ImageGenerator\ImageSources;

class ImageSourceTest extends TestCase
{
    /**
     * @return ImageSourceInterface[][]
     */
    public function imageSourceProvider()
    {
        $generator = \Faker\Factory::create();
        $imageProvider = new \Salopot\ImageGenerator\ImageProvider($generator);
        return [
            [new ImageSources\Local\SolidColorSource($imageProvider)],
            [new ImageSources\Local\GallerySource($imageProvider, __DIR__ . '/data')],
            [new ImageSources\Local\GradientSource($imageProvider)],
            /** Disabled: unstable */
            //[new ImageSources\Remote\LoremPixelSource($imageProvider)],
            [new ImageSources\Remote\PicsumPhotosSource($imageProvider)],
            [new ImageSources\Remote\UnsplashSource($imageProvider)],
            [new ImageSources\Remote\PlaceKittenSource($imageProvider)],
            [new ImageSources\Remote\PlaceImgSource($imageProvider)],
        ];
    }

    /**
     * @dataProvider imageSourceProvider
     */
    public function testGetName(ImageSourceInterface $imageSource)
    {
        $this->assertIsString($imageSource->getName());
    }

    /**
     * @dataProvider imageSourceProvider
     */
    public function testGetImage(ImageSourceInterface $imageSource)
    {
        $content = (string) $imageSource->getImage(150, 100)->encode();
        // check image
        $imageInfo = getimagesizefromstring($content);
        $this->assertNotFalse($imageInfo, 'Failed asserting that content is image');
        // check size
        $this->assertSame(150, $imageInfo[0], 'Failed asserting image width');
        $this->assertSame(100, $imageInfo[1], 'Failed asserting image height');

        // check same with selector
        /*
        if (!method_exists($imageSource, 'getSelectedImage')) {
            $this->assertSame(
                (string) $imageSource->getImage(150, 100, 'selector')->encode('png'),
                (string) $imageSource->getImage(150, 100, 'selector')->encode('png'),
                'Failed asserting same image with selector'
            );
        }
        */
    }
}
