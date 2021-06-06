<?php
require '../lib/MySQLSessionHandler.php';
require '../models/tags.php';
require '../models/lang.php';
require '../models/country.php';
ini_set('display_errors', 'yes');
error_reporting(E_ALL);

use models\Tags;
use models\Lang;
use models\Country;

$sesHandler = new MySQLSessionHandler(new mysqli('localhost', \DB::$user, \DB::$password, \DB::$dbName));
$sesHandler->start();

$tags = new Tags();
$lang = new Lang();
$country = new Country();

$paramName = '';

if (!empty($_GET['name'])) {
    $paramName = $_GET['name'];
}

$paramTags = [];

if (!isset($_GET['tags'])) {
    $paramTags = $tags->restoreParams();
} elseif (is_array($_GET['tags'])) {
    foreach($_GET['tags'] as $tag) {
        $paramTags[] = (int) $tag;
    }
} elseif (is_string($_GET['tags'])) {
    if ($_GET['tags'] !== '') {
        $paramTags = $tags->find_tags_id($_GET['tags']);
    }
} else {
    die ('Wrong params');
}

$tags->saveTags($paramTags);

$lang_id = 0;

if (isset($_GET['lang'])) {
    $lang_id = (int)$_GET['lang'];
    $lang->saveParam($lang_id);
} else {
    $lang_id = $lang->restoreParam();
}

$country_id = 0;

if (isset($_GET['country'])) {
    $country_id = (int)$_GET['country'];
    $country->saveParam($country_id);
} else {
    $country_id = $country->restoreParam();
}

$result = [];

$result['tags'] = $tags->get($paramName, $lang_id, $country_id, $paramTags);
$result['products_count'] = $tags->getTotalProducts($paramTags);

echo json_encode($result);

session_write_close();