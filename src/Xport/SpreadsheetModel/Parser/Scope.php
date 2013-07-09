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
     * @var mixed[]
     */
    private $values = [];
    /**
     * @var callable[]
     */
    private $functions = [];

    /**
     * Creates a new scope.
     *
     * @param \Xport\SpreadsheetModel\Parser\Scope|null $scope If not null, extends the given scope
     */
    public function __construct(Scope $scope = null)
    {
        if ($scope) {
            $this->values = $scope->values;
            $this->functions = $scope->functions;
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
     * Bind a function to a name.
     *
     * @param string   $name
     * @param callable $function
     */
    public function bindFunction($name, callable $function)
    {
        $this->functions[$name] = $function;
    }

    /**
     * Returns a value by its name.
     *
     * @param string $name
     * @throws \InvalidArgumentException
     * @return mixed
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
     * Returns all the values in an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->values;
    }

    /**
     * Returns a function by its name.
     *
     * @param string $name
     * @throws \InvalidArgumentException
     * @return callable
     */
    public function getFunction($name)
    {
        if (!array_key_exists($name, $this->functions)) {
            var_dump($this->functions);
            throw new \InvalidArgumentException("Unknown function '$name'");
        }

        return $this->functions[$name];
    }

    /**
     * Returns all the functions.
     *
     * @return callable[]
     */
    public function getFunctions()
    {
        return $this->functions;
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
