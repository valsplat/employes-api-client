<?php

use Valsplat\Employes\Connection;
use Valsplat\Employes\EmployesApi;

require 'vendor/autoload.php';

$connection = new Connection();
$connection->setAdministrationId('YOUR_ADMINISTRATION_GUID');
$connection->setBearerToken('YOUR_BEARER_TOKEN');

$employes = new EmployesApi($connection);

// List employees
$employees = $employes->employee()->list();
var_dump($employees);

// Get a single employee
$employee = $employes->employee()->get('EMPLOYEE_GUID');
var_dump($employee);
