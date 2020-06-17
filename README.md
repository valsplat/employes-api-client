<p align="center">
    <h3 align="center">Employes Api Client for PHP</h3>
</p>

## Contributing

```
$ git clone git@github.com:valsplat/employes-api-client.git
$ cd employes-api-client
$ composer update -o
```

## Installation

```
$ composer require valsplat/employes-api-client
```

## Endpoints

The API does -unfortunately- not expose all data at this time. The available endpoints:

- `employee`
- `leave`

## Authentication

Authentication is done via a token:

```
$connection = new \Valsplat\Employes\Connection;
$connection->setAdministrationId('YOUR_ADMINISTRATION_GUID');
$connection->setBearerToken('YOUR_BEARER_TOKEN');
```

## Errors

The API client throws two exceptions:

* `\Valsplat\Employes\Exceptions\NotFoundException` - Entity could not be found
* `\Valsplat\Employes\Exceptions\ApiException` - Generic Api exception

## Basic Usage

Each endpoint is accessible via its own method on the `\Valsplat\Employes\EmployesApi` instance. The method name is singular, camelcased:

```
$employes = new \Valsplat\Employes\EmployesApi($connection);
$employes->employees();
```

## Generic methods & filters

* `list((array)params)` - get a collection of entities. Available parameters:
    * `limit` - limit the amount of entities returned
    * `offset` - set offset
* `get((int)id)` - get a single entity via its id.
* `create()` - create given entity.
* `update()` - update given entity.
* `save()` - convenience method; calls `update()` if the entity already exists, `create()` otherwise.
* `delete()` - delete given entity.

## Usage examples

Authentication and usage examples: [example.php](example.php)
