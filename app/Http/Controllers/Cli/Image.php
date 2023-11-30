<?php
/**
 * This is jsut a QUICK and DIRTY solution!
 * This is NOT COMPLETED!
 * THERE is MUCH to more to be done!
 */
namespace Http\Controllers\Cli;

use MaplePHP\Http\Interfaces\ResponseInterface;
use MaplePHP\Http\Interfaces\RequestInterface;
use MaplePHP\Http\Interfaces\DirInterface;
use MaplePHP\Container\Interfaces\ContainerInterface;
use MaplePHP\Resize\Resize;
use MaplePHP\Validate\Inp;
use Http\Controllers\Cli\CliInterface;
use Services\Stream\Cli as Stream;

class Image implements CliInterface
{
    protected $container;
    protected $args;
    protected $dir;
    protected $cli;
    protected $img;

    public function __construct(ContainerInterface $container, RequestInterface $request, DirInterface $dir, Stream $cli)
    {
        $this->container = $container;
        $this->args = $request->getCliArgs();
        $this->dir = $dir;
        $this->cli = $cli;
    }

    public function resize()
    {
        $hex = isset($this->args['hex']) ? $this->args['hex'] : "";
        $opacity = isset($this->args['opacity']) ? (float)$this->args['opacity'] : 0;
        $type = isset($this->args['type']) ? $this->args['type'] : "resize";
        //$query = isset($this->args['query']) ? $this->args['query'] : null;

        if(!isset($this->args['image'])) {
            $this->cli->write("Error: You need to specify the image argument!");
            return $this->cli->getResponse();
        }

        if(!isset($this->args['resize'])) {
            $this->cli->write("Error: You need to specify the resize argument!");
            return $this->cli->getResponse();
        }

        $sizes = explode(",", $this->args['resize']);
        $savePath = $this->dir->getPublic("css/images/");
        $original = $savePath.$this->args['image'];
        $imgData = basename($this->args['image']);
        $imgData = explode(".", $imgData);
        $basename = ($imgData[0] ?? "");
        $ending = ($imgData[1] ?? "jpg");
        $this->img = new Resize($original);
        $valid = new Inp($hex);
        if($valid->equal("auto") || $valid->hex()) {
            if($hex === "auto") $hex = $this->img->getAverageColour();
            if($opacity > 1) $opacity = 0;
            if($opacity > 0) $hex = $this->img->getLuminance($hex, $opacity);
            $hex = str_replace("&", "#", $hex);
            if($this->img->isMime("image/png")) {
                $this->img->setBackgroundColor($hex, $opacity);
            }
        }

        $set = array();
        $path = "{$savePath}{$basename}-0";
        $this->resizeImg($type, $path, 2600, 2600);
        $this->img->execute();
        $set[] = $this->img->getTruePath();

        foreach($sizes as $key => $arr) {
            $int = $key + 1;
            $size = explode("x", $arr);
            $width = (int)($size[0] ?? 100);
            $heigth = (int)($size[1] ?? 100);
            $path = "{$savePath}{$basename}-{$int}-{$width}x{$heigth}";
            $this->resizeImg($type, $path, $width, $heigth);
            $this->img->convertTo("webp");
            $this->img->execute();
            $set[$width+$heigth] = $this->img->getTruePath();
        }

        $this->cli->write("Image has been resized and optimized.");
        $this->cli->write("You can use the template snippet bellow:");
        $this->cli->write("...");
        $this->cli->write($this->outputImgSet($set));

        return $this->cli->getResponse();
    }

    protected function outputImgSet(array $set): string
    {
        ksort($set);
        $original = array_shift($set);
        $orgInfo = $this->getImgData($original);
        $out = '<picture>';
        foreach($set as $img) {
            $setInfo = $this->getImgData($img);
            $out .= '<source srcset="'.$setInfo['file'].'" media="(max-width: '.$setInfo['size'][0].'px)" type="image/webp">';
        }
        $out .= '<source srcset="'.$setInfo['file'].'" type="image/webp">';
        $out .= '<img src="'.$orgInfo['file'].'" alt="Your alt info" width="'.$orgInfo['size'][0].'" height="'.$orgInfo['size'][0].'">';
        $out .= '</picture>';
        return $out;
    }

    protected function getImgData($file): array
    {
        return ["file" => $file, "size" => getimagesize($file)];
    }

    protected function resizeImg(string $type, string $file, int $width, int $height): void
    {
        $this->img->savePath($file);
        switch($type) {
            case "resize":
                $this->img->resize($width, $height);
            break;
            case "crop":
                $this->img->crop($width, $height);
            break;
            case "trim":
                $this->img->trim();
                $this->img->resize($width, $height);
            break;
        }
        if(!$this->img->isMime("image/gif")) $this->img->convertTo("jpg");
        //$fallbackImg = $this->img->getTrueBasename();
    }

    public function help()
    {
        $savePath = $this->dir->getPublic("css/images/");
        $this->cli->write('$ image [type] [--values, --values, ...]'."\n");
        $this->cli->write('--image arg is required and the image should be in the directory:');
        $this->cli->write($savePath."\n");
        $this->cli->write('Example usage:');
        $this->cli->write('php cli image resize --image=test.png --resize=1200x1200,600x600,200x200 --hex=#CCCCCC --opacity=0.5');
        return $this->cli->getResponse();
    }
}
