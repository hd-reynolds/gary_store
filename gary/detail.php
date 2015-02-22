<?php include 'inc/functions.php'?>
<?php include 'inc/header.php';?>

<?php
    
$item = getItem();
if ($item["id"] = 0) {
    echo $item["message"];
} else { 
?>

    <div id="item_detail">
        <div id="item_detail_image">
<?php echo "<img src=\"" . $item["image_url"] . "\" id=\"detail_image\"/>"; ?>
            <div style="clear:both"></div>     
        </div>
        <div id="item_detail_text">
            <h3>
<?php echo $item["name"]; ?>            
            </h3>
            <div id="item_detail_specs">
<?php echo $item["specs"]; ?> 
            </div>
            <div id="item_detail_desc">
<?php echo $item["description"]; ?>             
            </div>
            <div id="item_detail_addl_view">  
<?php
    $count = count($item["details"]);
    if ($count > 0) {
        echo "Additional Views: ";
        for ($i = 0; $i < $count; $i++) {
            $details = $item["details"][$i];
            echo "<a href=\"" . $details["image"] . "\">" . $i . "</a>"; 
        }
    }
?>          
            </div>
            <div id="item_pricing"></div>
            <div id="item_purchase">
<!-- http://stackoverflow.com/questions/6322247/dynamic-paypal-button-generation-isnt-it-very-insecure -->
<form target="paypal" action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="V2ER9A8ZR3DYY">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_cart_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
            </div>
        </div>
        <div style="clear:both"></div>
    </div>
<?php 
} 

?>

<?php include 'inc/footer.php';?>