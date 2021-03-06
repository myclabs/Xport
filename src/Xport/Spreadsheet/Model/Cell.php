<?php

namespace Xport\Spreadsheet\Model;

/**
 * Cell
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class Cell implements SpreadsheetModel
{
    /**
     * @var mixed
     */
    private $content;

    /**
     * @param mixed|null $content
     */
    public function __construct($content = null)
    {
        $this->content = $content;
    }

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
