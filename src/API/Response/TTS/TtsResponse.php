<?php

namespace GinoPane\PHPolyglot\API\Response\TTS;

use GinoPane\PHPolyglot\Exception\InvalidIoException;
use GinoPane\PHPolyglot\API\Factory\TTS\TtsApiFactory;
use GinoPane\PHPolyglot\Exception\InvalidPathException;
use GinoPane\PHPolyglot\API\Response\ApiResponseAbstract;
use GinoPane\PHPolyglot\API\Supplemental\TTS\TtsAudioFormat;

/**
 * Class TtsResponse
 *
 * @author Sergey <Gino Pane> Karavay
 */
class TtsResponse extends ApiResponseAbstract
{
    /**
     * Response audio format
     *
     * @var TtsAudioFormat
     */
    private $format = null;

    /**
     * The text that was used for generation
     *
     * @var string
     */
    private $text = '';

    /**
     * TtsResponse constructor
     *
     * @param string         $content
     * @param TtsAudioFormat $format
     */
    public function __construct(string $content, TtsAudioFormat $format, string $text)
    {
        $this->format = $format;

        $this->text = $text;

        $this->setData($content);
    }

    /**
     * Stores TTS data into specified file.
     * If no $fileName is specified, md5 of source text is used.
     * If no $extension is specified, the default extension for audio format is used.
     * If no $directory is specified, the default directory from the config is used.
     *
     * @param string $fileName Target file name without extension
     * @param string $extension Target file extension
     * @param string $directory Target directory
     *
     * @throws InvalidIoException
     * @throws InvalidPathException
     *
     * @return string
     */
    public function storeFile(string $fileName = '', string $extension = '', string $directory = ''): string
    {
        $fileName = $fileName ? $fileName : $this->generateFilename($this->text);

        $fullFileName = $extension
            ? "$fileName.$extension"
            : sprintf("%s.%s", $fileName, $this->format->getFileExtension());

        $directory = $directory ? $directory : $this->getTtsApiFactory()->getTargetDirectory();

        if (!$this->filePutContents($directory . DIRECTORY_SEPARATOR . $fullFileName, $this->getData())) {
            throw new InvalidIoException(
                sprintf(
                    'Failed to write the file "%s" to the directory "%s"',
                    $fileName, //@codeCoverageIgnore
                    $directory
                )
            );
        }

        return $fullFileName;
    }

    /**
     * @param string $fileName
     * @param string $data
     *
     * @return bool
     */
    protected function filePutContents(string $fileName, string $data): bool
    {
        return (bool)@file_put_contents($fileName, $data);
    }

    /**
     * @codeCoverageIgnore
     *
     * @return TtsApiFactory
     */
    protected function getTtsApiFactory(): TtsApiFactory
    {
        return new TtsApiFactory();
    }

    /**
     * @param string $text
     *
     * @return string
     */
    private function generateFilename(string $text): string
    {
        return md5($text);
    }
}
