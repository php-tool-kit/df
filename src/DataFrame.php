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

use PTK\DataFrame\Exception\InvalidDataFrameException;
use PTK\DataFrame\Reader\ReaderInterface;

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

        $this->validateStructure($df);

        $this->df = $reader->read();
    }

    /**
     *
     * @return array<mixed> Retorna um array com os dados.
     */
    public function getAsArray(): array
    {
    }

    /**
     * Valida a estrutura interna do data frame.
     *
     * @para array<mixed> $df
     *
     * @return bool
     * @throws InvalidDataFrameException
     */
    protected function validateStructure(array $df): bool
    {
        throw new InvalidDataFrameException();
    }

    /**
     * Retorna o nome das colunas.
     *
     * @return array<string>
     */
    public function getColNames(): array
    {
    }

    /**
     * Um data frame com as colunas especificadas.
     *
     * @param string $colName Lista com os nomes das colunas desejadas.
     * @return DataFrame Retorna um novo data frame com as colunas selecionadas.
     */
    public function getCols(string ...$colName): DataFrame
    {
    }

    /**
     * Retorna um data frame com o conjunto de linhas especificado.
     *
     * @param int $line Uma lista com as linhas desejadas.
     * @return DataFrame Retorna um novo data frame com as linhas selecionadas.
     */
    public function getLines(int $line): DataFrame
    {
    }

    /**
     * Retorna um novo data frame com as linhas entre $firstLine e $lastLine, inclusive.
     *
     * É preciso que $firstLine seja maior ou igual a $lastLine.
     *
     * @param int|null $firstLine Primeira linha. Se nulo, a primeira linha do data frame original é usada.
     * @param int|null $lastLine Última linha. Se nulo, a última linha do data frame original é usada.
     * @return DataFrame Retorna um novo data frame com as linhas selecionadas.
     */
    public function getLinesByRange(?int $firstLine = null, ?int $lastLine = null): DataFrame
    {
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
    }

    /**
     * Mescla o data frame atual acrescentando as linhas dos data frames fornecidos.
     *
     * Os data frames fornecidos precisam ter as mesmas colunas, inclusive quanto aos nomes, do
     * data frame atual.
     *
     * @param DataFrame $df
     * @return DataFrame Retorna o data frame atual.
     */
    public function mergeLines(DataFrame ...$df): DataFrame
    {
    }

    /**
     * Remove as linhas especificadas do data frame atual.
     *
     * @param int $lines Lista com as linhas para serem removidas.
     * @return DataFrame Retorna o data frame atual.
     */
    public function removeLines(int ...$lines): DataFrame
    {
    }

    /**
     * Remove as colunas especificadas do data frame atual.
     *
     * @param string $colName Lista com as colunas para remover.
     * @return DataFrame Retorna o data frame atual.
     */
    public function removeCols(string ...$colName): DataFrame
    {
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
    }

    /**
     * Filtra os dados do data frame atual retornando-os num novo data frame..
     *
     * @param callable $filter Uma função de filtro que será aplicada em cada linha do data frame atual.
     *  Ela deve retornar TRUE se a linha deva ser incluída no novo data frame ou FALSE se não.
     * @param bool $reindexLines Se TRUE (padrão), as linhas terão seus índices reindexados a partir
     * do ZERO.
     * @return DataFrame Retorna um novo data frame com so dados filtrados.
     */
    public function filter(callable $filter, bool $reindexLines = true): DataFrame
    {
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
    }

    /**
     * Aplica uma função em todas as linhas.
     *
     * @param callable $mapFunction Uma função que recebe cada uma das linhas (array) e deve devolver a
     *  linha processada no formato array.
     *
     * @return DataFrame Retorna o data frame atual.
     */
    public function mapLines(callable $mapFunction): DataFrame
    {
    }

    /**
     * Soma os valores de todas as linhas da coluna.
     *
     * @param string $colName
     * @return number Retorna o valor da soma.
     */
    public function sumCol(string $colName)
    {
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
    }

    /**
     * Altera o nome de todas as colunas.
     *
     * @param array<string> $newNames Array com os novos nomes de colunas.
     * @return DataFrame Retorna o data frame atual.
     */
    public function setColNames(array $newNames): DataFrame
    {
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
    }

    /**
     * Acrescenta uma nova coluna.
     *
     * Se o número de linhas de $data for diferente do número de linhas do data frame original, os
     * valores faltantes serão NULL.
     *
     * @param string $colName
     * @param array $data Um array com os dados da nova coluna.
     * @return DataFrame Retorna o data frame atual.
     */
    public function appendCol(string $colName, array $data = []): DataFrame
    {
    }

    /**
     * Conta o número de linhas.
     *
     * @return int
     */
    public function coutLines(): int
    {
    }
}
