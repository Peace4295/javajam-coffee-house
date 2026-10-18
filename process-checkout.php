<?php

require "includes/database.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Get submitted quantities
$qtyJava = (int) ($_POST["qty_java"] ?? 0);
$qtyCafe = (int) ($_POST["qty_cafe"] ?? 0);
$qtyCapp = (int) ($_POST["qty_capp"] ?? 0);

// Get selected categories
$cafeShot = $_POST["cafeshot"] ?? "";
$cappShot = $_POST["cappshot"] ?? "";

$validOptions = ["Single", "Double"];

if (!in_array($cafeShot, $validOptions, true)) {
    die("Invalid Cafe au Lait option.");
}

if (!in_array($cappShot, $validOptions, true)) {
    die("Invalid Iced Cappuccino option.");
}


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



} catch (Exception $e) {

    $conn->rollback();

    die("Checkout failed: " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Order Confirmation</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body class="bg-light">

<div class="container py-5">
     <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">

    <div class="alert alert-success">
        <h2 class="alert-heading">Order Successful!</h2>
        <p class="mb-0"> Your order has been saved successfully. </p>
    </div>
    <div class="card-body">
        <h2 class="card-title mb-4"> Order Confirmation </h2>
                    <p> <strong>Order ID:</strong> <?php echo $orderId; ?> </p>
                    <p> <strong>Total Price:</strong> $<?php echo number_format($totalPrice, 2); ?> </p>
                    <hr>
                    <h5 class="mb-3"> Order Summary </h5>
                    <ul class="list-group mb-4">

                        <?php foreach ($items as $item) { ?>

                            <li class="list-group-item d-flex justify-content-between">
                                <span>
                                    Quantity: <?php echo $item["quantity"]; ?>
                                </span>
                                <strong> $<?php echo number_format( $item["subtotal"], 2 ); ?> </strong>
                            </li>
                        <?php } ?>
                    </ul>
                    <a href="menu.php" class="btn btn-primary"> Back to Menu </a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>


<!-- cs5 enhancement 
- bootstrap-based admiinstrative UI enhancement
- applied to price update, sales report and checkout confirmation pages -->