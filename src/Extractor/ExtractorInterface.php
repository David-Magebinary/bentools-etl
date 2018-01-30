<?php

namespace BenTools\ETL\Extractor;

interface ExtractorInterface
{

    /**
     * @param $json
     * @return iterable
     */
    public function extract($input): iterable;
}
