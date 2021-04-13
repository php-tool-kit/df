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

namespace PTK\DataFrame;

use Exception;
use InvalidArgumentException;
use LengthException;
use OutOfBoundsException;
use PTK\DataFrame\Exception\InvalidColumnException;
use PTK\DataFrame\Exception\InvalidDataFrameException;
use PTK\DataFrame\Reader\ArrayReader;
use PTK\DataFrame\Reader\EmptyDataFrameReader;
use PTK\DataFrame\Reader\ReaderInterface;
use RangeException;

use function array_key_first;
use function sizeof;

class DataFrame
{

    /**
     *
     * @var array<mixed>
     */
    private array $df = [];

    /**
     *
     * @param ReaderInterface $reader
     */
    public function __construct(ReaderInterface $reader)
    {
        $df = $reader->read();

        if (!($reader instanceof EmptyDataFrameReader)) {
            $this->validateStructure($df);
        }

        $this->df = $df;
    }

    /**
     *
     * @return array<mixed> Retorna um array com os dados.
     */
    public function getAsArray(): array
    {
        return $this->df;
    }

    /**
     * Valida a estrutura interna do data frame.
     *
     * @param array<mixed> $df
     *
     * @return bool
     * @throws InvalidDataFrameException
     */
    protected function validateStructure(array $df): bool
    {
        $colNames = array_keys($df[array_key_first($df)]);

        $invalidLines = [];
        foreach ($df as $index => $line) {
            if (array_keys($line) !== $colNames) {
                $invalidLines[$index] = $line;
            }
        }

        if (sizeof($invalidLines) > 0) {
            throw new InvalidDataFrameException($invalidLines);
        }

        reset($df);

        return true;
    }

    /**
     * Detecta os tipos de dados existentes na coluna especificada.
     *
     * @param string $colName O nome da coluna para detectar o tipo.
     * @param int|null $lines Número d elinhas a considerar. Se omitido, considera todas.
     * @return array<int> Retorna um array onde a chave é o tipo encontrado e o valor é a quantidade
     *  de linhas com aquele tipo de dados.
     * @throws InvalidArgumentException
     */
    public function detectColTypes(string $colName, ?int $lines = null): array
    {
        if (!$this->colExists($colName)) {
            throw new InvalidColumnException($colName);
        }


        if (is_null($lines)) {
            $lines = sizeof($this->df);
        }

        if ($lines <= 0) {
            throw new InvalidArgumentException("$lines");
        }

        $result = [];
        $counter = 0;
        foreach ($this->df as $line) {
            $colType = gettype($line[$colName]);

            if (!key_exists($colType, $result)) {
                $result[$colType] = 0;
            }

            $result[$colType]++;

            $counter++;
            if ($counter >= $lines) {
                break;
            }
        }
        arsort($result, SORT_REGULAR);
        return $result;
    }

    /**
     * Identifica os tipos predominantes de cada coluna.
     *
     * Usa o método DataFrame::detectColTypes() e considera predominante o tipo com mais linhas para cada coluna.
     *
     * @return array<mixed> Retorna um array onde o nome da coluna é a chave e o tipo predominante é o valor.
     */
    public function getColTypes(): array
    {
        $colNames = $this->getColNames();
        $result = [];
        foreach ($colNames as $name) {
            $colTypes = $this->detectColTypes($name);
            $result[$name] = array_key_first($colTypes);
        }
        return $result;
    }

    /**
     * Verifica se a coluna existe no data frame.
     *
     * @param string $colName
     * @return bool
     */
    public function colExists(string $colName): bool
    {
        return in_array($colName, $this->getColNames());
    }

    /**
     * Retorna o nome das colunas.
     *
     * @return array<mixed>
     */
    public function getColNames(): array
    {
        $current = $this->current();
        // @codeCoverageIgnoreStart
        if ($current === false) {
            throw new RangeException();
        }
        // @codeCoverageIgnoreEnd
        return array_keys($current);
    }

    /**
     * Um data frame com as colunas especificadas.
     *
     * @param DataFrame $df
     * @param string $colName Lista com os nomes das colunas desejadas.
     * @return DataFrame Retorna um novo data frame com as colunas selecionadas.
     */
    public static function getCols(DataFrame $df, string ...$colName): DataFrame
    {
        $actual = $df->getAsArray();
        $filtered = [];

        foreach ($colName as $name) {
            if (!$df->colExists($name)) {
                throw new InvalidColumnException($name);
            }
        }

        foreach ($actual as $index => $line) {
            foreach ($colName as $name) {
                $filtered[$index][$name] = $line[$name];
            }
        }

        $reader = new ArrayReader($filtered);

        return new DataFrame($reader);
    }

