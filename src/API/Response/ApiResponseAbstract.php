<?php

namespace GinoPane\PHPolyglot\API\Response;

/**
 * Class ApiResponseAbstract
 *
 * @package GinoPane\PHPolyglot\API\Response
 */
abstract class ApiResponseAbstract implements ApiResponseInterface
{
    /**
     * @var bool
     */
    private $success = false;

    /**
     * @var string
     */
    private $errorMessage = '';

    /**
     * @var int
     */
    private $errorCode = 0;

    /**
     * @inheritDoc
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * @inheritDoc
     */
    public function setSuccess(bool $success): ApiResponseInterface
    {
        $this->success = $success;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    /**
     * @inheritDoc
     */
    public function setErrorMessage(string $errorMessage): ApiResponseInterface
    {
        $this->errorMessage = $errorMessage;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    /**
     * @inheritDoc
     */
    public function setErrorCode(int $errorCode): ApiResponseInterface
    {
        $this->errorCode = $errorCode;

        return $this;
    }
}
