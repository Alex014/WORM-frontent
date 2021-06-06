<?php
require '../models/lang.php';
ini_set('display_errors', 'yes');
error_reporting(E_ALL);

use models;
use models\Lang;

$lang = new Lang();

json_encode($lang->get());