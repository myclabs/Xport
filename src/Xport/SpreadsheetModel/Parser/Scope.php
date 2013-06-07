<?php

namespace Xport\SpreadsheetModel\Parser;

/**
 * Scope.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class Scope implements \ArrayAccess
{
    /**
     * @var array
     */
    private $values = [];

    /**
     * Creates a new scope.
     *
     * @param \Xport\SpreadsheetModel\Parser\Scope|null $scope If not null, extends the given scope
     */
    public function __construct(Scope $scope = null)
    {
        if ($scope) {
            $this->values = $scope->values;
        }
    }

    /**
     * Bind a value to a name.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function bind($name, $value)
    {
        $this->values[$name] = $value;
    }

    /**
     * Returns a value by its name.
     *
     * @param string $name
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function get($name)
    {
        if (!array_key_exists($name, $this->values)) {
            var_dump($this->values);
            throw new \InvalidArgumentException("Unknown entry for name '$name'");
        }

        return $this->values[$name];
    }

    /**
     * Returns all the values in an array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->values;
    }

    /**
     * Magic get method.
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($name)
    {
        return array_key_exists($name, $this->values);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($name)
    {
        return $this->get($name);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($name, $value)
    {
        $this->bind($name, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($name)
    {
        if (!array_key_exists($name, $this->values)) {
            return;
        }

        unset($this->values[$name]);
    }
}
