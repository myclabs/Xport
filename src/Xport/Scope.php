<?php

namespace Xport;

/**
 * Scope.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class Scope
{
    /**
     * @var array
     */
    private $values = [];

    /**
     * Creates a new scope.
     *
     * @param Scope|null $scope If not null, extends the given scope
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
            throw new \InvalidArgumentException("Unknown entry for name '$name'");
        }

        return $this->values[$name];
    }
}
