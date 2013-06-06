<?php

namespace Xport\Excel;

/**
 * Sheet
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class Sheet
{

    /**
     * @var Table[]
     */
    private $tables = [];

    /**
     * @param Table $table
     */
    public function addTable(Table $table)
    {
        $this->tables[] = $table;
    }

    /**
     * @return Table[]
     */
    public function getTables()
    {
        return $this->tables;
    }

}
