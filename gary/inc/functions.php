<?php

/* data access functions *********************************************/

/*
server access functions:
openshift.com
contact.hd.reynolds@gmail.com
P4ssw0rd!
*/

function dbCredentials() {
    return array(
        "servername" => "localhost",
        "username" => "cakeuser421",
        "password" => "?.0MX*6RqtsM",
        "dbname" => "mycakedb421",
    );
}

function dbConnection($sql) {
    $credentials = dbCredentials();
    $conn = new mysqli($credentials["servername"],
                        $credentials["username"],
                        $credentials["password"],
                        $credentials["dbname"]);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 
    $result = $conn->query($sql);
    $conn->close();
    return $result;
}

/* authentication functions *********************************************/

/* admin edit functions *********************************************/

function admin_getItemMenu() {
    $sql = "SELECT i.id, i.name, c.name as category from store_item i, store_category c where i.category_id = c.id order by c.name, i.name";
    $result = dbConnection($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<li>";
            echo "<a href=\"edit.php?id=" . $row["id"] . "\">";
            echo $row["category"] . " &raquo; " . $row["name"];
            echo "</a>";            
            echo "</li>";
        }
    }
}

function admin_getItem() {
    $itemId = $_GET["id"];
    if (is_numeric($itemId)) {
        return getItem();
    }
}

function admin_getCategories($selectedCategory) {
    $sql = "select c.id, c.name from store_category c order by c.name";
    $result = dbConnection($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $selected = ($row["id"] == $selectedCategory) ? " selected" : "";
            echo "<option value=\"" . $row["id"] . "\" " . $selected . ">" . $row["name"] . "</option>";
        }
    }
}


/* admin save functions *********************************************/

function adminSave_delete($id) {
    
    // delete any associated prices
    if ($id) {
        $deleteSQL = "delete from price where item_id = " . $id;
        dbConnection($sql);
    }

    // load up existing item to determine if there are files assoc
    $existingItem = getItemWithId($id);

    if ($existingItem["image_url"] != "") {
        unlink("images/" . $existingItem["image_url"]);  
    }

    if ($existingItem["image_thumbnail_url"] != "") {
        unlink("images/" . $existingItem["image_thumbnail_url"]);  
    }

    $sql = "delete from store_item where id = " . $id;

    //TODO : fixed URL
    header("Location: /gary/admin/edit.php?act=delete"); 
    exit();

}

function adminSave_formPost() {
    return array(
        "id" => $_POST["item_id"],
        "name" => $_POST["name"],
        "specs" => $_POST["specs"],
        "description" => $_POST["desc"],
        "image_url" => $_POST["image_detail_file_upload"],
        "image_thumbnail_url" => $_POST["image_thumb_file_upload"],
        "category_id" => $_POST["cat"],
        "on_sale" => ($_POST["showItem"] == 1),
        "feature_on_front" => ($_POST["shopFront"] == 1),
        "prices" => $_POST["prices"], // prices is an array
        "details" => NULL, // details will be an array
    );
}

