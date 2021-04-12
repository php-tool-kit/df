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
use PTK\DataFrame\Reader\FixedWidthFieldReader;
use PTK\Exception\ResourceException\InvalidResourceException;
use PTK\Exception\ResourceException\ResourceNotFoundException;

/**
 * Description of FWFReaderTest
 *
 * @author Everton
 */
class FWFReaderTest extends TestCase
{
    use TestToolsTrait;

    public function testReaderDefault()
    {
        $reader = new FixedWidthFieldReader(fopen('tests/assets/example.fwf', 'r'), true, 0, 2, 7, 3, 3);
        $this->assertInstanceOf(FixedWidthFieldReader::class, $reader);
        $this->assertEquals([
            [
                'id' => '01',
                'name' => 'John',
                'age' => '033',
                'sex' => 'M'
            ],
            [
                'id' => '02',
                'name' => 'Mary',
                'age' => '021',
                'sex' => 'F'
            ],
            [
                'id' => '03',
                'name' => 'Paul',
                'age' => '058',
                'sex' => 'M'
            ]
        ], $reader->read());
    }

    public function testReaderNoHeader()
    {
        $reader = new FixedWidthFieldReader(fopen('tests/assets/example-no-header.fwf', 'r'), false, 0, 2, 7, 3, 3);
        $this->assertInstanceOf(FixedWidthFieldReader::class, $reader);
        $this->assertEquals([
            [
                'col_0' => '01',
                'col_1' => 'John',
                'col_2' => '033',
                'col_3' => 'M'
            ],
            [
                'col_0' => '02',
                'col_1' => 'Mary',
                'col_2' => '021',
                'col_3' => 'F'
            ],
            [
                'col_0' => '03',
                'col_1' => 'Paul',
                'col_2' => '058',
                'col_3' => 'M'
            ]
        ], $reader->read());
    }

    public function testReaderSkipLines()
    {
        $reader = new FixedWidthFieldReader(fopen('tests/assets/example-skip-lines.fwf', 'r'), true, 3, 2, 7, 3, 3);
        $this->assertInstanceOf(FixedWidthFieldReader::class, $reader);
        $this->assertEquals([
            [
                'id' => '01',
                'name' => 'John',
                'age' => '033',
                'sex' => 'M'
            ],
            [
                'id' => '02',
                'name' => 'Mary',
                'age' => '021',
                'sex' => 'F'
            ],
            [
                'id' => '03',
                'name' => 'Paul',
                'age' => '058',
                'sex' => 'M'
            ]
        ], $reader->read());
    }
}
