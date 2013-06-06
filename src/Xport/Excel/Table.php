<?php

namespace Xport\Excel;

/**
 * Table
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class Table
{

    /**
     * @var Line[]
     */
    private $lines =[];

    /**
     * @var Column[]
     */
    private $columns =[];

    /**
     * Cells indexed by their coordinate
     * @var Cell[]
     */
    private $cells = [];

}
