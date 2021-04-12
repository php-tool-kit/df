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

namespace PTK\DataFrame\Test;

use PHPUnit\Framework\TestCase;
use PTK\DataFrame\Reader\CSVReader;
use PTK\Exception\ResourceException\ResourceNotFoundException;
use PTK\Exception\ResourceException\InvalidResourceException;

/**
 * Testes para CSVReaderTest
 *
 * @author Everton
 */
class CSVReaderTest extends TestCase
{
    use TestToolsTrait;

    public function testReaderDefault()
    {
        $reader = new CSVReader(fopen('tests/assets/example.csv', 'r'), ';', true);
        $this->assertInstanceOf(CSVReader::class, $reader);
        $this->assertEquals($this->arraySample, $reader->read());
    }

    public function testReaderNoHeader()
    {
        $reader = new CSVReader(fopen('tests/assets/example-no-header.csv', 'r'), ';', false);
        $this->assertInstanceOf(CSVReader::class, $reader);
        $this->assertEquals([
            [
                'col_0' => 1,
                'col_1' => 'John',
                'col_2' => '33',
                'col_3' => 'M'
            ],
            [
                'col_0' => 2,
                'col_1' => 'Mary',
                'col_2' => 21,
                'col_3' => 'F'
            ],[
                'col_0' => 3,
                'col_1' => 'Paul',
                'col_2' => 58,
                'col_3' => 'M'
            ]
        ], $reader->read());
    }

    public function testReaderSkipLines()
    {
        $reader = new CSVReader(fopen('tests/assets/example-skip-lines.csv', 'r'), ';', true, 4);
        $this->assertInstanceOf(CSVReader::class, $reader);
        $this->assertEquals($this->arraySample, $reader->read());
    }
}
