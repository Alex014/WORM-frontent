<?php
require '../models/country.php';
ini_set('display_errors', 'yes');
error_reporting(E_ALL);

use models;
use models\Country;

$country = new Country();

json_encode($country->get()); 