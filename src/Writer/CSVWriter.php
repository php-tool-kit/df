<?php

/*
 * The MIT License
 *
 * Copyright 2021 Everton.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace PTK\DataFrame\Writer;

use PTK\DataFrame\DataFrame;
use PTK\Exception\ResourceException\InvalidResourceException;

/**
 * Salva o data frame para um arquivo CSV.
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class CSVWriter implements WriterInterface {

    private DataFrame $df;
    private string $separator = '';
    private bool $hasHeader = true;

    /** @phpstan-ignore-next-line ignora a exigĂȘncia de declarar um tipo */
    private $handle;

    /**
     *
     * @param DataFrame $df
     * @param resource $handle Handle fornecido por fopen()
     * @param string $separator
     * @param bool $hasHeader
     */
    public function __construct(
            DataFrame $df,
            $handle,
            string $separator,
            bool $hasHeader
    ) {
        $this->df = $df;
        $this->handle = $handle;
        $this->separator = $separator;
        $this->hasHeader = $hasHeader;
    }

    public function write(): void {
        if ($this->hasHeader) {
            fputcsv($this->handle, $this->df->getColNames(), $this->separator);
        }

        foreach ($this->df as $buffer) {
            fputcsv($this->handle, $buffer, $this->separator);
        }
    }

}
