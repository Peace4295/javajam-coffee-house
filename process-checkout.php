<?php

require "includes/database.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Get submitted quantities
$qtyJava = (int) ($_POST["qty_java"] ?? 0);
$qtyCafe = (int) ($_POST["qty_cafe"] ?? 0);
$qtyCapp = (int) ($_POST["qty_capp"] ?? 0);

// Get selected categories
$cafeShot = $_POST["cafeshot"] ?? "Single";
$cappShot = $_POST["cappshot"] ?? "Single";


// Prevent negative quantities
if ($qtyJava < 0 || $qtyCafe < 0 || $qtyCapp < 0) {
    die("Quantity cannot be negative.");
}


// Function to retrieve CURRENT price from database
function getMenuPrice($conn, $menuItemId)
{
    $stmt = $conn->prepare(
        "SELECT price FROM menu_items WHERE id = ?"
    );

    $stmt->bind_param("i", $menuItemId);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    return $row["price"];
}


// Store items that customer actually ordered
$items = [];


// JUST JAVA
if ($qtyJava > 0) {

    $menuItemId = 1;
    $unitPrice = getMenuPrice($conn, $menuItemId);
    $subtotal = $unitPrice * $qtyJava;

    $items[] = [
        "menu_item_id" => $menuItemId,
        "quantity" => $qtyJava,
        "unit_price" => $unitPrice,
        "subtotal" => $subtotal
    ];
}


// CAFE AU LAIT
if ($qtyCafe > 0) {

    if ($cafeShot == "Double") {
        $menuItemId = 3;
    } else {
        $menuItemId = 2;
    }

    $unitPrice = getMenuPrice($conn, $menuItemId);
    $subtotal = $unitPrice * $qtyCafe;

    $items[] = [
        "menu_item_id" => $menuItemId,
        "quantity" => $qtyCafe,
        "unit_price" => $unitPrice,
        "subtotal" => $subtotal
    ];
}


// ICED CAPPUCCINO
if ($qtyCapp > 0) {

    if ($cappShot == "Double") {
        $menuItemId = 5;
    } else {
        $menuItemId = 4;
    }

    $unitPrice = getMenuPrice($conn, $menuItemId);
    $subtotal = $unitPrice * $qtyCapp;

    $items[] = [
        "menu_item_id" => $menuItemId,
        "quantity" => $qtyCapp,
        "unit_price" => $unitPrice,
        "subtotal" => $subtotal
    ];
}


// Do not allow an empty checkout
if (count($items) == 0) {
    die("Please select at least one item before checking out.");
}


// Calculate total price
$totalPrice = 0;

foreach ($items as $item) {
    $totalPrice += $item["subtotal"];
}


// Save everything to database
try {

    $conn->begin_transaction();


    // Insert into orders table
    $stmtOrder = $conn->prepare(
        "INSERT INTO orders (total_price) VALUES (?)"
    );

    $stmtOrder->bind_param("d", $totalPrice);
    $stmtOrder->execute();

    $orderId = $conn->insert_id;


    // Insert individual items
    $stmtItem = $conn->prepare(
        "INSERT INTO order_items
        (order_id, menu_item_id, quantity, unit_price, subtotal)
        VALUES (?, ?, ?, ?, ?)"
    );


    foreach ($items as $item) {

        $menuItemId = $item["menu_item_id"];
        $quantity = $item["quantity"];
        $unitPrice = $item["unit_price"];
        $subtotal = $item["subtotal"];

        $stmtItem->bind_param(
            "iiidd",
            $orderId,
            $menuItemId,
            $quantity,
            $unitPrice,
            $subtotal
        );

        $stmtItem->execute();
    }


    $conn->commit();


    echo "<h1>Order Successful</h1>";
    echo "Order ID: " . $orderId . "<br>";
    echo "Total Price: $" . number_format($totalPrice, 2);


} catch (Exception $e) {

    $conn->rollback();

    echo "Checkout failed: " . $e->getMessage();
}

?>