<?php

namespace BenTools\ETL\Exception;

use Throwable;

abstract class ItemException extends \RuntimeException
{
    private $key;
    private $value;

    /**
     * ItemException constructor.
     * @param Throwable $previous
     * @param           $key
     * @param           $value
     */
    final public function __construct(Throwable $previous, $key, $value)
    {
        if ($previous instanceof self) {
            throw new \RuntimeException(sprintf('Previous exception cannot be instance of %s.', __CLASS__));
        }
        parent::__construct($previous->getMessage(), $previous->getCode(), $previous);
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
