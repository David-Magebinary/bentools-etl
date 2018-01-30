<?php

namespace BenTools\ETL\Transformer;

interface TransformerInterface
{

    /**
     * Transform $value.
     *
     * @param $key
     * @param $value
     */
    public function transform(&$key, &$value): void;
}
