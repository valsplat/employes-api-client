<?php

namespace Valsplat\Employes\Entities;

use Valsplat\Employes\Actions;
use Valsplat\Employes\Entity;

class Employee extends Entity
{
    use Actions\Getable;
    use Actions\Listable;

    public function getEndpoint(): string
    {
        return $this->connection()->getAdministrationId().'/employees/';
    }
}