    /**
     * Retorna um data frame com o conjunto de linhas especificado.
     *
     * O índice das linhas não é modificado no data frame resultante. Se quiser reindexar, use
     * DataFrame::reindex().
     *
     * @param DataFrame $df
     * @param int $line Uma lista com as linhas desejadas.
     * @return DataFrame Retorna um novo data frame com as linhas selecionadas.
     */
    public static function getLines(DataFrame $df, int ...$line): DataFrame
    {
        $actual = $df->getAsArray();
        $filtered = [];

        //creio não ser necessário testar as linhas. se uma linha for fornecida mas ela não existir,
        // apenas ela não será retornada.
//        foreach ($line as $index) {
//            if (!key_exists($index, $actual)) {
//                throw new InvalidArgumentException("$index");
//            }
//        }

        foreach ($line as $index) {
            if (key_exists($index, $actual)) {
                $filtered[$index] = $actual[$index];
            }
        }

        $reader = new ArrayReader($filtered);

        if ($filtered === []) {
            $reader = new EmptyDataFrameReader();
        }

        return new DataFrame($reader);
    }

    /**
     * Reindexa as linhas do data frame.
     *
     * @return DataFrame Retorna o data frame atual.
     */
    public function reindex(): DataFrame
    {
        $this->df = array_values($this->df);
        return $this;
    }

    /**
     * Retorna um novo data frame com as linhas entre $firstLine e $lastLine, inclusive.
     *
     * É preciso que $firstLine seja maior ou igual a $lastLine.
     *
     * O índice das linhas não é modificado no data frame resultante. Se quiser reindexar, use
     * DataFrame::reindex().
     *
     * @param DataFrame $df
     * @param int|null $firstLine Primeira linha. Se nulo, a primeira linha do data frame original é usada.
     * @param int|null $lastLine Última linha. Se nulo, a última linha do data frame original é usada.
     * @return DataFrame Retorna um novo data frame com as linhas selecionadas.
     */
    public static function getLinesByRange(DataFrame $df, ?int $firstLine = null, ?int $lastLine = null): DataFrame
    {
        if ($firstLine > $lastLine) {
            throw new InvalidArgumentException("$firstLine:$lastLine");
        }
        $lines = [];
        for ($i = $firstLine; $i <= $lastLine; $i++) {
            $lines[] = (int) $i;
        }

        // @codeCoverageIgnoreStart
        if (sizeof($lines) === 0) {
            throw new InvalidArgumentException("$firstLine:$lastLine");
        }
        // @codeCoverageIgnoreEnd
        return self::getLines($df, ...$lines);
    }

    /**
     * Mescla o data frame atual com as colunas dos data frames fornecidos.
     *
     * Os data frames fornecidos precisam ter a mesma quantidade de linhas que o data frame atual.
     *
     * @param DataFrame $df
     * @return DataFrame Retorna o data frame atual.
     */
    public function mergeCols(DataFrame ...$df): DataFrame
    {
        foreach ($df as $index => $dfn) {
            if ($this->countLines() !== $dfn->countLines()) {
                throw new InvalidDataFrameException([
                    0 => $this->countLines(),
                    $index => $dfn->countLines()
                ]);
            }
        }

        $this->reindex();

        foreach ($df as $dfn) {
            $dfn->reindex();
            foreach ($dfn->getAsArray() as $index => $line) {
                $this->df[$index] = array_merge($this->df[$index], $line);
            }
        }

        $this->validateStructure($this->df);

        return $this;
    }

    /**
     * Mescla o data frame atual acrescentando as linhas dos data frames fornecidos.
     *
     * Os data frames fornecidos precisam ter as mesmas colunas, inclusive quanto aos nomes, do
     * data frame atual.
     *
     * Ao final, o data frame será reindexado.
     *
     * @param DataFrame $df
     * @return DataFrame Retorna o data frame atual.
     */
    public function mergeLines(DataFrame ...$df): DataFrame
    {
        $colNames = $this->getColNames();

        foreach ($df as $dfn) {
            if ($colNames !== $dfn->getColNames()) {
                throw new InvalidColumnException(join(", ", $dfn->getColNames()));
            }
        }

        foreach ($df as $dfn) {
            $this->df = array_merge($this->df, $dfn->getAsArray());
        }

        return $this;
    }

