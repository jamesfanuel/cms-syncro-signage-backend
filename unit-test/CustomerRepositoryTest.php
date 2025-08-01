<?php

require_once __DIR__ . '/../models/Customer.php';
require_once __DIR__ . '/../repository/CustomerRepository.php';

$host = "localhost";
$database   = "syncro_signage_db";
$username = "root";
$password = "rahasia123"; // Gunakan DB khusus testing agar data asli tidak terganggu

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$repo = new CustomerRepository($conn);

// Reset tabel
$conn->query("DELETE FROM ds_customer");

// Seed
$repo->create([
    'customer_name' => 'Test Customer',
    'email' => 'test@unit.local',
    'licence_date' => date('Y-m-d H:i:s'),
    'created_by' => 'unittest'
]);

function assertEqual($expected, $actual, $message)
{
    if ($expected === $actual) {
        echo "✅ $message\n";
    } else {
        echo "❌ $message (Expected: $expected, Got: $actual)\n";
    }
}

function assertTrue($value, $message)
{
    if ($value) {
        echo "✅ $message\n";
    } else {
        echo "❌ $message (Expected: true, Got: false)\n";
    }
}

function assertNull($value, $message)
{
    if ($value === null) {
        echo "✅ $message\n";
    } else {
        echo "❌ $message (Expected: null, Got: " . print_r($value, true) . ")\n";
    }
}

// 🔸 Test getAll
$all = $repo->getAll();
assertTrue(count($all) > 0, 'getAll should return at least 1 customer');

// 🔸 Test findById
$id = $all[0]['customer_id'];
$found = $repo->findById($id);
assertEqual('Test Customer', $found['customer_name'], 'findById should return correct name');

// 🔸 Test update
$repo->update($id, [
    'customer_name' => 'Updated Customer',
    'email' => 'updated@example.com',
    'licence_date' => '2025-12-12 00:00:00'
]);
$updated = $repo->findById($id);
assertEqual('Updated Customer', $updated['customer_name'], 'update should change name');

// 🔸 Test delete
$repo->delete($id);
$deleted = $repo->findById($id);
assertNull($deleted, 'delete should soft remove customer');

echo "\n--- TEST SELESAI ---\n";
