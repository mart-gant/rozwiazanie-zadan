<?php

require_once __DIR__ . '/src/Database.php';
require_once __DIR__ . '/src/InvoiceManager.php';

$database = new Database('mysql:host=localhost;dbname=invoice_management;charset=utf8mb4', 'root', '');
$manager = new InvoiceManager($database);

$manager->setupDatabase();
$manager->insertSampleData();

echo "--- Nadpłaty ---\n";
foreach ($manager->getOverpayments() as $row) {
    echo "Klient: {$row['name']}, Nadpłata: {$row['overpayment']}\n";
}
