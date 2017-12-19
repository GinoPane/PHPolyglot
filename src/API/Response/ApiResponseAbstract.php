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
     * @var mixed
     */
    protected $data;

    /**
     * Returns response value
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Sets response data
     *
     * @param $data
     *
     * @return void
     */
    protected function setData($data)
    {
        $this->data = $data;
    }
}
