<?php
// Database connection
$get_name_server = "localhost";
$get_user_name = "root";
$get_password = "";
$get_database = "old_pos";

$post_name_server = "localhost";
$post_user_name = "root";
$post_password = "";
$post_database = "n_company_pos_2";

$get_data_conn = new mysqli($get_name_server, $get_user_name, $get_password, $get_database);
$post_data_conn = new mysqli($post_name_server, $post_user_name, $post_password, $post_database);

// Check connection
if ($get_data_conn->connect_error) {
    die("Connection failed: " . $get_data_conn->connect_error);
}

if ($post_data_conn->connect_error) {
    die("Connection failed: " . $post_data_conn->connect_error);
}

// Function to generate random string
function randomString($length = 4) {
    return strtoupper(substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length));
}

function generateUUID() {
    $data = openssl_random_pseudo_bytes(16);
    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
    $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}



// Handle button click
if (isset($_POST['generate'])) {
    // categories table
    $categories_table = "SELECT * FROM categories";
    $categories = $get_data_conn->query($categories_table);
    
    $categories_tamp_id = "ALTER TABLE categories ADD COLUMN temp_id INT(11) NULL";
    $post_data_conn->query($categories_tamp_id);

    foreach ($categories as $row) { 
        $uniqueId = generateUUID();
        $temp_id =  $row['id'];
        $name =  $row['name'];

        $insertCategories = "INSERT INTO categories (id, temp_id, name, created_at, updated_at) VALUES ('$uniqueId', '$temp_id', '$name', NOW(), NOW())";
        
        if ($post_data_conn->query($insertCategories) === TRUE) {
            echo "Categories inserted successfully <br>";
        } else {
            echo "Error Categories data: " . $post_data_conn->error . "<br>";
        }

    }



    // option table
    $addons_table = "SELECT * FROM addon";
    $addons = $get_data_conn->query($addons_table);

    $options_tamp_id_addons = "ALTER TABLE option ADD COLUMN tamp_id_addons INT(11) NULL";
    $post_data_conn->query($options_tamp_id_addons);
    $options_tamp_price_addons = "ALTER TABLE option ADD COLUMN tamp_price_addons INT(11) NULL";
    $post_data_conn->query($options_tamp_price_addons);
    
    foreach ($addons as $row) {
        $uniqueId = generateUUID();
        $tamp_id_addons =  $row['id'];
        $name =  $row['name'];
        $price =  $row['price'];

        $insertAddons = "INSERT INTO option (id, tamp_id_addons, name, tamp_price_addons, created_at, updated_at) VALUES ('$uniqueId', '$tamp_id_addons', '$name', '$price', NOW(), NOW())";
        
        if ($post_data_conn->query($insertAddons) === TRUE) {
            echo "addons inserted successfully <br>";
        } else {
            echo "Error addons data: " . $post_data_conn->error . "<br>";
        }
    }

    $variants_table = "SELECT * FROM variants";
    $variants = $get_data_conn->query($variants_table);

    $options_tamp_id_variants = "ALTER TABLE option ADD COLUMN tamp_id_variants INT(11) NULL";
    $post_data_conn->query($options_tamp_id_variants);
    $options_tamp_price_variants = "ALTER TABLE option ADD COLUMN tamp_price_variants INT(11) NULL";
    $post_data_conn->query($options_tamp_price_variants);
    
    foreach ($variants as $row) {
        $uniqueId = generateUUID();
        $tamp_id_variants =  $row['id'];
        $name =  $row['name'];
        $price =  $row['price'];

        $insertVariants = "INSERT INTO option (id, tamp_id_variants, name, tamp_price_variants, created_at, updated_at) VALUES ('$uniqueId','$tamp_id_variants', '$name', '$price', NOW(), NOW())";
        
        if ($post_data_conn->query($insertVariants) === TRUE) {
            echo "variants inserted successfully <br>";
        } else {
            echo "Error variants data: " . $post_data_conn->error . "<br>";
        }
    }


    // option groups table
    $variant_groups_table = "SELECT * FROM variant_groups";
    $variant_groups = $get_data_conn->query($variant_groups_table);

    $options_tamp_id_variant_groups = "ALTER TABLE option_groups ADD COLUMN tamp_id_variant_groups INT(11) NULL";
    $post_data_conn->query($options_tamp_id_variant_groups);
    
    foreach ($variant_groups as $row) {
        $uniqueId = generateUUID();
        $tamp_id_variant_groups =  $row['id'];
        $name =  $row['name'];
        $variants = explode(',',$row['variants']);
        $option_ids = [];
        foreach($variants as $variant){
            $options_table = "SELECT * FROM option WHERE tamp_id_variants = $variant LIMIT 1";
            $option = $post_data_conn->query($options_table)->fetch_assoc();
            $option_ids[] = $option['id'];
        }

        $option_id =implode(',',$option_ids);

        $insertVariantGroups = "INSERT INTO option_groups (id, tamp_id_variant_groups, name, option_id, created_at, updated_at) VALUES ('$uniqueId','$tamp_id_variant_groups', '$name', '$option_id', NOW(), NOW())";
        
        if ($post_data_conn->query($insertVariantGroups) === TRUE) {
            echo "variant groups inserted successfully <br>";
        } else {
            echo "Error variant groups data: " . $post_data_conn->error . "<br>";
        }
    }

    $addon_groups_table = "SELECT * FROM addon_groups";
    $addon_groups = $get_data_conn->query($addon_groups_table);

    $options_tamp_id_addon_groups = "ALTER TABLE option_groups ADD COLUMN tamp_id_addon_groups INT(11) NULL";
    $post_data_conn->query($options_tamp_id_addon_groups);
    
    foreach ($addon_groups as $row) {
        $uniqueId = generateUUID();
        $tamp_id_addon_groups =  $row['id'];
        $name =  $row['name'];
        $addons = explode(',',$row['addons']);
        $option_ids = [];
        foreach($addons as $addon){
            $options_table = "SELECT * FROM option WHERE tamp_id_addons = $addon LIMIT 1";
            $option = $post_data_conn->query($options_table)->fetch_assoc();
            $option_ids[] = $option['id'];
        }

        $option_id =implode(',',$option_ids);

        $insertAddonGroups = "INSERT INTO option_groups (id, tamp_id_addon_groups, name, option_id, created_at, updated_at) VALUES ('$uniqueId','$tamp_id_addon_groups', '$name', '$option_id', NOW(), NOW())";
        
        if ($post_data_conn->query($insertAddonGroups) === TRUE) {
            echo "addon groups inserted successfully <br>";
        } else {
            echo "Error addon groups data: " . $post_data_conn->error . "<br>";
        }
    }


    // Products table
    $products_table = "SELECT * FROM products";
    $products = $get_data_conn->query($products_table);

    $products_tamp_id = "ALTER TABLE products ADD COLUMN temp_id INT(11) NULL";
    $post_data_conn->query($products_tamp_id);

    foreach ($products as $row) {
        $uniqueId = generateUUID();
        $temp_id =  $row['id'];
        $image = basename($row['image']); // ALL images will be stored in assets/images/products/
        $name =  $row['name'];
        $barcode =  $row['barcode'];
        $sku =  $row['sku'];
        $price =  $row['price'];
        $qty =  $row['qty'];
        $brand_id =  $row['brand_id'];
        $categories = json_decode($row['category_id']);
        $availability =  $row['availability'];
        $description =  $row['description'];
        $category_ids = [];
        foreach ($categories as $category) {
            $categories_table = "SELECT * FROM categories WHERE temp_id = $category LIMIT 1";
            $category_data = $post_data_conn->query($categories_table)->fetch_assoc();
            $category_ids[] = $category_data['id'];
        }
        $category_id = implode(',', $category_ids);

        $insertProducts = "INSERT INTO products (id, temp_id, image, product_name, bar_code, sku, price, quantity, brands, category, availability, description, created_at, updated_at) VALUES ('$uniqueId','$temp_id', '$image', '$name','$barcode', '$sku', '$price', '$qty', '$brand_id', '$category_id', '$availability', '$description', NOW(), NOW())";

        if ($post_data_conn->query($insertProducts) === TRUE) {
            echo "products inserted successfully <br>";
        } else {
            echo "Error products data: " . $post_data_conn->error . "<br>";
        }


        $variantGroups = explode(',',$row['variantgroups']);
        foreach ($variantGroups as $group){
            if($group){
                $uuid = generateUUID();
                $id = (int)$group;
                $option_group_table = "SELECT * FROM option_groups WHERE tamp_id_variant_groups = $id LIMIT 1";
                $option_group_data = $post_data_conn->query($option_group_table)->fetch_assoc();
                if(isset($option_group_data)){
                    $option_group_id = $option_group_data['id'];

                    $option_ids = !empty($option_group_data['option_id'])? explode(',',$option_group_data['option_id']) : '';
                    if(!empty($option_ids)){
                        foreach ($option_ids as $option_id) {
                            $option_table = "SELECT * FROM option WHERE id = '$option_id' LIMIT 1";
                            $option_data = $post_data_conn->query($option_table)->fetch_assoc();
                            $option_addon_price = !empty($option_data['tamp_price_addons'])? $option_data['tamp_price_addons'] : null;
                            $option_uuid = generateUUID();
                            $option_id = !empty($option_data['id'])? $option_data['id'] : null;
                            $insertProducts = "INSERT INTO product_option (id, product_id, option_group_id, option_id, price, created_at, updated_at) VALUES ('$option_uuid', '$uniqueId', '$option_group_id', '$option_id', '$option_addon_price', NOW(), NOW())";
        
                            if ($post_data_conn->query($insertProducts) === TRUE) {
                                echo "product option inserted successfully <br>";
                            } else {
                                echo "Error product option data: " . $post_data_conn->error . "<br>";
                            }
                        }
                    }else{
                        $option_ids = ''; 
                    }
                    
    
                    $insertProducts = "INSERT INTO product_option_group (id, product_id, option_group_id, required_status, created_at, updated_at) VALUES ('$uuid', '$uniqueId', '$option_group_id', 1, NOW(), NOW())";
    
                    if ($post_data_conn->query($insertProducts) === TRUE) {
                        echo "product option group inserted successfully <br>";
                    } else {
                        echo "Error product option group data: " . $post_data_conn->error . "<br>";
                    }
                }
            }
        }

        $addonGroups = explode(',',$row['addongroups']);
        foreach ($addonGroups as $group){
            if($group){
                $uuid = generateUUID();
                $id = (int)$group;
                $option_group_table = "SELECT * FROM option_groups WHERE tamp_id_addon_groups = $id LIMIT 1";
                $option_group_data = $post_data_conn->query($option_group_table)->fetch_assoc();
                if(isset($option_group_data)){
                    $option_group_id = $option_group_data['id'];

                    $option_ids = explode(',',$option_group_data['option_id']);
                    foreach ($option_ids as $option_id) {
                        $option_table = "SELECT * FROM option WHERE id = '$option_id' LIMIT 1";
                        $option_data = $post_data_conn->query($option_table)->fetch_assoc();
                        $option_addon_price = $option_data['tamp_price_addons'];
                        $option_uuid = generateUUID();

                        $option_id = $option_data['id'];
                        $insertProducts = "INSERT INTO product_option (id, product_id, option_group_id, option_id, price, created_at, updated_at) VALUES ('$option_uuid', '$uniqueId', '$option_group_id', '$option_id', '$option_addon_price', NOW(), NOW())";

                        if ($post_data_conn->query($insertProducts) === TRUE) {
                            echo "product option inserted successfully <br>";
                        } else {
                            echo "Error product option data: " . $post_data_conn->error . "<br>";
                        }
                    }

    
                    $insertProducts = "INSERT INTO product_option_group (id, product_id, option_group_id, created_at, updated_at) VALUES ('$uuid', '$uniqueId', '$option_group_id', NOW(), NOW())";
    
                    if ($post_data_conn->query($insertProducts) === TRUE) {
                        echo "product option group inserted successfully <br>";
                    } else {
                        echo "Error product option group data: " . $post_data_conn->error . "<br>";
                    }
                }
            }
        }
    }


    // orders table
    $orders_table = "SELECT * FROM orders";
    $orders = $get_data_conn->query($orders_table);
    
    $orders_tamp_id = "ALTER TABLE orders ADD COLUMN tamp_id INT(11) NULL";
    $post_data_conn->query($orders_tamp_id);
    
    foreach ($orders as $row) {
        $uniqueId = generateUUID();
        $tamp_id =  $row['id'];
        $customer_id =  $row['customer_id'];
        $customer_name =  $row['customer_name'];
        $customer_phone =  $row['customer_phone'];
        $gross_amount =  $row['gross_amount'];
        $grand_total =  $row['net_amount'];
        $service_charge =  $row['service_charge'];
        $vat_charge_rate =  $row['vat_charge_rate'];
        $vat_charge =  $row['vat_charge'];
        $discount =  $row['discount'];
        $pos_order_type =  $row['pos_order_type'];
        $table_number =  $row['table_number'];
        $order_date =  $row['order_date'];
        $order_status =  $row['order_status'];
        // bill_no number is not import

        $insertOrders = "INSERT INTO orders (id, tamp_id, order_status, customer_id, walk_in_customer_name, walk_in_customer_phone, sub_total, grand_total, tax_name, total_tax, discount, extra_charges, table_no, order_type, created_at, updated_at) VALUES ('$uniqueId', '$tamp_id', '$order_status', '$customer_id', '$customer_name', '$customer_phone', '$gross_amount', '$grand_total', '$vat_charge_rate', '$vat_charge', '$discount', '$service_charge', '$table_number', '$pos_order_type', '$order_date', '$order_date')";
        
        if ($post_data_conn->query($insertOrders) === TRUE) {
            echo "orders inserted successfully <br>";
        } else {
            echo "Error orders data: " . $post_data_conn->error . "<br>";
        }
    }


    // order Products table
    $orders_item_table = "SELECT * FROM orders_item";
    $orders_item = $get_data_conn->query($orders_item_table);
    
    foreach ($orders_item as $row) {
        $uniqueId = generateUUID();
        $is_new_for_kitchen =  $row['is_new_for_kitchen'];
        $order_id =  $row['order_id'];
        $product_id =  $row['product_id'];
        $qty =  $row['qty'];
        $rate =  $row['rate'];
        $amount =  $row['amount'];

        $orders_table = "SELECT * FROM orders WHERE tamp_id = '$order_id' limit 1";
        $orders = $post_data_conn->query($orders_table)->fetch_assoc();
        $order_table_id = $orders['id'];
        $order_table_sub_total = $orders['sub_total'];
        $order_table_created_at = $orders['created_at'];
        
        $products_table = "SELECT * FROM products WHERE temp_id = '$product_id' limit 1";
        $products = $post_data_conn->query($products_table)->fetch_assoc();
        $product_table_id = $products['id'];

        $insertOrdersItem = "INSERT INTO order_products (id, order_id, product_id, quantity, price, unit_price, total_unit_price, total_price, kitchen_print_status, created_at, updated_at) VALUES ('$uniqueId', '$order_table_id', '$product_table_id', '$qty', '$rate', '$rate', '$amount', '$order_table_sub_total', '$is_new_for_kitchen', '$order_table_created_at', '$order_table_created_at')";
        
        if ($post_data_conn->query($insertOrdersItem) === TRUE) {
            echo "order products inserted successfully <br>";
        } else {
            echo "Error order products data: " . $post_data_conn->error . "<br>";
        }

        $quantityQuery = "SELECT SUM(quantity) as total_quantity FROM order_products WHERE order_id = '$order_table_id'";
        $quantityResult = $post_data_conn->query($quantityQuery)->fetch_assoc();
        $totalQuantity = $quantityResult['total_quantity'];

        $updateOrderQuantity = "UPDATE orders SET total_quantity = '$totalQuantity' WHERE id = '$order_table_id'";
        if ($post_data_conn->query($updateOrderQuantity) === TRUE) {
            echo "Order total quantity updated successfully <br>";
        } else {
            echo "Error updating total quantity: " . $post_data_conn->error . "<br>";
        }


        // option data not correct in old db

        // $product_options = json_decode($row['options']);
        // $all_options = [];
        // foreach($product_options as $option){
        //     $type = $option->type;
        //     if($type == 'addon'){
        //         $group_id = $option->group_id;
        //         $option_groups_table = "SELECT * FROM option_groups WHERE tamp_id_addon_groups = '$group_id' LIMIT 1";
        //         $option_group_data = $post_data_conn->query($option_groups_table)->fetch_assoc();
        //         $option_group_id = $option_group_data['id'];
                
        //         if($option->option_id) {
        //             $op_id = $option->option_id;
        //             $option_table = "SELECT * FROM option WHERE tamp_id_addons = '$op_id' LIMIT 1";
        //             $option_data = $post_data_conn->query($option_table)->fetch_assoc();

        //             $option_table_id = $option_data['id'];
        //             $option_table_name = $option_data['name'];
        //             $option_table_price = $option_data['tamp_price_addons'];
        //             $data = [
        //                 "name" => $option_table_name,
        //                 "id" => $option_table_id,
        //                 "price" => $option_table_price,
        //                 "price_prefix" => 0,
        //                 "option_group_id" => $option_group_id,
        //                 "option_required_status" => 0
        //             ];

        //         }else{

        //             $data = [
        //                 "name" => '',
        //                 "id" => "",
        //                 "price" => '',
        //                 "price_prefix" => 0,
        //                 "option_group_id" => $option_group_id,
        //                 "option_required_status" => 0
        //             ];
        //         }
        //     }else {

        //         $group_id = $option->group_id;
        //         $option_groups_table = "SELECT * FROM option_groups WHERE tamp_id_variant_groups = '$group_id' LIMIT 1";
        //         $option_group_data = $post_data_conn->query($option_groups_table)->fetch_assoc();
        //         $option_group_id = $option_group_data['id'];
                
        //         if($option->option_id) {
        //             $op_id = $option->option_id;
        //             $option_table = "SELECT * FROM option WHERE tamp_id_variants = '$op_id' LIMIT 1";
        //             $option_data = $post_data_conn->query($option_table)->fetch_assoc();

        //             $option_table_id = $option_data['id'];
        //             $option_table_name = $option_data['name'];
        //             $option_table_price = $option_data['tamp_price_variants'];
        //             $data = [
        //                 "name" => $option_table_name,
        //                 "id" => $option_table_id,
        //                 "price" => $option_table_price,
        //                 "price_prefix" => 0,
        //                 "option_group_id" => $option_group_id,
        //                 "option_required_status" => 0
        //             ];
        //             var_dump($data);
        //         }else{
        //             $data = [
        //                 "name" => '',
        //                 "id" => "",
        //                 "price" => '',
        //                 "price_prefix" => 0,
        //                 "option_group_id" => $option_group_id,
        //                 "option_required_status" => 0
        //             ];
        //         }
        //     }
        // }
        // $all_options[] = $data;
        // $json_result = json_encode($all_options);
    }









    $drop_categories_temp_id = "ALTER TABLE categories DROP COLUMN temp_id";
    $post_data_conn->query($drop_categories_temp_id);
    $drop_options_tamp_id_variants = "ALTER TABLE option DROP COLUMN tamp_id_variants";
    $post_data_conn->query($drop_options_tamp_id_variants);
    $drop_options_tamp_price_variants = "ALTER TABLE option DROP COLUMN tamp_price_variants";
    $post_data_conn->query($drop_options_tamp_price_variants);
    $drop_options_tamp_id_addons = "ALTER TABLE option DROP COLUMN tamp_id_addons";
    $post_data_conn->query($drop_options_tamp_id_addons);
    $drop_options_tamp_price_addons = "ALTER TABLE option DROP COLUMN tamp_price_addons";
    $post_data_conn->query($drop_options_tamp_price_addons);
    $drop_options_tamp_id_variant_groups = "ALTER TABLE option_groups DROP COLUMN tamp_id_variant_groups";
    $post_data_conn->query($drop_options_tamp_id_variant_groups);
    $drop_options_tamp_id_addon_groups = "ALTER TABLE option_groups DROP COLUMN tamp_id_addon_groups";
    $post_data_conn->query($drop_options_tamp_id_addon_groups);
    $drop_products_temp_id = "ALTER TABLE products DROP COLUMN temp_id";
    $post_data_conn->query($drop_products_temp_id);
    $drop_orders_tamp_id = "ALTER TABLE orders DROP COLUMN tamp_id";
    $post_data_conn->query($drop_orders_tamp_id);

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
