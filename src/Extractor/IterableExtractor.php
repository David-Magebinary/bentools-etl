<?php

namespace BenTools\ETL\Extractor;

final class IterableExtractor implements ExtractorInterface
{

    /**
     * @inheritDoc
     */
    public function extract($iterable): iterable
    {
        if (!is_iterable($iterable)) {
            throw new \InvalidArgumentException(sprintf("Expected Traversable or array, got %s", gettype($iterable)));
        }
        return $iterable;
    }
}
