<?php

namespace BenTools\ETL\Transformer;

final class CallableTransformer implements TransformerInterface
{
    /**
     * @var callable
     */
    private $callable;

    /**
     * CallableTransformer constructor.
     */
    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    /**
     * @inheritDoc
     */
    public function transform($value)
    {
        $call = $this->callable;
        return $call($value);
    }
}
