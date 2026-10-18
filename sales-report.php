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

<body class="admin-page">
    <div class="admin-shell">
     <header class="admin-topbar">
        <div>
            <p class="admin-eyebrow">JavaJam Coffee House</p>
            <h1>Admin Panel</h1>
            </div>
            <nav class="admin-nav">
                <a class="admin-nav-link active" href="sales-report.php"> Sales Report </a>
                <a class="admin-nav-link" href="price-update.php"> Price Update </a>
            </nav>
        </header>
        <main class="admin-main">
        <section class="report-heading">
            <div>
                <p class="section-kicker">Daily Sales Report</p>
                <h2>Sales Overview</h2>
                <p class="report-subtitle">Date: <?php echo date("Y-m-d"); ?></p>
            </div>
         </section>
                
                <section class="admin-form-card report-section">
                    <h3>Sales by Category</h3>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Total Quantity</th>
                                <th>Total Sales</th>
                            </tr>
                        </thead>    
                        <tbody>

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

                                <td> <?php echo $row["total_quantity"]; ?> </td>
                                <td> $<?php echo number_format($row["total_sales"], 2); ?> </td>
                            </tr>

                        <?php } ?>
                        </tbody>
                    </table>
                </section>
                
            

    
            <section class="admin-form-card report-section">
            <h3>Sales by Product</h3>
            <table class="admin-table">

                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Total Quantity</th>
                        <th>Total Sales</th>
                    </tr>
                </thead>

                <tbody>
                <?php while ($row = $productResult->fetch_assoc()) { ?>
                    <tr>
                        <td> <?php echo $row["product_name"]; ?> </td>
                        <td> <?php echo $row["total_quantity"]; ?> </td>
                        <td> $<?php echo number_format($row["total_sales"], 2); ?> </td>
                    </tr>

                <?php } ?>
                </tbody>
            </table>
        </section>
        <section class="admin-form-card report-section best-seller">

            <p class="section-kicker">Top Performer</p>
            <h3> <?php echo $bestProduct["product_name"]; ?> </h3>

            <div>

                <p>
                    <span>Total Sales</span>
                    <strong> $<?php echo number_format( $bestProduct["total_sales"], 2 ); ?> </strong>
                </p>

                <p>
                    <span>Most Popular Option</span>
                    <strong> <?php echo $popularOption; ?> </strong>
                </p>

            </div>

        </section>

    </main>

</div>

</body>
</html>