<?php
require '../models/products.php';
ini_set('display_errors', 'yes');
error_reporting(E_ALL);

use models;
use models\Products;

$products = new Products();

$paramName = '';

if (!empty($_GET['name'])) {
    $paramName = $_GET['name'];
}

$paramTags = [];

if (!empty($_GET['tags'])) {
    foreach(explode(',', $_GET['tags']) as $tag) {
        $paramTags[] = (int) $tag;
    }
} else {
    echo json_encode([]);
    die ();
}

if (isset($_GET['price_from'])) {
    $priceFrom = (float) $_GET['price_from'];
} else {
    $priceFrom = 0;
}

if (isset($_GET['price_to'])) {
    $priceTo = (float) $_GET['price_to'];
} else {
    $priceTo = PHP_FLOAT_MAX;
}

if (0.0 === $priceTo) {
    $priceTo = PHP_FLOAT_MAX;
}

$result = $products->get($paramTags, $priceFrom, $priceTo);

echo json_encode($result);