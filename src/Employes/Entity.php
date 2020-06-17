<?php

namespace Valsplat\Employes;

abstract class Entity
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var array The model's attributes
     */
    protected $attributes = [];

    /**
     * @var array The model's fillable attributes
     */
    protected $fillable = [];

    /**
     * @var string The URL endpoint of this model
     */
    protected $endpoint = '';

    /**
     * @var string Name of the primary key for this model
     */
    protected $primaryKey = 'id';

    /**
     * @var string Namespace of the model (for POST and PATCH requests)
     */
    protected $namespace = '';

    /**
     * @var array
     */
    protected $singleNestedEntities = [];

    /**
     * Array containing the name of the attribute that contains nested objects as key and an array with the entity name
     * and json representation type.
     *
     * JSON representation of an array of objects (NESTING_TYPE_ARRAY_OF_OBJECTS) : [ {}, {} ]
     * JSON representation of nested objects (NESTING_TYPE_NESTED_OBJECTS): { "0": {}, "1": {} }
     *
     * @var array
     */
    protected $multipleNestedEntities = [];

    /**
     * Entity constructor.
     */
    public function __construct(Connection $connection, array $attributes = [])
    {
        $this->connection = $connection;
        $this->fill($attributes);
    }

    /**
     * Get the connection instance.
     *
     * @return Connection
     */
    public function connection()
    {
        return $this->connection;
    }

    /**
     * Get the model's attributes.
     *
     * @return array
     */
    public function attributes()
    {
        return $this->attributes;
    }

    /**
     * Get the model's fillable attributes.
     *
     * @return array
     */
    public function fillables()
    {
        return array_filter($this->attributes, function ($attribute) {
            return $this->isFillable($attribute);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Fill the entity from an array.
     */
    protected function fill(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }
    }

    /**
     * @param $key
     *
     * @return bool
     */
    protected function isFillable($key)
    {
        return in_array($key, $this->fillable);
    }

    /**
     * @param $key
     * @param $value
     */
    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        if (isset($this->attributes[$key])) {
            return $this->attributes[$key];
        }
    }

    /**
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
        if ($this->isFillable($key)) {
            return $this->setAttribute($key, $value);
        }
    }

    /**
     * @return bool
     */
    public function exists()
    {
        if (!array_key_exists($this->primaryKey, $this->attributes)) {
            return false;
        }

        return !empty($this->attributes[$this->primaryKey]);
    }

    /**
     * Create a new object with the response from the API.
     *
     * @param $response
     *
     * @return static
     */
    public function makeFromResponse($response)
    {
        $entity = new static($this->connection);
        $entity->selfFromResponse($response);

        return $entity;
    }

    /**
     * Recreate this object with the response from the API.
     *
     * @param $response
     *
     * @return $this
     */
    public function selfFromResponse($response)
    {
        $this->fill($response);

        return $this;
    }

    /**
     * @param $result
     *
     * @return array
     */
    public function collectionFromResult($result)
    {
        $collection = [];

        foreach ($result['data'] as $r) {
            $collection[] = $this->makeFromResponse($r);
        }

        return $collection;
    }

    /**
     * Make var_dump and print_r look pretty.
     *
     * @return array
     */
    public function __debugInfo()
    {
        return $this->attributes;
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * Determine if an attribute exists on the model.
     *
     * @param $name
     */
    public function __isset($name): bool
    {
        return isset($this->attributes[$name]);
    }

    /**
     * String representation of Entity.
     *
     * @return string fe. Entities\Deelname[id=1234]
     *
     * @author Bjorn
     */
    public function __toString(): string
    {
        $attributes = $this->attributes;
        $keyValues = array_map(
            function ($k) use ($attributes) {
                return sprintf('%s=%s', $k, $attributes[$k]);
            },
            array_keys($attributes)
        );

        return sprintf('%s[%s]', get_class($this), $keyValues[0]);
    }
}
