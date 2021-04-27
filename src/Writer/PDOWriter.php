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

use InvalidArgumentException;
use PDO;
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

     foreach ($this->df as $data){
         try{
             $this->stmt->execute($data);
             // @codeCoverageIgnoreStart
         } catch (PDOException $ex) {
                throw $ex;
                // @codeCoverageIgnoreEnd
         }
     }
    }

    /**
     * Cria uma tabela no padrão SQLITE a partir do data frame.
     *
     * @param DataFrame $df
     * @param PDO $pdo
     * @param string $tableName
     * @param string $primaryKey Nome do campo que será a chave primária (opcional)
     * @param bool $rowId TRUE para indicar se será gerada ROWID na tabela.
     * @return bool
     * @throws InvalidArgumentException
     *
     * @SuppressWarnings(CyclomaticComplexity)
     * @SuppressWarnings(BooleanArgumentFlag)
     */
    public static function createSQliteTable(
        DataFrame $df,
        PDO $pdo,
        string $tableName,
        string $primaryKey = '',
        bool $rowId = true
    ): bool {
        $colNames = $df->getColNames();
        $colTypes = $df->getColTypes();

        $dbTypes = [];
        foreach ($colTypes as $type) {
            switch ($type) {
                case 'string':
                    $dbTypes[] = 'TEXT';
                    break;
                case 'int':
                case 'integer':
                    $dbTypes[] = 'INTEGER';
                    break;
                case 'float':
                case 'double':
                    $dbTypes[] = 'REAL';
                    break;
                default:
//                    throw new InvalidArgumentException($type);
                    $dbTypes[] = 'TEXT';
            }
        }

        $fields = [];
        foreach ($colNames as $index => $name) {
            $primary = '';
            if ($name === $primaryKey) {
                $primary = ' PRIMARY KEY';
            }
            $fields[] = "$name {$dbTypes[$index]}$primary";
        }
        $fieldSpec = join(', ', $fields);

        $sql = "CREATE TABLE IF NOT EXISTS '$tableName' ($fieldSpec)";

        if ($rowId === false) {
            $sql .= " WITHOUT ROWID";
        }

        $sql .= ';';
        
        // @codeCoverageIgnoreStart
        if ($pdo->exec($sql) === false) {
            return false;
        }
        // @codeCoverageIgnoreEnd
        return true;
    }
}
