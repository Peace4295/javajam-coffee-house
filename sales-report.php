<?php
require "includes/database.php";

//"where date" only incl orders placed today, not all orders in db
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

$productResult = $conn->query($sqlProduct);

$sqlCategory = "
    SELECT
        m.category,
        SUM(oi.quantity) AS total_quantity,
        SUM(oi.subtotal) AS total_sales
    FROM order_items oi

    JOIN menu_items m
        ON oi.menu_item_id = m.id

    JOIN orders o
        ON oi.order_id = o.order_id

    WHERE DATE(o.order_date) = CURDATE()

    GROUP BY m.category
";

$categoryResult = $conn->query($sqlCategory);

//limit 1 means give me the first one
$sqlBestProduct = "
    SELECT
        m.product_name,
        SUM(oi.subtotal) AS total_sales
    FROM order_items oi

    JOIN menu_items m
        ON oi.menu_item_id = m.id

    JOIN orders o
        ON oi.order_id = o.order_id

    WHERE DATE(o.order_date) = CURDATE()

    GROUP BY m.product_name

    ORDER BY total_sales DESC

    LIMIT 1
";

$bestProductResult = $conn->query($sqlBestProduct);

$bestProduct = $bestProductResult->fetch_assoc();

$bestProductName = $bestProduct["product_name"];

$stmtPopular = $conn->prepare("
    SELECT
        m.category,
        SUM(oi.quantity) AS total_quantity

    FROM order_items oi

    JOIN menu_items m
        ON oi.menu_item_id = m.id

    JOIN orders o
        ON oi.order_id = o.order_id

    WHERE DATE(o.order_date) = CURDATE()
      AND m.product_name = ?

    GROUP BY m.category

    ORDER BY total_quantity DESC

    LIMIT 1
");

$stmtPopular->bind_param("s", $bestProductName);

$stmtPopular->execute();

$popularResult = $stmtPopular->get_result();

$popularCategory = $popularResult->fetch_assoc();
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
<h2>Sales by Category</h2>

<table border="1">

    <tr>
        <th>Category</th>
        <th>Total Quantity</th>
        <th>Total Sales</th>
    </tr>

    <?php while ($row = $categoryResult->fetch_assoc()) { ?>

        <tr>
            <td>
                <?php
                if ($row["category"] == NULL) {
                    echo "Null";
                } else {
                    echo $row["category"];
                }
                ?>
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
<h2>Best-Selling Product</h2>

<p>
    Best-selling product:
    <strong>
        <?php echo $bestProduct["product_name"]; ?>
    </strong>

    ($<?php echo number_format($bestProduct["total_sales"], 2); ?>)
</p>

<p>
    Most popular option:
    <strong>
        <?php
        if ($popularCategory["category"] == NULL) {
            echo "Null";
        } else {
            echo $popularCategory["category"];
        }
        ?>
    </strong>
</p>

</body>
</html>