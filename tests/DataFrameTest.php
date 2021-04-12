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

use InvalidArgumentException;
use LengthException;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use PTK\DataFrame\DataFrame;
use PTK\DataFrame\Exception\InvalidColumnException;
use PTK\DataFrame\Exception\InvalidDataFrameException;
use PTK\DataFrame\Reader\ArrayReader;
use PTK\DataFrame\Reader\EmptyDataFrameReader;

/**
 * Test for DataFrame
 *
 * @author Everton
 */
class DataFrameTest extends TestCase
{
    use TestToolsTrait;

    public function testInstanceCreationWithEmptyDataFrame()
    {
        $this->assertInstanceOf(DataFrame::class, new DataFrame(new EmptyDataFrameReader()));
    }

    public function testInstanceCreationSuccess()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $this->assertInstanceOf(DataFrame::class, $df);
        $this->assertEquals($this->arraySample, $df->getAsArray());
    }

    public function testInstanceCreationFails()
    {
        $reader = new ArrayReader($this->arraySampleInvalid);
        $this->expectException(InvalidDataFrameException::class);
        $df = new DataFrame($reader);
    }

    public function testColTypesDetection()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $this->assertEquals(['integer' => 2, 'string' => 1], $df->detectColTypes('age'));
        $this->assertEquals(['integer' => 1, 'string' => 1], $df->detectColTypes('age', 2));
    }

    public function testColTypesDetectionFailsColUnknow()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $this->expectException(InvalidColumnException::class);
        $df->detectColTypes('unknow');
    }

    public function testColTypesDetectionFailsLinesInvalid()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $this->expectException(InvalidArgumentException::class);
        $df->detectColTypes('age', 0);
    }

    public function testGetColTypes()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $this->assertEquals(
            [
                'id' => 'integer',
                'name' => 'string',
                'age' => 'integer',
                'sex' => 'string',
            ],
            $df->getColTypes()
        );
    }

    public function testColExistsSuccess()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $this->assertTrue($df->colExists('age'));
    }

    public function testColExistsFails()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $this->assertFalse($df->colExists('unknow'));
    }

    public function testGetColNames()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $this->assertEquals(array_keys($this->arraySample[0]), $df->getColNames());
    }

    public function testGetColsSuccess()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $dff = $df->getCols('id', 'age');
        $this->assertInstanceOf(DataFrame::class, $dff);
        $this->assertEquals(
            [
                [
                    'id' => 1,
                    'age' => "33",
                ],
                [
                    'id' => 2,
                    'age' => 21,
                ],
                [
                    'id' => 3,
                    'age' => 58,
                ],
            ],
            $dff->getAsArray()
        );
    }

    public function testGetColsFails()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $this->expectException(InvalidColumnException::class);
        $dff = $df->getCols('id', 'unknow');
    }

    public function testGetLinesSuccess()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $dff = $df->getLines(0, 2);
        $this->assertInstanceOf(DataFrame::class, $dff);
        $this->assertEquals(
            [
                0 => [
                    'id' => 1,
                    'name' => 'John',
                    'age' => "33",
                    'sex' => 'M'
                ],
                2 => [
                    'id' => 3,
                    'name' => 'Paul',
                    'age' => 58,
                    'sex' => 'M'
                ],
            ],
            $dff->getAsArray()
        );
    }

    public function testGetLinesEmpty()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $dff = $df->getLines(9);
        $this->assertInstanceOf(DataFrame::class, $dff);
        $this->assertEquals([], $dff->getAsArray());
    }

    public function testReindexSuccess()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $dff = $df->getLines(0, 2);
        $this->assertInstanceOf(DataFrame::class, $dff->reindex());
        $this->assertEquals(
            [
                [
                    'id' => 1,
                    'name' => 'John',
                    'age' => "33",
                    'sex' => 'M'
                ],
                [
                    'id' => 3,
                    'name' => 'Paul',
                    'age' => 58,
                    'sex' => 'M'
                ],
            ],
            $dff->getAsArray()
        );
    }

    public function testGetLinesByRangeFails()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $this->expectException(InvalidArgumentException::class);
        $dff = $df->getLinesByRange(2, 1);
    }

    public function testGetLinesByRangeSuccess()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $dff = $df->getLinesByRange(1, 2);
        $this->assertInstanceOf(DataFrame::class, $dff);
        $this->assertEquals(
            [
                1 => [
                    'id' => 2,
                    'name' => 'Mary',
                    'age' => 21,
                    'sex' => 'F'
                ],
                2 => [
                    'id' => 3,
                    'name' => 'Paul',
                    'age' => 58,
                    'sex' => 'M'
                ]
            ],
            $dff->getAsArray()
        );
    }

    public function testMergeColsSuccess()
    {
        $reader = new ArrayReader($this->arraySample);
        $reader1 = new ArrayReader($this->otherSample);
        $reader2 = new ArrayReader($this->anExample);
        $df = new DataFrame($reader);
        $df1 = new DataFrame($reader1);
        $df2 = new DataFrame($reader2);
        $this->assertInstanceOf(DataFrame::class, $df->mergeCols($df1, $df2));
        $this->assertEquals(
            [
                [
                    'id' => 1,
                    'name' => 'John',
                    'age' => "33",
                    'sex' => 'M',
                    'score' => 50,
                    'good' => 0
                ],
                [
                    'id' => 2,
                    'name' => 'Mary',
                    'age' => 21,
                    'sex' => 'F',
                    'score' => 35,
                    'good' => -1
                ],
                [
                    'id' => 3,
                    'name' => 'Paul',
                    'age' => 58,
                    'sex' => 'M',
                    'score' => 98,
                    'good' => 1
                ]
            ],
            $df->getAsArray()
        );
    }

    public function testMergeColsFails()
    {
        $reader = new ArrayReader($this->arraySample);
        $reader1 = new ArrayReader(
            [
                [
                    'good' => 0
                ],
                [
                    'good' => -1
                ]
            ]
        );
        $df = new DataFrame($reader);
        $df1 = new DataFrame($reader1);
        $this->expectException(InvalidDataFrameException::class);
        $df->mergeCols($df1);
    }

    public function testMergeLinesSuccess()
    {
        $reader = new ArrayReader($this->arraySample);
        $reader1 = new ArrayReader([
            [
                'id' => 4,
                'name' => 'Gary',
                'age' => 18,
                'sex' => 'M'
            ]
        ]);
        $reader2 = new ArrayReader([
            [
                'id' => 5,
                'name' => 'Anne',
                'age' => 44,
                'sex' => 'F'
            ]
        ]);
        $df = new DataFrame($reader);
        $df1 = new DataFrame($reader1);
        $df2 = new DataFrame($reader2);
        $this->assertInstanceOf(DataFrame::class, $df->mergeLines($df1, $df2));
        $this->assertEquals(
            [
                [
                    'id' => 1,
                    'name' => 'John',
                    'age' => "33",
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
                ],
                [
                    'id' => 5,
                    'name' => 'Anne',
                    'age' => 44,
                    'sex' => 'F'
                ]
            ],
            $df->getAsArray()
        );
    }

    public function testMergeLinesFail()
    {
        $reader = new ArrayReader($this->arraySample);
        $reader1 = new ArrayReader([
            [
                'id' => 4,
                'name' => 'Gary',
                'age' => 18,
            ]
        ]);
        $df = new DataFrame($reader);
        $df1 = new DataFrame($reader1);
        $this->expectException(InvalidColumnException::class);
        $df->mergeLines($df1);
    }

    public function testRemoveLinesSuccess()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $this->assertInstanceOf(DataFrame::class, $df->removeLines(1, 2, 9));
        $this->assertEquals(
            [
                [
                    'id' => 1,
                    'name' => 'John',
                    'age' => "33",
                    'sex' => 'M'
                ]
            ],
            $df->getAsArray()
        );
    }

    public function testRemoveColsSuccess()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $this->assertInstanceOf(DataFrame::class, $df->removeCols('age', 'sex'));
        $this->assertEquals(
            [
                [
                    'id' => 1,
                    'name' => 'John'
                ],
                [
                    'id' => 2,
                    'name' => 'Mary'
                ],
                [
                    'id' => 3,
                    'name' => 'Paul'
                ],
            ],
            $df->getAsArray()
        );
    }

    public function testRemoveColsFails()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $this->expectException(InvalidColumnException::class);
        $df->removeCols('age', 'unknow');
    }

    public function testSortingSuccess()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $this->assertInstanceOf(DataFrame::class, $df->sort([
            'sex' => 'asc',
            'id' => 'desc'
        ]));
        $this->assertEquals(
            [
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
                    'id' => 1,
                    'name' => 'John',
                    'age' => "33",
                    'sex' => 'M'
                ]
            ],
            $df->getAsArray()
        );
    }

    public function testSortingColNameUnknowFails()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $this->expectException(InvalidColumnException::class);
        $df->sort([
            'unknow' => 'asc',
            'id' => 'desc'
        ]);
    }

    public function testSortingSortFlagInvalidFails()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $this->expectException(InvalidArgumentException::class);
        $df->sort([
            'sex' => 'invalid',
            'id' => 'desc'
        ]);
    }

    public function testFilterSuccess()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $dff = $df->filter(function ($data): bool {
            if ($data['age'] == 21) {
                return true;
            }
            return false;
        });
        $this->assertInstanceOf(DataFrame::class, $dff);
        $this->assertEquals(
            [
                1 => [
                    'id' => 2,
                    'name' => 'Mary',
                    'age' => 21,
                    'sex' => 'F'
                ]
            ],
            $dff->getAsArray()
        );
    }

    public function testFilterEmpty()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $dff = $df->filter(function ($data): bool {
            if ($data['age'] == 22) {
                return true;
            }
            return false;
        });
        $this->assertInstanceOf(DataFrame::class, $dff);
        $this->assertEquals([], $dff->getAsArray());
    }

    public function testSeekSuccess()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $seek = $df->seek(function ($data): bool {
            if ($data['age'] == 21) {
                return true;
            }
            return false;
        });
        $this->assertEquals([1], $seek);
    }

    public function testSeekEmpty()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $seek = $df->seek(function ($data): bool {
            if ($data['age'] == 22) {
                return true;
            }
            return false;
        });
        $this->assertEquals([], $seek);
    }

    public function testDuplicatedSuccess()
    {
        $reader = new ArrayReader($this->duplicatedSample);
        $df = new DataFrame($reader);
        $duplicated = $df->getDuplicatedLines('name', 'age', 'sex');
        $this->assertEquals([1, 3], $duplicated);
    }

    public function testDuplicatedEmpty()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $duplicated = $df->getDuplicatedLines('name', 'age', 'sex');
        $this->assertEquals([], $duplicated);
    }

    public function testDuplicatedFails()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $this->expectException(InvalidColumnException::class);
        $duplicated = $df->getDuplicatedLines('name', 'age', 'unknow');
    }

    public function testApplyFunctionOnLinesSuccess()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $this->assertInstanceOf(DataFrame::class, $df->applyOnLines(function ($line) {
            $line['age'] += 10;
            return $line;
        }));
        $this->assertEquals(
            [
                [
                    'id' => 1,
                    'name' => 'John',
                    'age' => 43,
                    'sex' => 'M'
                ],
                [
                    'id' => 2,
                    'name' => 'Mary',
                    'age' => 31,
                    'sex' => 'F'
                ],
                [
                    'id' => 3,
                    'name' => 'Paul',
                    'age' => 68,
                    'sex' => 'M'
                ]
            ],
            $df->getAsArray()
        );
    }

    public function testApplyFunctionOnColsSuccess()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $this->assertInstanceOf(DataFrame::class, $df->applyOnCols('sex', function ($cell) {
            return strtolower($cell);
        }));
        $this->assertEquals(
            [
                [
                    'id' => 1,
                    'name' => 'John',
                    'age' => "33",
                    'sex' => 'm'
                ],
                [
                    'id' => 2,
                    'name' => 'Mary',
                    'age' => 21,
                    'sex' => 'f'
                ],
                [
                    'id' => 3,
                    'name' => 'Paul',
                    'age' => 58,
                    'sex' => 'm'
                ]
            ],
            $df->getAsArray()
        );
    }

    public function testApplyFunctionOnColsFails()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $this->expectException(InvalidColumnException::class);
        $df->applyOnCols('unknow', function ($cell) {
            return strtolower($cell);
        });
    }

    public function testSumColFails()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $this->expectException(InvalidColumnException::class);
        $df->sumCol('unknow');
    }

    public function testSumColSuccess()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $this->assertEquals(112, $df->sumCol('age'));
    }

    public function testSumLinesSuccess()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $this->assertEquals([34, 23, 61], $df->sumLines('id', 'age'));
    }

    public function testSumLinesFails()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $this->expectException(InvalidColumnException::class);
        $df->sumLines('unknow');
    }

    public function testSetColNamesFails()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $this->expectException(LengthException::class);
        $df->setColNames('cod', 'nome', 'idade');
    }

    public function testSetColNamesSuccess()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $newColNames = ['cod', 'nome', 'idade', 'sexo'];
        $this->assertInstanceOf(DataFrame::class, $df->setColNames(...$newColNames));
        $this->assertEquals($newColNames, $df->getColNames());
    }

    public function testSetPartialColNamesSuccess()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $newColNames = ['id', 'nome', 'age', 'sexo'];
        $this->assertInstanceOf(DataFrame::class, $df->setColNames(...$newColNames));
        $this->assertEquals($newColNames, $df->getColNames());
    }

    public function testChangeColNameFails()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $this->expectException(InvalidColumnException::class);
        $df->changeColName('unknow', 'newCol');
    }

    public function testChangeColNameSuccess()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $this->assertInstanceOf(DataFrame::class, $df->changeColName('name', 'nome'));
        $this->assertEquals(['id', 'nome', 'age', 'sex'], $df->getColNames());
    }

    public function testAppendColSuccess()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $this->assertInstanceOf(
            DataFrame::class,
            $df->appendCol('email', ['john@mail.com', 'mary@mail.com', 'paul@mail.com'])
        );
        $this->assertEquals([
            [
                'id' => 1,
                'name' => 'John',
                'age' => "33",
                'sex' => 'M',
                'email' => 'john@mail.com'
            ],
            [
                'id' => 2,
                'name' => 'Mary',
                'age' => 21,
                'sex' => 'F',
                'email' => 'mary@mail.com'
            ],
            [
                'id' => 3,
                'name' => 'Paul',
                'age' => 58,
                'sex' => 'M',
                'email' => 'paul@mail.com'
            ]
        ], $df->getAsArray());
    }

    public function testReplaceColSuccess()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $this->assertInstanceOf(DataFrame::class, $df->appendCol('name', ['joao', 'maria', 'paulo']));
        $this->assertEquals([
            [
                'id' => 1,
                'name' => 'joao',
                'age' => "33",
                'sex' => 'M'
            ],
            [
                'id' => 2,
                'name' => 'maria',
                'age' => 21,
                'sex' => 'F'
            ],
            [
                'id' => 3,
                'name' => 'paulo',
                'age' => 58,
                'sex' => 'M'
            ]
        ], $df->getAsArray());
    }

    public function testAppendColWithDiffLineNumbersFails()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $this->expectException(LengthException::class);
        $df->appendCol('email', ['john@mail.com', 'mary@mail.com']);
    }

    public function testAppendColWithInvalidKeyOnNewColFails()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $this->expectException(OutOfBoundsException::class);
        $df->appendCol('email', [0 => 'john@mail.com', 1 => 'mary@mail.com', 3 => 'paul@mail.com']);
    }

    public function testNext()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);

        $this->assertEquals([
            'id' => 2,
            'name' => 'Mary',
            'age' => 21,
            'sex' => 'F'
        ], $df->next());
        $this->assertEquals([
            'id' => 3,
            'name' => 'Paul',
            'age' => 58,
            'sex' => 'M'
        ], $df->next());
        $this->assertFalse($df->next());
    }

    public function testPrevious()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $df->next();
        $df->next();
        $this->assertEquals([
            'id' => 2,
            'name' => 'Mary',
            'age' => 21,
            'sex' => 'F'
        ], $df->previous());
        $this->assertEquals([
            'id' => 1,
            'name' => 'John',
            'age' => "33",
            'sex' => 'M'
        ], $df->previous());
        $this->assertFalse($df->previous());
    }

    public function testFirst()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $df->next();
        $df->next();
        $this->assertEquals([
            'id' => 1,
            'name' => 'John',
            'age' => "33",
            'sex' => 'M'
        ], $df->first());
    }

    public function testLast()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $this->assertEquals([
            'id' => 3,
            'name' => 'Paul',
            'age' => 58,
            'sex' => 'M'
        ], $df->last());
    }

    public function testGoToLineSuccess()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $this->assertEquals([
            'id' => 2,
            'name' => 'Mary',
            'age' => 21,
            'sex' => 'F'
        ], $df->goToLine(1));
    }

    public function testGoToLineFails()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $this->expectException(OutOfBoundsException::class);
        $df->goToLine(9);
    }

    public function testGetCellSuccess()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $this->assertEquals('Mary', $df->getCell(1, 'name'));
    }

    public function testGetCellInvalidLineFails()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $this->expectException(OutOfBoundsException::class);
        $this->assertEquals('Mary', $df->getCell(9, 'name'));
    }


    public function testGetCellInvalidColFails()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $this->expectException(InvalidColumnException::class);
        $this->assertEquals('Mary', $df->getCell(1, 'unknow'));
    }


    public function testSetCellSuccess()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $this->assertInstanceOf(DataFrame::class, $df->setCell(1, 'name', 'maria'));
        $this->assertEquals('maria', $df->getCell(1, 'name'));
    }

    public function testSetCellInvalidLineFails()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $this->expectException(OutOfBoundsException::class);
        $this->assertEquals('Mary', $df->setCell(9, 'name', 'maria'));
    }


    public function testSetCellInvalidColFails()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $this->expectException(InvalidColumnException::class);
        $this->assertEquals('Mary', $df->setCell(1, 'unknow', 'maria'));
    }

    public function testLineExists()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $this->assertTrue($df->lineExists(2));
    }


    public function testLineNotExists()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $this->assertFalse($df->lineExists(9));
    }
}
