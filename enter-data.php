<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "edus2";

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to generate random string
function randomString($length = 4) {
    return strtoupper(substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length));
}

// Handle button click
if (isset($_POST['generate'])) {
    $totalNetAmount = 0;

    for ($i = 0; $i < 100; $i++) {
        if ($totalNetAmount >= 705.3) {
            break;
        }

        $billNo = 'BILPR-' . randomString();
        $dateTime = strtotime(date('Y-m-d H:i:s', rand(strtotime('2023-06-01'), strtotime('2023-06-30'))));
        $orderDate = date('Y-m-d H:i:s', $dateTime);

        $order = [
            'bill_no' => $billNo,
            'customer_id' => 0,
            'customer_name' => 'Walk-in-Customer',
            'customer_address' => '123123123',
            'customer_phone' => '212312312',
            'date_time' => $dateTime,
            'gross_amount' => 0,
            'service_charge_rate' => 0,
            'service_charge' => 0,
            'vat_charge_rate' => 0,
            'vat_charge' => 0,
            'total_servicecharges' => 0,
            'amount_tendered' => 0,
            'net_amount' => 0,
            'discount' => 0,
            'paid_status' => 2,
            'user_id' => 1,
            'deliver_to' => '',
            'deliver_from' => '',
            'address' => '',
            'order_type' => 1,
            'order_name' => '',
            'pos_order_type' => 2,
            'table_number' => '',
            'last_item_id' => 0,
            'order_date' => $orderDate
        ];

        // Insert into orders table
        $columns = implode(", ", array_keys($order));
        $values = implode("', '", array_values($order));
        $sql = "INSERT INTO orders ($columns) VALUES ('$values')";
        if ($conn->query($sql)) {
            $orderId = $conn->insert_id;

            $itemCount = rand(1, 5);
            $totalAmount = 0;

            for ($a = 0; $a < $itemCount; $a++) {
                $random = rand(1, 134);
                $rand_quantity = rand(1, 5);

                // Fetch random product
                $productSql = "SELECT * FROM products WHERE id = $random";
                $productResult = $conn->query($productSql);
                if ($productResult && $product = $productResult->fetch_assoc()) {
                    $hasOptions = rand(0, 1) === 1;
                    $itemOptions = $hasOptions ? json_encode([
                        'type' => 'addon',
                        'group_id' => rand(1, 5),
                        'group_name' => 'Pizza Type',
                        'option_id' => rand(1, 5),
                        "option_name" => "Klein",
                        "option_price" => rand(1, 5),
                        "object" => "954"
                    ]) : '';

                    $item = [
                        'order_id' => $orderId,
                        'product_id' => $product['id'],
                        'qty' => $rand_quantity,
                        'rate' => $product['price'] * $rand_quantity,
                        'amount' => $product['price'] * $rand_quantity,
                        'discount' => 0,
                        'discount_type' => 'percentage',
                        'options' => $itemOptions,
                    ];

                    // Insert into orders_item table
                    $itemColumns = implode(", ", array_keys($item));
                    $itemValues = implode("', '", array_values($item));
                    $conn->query("INSERT INTO orders_item ($itemColumns) VALUES ('$itemValues')");

                    $totalAmount += $product['price'] * $rand_quantity;
                }
            }

            // Update orders table
            $updateSql = "UPDATE orders SET 
                gross_amount = $totalAmount,
                amount_tendered = $totalAmount,
                net_amount = $totalAmount
                WHERE id = $orderId";
            $conn->query($updateSql);

            $totalNetAmount += $totalAmount;

            if ($totalNetAmount >= 705.3) {
                break;
            }
        }
    }

    echo "Data generated successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Data</title>
</head>
<body>
    <h2>Generate Data</h2>
    <form method="POST" action="">
        <button type="submit" name="generate">Generate Data</button>
    </form>
</body>
</html>
