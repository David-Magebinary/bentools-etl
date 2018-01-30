<?php

namespace BenTools\ETL\Extractor;

final class JsonExtractor implements ExtractorInterface
{
    /**
     * @inheritDoc
     */
    public function extract($json): iterable
    {
        if (is_string($json)) {
            $json = json_decode($json, true);
            if (JSON_ERROR_NONE !== json_last_error()) {
                throw new \RuntimeException(json_last_error_msg());
            }
            return $json;
        }

        if (!is_array($json)) {
            throw new \InvalidArgumentException(sprintf('Expected string or array, got %s', is_object($json) ? get_class($json) : gettype($json)));
        }

        return $json;
    }
}
