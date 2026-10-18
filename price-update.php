<?php

require "includes/database.php";

$message = "";

// Check if Update Prices button was clicked
if (isset($_POST["updatePrices"])) {

    // Check whether at least one checkbox was selected
    if (isset($_POST["selected"])) {

        foreach ($_POST["selected"] as $id) {

            $priceField = "price_" . $id;
            $newPrice = $_POST[$priceField];

            // Only update if a new price was entered
            if ($newPrice != "" && $newPrice > 0) {

                $stmt = $conn->prepare(
                    "UPDATE menu_items SET price = ? WHERE id = ?"
                );

                $stmt->bind_param("di", $newPrice, $id);
                $stmt->execute();
            }
        }

        $message = "Prices updated successfully.";

    } else {

        $message = "Please select at least one product.";
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
    <link rel="stylesheet" href="style.css">
</head>

<body>

<h1>Product Price Update</h1>
<?php
if ($message != "") {
    echo "<p>$message</p>";
}
?>

<form method="post">

    <table>
        <tr>
            <th>Select</th>
            <th>Product</th>
            <th>Category</th>
            <th>Current Price</th>
            <th>New Price</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()) { ?>

        <tr>
            <td>
                <input type="checkbox"
                       name="selected[]"
                       value="<?php echo $row['id']; ?>">
            </td>

            <td>
                <?php echo $row['product_name']; ?>
            </td>

            <td>
                <?php
                if ($row['category'] == NULL) {
                    echo "-";
                } else {
                    echo $row['category'];
                }
                ?>
            </td>

            <td>
                $<?php echo number_format($row['price'], 2); ?>
            </td>

            <td>
                <input type="number"
                       name="price_<?php echo $row['id']; ?>"
                       step="0.01"
                       min="0">
            </td>
        </tr>

        <?php } ?>

    </table>

    <br>

    <input type="submit" name="updatePrices" value="Update Prices">

</form>

</body>
</html>