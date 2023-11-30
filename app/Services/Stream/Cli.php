<?php

namespace Services\Stream;

use MaplePHP\Http\Interfaces\ResponseInterface;
use MaplePHP\Http\Stream;
use MaplePHP\Http\UploadedFile;
use Exception;

class Cli
{
    private $stream;
    private $response;
    private $jsonFileStream;
    private $jsonFileStreamFile;

    public function __construct(ResponseInterface $response)
    {
        $this->stream = new Stream(Stream::STDIN, "r");
        $this->response = $response;
    }

    public function confirm(string $message, callable $call)
    {
        $this->write($message);
        $this->write("Type 'yes' to continue: ", false);
        if (strtolower($this->stream->getLine()) !== "yes") {
            $this->write("Aborting");
        } else {
            $this->write("...\n");
            $call($this->stream);
        }
    }

    public function step(string $message, ?string $default = null, ?string $response = null)
    {
        if (!is_null($default)) {
            $message .= " ({$default})";
        }
        $message .= ": ";
        $this->write($message, false);
        $getLine = $this->stream->getLine();
        if ($response) {
            $this->write($response);
        }
        return ($getLine ? $getLine : $default);
    }

    public function write(string $message, bool $lineBreak = true)
    {
        if ($lineBreak) {
            $message = "{$message}\n";
        }
        $this->stream->write($message);
    }

    public function createFile(string $content, string $file)
    {
        $envStream = new Stream(Stream::TEMP);
        $envStream->write($content);
        $upload = new UploadedFile($envStream);
        $upload->moveTo($file);
    }

    public function readFile(string $file): string
    {
        $stream = new Stream($file);
        return $stream->getContents();
    }

    public function setJsonFileStream(string $file)
    {
        if (is_null($this->jsonFileStream)) {
            $this->jsonFileStreamFile = $file;
            $this->jsonFileStream = false;
            if (is_file($file)) {
                $data = $this->readFile($file);
                $this->jsonFileStream = json_decode($data, true);
            }
        }
        return $this->jsonFileStream;
    }

    public function getJsonData()
    {
        return $this->jsonFileStream;
    }

    public function jsonToFile(array $array, ?callable $call = null)
    {

        if (is_null($this->jsonFileStream)) {
            throw new Exception("You need to set @setJsonFileStream([FILE_PATH]) first!", 1);
        }

        $insert = $array;
        if (is_file($this->jsonFileStreamFile)) {
            if ($data = $this->jsonFileStream) {
                if (!is_null($call)) {
                    $insert = $call($data, $array);
                    if (!is_array($insert)) {
                        throw new Exception("Arg 3 (callable) Needs to return an array", 1);
                    }
                } else {
                    $insert = array_merge($data, $array);
                }
            }
        }

        $this->createFile(json_encode($insert, JSON_PRETTY_PRINT), $this->jsonFileStreamFile);
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response->withBody($this->stream);
    }

    public function getConfig(): array
    {
        return (array)$_ENV['config'];
    }
}
