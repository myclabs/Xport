<?php

namespace Xport\Excel;

/**
 * Cell
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class Cell
{

    /**
     * @var mixed
     */
    private $content;

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

}
