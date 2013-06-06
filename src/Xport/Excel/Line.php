<?php

namespace Xport\Excel;

/**
 * Line
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class Line
{

    /**
     * @var string
     */
    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

}
