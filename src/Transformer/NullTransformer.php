<?php

namespace BenTools\ETL\Transformer;

class NullTransformer implements TransformerInterface
{
    /**
     * @inheritDoc
     */
    public function transform($key, $value)
    {
        return $value;
    }
}
