<?php

namespace GinoPane\PHPolyglot\API\Response;

/**
 * Interface ApiResponseInterface
 *
 * Interface that provides a method to get the necessary API object
 */
interface ApiResponseInterface
{
    /**
     * Returns relevant value for successful response
     *
     * @return bool
     */
    public function isSuccess(): bool;

    /**
     * Sets relevant value for response success state
     *
     * @param bool $success
     *
     * @return ApiResponseInterface
     */
    public function setSuccess(bool $success): ApiResponseInterface;

    /**
     * Returns saved error message
     *
     * @return string
     */
    public function getErrorMessage(): string;

    /**
     * Sets error message based on API response
     *
     * @param string $errorMessage
     *
     * @return ApiResponseInterface
     */
    public function setErrorMessage(string $errorMessage): ApiResponseInterface;

    /**
     * Returns saved error code
     *
     * @return int
     */
    public function getErrorCode(): int;

    /**
     * Sets error message based on API response
     *
     * @param int $errorCode
     *
     * @return ApiResponseInterface
     */
    public function setErrorCode(int $errorCode): ApiResponseInterface;
}
