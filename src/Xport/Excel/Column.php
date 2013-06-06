<?php

namespace Xport\Excel;

/**
 * Column
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class Column
{

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $path;

    public function __construct($id, $label, $path)
    {
        $this->id = $id;
        $this->label = $label;
        $this->path = $path;
    }

}
