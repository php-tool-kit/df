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
class CSVWriter implements WriterInterface
{
    private DataFrame $df;
    private string $filename = '';
    private string $separator = '';
    private bool $hasHeader = true;
    private bool $append = false;
    /** @phpstan-ignore-next-line ignora a exigência de declarar um tipo */
    private $handle;

    public function __construct(
        DataFrame $df,
        string $filename,
        string $separator,
        bool $hasHeader,
        bool $append = false
    ) {
        $this->df = $df;
        $this->filename = $filename;
        $this->separator = $separator;
        $this->hasHeader = $hasHeader;
        $this->append = $append;

        $this->open();
    }

    protected function open(): void
    {
        $mode = '';
        switch ($this->append) {
            case true:
                $mode = 'a';
                break;
            case false:
                $mode = 'w';
                break;
        }

        $handle = fopen($this->filename, $mode);

        // @codeCoverageIgnoreStart
        if (!$handle) {
            throw new InvalidResourceException($this->filename);
        }
        // @codeCoverageIgnoreEnd

        $this->handle = $handle;
    }

    public function write(): void
    {
        if ($this->hasHeader) {
            fputcsv($this->handle, $this->df->getColNames(), $this->separator);
        }

        $buffer = $this->df->current();
        while ($buffer !== false) {
            fputcsv($this->handle, $buffer, $this->separator);
            $buffer = $this->df->next();
        }

        fclose($this->handle);
    }
}
