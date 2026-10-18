<?php

require "includes/database.php";

$sql = "SELECT * FROM menu_items";
$result = $conn->query($sql);

$prices = [];

while ($row = $result->fetch_assoc()) {
    $prices[$row["id"]] = $row["price"];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>JavaJam Coffee House</title>
<meta charset="utf-8">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="style.css">

</head>
<body>
<div id="wrapper">
  <header>
    <img src="header.png" width="600" height="100" alt="JavaJam Coffee House">
  </header>
  <div id="leftcolumn">
    <nav>
        <ul>
        <li><a href="index.html">Home</a></li>
        <li><a href="menu.php">Menu</a></li>
        <li><a href="music.html">Music</a></li>
        <li><a href="jobs.html">Jobs</a></li>
        </ul>
    </nav>	
  </div>
  <div id="rightcolumn">
        <div class="content"> 
            <div>
                <h1>Coffee at JavaJam</h1>
                <form id="menuForm">
                    <table class="menu-table">
                        <tr>
                            <td>Just Java</td>
                            <td>Regular house blend, decafinated coffee, or flavour of the day. 
                                <br><strong>Endless Cup $<?php echo number_format($prices[1],2); ?></strong>
                                <input  type="hidden" name="java_price" id="java_price" value="<?php echo $prices[1]; ?>">. <!-- hidden unit price from db-->
                            </td>
                            <td>
                                <input type="text" id="qty_java" name="qty_java"> <!-- customer enter qty -->
                                </td>
                            <td>
                                <input type="text" id="price_java" name="price_java" readonly> <!-- calc subtotal shown to customer -->
                            
                            </td>
                        </tr>
                        <tr>
                            <td>Cafe au Lait</td>
                            <td>House blended coffee infused into a smooth,steamed milk
                                <!-- <br><strong>Single $2.00, Double $3.00</strong> -->
                                 <br>
                                <input type="radio" name="cafeshot" id="cafesingle" value="<?php echo $prices[2];?>" checked>
                                <label> Single $<?php echo number_format($prices[2],2); ?></label>

                                <input type="radio" name="cafeshot" id="cafedouble" value="<?php echo $prices[3];?>">
                                <label for="CafeDouble">Double $<?php echo number_format($prices[3],2); ?></label>
                            </td>
                            <td> 
                                <input type="text" id="qty_cafe" name="Qty_cafe"> 
                            </td>
                            <td> 
                                <input type="text" id="price_cafe" name="price_cafe" readonly> 
                            </td>
                        </tr>
                        <tr>
                            <td>Iced Cappuccino</td>
                            <td>Sweetened espresso blended with icy-cold milkand served in a chilled glass
                                <!-- <br><strong>Single $4.75, Double $5.75</strong> -->
                                 <br>
                            <input type="radio" name="cappshot" id="cappsingle" value="<?php echo $prices[4];?>" checked>
                                <label> Single $<?php echo number_format($prices[4],2); ?></label>

                                <input type="radio" name="cappshot" id="cappdouble" value="<?php echo $prices[5];?>">
                                <label for="CafeDouble">Double $<?php echo number_format($prices[5],2); ?></label>
                            </td>
                            <td> 
                                <input type="text" id="qty_capp" name="qty_capp"> 
                            </td>
                            <td> 
                                <input type="text" id="price_capp" name="price_capp" readonly> 
                            </td>
                        </tr>
                    </table>  
                    <div class="total-price">
                        <label for="TotalPrice">Total Price</label>
                            <input type="text" id="total_price" name="total_price" readonly>
                            <input type="button" id="resetOrder" value="Reset Order" class="btn btn-outline-secondary btn-sm"> 
                    </div>
                </form>
                
            </div>
        </div>
  </div>
    <footer>
        Copyright &copy; 2014 JavaJam Coffee House<br>
        Priscilla@Chong.com
    </footer>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="menuUpdate.js"></script>
</body>
</html>

<!-- enhancement for casestudy 4
1. inline validation Q4i
- instead of alert, i added a span and use innerHTML to display error message (formvalidation.js)

2.jQuery Reset Order Q4ii
- main coffee cal use traditional javascript
-jQuery used specifically for reset order

3. Bootstrap button styling
- for reset order, clear and apply now (menu.html and jobs.html)-->

