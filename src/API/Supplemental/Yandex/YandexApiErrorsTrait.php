<?php

namespace GinoPane\PHPolyglot\API\Supplemental\Yandex;

use GinoPane\PHPolyglot\Exception\BadResponseContextException;

/**
 * Trait YandexApiErrorsTrait
 *
 * Handles Yandex-specific errors
 *
 * @author Sergey <Gino Pane> Karavay
 */
trait YandexApiErrorsTrait
{
    /**
     * Custom status messages for error statuses
     *
     * @var array
     */
    private static $customStatusMessages = [
        YandexApiStatusesInterface::STATUS_INVALID_API_KEY                => "Invalid API key",
        YandexApiStatusesInterface::STATUS_BLOCKED_API_KEY                => "Blocked API key",
        YandexApiStatusesInterface::STATUS_REQUEST_AMOUNT_LIMIT_EXCEEDED  =>
            "Exceeded the daily limit on the number of requests",
        YandexApiStatusesInterface::STATUS_TEXT_AMOUNT_LIMIT_EXCEEDED     =>
            "Exceeded the daily limit on the amount of translated text",
        YandexApiStatusesInterface::STATUS_TEXT_SIZE_LIMIT_EXCEEDED       => "Exceeded the maximum text size",
        YandexApiStatusesInterface::STATUS_TEXT_CANNOT_BE_TRANSLATED      => "The text cannot be translated",
        YandexApiStatusesInterface::STATUS_TRANSLATION_DIRECTION_INVALID  =>
            "The specified translation direction is not supported"
    ];

    /**
     * Checks for Yandex-specific errors and throws exception if error detected
     *
     * @param array $responseArray
     *
     * @throws BadResponseContextException
     */
    public function filterYandexSpecificErrors(array $responseArray): void
    {
        if (!isset($responseArray['code'])) {
            throw new BadResponseContextException('Response status undefined');
        }

        if (($responseArray['code'] !== YandexApiStatusesInterface::STATUS_SUCCESS) &&
            isset(self::$customStatusMessages[$responseArray['code']])
        ) {
            $errorMessage = $responseArray['message']
                ?? self::$customStatusMessages[$responseArray['code']];

            throw new BadResponseContextException($errorMessage, $responseArray['code']);
        }
    }
}