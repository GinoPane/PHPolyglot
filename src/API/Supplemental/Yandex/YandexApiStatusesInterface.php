<?php

namespace GinoPane\PHPolyglot\API\Supplemental\Yandex;

/**
 * Interface YandexApiErrorsInterface
 *
 * Contains general error list that can be explicitly returned by API calls
 *
 * @author Sergey <Gino Pane> Karavay
 */
interface YandexApiStatusesInterface
{
    const STATUS_SUCCESS                        = 200;
    const STATUS_INVALID_API_KEY                = 401;
    const STATUS_BLOCKED_API_KEY                = 402;
    const STATUS_REQUEST_AMOUNT_LIMIT_EXCEEDED  = 403;
    const STATUS_TEXT_AMOUNT_LIMIT_EXCEEDED     = 404;
    const STATUS_TEXT_SIZE_LIMIT_EXCEEDED       = 413;
    const STATUS_TEXT_CANNOT_BE_TRANSLATED      = 422;
    const STATUS_TRANSLATION_DIRECTION_INVALID  = 501;
}