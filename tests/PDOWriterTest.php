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
use PDO;
use PHPUnit\Framework\TestCase;
use PTK\DataFrame\DataFrame;
use PTK\DataFrame\Reader\ArrayReader;
use PTK\DataFrame\Reader\PDOReader;
use PTK\DataFrame\Writer\PDOWriter;

/**
 * Description of PDOWriterTest
 *
 * @author Everton
 */
class PDOWriterTest extends TestCase
{
    use TestToolsTrait;

    public function testWriterSuccess()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);

        if (file_exists('tests/assets/cache/example.sqlite')) {
            unlink('tests/assets/cache/example.sqlite');
        }

        $pdo = new PDO("sqlite:tests/assets/cache/example.sqlite");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec('CREATE TABLE test (id INTEGER, name TEXT, age INTEGER, sex TEXT)');
        $stmt = $pdo->prepare('INSERT INTO test(id, name, age, sex) VALUES(:id, :name, :age, :sex)');

        $this->assertInstanceOf(PDOWriter::class, $writer = new PDOWriter($df, $stmt));
        $writer->write();

        $stmt = $pdo->query("SELECT id, name, age, sex FROM test ORDER BY id ASC");
        $reader = new PDOReader($stmt);
        $df = new DataFrame($reader);
        $this->assertEquals($this->arraySample, $df->getAsArray());
    }

    public function testCreateTableSuccess()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $df->appendCol('rent', [1.5, 2.6, 0.8]);

        if (file_exists('tests/assets/cache/example.sqlite')) {
            unlink('tests/assets/cache/example.sqlite');
        }

        $pdo = new PDO("sqlite:tests/assets/cache/example.sqlite");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->assertTrue(PDOWriter::createSQliteTable($df, $pdo, 'df', 'id', false));
        $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='df'");
        $this->assertEquals(1, $stmt->columnCount());
    }

    /*public function testCreateTableInvalidTypeFails()
    {
        $reader = new ArrayReader($this->arraySample);
        $df = new DataFrame($reader);
        $df->appendCol('itsFails', [null, null, null]);

        if (file_exists('tests/assets/cache/example.sqlite')) {
            unlink('tests/assets/cache/example.sqlite');
        }

        $pdo = new PDO("sqlite:tests/assets/cache/example.sqlite");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->expectException(InvalidArgumentException::class);
        PDOWriter::createSQliteTable($df, $pdo, 'df');
    }*/
}
