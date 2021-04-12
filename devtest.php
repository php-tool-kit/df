<?php

use PTK\DataFrame\DataFrame;
use PTK\DataFrame\Exception\InvalidDataFrameException;
use PTK\DataFrame\Reader\ArrayReader;

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

/**
 * Arquivo para testes durante o desenvolvimento.
 */

echo gettype(fopen('tests/assets/example.csv', 'r'));exit();
require 'vendor/autoload.php';
try {
    $reader = new ArrayReader(
            [
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
        ]
            ]
    );

    $df = new DataFrame($reader);

    $df->setColNames('id', 'nome', 'age', 'sexo');
//    $df->changeColName('name', 'nome');
            
    print_r($df->getColNames());
}
catch (InvalidDataFrameException $ex){
    echo $ex->getTraceAsString();
    print_r($ex->getInvalidLines());
}
catch (Exception $ex) {
    echo $ex->getTraceAsString();
}