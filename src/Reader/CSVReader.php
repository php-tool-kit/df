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

namespace PTK\DataFrame\Reader;

use ParseError;
use PTK\Exception\ResourceException\InvalidResourceException;
use PTK\Exception\ResourceException\ResourceNotFoundException;

use function array_key_first;
use function sizeof;

/**
 * Reader pra arquivos CSV.
 */

class CSVReader implements ReaderInterface
{
    private string $separator = '';
    private bool $hasHeader = true;
    private int $skipLines = 0;
    /** @phpstan-ignore-next-line */
    private $handle = null;

    /**
     *
     * @param resource $handle O resource fornecido por fopen().
     * @param string $separator O separador utilizado.
     * @param bool $hasHeader True se possui linha de cabeçalho.
     * @param int $skipLines Número de linhas para pular no início do arquivo.
     */
    public function __construct($handle, string $separator, bool $hasHeader, int $skipLines = 0)
    {
        $this->handle = $handle;
        $this->separator = $separator;
        $this->hasHeader = $hasHeader;
        $this->skipLines = $skipLines;
    }

    public function read(): array
    {
        $this->skipLines();

        $header = [];
        if ($this->hasHeader === true) {
            $header = fgetcsv($this->handle, 0, $this->separator);
            // @codeCoverageIgnoreStart
            if ($header === false || is_null($header)) {
                throw new ParseError();
            }
            // @codeCoverageIgnoreEnd
        }

        $data = $this->parse();

        if ($header === []) {
            $numCols = sizeof($data[array_key_first($data)]);
            for ($i = 0; $i < $numCols; $i++) {
                $header[] = "col_$i";
            }
        }

        foreach ($data as $index => $line) {
            $newLine = [];
            foreach ($header as $i => $k) {
                $newLine[$k] = $line[$i];
            }
            $data[$index] = $newLine;
        }

        return $data;
    }

    /**
     *
     * @return array<mixed>
     */
    protected function parse(): array
    {
        $buffer = true;
        $data = [];
        while ($buffer !== false) {
            $buffer = fgetcsv($this->handle, 0, $this->separator);
            if ($buffer != false) {
                $data[] = $buffer;
            }
        }

        return $data;
    }

    /**
     *
     * @return void
     */
    protected function skipLines(): void
    {
        if ($this->skipLines > 0) {
            for ($i = 0; $i < $this->skipLines; $i++) {
                fgets($this->handle);
            }
        }
    }
}
