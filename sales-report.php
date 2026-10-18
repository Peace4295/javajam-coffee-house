<?php
require "includes/database.php";


$sqlProduct = "
    SELECT
        m.product_name,
        SUM(oi.quantity) AS total_quantity,
        SUM(oi.subtotal) AS total_sales
    FROM order_items oi

    JOIN menu_items m
        ON oi.menu_item_id = m.id

    JOIN orders o
        ON oi.order_id = o.order_id

    WHERE DATE(o.order_date) = CURDATE()

    GROUP BY m.product_name
";
//where date only incl orders placed today, not all orders in db
$productResult = $conn->query($sqlProduct);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>JavaJam Sales Report</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<h1>Daily Sales Report</h1>

<h2>Sales by Product</h2>

<table border="1">

    <tr>
        <th>Product</th>
        <th>Total Quantity</th>
        <th>Total Sales</th>
    </tr>

    <?php while ($row = $productResult->fetch_assoc()) { ?>

        <tr>
            <td>
                <?php echo $row["product_name"]; ?>
            </td>

            <td>
                <?php echo $row["total_quantity"]; ?>
            </td>

            <td>
                $<?php echo number_format($row["total_sales"], 2); ?>
            </td>
        </tr>

    <?php } ?>

</table>

</body>
</html>