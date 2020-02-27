<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use \Salopot\ImageGenerator\ImageGenerator;

final class ImageGeneratorTest extends TestCase
{
    /** @var ImageGenerator */
    protected $imageGenerator;

    protected function setUp(): void
    {
        $faker = \Faker\Factory::create();
        $imageProvider = new \Salopot\ImageGenerator\ImageProvider($faker);
        $this->imageGenerator = new ImageGenerator(
            $imageProvider,
            $imageProvider->getImageManager()->canvas(100, 100, '#ff0000')
            // TODO: possible set small gradient image
            //$imageProvider->getImageManager()->make(__DIR__ . '/data/test.jpg')
        );
    }

    public function testGetContent()
    {
        $content = $this->imageGenerator->getContent();
        $this->assetImage($content);
    }

    protected function assetImage(string $content, ?string $message = null)
    {
        $imageInfo = getimagesizefromstring($content);
        $this->assertNotFalse($imageInfo, $message ?? 'Failed asserting that content is image');
    }

    public function testSetExtension()
    {
        foreach (['jpg' => IMAGETYPE_JPEG, 'png' => IMAGETYPE_PNG, 'gif' => IMAGETYPE_GIF] as $extension => $imageType) {
            $result = $this->imageGenerator->setExtension($extension);
            $imageInfo = getimagesizefromstring($result->getContent());
            $this->assertNotFalse($imageInfo);
            $this->assertSame($imageType, $imageInfo[2]);
        }

        // check invalid type
        $this->expectException(InvalidArgumentException::class);
        $this->imageGenerator->setExtension('txt');
    }

    public function testGetDataUrl()
    {
        $url = $this->imageGenerator->getDataUrl();
        $this->assertRegexp('#^data:image/jpeg;base64,[A-Za-z0-9+/]+={0,2}$#', $url);
    }

    public function testGetFilePath()
    {
        $path = $this->imageGenerator->getFilePath();
        $this->assertFileExists($path);
        if (file_exists($path)) {
            unlink($path);
        }
    }

    public function imageProcessingProvider()
    {
        $faker = \Faker\Factory::create();
        $imageProvider = new \Salopot\ImageGenerator\ImageProvider($faker);
        return [
            ['grayscale', []],
            ['opacity', [95]],
            ['brightness', [15]],

            ['negative', []],
            ['text', ['Hello world', 35, 'left', 'top', 30, -90]],
            ['insertImage', [
                $imageProvider->getImageManager()->canvas(10, 10, '#fff'),
                'right',
                'top'
            ]],
            //TODO: after gradient was implemented
            //['contrast', [65]],
            //['gamma', [1.6]],
            //['blur', [15]],
        ];
    }

    /**
     * @dataProvider imageProcessingProvider
     */
    public function testImageProcessing(string $name, array $params)
    {
        $initContent = $this->imageGenerator->getContent();
        call_user_func([$this->imageGenerator, $name], ...$params);
        $resultContent = $this->imageGenerator->getContent();
        $this->assetImage($resultContent, "Invalid {$name} processing");
        $this->assertNotSame($initContent, $resultContent);
    }
}