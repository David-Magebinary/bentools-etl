<?php

namespace BenTools\ETL\Transformer;

interface TransformerInterface
{

    /**
     * Transform $value.
     *
     * @param $value
     * @return mixed - The transformed value
     */
    public function transform($value);
}
