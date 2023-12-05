<?php

namespace Services\Stream;

use MaplePHP\Http\Interfaces\ResponseInterface;
use MaplePHP\Http\Stream;
use MaplePHP\Http\UploadedFile;
use MaplePHP\Validate\Inp;
use Exception;

/**
 * Is used to install dkim packages to extend MaplePHP functionallity
 * @psalm-suppress ForbiddenCode
 */
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

    /**
     * Will create steps
     * @param  string      $message
     * @param  string|null $default
     * @param  string|null $response
     */
    public function step(?string $message, ?string $default = null, ?string $response = null)
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

    /**
     * Will mask out ouput stream
     * @param  string|null $prompt
     * @return string
     */
    function maskedInput(?string $prompt = null, string $valid = "required", array $args = []): string
    {
        $this->stream = new Stream(Stream::STDIN, "r");
        $prompt = $this->prompt($prompt." (masked input)");

        if(function_exists("shell_exec")) {
            $this->stream->write($prompt);
            // Mask input
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                // Not yet tested. But should work if my research is right
                $input = rtrim((string)shell_exec("powershell -Command \$input = Read-Host -AsSecureString; [Runtime.InteropServices.Marshal]::PtrToStringAuto([Runtime.InteropServices.Marshal]::SecureStringToBSTR(\$input))"));
            } else {
                // Tested and works
                $input = rtrim((string)shell_exec('stty -echo; read input; stty echo; echo $input'));
            }

            if(!$this->validate($input, $valid, $args)) {
                $this->stream->write(PHP_EOL."Input is requied. Try again!".PHP_EOL);
                $input = $this->maskedInput($prompt, $valid, $args);
            }

        } else {
            $input = $this->step("Warning: The input will not be mask. Your server do not support the \"shell_exec\" function. MaplePHP is using shell_exec to mask the input.\n\nPress Enter to continue");
            $this->required($prompt, $valid, $args);
        }

        $this->stream->write(PHP_EOL);
        return $input;
    }

    /**
     * Will give you multiple option to choose between 
     * @param  array  $choises
     * @return string
     */
    public function chooseInput(array $choises, ?string $prompt = null): string
    {
        if(count($choises) === 0) {
            throw new Exception("Arg1 choises is an empty array!", 1);
        }

        $keys = array_keys($choises);
        $firstOpt = reset($keys);
        $lastOpt = end($keys);

        $out = $this->prompt($prompt, "Choose input")."\n";
        foreach($choises as $key => $value) {
            $out .= "{$key}: {$value}\n";
        }
        $this->write($out);

        $message = "Choose input between ({$firstOpt}-{$lastOpt})";
        if($firstOpt === $lastOpt) {
            $message = "You can at the moment only choose ({$firstOpt})";
        }
        $value = $this->required($message);
        if(!isset($choises[$value])) {
            $value = $this->chooseInput($choises);
        }
        return $value;
    }

    /**
     * Will make input required
     * @param  string|null $message
     * @return string
     */
    public function required(?string $message, string $valid = "required", array $args = []): string
    {
        $line = $this->step($message);
        if(!$this->validate((string)$line, $valid, $args)) {
            $line = $this->required($message, $valid, $args);
        }
        return $line;
    }

    protected function validate(string $value, string $valid = "required", array $args = [])
    {
        $inp = new Inp($value);
        if(!method_exists($inp, $valid)) {
            throw new Exception("The validation do not exists", 1);
        }
        return call_user_func_array([$inp, $valid], $args);
    }

    /**
     * Write to stream
     * @param  string       $message
     * @param  bool|boolean $lineBreak
     * @return void
     */
    public function write(string $message, bool $lineBreak = true): void
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

    protected function prompt(?string $prompt = null, string $default = "Input your value"): string
    {
        $prompt = (is_null($prompt) ? $default : $prompt);
        return rtrim($prompt, ":").":";
    }
}
