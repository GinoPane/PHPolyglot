<?php

namespace GinoPane\PHPolyglot\API\Response\SpellCheck;

use GinoPane\PHPolyglot\API\Response\ApiResponseAbstract;

/**
 * Class SpellcheckResponse
 *
 * @author Sergey <Gino Pane> Karavay
 */
class SpellCheckResponse extends ApiResponseAbstract
{
    /**
     * Returns an associative array of corrections
     *
     * @return string[]
     */
    public function getCorrections(): array
    {
        return (array)$this->getData();
    }

    /**
     * Sets an array of corrections
     *
     * @param string[] $corrections
     */
    public function setCorrections(array $corrections): void
    {
        $this->setData($corrections);
    }
}
