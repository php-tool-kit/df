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

namespace PTK\DataFrame\Writer;

use PDOException;
use PDOStatement;
use PTK\DataFrame\DataFrame;

/**
 * Escreve o data frame em um banco de dados relacional utilizando PDO.
 *
 * @author Everton
 * 
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class PDOWriter implements WriterInterface
{
    private DataFrame $df;
    private PDOStatement $stmt;

    /**
     *
     * Recomenda-se setar ```$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);```
     *
     * @param DataFrame $df O data frame para escrever no banco de dados.
     * @param PDOStatement $stmt Uma instância do PDOStatement do tipo:
     *
     * INSERT INTO table_name (field_name1, field_nameN) VALUES(:df_col_name1, :df_col_nameN)
     */
    public function __construct(DataFrame $df, PDOStatement $stmt)
    {
        $this->df = $df;
        $this->stmt = $stmt;
    }

    /**
     * Escreve os dados do data frame no banco de dados.
     *
     * Utiliza o método PDOStatement::execute($df->getAsArray())
     * @return void
     * @throws PDOException
     */
    public function write(): void
    {
        $paramNames = [];
        foreach ($this->df->getColNames() as $colName) {
            $paramNames[] = ":$colName";
        }
        $this->df->setColNames(...$paramNames);

        $data = $this->df->current();
        while ($data !== false) {
//            print_r($data);
            try {
                $this->stmt->execute($data);
                // @codeCoverageIgnoreStart
            } catch (PDOException $ex) {
                throw $ex;
                // @codeCoverageIgnoreEnd
            }
            $data = $this->df->next();
        }
    }
}
