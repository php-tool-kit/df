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

/**
 * Ferramentas para os testes.
 *
 * @author Everton
 */
trait TestToolsTrait
{
    protected array $arraySample = [
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
        ]
    ];
    protected array $arraySampleInvalid = [
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
            'age' => 58
        ],
    ];

    protected array $otherSample = [
        [
            'score' => 50
        ],
        [
            'score' => 35
        ],
        [
            'score' => 98
        ],
    ];

    protected array $anExample = [
        [
            'good' => 0
        ],
        [
            'good' => -1
        ],
        [
            'good' => 1
        ],
    ];
    protected array $duplicatedSample = [
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
            'name' => 'Mary',
            'age' => 21,
            'sex' => 'F'
        ]
    ];
}
