<?php
/**
 * API — Products & Categories JSON
 */
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../classes/Product.php';
require_once __DIR__ . '/../classes/Category.php';

$productModel = new Product();
$categoryModel = new Category();

$products = $productModel->getAll();
$categories = $categoryModel->getAll();

$response = [
    'products'   => array_map([$productModel, 'toApiFormat'], $products),
    'categories' => array_merge(
        [['key' => 'all', 'label' => 'الكل']],
        array_map([$categoryModel, 'toApiFormat'], $categories)
    )
];

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
