<?php

namespace Valsplat\Employes\Actions;

trait Getable
{
    /**
     * @return mixed
     */
    public function get(string $id)
    {
        $result = $this->connection()->get($this->getEndpoint().urlencode($id));

        return $this->makeFromResponse($result);
    }
}
