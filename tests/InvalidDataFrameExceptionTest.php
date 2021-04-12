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
use PTK\DataFrame\Exception\InvalidDataFrameException;
use PTK\DataFrame\Reader\ArrayReader;

/**
 * Testes para InvalidDataFrameExceptionTest
 *
 * @author Everton
 */
class InvalidDataFrameExceptionTest extends TestCase
{
    use TestToolsTrait;

    public function testGetInvalidLines()
    {
        $reader = new ArrayReader($this->arraySampleInvalid);

        try {
            $df = new DataFrame($reader);
        } catch (InvalidDataFrameException $ex) {
            $this->assertEquals([
                2 => [
                    'id' => 3,
                    'name' => 'Paul',
                    'age' => 58
                ]
            ], $ex->getInvalidLines());
        }
    }
}
