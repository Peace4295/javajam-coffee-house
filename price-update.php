<?php

require "includes/database.php";

$message = "";

// Check if Update Prices button was clicked
if (isset($_POST["updatePrices"])) {

    // Check whether at least one product was selected
    if (!isset($_POST["selected"])) {

        $message = "Please select at least one product.";

    } else {

        $valid = true;

        // First check all selected products
        foreach ($_POST["selected"] as $id) {

            $priceField = "price_" . $id;

            // Get entered price safely
            $newPrice = trim($_POST[$priceField] ?? "");
            if($newPrice === "") {
                $missingPrice++;
            }

            // Selected product has no new price
            if ( $newPrice == "" || !is_numeric($newPrice) || $newPrice < 0.01 || $newPrice > 999.99 ) {
                $message = "Please enter a valid price between $0.01 and $999.99 for all selected products.";

                $valid = false;
                break;
            }

            // Price must be numeric and greater than 0
            if (!is_numeric($newPrice) || $newPrice <= 0) {

                $message = "Please enter a valid price greater than $0.";
                $valid = false;
                break;
            }
        }
            if ($missingPrice > 0) {
                if ($missingPrices == 1){
                    $message = "1 selected product is missing a new price.";
                } else {
                    $message = $missingPrices . " selected products are missing new prices.";
                }
                    $valid = false;
            }


        // Only update database if all selected inputs are valid
        if ($valid == true) {

            foreach ($_POST["selected"] as $id) {

                $priceField = "price_" . $id;
                $newPrice = $_POST[$priceField];

                $stmt = $conn->prepare(
                    "UPDATE menu_items
                     SET price = ?
                     WHERE id = ?"
                );

                $stmt->bind_param("di", $newPrice, $id);
                $stmt->execute();
            }

            $message = "Prices updated successfully.";
        }
    }
}


// Retrieve prices AFTER updating
$sql = "SELECT * FROM menu_items";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>JavaJam Price Update</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
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
                <a class="admin-nav-link" href="sales-report.php"> Sales Report </a>
                <a class="admin-nav-link active" href="price-update.php"> Price Update </a>
            </nav>
        </header>
        <main class="admin-main">
            <section class="report-heading">
                <div>
                <p class="section-kicker">Product management</p>

                <h2>Price Update</h2>

                <p class="report-subtitle">
                    Select a menu item and enter its new selling price.
                </p>
            </div>

        </section>

        <?php if ($message != "") { ?>

            <div class="admin-message">
                <?php echo $message; ?>
            </div>

        <?php } ?>
        <section class="admin-form-card">

            <form method="post" class="price-update-form">

                <table class="admin-table">

                    <thead>
                        <tr>
                            <th>Select</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Current Price</th>
                            <th>New Price</th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php while ($row = $result->fetch_assoc()) { ?>

                        <tr>

                            <td>
                                <input type="checkbox" name="selected[]" value="<?php echo $row['id']; ?>">
                            </td>

                            <td>
                                <?php echo $row['product_name']; ?>
                            </td>
                            <td>
                                <?php
                                if ($row['category'] == NULL) {
                                    echo "—";
                                } else {
                                    echo $row['category'];
                                }
                                ?>
                            </td>

                            <td class="current-price">
                                $<?php echo number_format($row['price'], 2); ?>
                            </td>
                            <td>
                                <input type="number" name="price_<?php echo $row['id']; ?>" step="0.01" min="0.01" max="999.99" class="price-input">
                            </td>

                        </tr>

                    <?php } ?>

                    </tbody>

                </table>
                <div class="admin-actions">

                    <input type="submit" name="updatePrices" value="Update Prices" class="admin-button">

                </div>

            </form>

        </section>

    </main>

</div>

</body>
</html>

