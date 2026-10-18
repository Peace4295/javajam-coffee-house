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
");

$stmtPopular->bind_param("s", $bestProductName);

$stmtPopular->execute();

$popularResult = $stmtPopular->get_result();

$categories = [];

while ($row = $popularResult->fetch_assoc()){
    $categories[] = $row;
}

$popularOption = "";
//make sure there is at least one category result
if (count($categories) > 0) {

    //since the query is ordered by qty descending, the first row will have the highest quantity
    $highestQuantity = $categories[0]["total_quantity"];

    //stores all categories that share the highest qty
    $topCategories = [];

    foreach ($categories as $category) {

        //check if this cat has the same qty as the highest qty
        if ($category["total_quantity"] == $highestQuantity) {
            //justjava has no single/double cat, so cat may be null
            if ($category["category"] == NULL) {
                $topCategories[] = "Null";
            } else {
                $topCategories[] = $category["category"];
            }
        }
    }

    //if more than one cat has the same highest qty, then there is a tie.
    if (count($topCategories) > 1) {
        // combine the tied cat names into one msg
        $popularOption = implode(" and ", $topCategories) . " (Tie)";

    } else {
        //if there is only one highest cat, dispaly that cat normally
        $popularOption = $topCategories[0];
    }
}
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
        <?php echo $popularOption; ?>
    </strong>
</p>

</body>
</html>