<?php

namespace BenTools\ETL\Transformer;

final class CallableValueTransformer implements TransformerInterface
{
    /**
     * @var callable
     */
    private $callable;

    /**
     * CallableTransformer constructor.
     * @param callable $callable
     */
    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    public function transform(&$key, &$value): void
    {
        $call = $this->callable;
        $value = $call($value);
    }
}