    /**
     * Remove as linhas especificadas do data frame atual.
     *
     * Se uma linha inexistente for fornecida, nenha mensagem de erro será fornecida.
     *
     * @param int $lines Lista com as linhas para serem removidas.
     * @return DataFrame Retorna o data frame atual.
     */
    public function removeLines(int ...$lines): DataFrame
    {
        foreach ($lines as $index) {
            unset($this->df[$index]);
        }

        return $this;
    }

    /**
     * Remove as colunas especificadas do data frame atual.
     *
     * @param string $colName Lista com as colunas para remover.
     * @return DataFrame Retorna o data frame atual.
     */
    public function removeCols(string ...$colName): DataFrame
    {
        foreach ($colName as $index => $name) {
            if (!$this->colExists($name)) {
                throw new InvalidColumnException($name);
            }
        }

        foreach ($this->df as $index => $line) {
            foreach ($colName as $name) {
                unset($this->df[$index][$name]);
            }
        }

        return $this;
    }

    /**
     * Ordena o data frame atual.
     *
     * @param array<mixed> $order Os critérios de ordenação, onde a chave do array corresponde a coluna
     *  e o valor de cada entrada à direção de ordenação (asc|ASC ou desc|DESC).
     * @return DataFrame Retorna o data frame atual.
     */
    public function sort(array $order): DataFrame
    {
        foreach (array_keys($order) as $colName) {
            if (!$this->colExists($colName)) {
                throw new InvalidColumnException($colName);
            }
        }

        $args = [];
        foreach ($order as $colName => $orderBy) {
            $tmp = [];
            foreach ($this->df as $index => $line) {
                $tmp[$index] = $line[$colName];
            }

            $args[] = $tmp;
            switch ($orderBy) {
                case 'asc':
                case 'ASC':
                    $args[] = SORT_ASC;
                    break;
                case 'desc':
                case 'DESC':
                    $args[] = SORT_DESC;
                    break;
                default:
                    throw new InvalidArgumentException($orderBy);
            }
        }

        $args[] = &$this->df;

        call_user_func_array('array_multisort', $args);

        $return = array_pop($args);
        //@codeCoverageIgnoreStart
        if (!is_array($return)) {
            throw new Exception();
        }
        //@codeCoverageIgnoreEnd

//        $this->df = $result;

        return $this;
    }

    /**
     * Filtra os dados do data frame atual retornando-os num novo data frame.
     *
     * @param DataFrame $df
     * @param callable $filter Uma função de filtro que será aplicada em cada linha do data frame atual.
     *  Ela deve retornar TRUE caso a linha deva ser incluída no novo data frame ou FALSE se não.
     * @return DataFrame Retorna um novo data frame com os dados filtrados.
     */
    public static function filter(DataFrame $df, callable $filter): DataFrame
    {
        $result = [];

        foreach ($df->getAsArray() as $index => $line) {
            if ($filter($line) === true) {
                $result[$index] = $line;
            }
        }

        $reader = new ArrayReader($result);

        if ($result === []) {
            $reader = new EmptyDataFrameReader();
        }
        return new DataFrame($reader);
    }

    /**
     * Retorna um array com os índices das linhas selecionadas por $filter.
     *
     * Difere de DataFrame::filter() porque retorna não um novo data frame, mas os índices das linhas
     *  filtradas.
     *
     * Isso é útil para, por exemplo, excluir linhas com base em determinados critérios.
     *
     * @param callable $filter Uma função de filtro que será aplicada em cada linha do data frame atual.
     *  Ela deve retornar TRUE se a linha deva ser incluída no novo data frame ou TRUE se não.
     * @return array<int>
     */
    public function seek(callable $filter): array
    {
        $result = [];

        foreach ($this->df as $index => $line) {
            if ($filter($line) === true) {
                $result[] = $index;
            }
        }

        return $result;
    }