function adminSave_manageItem() {
    
    $editedItem = adminSave_formPost();
    //var_dump($editedItem);
    //echo "<BR/>";

    $sql = "";

    // editing existing 
    if (intval($editedItem["id"]) > 0) {
    
        $existingItem = getItemWithId($editedItem["id"]);

        // handle images
        $detailImage = adminSave_manageImage($editedItem["image_url"], $existingItem["image_url"]); 
        $thumbImage = adminSave_manageImage($editedItem["image_thumbnail_url"], $existingItem["image_thumbnail_url"]); 

        $sql = "update store_item
                set name = '" . addslashes($editedItem["name"]) . "'," .
                "description = '" . addslashes($editedItem["description"]) . "'," .
                "category_id = " . $editedItem["category_id"] . "," .
                "image_name = '" . $detailImage . "'," .
                "image_thumbnail = '" . $thumbImage . "'," .
                "on_sale = " . $editedItem["on_sale"] . "," .
                "feature_on_front = " . $editedItem["feature_on_front"] . "," .
                "product_specs = '" . addslashes($editedItem["specs"]) . "' " .
                "where id = " . $editedItem["id"]; 

        echo "detailImage: " . $detailImage . "<BR/>";
        echo "thumbImage: " . $thumbImage . "<BR/>";

    // adding new
    } elseif (intval($editedItem["id"]) == 0) {
        
        // handle images
        $detailImage = adminSave_manageImage($editedItem["image_url"], ""); 
        $thumbImage = adminSave_manageImage($editedItem["image_thumbnail_url"], ""); 

        echo "detailImage: " . $detailImage . "<BR/>";
        echo "thumbImage: " . $thumbImage . "<BR/>";


        $sql = "insert into store_item (name, description, category_id, image_name, image_thumbnail, on_sale, feature_on_front, product_specs)
                values ('" . addslashes($editedItem["name"]) . "'," .
                    "'" . addslashes($editedItem["description"]) . "'," .
                    $editedItem["category_id"] . ", " .
                    "'" . $detailImage . "'," .
                        "'" . $thumbImage . "'," .
                    (int)$editedItem["on_sale"] . "," .
                    (int)$editedItem["feature_on_front"] . "," .
                    "'" . addslashes($editedItem["specs"]) . "')";/**/

        //echo "Sql: " . $sql . "<BR/>";

    }

    $result = dbConnection($sql);

    // get ID to set prices 
    if (count($editedItem["prices"]) > 0) {

        if (intval($editedItem["id"]) == 0) {
      
          $newIdSQL = "select max(id) from store_item";
          $result = dbConnection($newIdSQL);

      
          if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $editedItem["id"] = $row["id"];
            }
          }    
        }
    
        adminSave_managePrices($editedItem["prices"], $editedItem["id"]); 

    }

    //TODO : fixed URL
    header("Location: /gary/admin/edit.php?id=" . $editedItem["id"] . "&act=save"); 
    exit();

}

function adminSave_managePrices($priceArray, $itemId) {

    // delete existing prices before saving edited price data
    if ($itemId > 0) {
        $deleteSQL = "delete from price where item_id = " . $itemId;
        dbConnection($sql);
    }

    echo ("priceArray count: " . count($priceArray));

    foreach ($priceArray as $price)
    {
        $priceData = explode(":",$price);
        // 0: price row ID; 1: price, 2: shipping, 3: desc

        // insert new price data
        $sql = "insert into price (item_id, price, description, shipping_cost) values (" . $itemId . ", " . $priceData[1] . ", '" . addslashes($priceData[3]) . "', " . $priceData[2] . ")";

        $result = dbConnection($sql);

    }   
    
}

function adminSave_manageImage($imageURL, $existingURL) {

    // if existingUrl doesn't contain newFileName :
    //  - delete existing image
    //  - move new image from uploads to images
    // if newUrl is blank
    //  - delete image
    // if existingUrl includes fileName, do nothing
    // if existingUrl is blank and newUrl is not
    //  - move new image from uploads to images

    //echo "imageURl: " . $imageURL . "<BR/>";
    //echo "existingURL:" . $existingURL. "<BR/>";

    $newFileName = "";
    if ($imageURL != "") {
        $URLArray = explode("/",$imageURL);

        //var_dump($URLArray);
        //echo "<BR/>";

        $newFileName = $URLArray[count($URLArray)-1];
    }

    try {

    if ($existingURL == "") { // no existing image
        if ($imageURL != "") { 
            // move uploaded image to /images
            rename($imageURL, str_replace("images/upload/","images/", $imageURL));
        }
    } else {
        if ($imageURL == "") {
            // has selected to delete existing image, didn't replace
            unlink("images/" . $existingURL);  
        }
        if (strpos(strtolower($existingURL),strtolower($newFileName)) === FALSE) {
            // existing image doesn't match new image
            // delete existing image, move uploaded image into it's place
            unlink("images/" . $imageURL);
            rename($imageURL, str_replace("images/upload/","images/", $imageURL));
        }
    }
    
    //echo "newFileName: " . $newFileName. "<BR/>";
    //echo "existingURL: " . $existingURL. "<BR/>";

    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }

    return $newFileName;

}


