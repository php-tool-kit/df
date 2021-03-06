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

/**
 * Reader para dados já no formato array.
 *
 * @author Everton
 */
class ArrayReader implements ReaderInterface
{

    /**
     *
     * @var array<mixed>
     */
    private array $data = [];

    /**
     * Observe que nenhum teste de integridade da estrutura do array é feito. Isso será feito por
     *  PTK\DataFrame\DataFrame quando self::reade() for utilizado.
     *
     * @param array<mixed> $data Array no formato $data = [linha (int)][coluna (string)] = valor (mixed)
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function read(): array
    {
        return $this->data;
    }
}