    /**
     * Retorna um array com os índices das linhas duplicadas.
     *
     * A comparação é feita apenas sobre os valores das colunas de $colName.
     *
     * @param string $colName Uma lista das colunas a considerar na comparação dos valores.
     * @return array<int>
     */
    public function getDuplicatedLines(string ...$colName): array
    {
        foreach ($colName as $name) {
            if (!$this->colExists($name)) {
                throw new InvalidColumnException($name);
            }
        }

        $hashTable = [];
        foreach ($this->df as $index => $line) {
            $hash = '';
            foreach ($colName as $name) {
                $value = $line[$name];
                $hash .= (string) $value;
            }
            $hashTable[$index] = $hash;
        }

        $countHash = array_count_values($hashTable);

        $duplicatedHashes = array_filter($countHash, function ($counter) {
            if ($counter > 1) {
                return true;
            }
            return false;
        });

        $result = [];
        foreach ($duplicatedHashes as $hash => $counter) {
            $search = array_keys($hashTable, $hash, true);
            if ($search !== false) {
                $result = array_merge($result, $search);
            }
        }

        return $result;
    }

    /**
     * Aplica uma função em todas as linhas.
     *
     * @param callable $callable Uma função que recebe cada uma das linhas (array) e deve devolver a
     *  linha processada no formato array.
     *
     * @return DataFrame Retorna o data frame atual.
     */
    public function applyOnLines(callable $callable): DataFrame
    {
        foreach ($this->df as $index => $line) {
            $this->df[$index] = $callable($line);
        }

        return $this;
    }

    /**
     * Aplica uma função sobre cada coluna, linha a linha.
     *
     * @param string $colName Nome da coluna sobre a qual a função será executada.
     * @param callable $callable Uma função que recebe cada uma das células da coluna especificada e
     *  deve devolver um valor para substituir na célula original.
     * @return DataFrame Retorna o data frame atual.
     */
    public function applyOnCols(string $colName, callable $callable): DataFrame
    {
        if (!$this->colExists($colName)) {
            throw new InvalidColumnException($colName);
        }

        foreach ($this->df as $index => $line) {
            $this->df[$index][$colName] = $callable($line[$colName]);
        }

        return $this;
    }

    /**
     * Soma os valores de todas as linhas da coluna.
     *
     * @param string $colName
     * @return number Retorna o valor da soma.
     */
    public function sumCol(string $colName)
    {
        if (!$this->colExists($colName)) {
            throw new InvalidColumnException($colName);
        }

        $sum = 0;
        foreach ($this->df as $line) {
            $sum += $line[$colName];
        }

        return $sum;
    }

    /**
     * Soma, linha a linha, os valores das colunas especificadas.
     *
     * @param string $colName Lista de colunas para somar.
     * @return array<number> Retorna um array com o número de cada linha como chave e o
     *  resultado da soma como valor.
     */
    public function sumLines(string ...$colName): array
    {
        $result = [];
        foreach ($this->df as $index => $line) {
            $sum = 0;
            foreach ($colName as $name) {
                if (!$this->colExists($name)) {
                    throw new InvalidColumnException($name);
                }
                $sum += $line[$name];
            }
            $result[$index] = $sum;
        }

        return $result;
    }

    /**
     * Altera o nome de todas as colunas.
     *
     * @param string $newColName Lista com os novos nomes de colunas.
     * @return DataFrame Retorna o data frame atual.
     */
    public function setColNames(string ...$newColName): DataFrame
    {
        if (sizeof($this->getColNames()) !== sizeof($newColName)) {
            throw new LengthException((string) sizeof($newColName));
        }

        $tmp = [];
        foreach ($this->getColNames() as $index => $colName) {
            $tmp[$colName] = $newColName[$index];
        }
        foreach ($this->df as $index => $line) {
            $newLine = [];
            foreach ($tmp as $old => $new) {
                $newLine[$new]  = $line[$old];
            }
            $this->df[$index] = $newLine;
        }
        return $this;
    }

    /**
     * Altera no nome de uma coluna específica.
     *
     * @param string $oldColName
     * @param string $newColName
     * @return DataFrame Retorna o data frame atual.
     */
    public function changeColName(string $oldColName, string $newColName): DataFrame
    {
        if (!$this->colExists($oldColName)) {
            throw new InvalidColumnException($oldColName);
        }

        $new = $this->getColNames();
        foreach ($new as $index => $colName) {
            if ($colName === $oldColName) {
                $new[$index] = $newColName;
            }
        }

        $this->setColNames(...$new);

        return $this;
    }

