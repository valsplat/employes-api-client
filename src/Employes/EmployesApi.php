<?php

namespace Valsplat\Employes;

class EmployesApi
{
    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function employee($attributes = []): Entities\Employee
    {
        return new Entities\Employee($this->connection, $attributes);
    }

    public function leave($attributes = []): Entities\Leave
    {
        return new Entities\Leave($this->connection, $attributes);
    }
}
