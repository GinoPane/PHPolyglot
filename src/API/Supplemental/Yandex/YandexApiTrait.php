<?php

namespace GinoPane\PHPolyglot\API\Supplemental\Yandex;

use GinoPane\NanoRest\Request\RequestContext;
use GinoPane\NanoRest\Response\JsonResponseContext;
use GinoPane\PHPolyglot\Supplemental\Language\Language;
use GinoPane\NanoRest\Exceptions\RequestContextException;
use GinoPane\PHPolyglot\Exception\BadResponseContextException;

/**
 * Trait YandexApiErrorsTrait
 *
 * Handles Yandex-specific errors
 *
 * @author Sergey <Gino Pane> Karavay
 */
trait YandexApiTrait
{
    /**
     * API key required for calls
     *
     * @var string
     */
    protected $apiKey = '';

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
        if (isset($responseArray['code'])) {
            if (($responseArray['code'] !== YandexApiStatusesInterface::STATUS_SUCCESS) &&
                isset(self::$customStatusMessages[$responseArray['code']])
            ) {
                $errorMessage = $responseArray['message']
                    ?? self::$customStatusMessages[$responseArray['code']];

                throw new BadResponseContextException($errorMessage, $responseArray['code']);
            }
        }
    }

    /**
     * @param RequestContext $requestContext
     *
     * @throws RequestContextException
     *
     * @return RequestContext
     */
    private function fillGeneralRequestSettings(RequestContext $requestContext): RequestContext
    {
        $requestContext
            ->setContentType(RequestContext::CONTENT_TYPE_FORM_URLENCODED)
            ->setResponseContextClass(JsonResponseContext::class);

        return $requestContext;
    }

    /**
     * Get auth part of the request data
     *
     * @return array
     */
    private function getAuthData(): array
    {
        return ['key' => $this->apiKey];
    }

    /**
     * @param Language $languageTo
     * @param Language $languageFrom
     *
     * @return string
     */
    private function getLanguageString(Language $languageTo, Language $languageFrom): string
    {
        return implode("-", array_filter([$languageFrom->getCode(), $languageTo->getCode()]));
    }
}
