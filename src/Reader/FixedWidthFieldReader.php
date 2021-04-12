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
 * Reader para arquivos com colunas de largura fixa.
 *
 * @author Everton
 */
class FixedWidthFieldReader implements ReaderInterface
{
    private bool $hasHeader = true;
    private int $skipLines = 0;
    /**
     *
     * @var array<int>
     */
    private array $colSizes = [];
    /** @phpstan-ignore-next-line */
    private $handle = null;

    /**
     *
     * @param resource $handle Resource fornecido por fopen()
     * @param bool $hasHeader TRUE se o arquivo possui linha de cabeçalho.
     * @param int $skipLines Quantidade de linhas para pular no início do arquivo.
     * @param int $colSize Lista com inteiros representando o tamanho de cada coluna.
     */
    public function __construct($handle, bool $hasHeader, int $skipLines, int ...$colSize)
    {
        $this->handle = $handle;
        $this->hasHeader = $hasHeader;
        $this->skipLines = $skipLines;
        $this->colSizes = $colSize;
    }

    /**
     * Aplica trim() em cada um dos campos.
     *
     * @param string $buffer
     * @return array<mixed>
     */
    protected function parse(string $buffer): array
    {
        $data = [];
        $start = 0;
        foreach ($this->colSizes as $size) {
            $data[] = trim(substr($buffer, $start, $size));
            $start += $size;
        }

        return $data;
    }

    /**
     *
     * @return array<mixed>
     * @throws ParseError
     */
    public function read(): array
    {
        $this->skipLines();

        $header = [];
        if ($this->hasHeader === true) {
            $buffer = fgets($this->handle);
            // @codeCoverageIgnoreStart
            if ($buffer === false) {
                throw new ParseError();
            }
            // @codeCoverageIgnoreEnd
            $header = $this->parse($buffer);
        }

        $buffer = true;
        $data = [];
        while ($buffer !== false) {
            $buffer = fgets($this->handle);
            if ($buffer != false) {
                $data[] = $this->parse($buffer);
            }
        }

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

    protected function skipLines(): void
    {
        if ($this->skipLines > 0) {
            for ($i = 0; $i < $this->skipLines; $i++) {
                fgets($this->handle);
            }
        }
    }
}