/* frontend functions *********************************************/

function menuBuilder($selectedCategoryId = 0) {

    $sql = "SELECT * FROM store_category WHERE visible = 1 ORDER BY rank desc";
    $result = dbConnection($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<div class=\"menu_item\">";
            if ($row["id"] == $selectedCategoryId) {
               echo $row["name"];  
            } else {
               echo "<a href=\"category.php?id=" . $row["id"] . "\">" . $row["name"] . "</a>";  
            }
            echo "</a></div>";
        }
    }
}

function getCategory() {
    $categoryId = $_GET["id"];
    if (is_numeric($categoryId)) {        
        $sql = "select i.id, i.name, i.image_thumbnail from store_item i where i.category_id = " . $categoryId . " and i.on_sale = 1";
        $result = dbConnection($sql);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<div class=\"category_detail\">";
                echo "<a href=\"detail.php?id=" . $row["id"] . "\">";
                echo "<img src=\"images/" . $row["image_thumbnail"] ."\"/>";
                echo "</a>";
                echo "</div>";
            }
        }

    } else {
        echo "no way jose"; 
        // redirect to landing page if cat is bad
    }
}

function getItemWithId($id) {
   return getItemFromDatabase($id); 
}

function getItem() {
    return getItemFromDatabase($_GET["id"]);
}

function getItemFromDatabase($itemId) {
    //$itemId = $_GET["id"];
    $sql = (is_numeric($itemId)) ?
        "select i.id, i.name, i.product_specs, i.description, i.image_name, i.image_thumbnail, i.category_id, i.on_sale, i.feature_on_front from store_item i where i.id = " . $itemId :
        "select i.id, i.name, i.product_specs, i.description, i.image_name, i.image_thumbnail, i.category_id, i.on_sale, i.feature_on_front from store_item i where i.feature_on_front = 1";

    $item = array(
        "id" => 0,
        "name" => "",
        "specs" => "",
        "description" => "",
        "image_url" => "",
        "image_thumbnail_url" => "",
        "category_id" => 0,
        "on_sale" => false,
        "feature_on_front" => false,
        "prices" => NULL,
        "details" => NULL,
        "message" => "Invalid Item Id"
    );

    $result = dbConnection($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $item["id"] = $row["id"];
            $item["name"] = $row["name"];
            $item["specs"] = $row["product_specs"];
            $item["description"] = $row["description"];
            $item["image_url"] = "images/" . $row["image_name"];
            $item["image_thumbnail_url"] = "images/" . $row["image_thumbnail"];
            $item["category_id"] = $row["category_id"];
            $item["on_sale"] = ($row["on_sale"] == 1);
            $item["feature_on_front"] = ($row["feature_on_front"] == 1);
            $item["message"] = "";
        }
    }

    if ($item["id"] > 0) {
        // get prices
        $item["prices"] = getPrices($item["id"]);
        // get details
        $item["details"] = getDetails($item["id"]);
    }

    return $item;
}

function getPrices($id) {
    $priceArray = array();
    $sql = "select p.item_id, p.price, p.shipping_cost, p.description
            from price p
            where p.item_id = " . $id;
    $priceResult = dbConnection($sql);
    if ($priceResult->num_rows > 0) {
        $x = 0;
        while($row = $priceResult->fetch_assoc()) {
            $priceArray[$x] = array(
                "id" => $row["item_id"],
                "price" => $row["price"],
                "shipping_cost" => $row["shipping_cost"],
                "description" => $row["description"],
            );
            $x += 1;
        }
    }
    return $priceArray;
}

function getDetails($id) {
    $detailArray = array();
    $sql = "select d.detail_image, d.description
            from store_item_detail d
            where d.item_id = " . $id;
    $detailResult = dbConnection($sql);
    if ($detailResult->num_rows > 0) {
        $x = 0;
        while($row = $detailResult->fetch_assoc()) {
            $detailArray[$x] = array(
                "image" => $row["detail_image"],
                "description" => $row["description"],
            );
            $x += 1;
        }
    }
    return $detailArray;
}

?>