    /**
     * Acrescenta uma nova coluna.
     *
     * Se o número de linhas de $data deve ser igual ao número de linhas do data frame.
     *
     * Se $colName contém o nome de uma coluna já existente, ela será substituída.
     *
     * Se o data frame atual tiver índices de linha inexistentes em $data, ou vice-versa,
     * uma exceção é disparada.
     *
     * @param string $colName
     * @param array<mixed> $data Um array com os dados da nova coluna.
     * @return DataFrame Retorna o data frame atual.
     */
    public function appendCol(string $colName, array $data = []): DataFrame
    {
        if ($this->countLines() !== sizeof($data)) {
            throw new LengthException((string) sizeof($data));
        }
        $counter = sizeof($data);//necessário para controlar se alguma chave de $data não foi importada.
        foreach ($this->df as $index => $line) {
            if (!key_exists($index, $data)) {
                throw new OutOfBoundsException($index);
            }
            $this->df[$index][$colName] = $data[$index];
            $counter--;
        }
        // @codeCoverageIgnoreStart
        // Não vejo um modo de testar isso, já que, invariavelmente vai cair numa das exceções prévias
        if ($counter !== 0) {//se $data tinha chaves inexistentes no data frame atual, vai sobrar valor no counter
            throw new LengthException("$counter");
        }
        // @codeCoverageIgnoreEnd

        return $this;
    }

    /**
     * Conta o número de linhas.
     *
     * @return int
     */
    public function countLines(): int
    {
        return sizeof($this->df);
    }

    /**
     * Busca a linha atual.
     *
     * A linha atual é aquela para qual o ponteiro do data frame está apontando, sem movê-lo.
     *
     * @return false|array<mixed> Retorna a linha atual ou false se não houverem mais linhas para retornar.
     */
    public function current()
    {
        return current($this->df);
    }

    /**
     * Avança o ponteiro do data frame em uma linha e retorna ela.
     *
     * @return false|array<mixed> Retorna a linha atual ou false se não houverem mais linhas para retornar.
     */
    public function next()
    {
        return next($this->df);
    }

    /**
     * Retrocede o ponteiro do data frame uma linha e retorna ela.
     *
     * @return false|array<mixed> Retorna a linha atual ou false se não houverem mais linhas para retornar.
     */
    public function previous()
    {
        return prev($this->df);
    }

    /**
     * Coloca o ponteiro do data frame na primeira linha e retorna ela.
     *
     * @return false|array<mixed> Retorna a linha atual ou false no caso de array vazio.
     */
    public function first()
    {
        reset($this->df);
        return current($this->df);
    }

    /**
     * Coloca o ponteiro na última linha do data frame e retorna ela.
     *
     * @return false|array<mixed> Retorna a linha atual ou false no caso de array vazio.
     */
    public function last()
    {
        return end($this->df);
    }

    /**
     * Coloca o ponteiro do data frame na linha especificada e retorna ela.
     *
     * @param int $line
     * @return array<mixed>
     */
    public function goToLine(int $line): array
    {
        if (!key_exists($line, $this->df)) {
            throw new OutOfBoundsException("$line");
        }

        reset($this->df);
        foreach ($this->df as $index => $l) {
            if ($index === $line) {
                return $l;
            }
        }

        // @codeCoverageIgnoreStart
        // em nenhuma situação vai chegar nesta linha
        return [];
        // @codeCoverageIgnoreEnd
    }

    /**
     * Busca o valor de uma célula específica.
     *
     * @param int $line
     * @param string $colName
     * @return mixed
     */
    public function getCell(int $line, string $colName)
    {
        if (!key_exists($line, $this->df)) {
            throw new OutOfBoundsException("$line");
        }

        if (!$this->colExists($colName)) {
            throw new InvalidColumnException($colName);
        }

        return $this->df[$line][$colName];
    }

    /**
     * Define o valor de uma célula específica.
     *
     * @param int $line
     * @param string $colName
     * @param mixed $newValue
     * @return DataFrame Retorna o data frame atual.
     */
    public function setCell(int $line, string $colName, $newValue): DataFrame
    {
        if (!key_exists($line, $this->df)) {
            throw new OutOfBoundsException("$line");
        }

        if (!$this->colExists($colName)) {
            throw new InvalidColumnException($colName);
        }
        $this->df[$line][$colName] = $newValue;
        return $this;
    }

    /**
     * Verifica a existência de determinada linha pelo seu índice.
     *
     * @param int $line
     * @return bool
     */
    public function lineExists(int $line): bool
    {
        return key_exists($line, $this->df);
    }
    
    /**
     * Copia os dados de um data frame.
     * 
     * @param DataFrame $df
     * @return DataFrame Retorna um dovo data frame com os dados do original.
     */
    public static function copy(DataFrame $df): DataFrame
    {
        $reader = new ArrayReader($df->getAsArray());
        return new DataFrame($reader);
    }
}
