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
use PTK\DataFrame\DataFrame;
use PTK\DataFrame\Reader\ArrayReader;
use PTK\DataFrame\Reader\CSVReader;
use PTK\DataFrame\Writer\CSVWriter;

/**
 * Description of CSVWriterTest
 *
 * @author Everton
 */
class CSVWriterTest extends TestCase
{
    use TestToolsTrait;

    public function testWriterDefault()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $csv = 'tests/assets/cache/example.csv';
        $writer = new CSVWriter($df, $csv, ';', true);
        $this->assertInstanceOf(CSVWriter::class, $writer);
        $writer->write();
        $reader = new CSVReader($csv, ';', true);
        $this->assertEquals($this->arraySample, $reader->read());
    }

    public function testWriterNoHeader()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $csv = 'tests/assets/cache/example-no-header.csv';
        $writer = new CSVWriter($df, $csv, ';', false);
        $writer->write();
        $reader = new CSVReader($csv, ';', false);
        $this->assertEquals([
            [
                'col_0' => '1',
                'col_1' => 'John',
                'col_2' => '33',
                'col_3' => 'M'
            ],
            [
                'col_0' => '2',
                'col_1' => 'Mary',
                'col_2' => '21',
                'col_3' => 'F'
            ],
            [
                'col_0' => '3',
                'col_1' => 'Paul',
                'col_2' => '58',
                'col_3' => 'M'
            ]
        ], $reader->read());
    }

    public function testWriterAppend()
    {
        $csv = 'tests/assets/cache/example-append.csv';
        if (file_exists($csv)) {
            unlink($csv);
        }

        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);

        $writer = new CSVWriter($df, $csv, ';', true);
        $writer->write();

        $reader = new ArrayReader([
            [
                'id' => 4,
                'name' => 'Gary',
                'age' => 18,
                'sex' => 'M'
            ]
        ]);
        $df = new DataFrame($reader);

        $writer = new CSVWriter($df, $csv, ';', false, true);
        $writer->write();

        $reader = new CSVReader($csv, ';', true);
        $this->assertEquals([
            [
                'id' => 1,
                'name' => 'John',
                'age' => 33,
                'sex' => 'M'
            ],
            [
                'id' => 2,
                'name' => 'Mary',
                'age' => 21,
                'sex' => 'F'
            ],
            [
                'id' => 3,
                'name' => 'Paul',
                'age' => 58,
                'sex' => 'M'
            ],
            [
                'id' => 4,
                'name' => 'Gary',
                'age' => 18,
                'sex' => 'M'
            ]
        ], $reader->read());
    }
}
