<?php require '../inc/access.php'?>
<?php include '../inc/functions.php'?>

<?php 

/*

Remaining to-do items
* support management of additional detail images
* field validation on page submit
* data saving
* session authentication

* Port to free site for Gary vetting*/

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Welcome, Gary</title>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/themes/smoothness/jquery-ui.css" />
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">

        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">

        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
    <script>
    $(function() {
        $( "#accordion" ).accordion({
            active: false,
            collapsible: true            
        });
    });

    </script>
<style>
    div#price-button { padding:  10px;}
    div#price-button input[type='button'] { padding:  10px;}
    
    div#image_thumb, div#image_detail { float:  left; padding: 10px; border:  1px solid #CCC; display:  block; text-align: center; vertical-align: middle; width: 300px; height:  auto; margin: 15px; }
    div#image_detail_title, div#image_thumb_title { text-align: center; font-weight: bold; display: block; width:  100%; float:  none;}
    div#image_detail_file, div#image_thumb_file {text-align: center; width: 100%; min-width: 250px; padding:  5px; display:  block; clear:  both;}
    div#image_detail_file img, div#image_thumb_file img { max-width:  250px; text-align: center; display:  block; }

    
    div#save_button { text-align: right; padding:  10px; }
    div#save_button input#save_button, div#save_button input#delete_button { font-size:  18px; font-weight:  bold; }
    
</style>
<style>
.dragandrophandler
{
border:2px dotted #0B85A1;
/*width:240px;*/
color:#92AAB0;
text-align:left;vertical-align:middle;
padding:10px 10px 10 10px;
margin-bottom:10px;
font-size:18px;
}
.filename
{
display:inline-block;
vertical-align:top;
width:250px;
}
.filesize
{
display:inline-block;
vertical-align:top;
color:#30693D;
width:100px;
margin-left:10px;
margin-right:5px;
}
.abort{
    background-color:#A8352F;
    -moz-border-radius:4px;
    -webkit-border-radius:4px;
    border-radius:4px;display:inline-block;
    color:#fff;
    font-family:arial;font-size:13px;font-weight:normal;
    padding:4px 15px;
    cursor:pointer;
    vertical-align:top
    }
</style>



</head>
<body>
<form id="item_edit" method="POST" action="save.php">
    <nav class="navbar navbar-default">
      <div class="container">
        <ul class="nav navbar-nav">
            <li class="navbar-brand"><b>Hi Gary!</b></li>
            <li><a href="edit.php?id=0">Add New Item</a></li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Edit Existing Item<span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                  <?php admin_getItemMenu(); ?>
              </ul>
            </li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
      </div>
    </nav>
    
    <?php if (count($_REQUEST["act"]) > 0) { ?>
    <div class="panel panel-default">
        <div class="panel-body panel-success">
            <?php if ($_REQUEST["act"] == "save") { ?>
            Your changes have been saved
            <?php } elseif ($_REQUEST["act"] == "delete") { ?>
            The selected item has been deleted
            <?php } ?>
        </div>
    </div>
    <?php } ?>

    <div style="clear: both;"></div>
<?php if (is_numeric($_GET["id"])) {  
    $item = admin_getItem();
    //var_dump($item);
?>
<input type="hidden" name="item_id" value="<?php echo $item["id"]; ?>" />
<div id="accordion">
  <h3>Images</h3>
  <div id="accordion_images">
      <div id="image_detail">
        <div id="image_detail_title">Item Detail Image</div>
        <?php if ($item["image_url"] != "") { ?>
        <div id="image_detail_file"><img src="../<?php echo $item["image_url"];?>"/></div>
        <input type="hidden" name="image_detail_file" value="<?php echo $item["image_url"];?>"/>
        <div class="btn-group" id="image_detail_buttons"  role="group">            
            <button type="button" class="view-image btn btn-sm btn-default" data-target="image_detail_file">View Image</button>
            <button type="button" class="delete-image btn btn-sm btn-warning" data-target="image_detail_file">Delete Image</button>
        </div>
        <?php } else { ?>
        <div class="dragandrophandler" id="image_detail_file">Drag & Drop<br/>Image Here</div>
        <?php } ?>
      </div>
      <div id="image_thumb">
        <div id="image_thumb_title">Item Detail Thumbnail</div>
        <?php if ($item["image_thumbnail_url"] != "") { ?>
        <div id="image_thumb_file"><img src="../<?php echo $item["image_thumbnail_url"];?>"/></div>
        <input type="hidden" name="image_thumb_file" value="<?php echo $item["image_thumbnail_url"];?>"/>
        <div class="btn-group" id="image_thumb_buttons" role="group">            
            <button type="button" class="view-image btn btn-sm btn-default" data-target="image_thumb_file">View Image</button>
            <button type="button" class="delete-image btn btn-sm btn-warning" data-target="image_thumb_file">Delete Image</button>
        </div>
        <?php } else { ?>
        <div class="dragandrophandler" id="image_thumb_file">Drag & Drop<br/>Image Here</div>
        <?php } ?>
      </div>
  </div>
  <h3>Details</h3>
  <div id="accordion_form" class="form-horizontal">
      <div class="form-group">
        <label for="name" class="col-sm-2 control-label">Item Name</label>
        <div class="col-sm-10">
          <input type="text" class="form-control" name="name" id="name" placeholder="Item Name" value="<?php echo $item["name"];?>">
        </div>
      </div>
      <div class="form-group">
        <label for="specs" class="col-sm-2 control-label">Item Specs</label>
        <div class="col-sm-10">
          <input type="text" class="form-control" name="specs" id="specs" placeholder="Item Specs" value="<?php echo $item["specs"];?>">
        </div>
      </div>
      <div class="form-group">
        <label for="desc" class="col-sm-2 control-label">Item Description</label>
        <div class="col-sm-10">
          <textarea class="form-control" rows="5" name="desc" id="desc"><?php echo $item["description"];?></textarea>
        </div>
      </div>
      <div class="form-group">
        <label for="cat" class="col-sm-2 control-label">Category</label>
        <div class="col-sm-10">
          <select class="form-control" name="cat" id="cat">
            <?php admin_getCategories($item["category_id"]);?>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label for="showItem" class="col-sm-2 control-label">Show Item On Site?</label>
        <label class="radio-inline">
          <input type="radio" name="showItem" id="showItemTrue" value="1" <?php if ($item["on_sale"]) echo "checked";?>> Yes
        </label>
        <label class="radio-inline">
          <input type="radio" name="showItem" id="showItemFalse" value="0" <?php if (!$item["on_sale"]) echo "checked";?>> No
        </label>
      </div>
      <div class="form-group">
        <label for="shopFront" class="col-sm-2 control-label">Display Item on Shop Front Page?</label>
        <label class="radio-inline">
          <input type="radio" name="shopFront" id="shopFrontTrue" value="1" <?php if ($item["feature_on_front"]) echo "checked";?>> Yes
        </label>
        <label class="radio-inline">
          <input type="radio" name="shopFront" id="shopFrontFalse" value="0" <?php if ($item["feature_on_front"]) echo "checked";?>> No
        </label>
      </div>
  </div>
  <h3>Prices</h3>
  <div id="accordion_price">
    <div class="form-group">
        <label for="prices" class="col-sm-2 control-label">Prices</label>
        <div class="col-sm-10">
          <div id="price-button">
              <input type="button" value="Add New Price" class="btn btn-success" data-toggle="modal" data-target="#exampleModal" data-action="add"/>
              <input type="button" value="Edit Selected Price" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal" data-action="edit"/>
              <input type="button" value="Delete Selected Price" id="delete_selected_price" class="btn btn-warning"/>
          </div>
          <select class="form-control" multiple id="prices" name="prices[]">
            <?php 
            if (count($item["prices"]) > 0) {
                foreach ($item["prices"] as $price) {  ?>
            <option 
              data-id='<?php echo $price["id"];?>' 
              data-price='<?php echo $price["price"];?>'
              data-shipping='<?php echo $price["shipping_cost"];?>'
              data-description='<?php echo addslashes($price["description"]);?>'>
            <?php 
                    $total = (double)$price["price"] + (double)$price["shipping_cost"];
                    $description = "$" . $total . " - ";
                    $description .= ((double)$price["shipping_cost"] > 0) ?
                        "$" . $price["price"] . " + $" . $price["shipping_cost"] : 
                        "$" . $price["price"];
                    $description .= " " . $price["description"];
                    echo $description;
            ?>
            </option>
            <?php }
            } ?>
          </select>
        </div>
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="exampleModalLabel">Edit Price</h4>
              </div>
              <div class="modal-body">
                  <input type="hidden" id="edited_price_id"/>
                  <div class="form-group">
                    <label for="edited_price" class="control-label">Price</label>
                    <input type="text" class="form-control" id="edited_price">
                  </div>
                  <div class="form-group">
                    <label for="edited_shipping" class="control-label">Shipping Cost</label>
                    <input type="text" class="form-control" id="edited_shipping">
                  </div>
                  <div class="form-group">
                    <label for="edited_price_desc" class="control-label">Description</label>
                    <input type="text" class="form-control" id="edited_price_desc">
                  </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="edit_price">Save</button>
              </div>
            </div>
          </div>
        </div>
      </div>
  </div>
</div>
<div id="save_button">
    <?php if (intval($item["id"]) > 0) {?>
    <input type="button" id="delete_button" class="btn btn-danger" value="Delete This Item"/>
    <?php } ?>
    <input type="submit" id="save_button" class="btn btn-success" value="Save My Changes!"/>
</div>
<?php
    }
?>
</form>
<script>

    $("button.view-image").click(function (event) {
        target = $(this).data("target");
        imageUrl = "../" + $("input[name=" + target + "]").val();
        window.open(imageUrl, 'image_detail', '');
        return false;
    });

    $("button.delete-image").click(function (event) {
        target = $(this).data("target");
        imageUrl = $("input[name=" + target + "]").val();
        var input = confirm("Are you sure you want to delete this image?  File will be removed from the server once you save your changes ...");
        if (input == true) {
            $("input[name=" + target + "]").val("");
            var imageContainer = $("#" + target);
            imageContainer.html("Drag & Drop<br/>Image Here");
            imageContainer.addClass("dragandrophandler");
            prepDragDropHandler();
            var imageButtonContainer = $("#" + target.replace("_file", "_buttons"));
            imageButtonContainer.hide();
        }
        return false;
    });

    $("#item_edit").submit(function (event) {
        //alert( "Handler for .submit() called." );
        //event.preventDefault();

        selectBox = document.getElementById("prices");
        for (var i = 0; i < selectBox.options.length; i++) {
            selectBox.options[i].value =
                selectBox.options[i].getAttribute("data-id") + ":" +
                selectBox.options[i].getAttribute("data-price") + ":" +
                selectBox.options[i].getAttribute("data-shipping") + ":" +
                selectBox.options[i].getAttribute("data-description") + ";";
            selectBox.options[i].text = selectBox.options[i].value;
            selectBox.options[i].selected = true;
        }

    });

    $("#delete_button").click(function (event) {
        itemId = $("input[name=itemId]").val();
        var input = confirm("Are you sure you want to delete this item?  Item and associated images will be deleted and action cannot be undone.");
        if (input == true) {
            location.href = "delete.php?id=" + itemId;
        }
        return false;
    });

    function sendFileToServer(formData, imageDiv, fileName) {
        var uploadURL = "http://localhost:49463/gary/admin/uploader.php"; //Upload URL
        var extraData = {}; //Extra Data.
        var jqXHR = $.ajax({
            xhr: function () {
                var xhrobj = $.ajaxSettings.xhr();
                if (xhrobj.upload) {
                    xhrobj.upload.addEventListener('progress', function (event) {
                        var percent = 0;
                        var position = event.loaded || event.position;
                        var total = event.total;
                        if (event.lengthComputable) {
                            percent = Math.ceil(position / total * 100);
                        }
                        //Set progress
                        //status.setProgress(percent);
                    }, false);
                }
                return xhrobj;
            },
            url: uploadURL,
            type: "POST",
            contentType: false,
            processData: false,
            cache: false,
            data: formData,
            success: function (data) {
                var imageContainer = $("#" + imageDiv);
                imageContainer.html("");
                imageContainer.append($("<img/>")
                //    .attr('style', 'max-width: 250px')
                    .attr('src', '../images/upload/' + fileName));
                imageContainer.append($("<input/>")
                    .attr('type', 'hidden')
                    .attr('name', imageDiv + '_upload')
                    .attr('value', '../images/upload/' + fileName));
                imageContainer.append($(
                    "<div class=\"image_upload_text\">" +
                    "<a href=\"../images/upload" + fileName + "\" target=\"_blank\">" +
                    "View Full Size</a></div>"));
                imageContainer.removeClass("dragandrophandler");
            }
        });

        //status.setAbort(jqXHR);
    }

    function handleFileUpload(files, obj) {

        for (var i = 0; i < files.length; i++) {
            var fd = new FormData();
            fd.append('file', files[i]);

            //var status = new createStatusbar(obj); //Using this we can set progress.
            //status.setFileNameSize(files[i].name, files[i].size);
            sendFileToServer(fd, obj.attr("id"), files[i].name);

        }
    }
    $(document).ready(function () {

        prepDragDropHandler();

    });

    function prepDragDropHandler() {

        var obj = $(".dragandrophandler");
        obj.on('dragenter', function (e) {
            e.stopPropagation();
            e.preventDefault();
            $(this).css('border', '2px solid #0B85A1');
        });
        obj.on('dragover', function (e) {
            e.stopPropagation();
            e.preventDefault();
        });
        obj.on('drop', function (e) {

            $(this).css('border', '2px dotted #0B85A1');
            e.preventDefault();
            var files = e.originalEvent.dataTransfer.files;

            //We need to send dropped files to Server

            handleFileUpload(files, $(this));
        });
        $(document).on('dragenter', function (e) {
            e.stopPropagation();
            e.preventDefault();
        });
        $(document).on('dragover', function (e) {
            e.stopPropagation();
            e.preventDefault();
            obj.css('border', '2px dotted #0B85A1');
        });
        $(document).on('drop', function (e) {
            e.stopPropagation();
            e.preventDefault();
        });

    }
</script>


<script type="text/javascript">

    $('#exampleModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) // Button that triggered the modal
        var action = button.data('action');
        var modal = $(this);
        var message = "Add Price";
        if (action == 'edit') {
            if ($("#prices")[0].selectedIndex < 0) {
                alert("Please select a price before continuing");
                modal.modal('hide');
            } else {
                var selectedPrice = $("#prices option:selected");
                message = "Edit Price";
                modal.find('.modal-body input#edited_price_id').val(selectedPrice.data('id'));
                modal.find('.modal-body input#edited_price').val(selectedPrice.data('price'));
                modal.find('.modal-body input#edited_shipping').val(selectedPrice.data('shipping'));
                modal.find('.modal-body input#edited_price_desc').val(selectedPrice.data('description'));
            }
        } else {
            $("#prices")[0].selectedIndex = -1;
            modal.find('.modal-body input#edited_price_id').val('');
            modal.find('.modal-body input#edited_price').val('');
            modal.find('.modal-body input#edited_shipping').val('');
            modal.find('.modal-body input#edited_price_desc').val('');
        }
        modal.find('.modal-title').text(message)
    });

    $("#delete_selected_price").click(function () {
        var modal = $('#exampleModal');
        if ($("#prices")[0].selectedIndex < 0) {
            alert("Please select a price before continuing");
            modal.modal('hide');
        } else {
            $("#prices option:selected").remove();
        }
    });

    $("#edit_price").click(function () {
        var modal = $('#exampleModal');
        var id = modal.find('.modal-body input#edited_price_id').val();
        var price = modal.find('.modal-body input#edited_price').val();
        var shipping = modal.find('.modal-body input#edited_shipping').val();
        var desc = modal.find('.modal-body input#edited_price_desc').val();
        var option = "";
        if (id != '') {
            var option = $("#prices").find("[data-id='" + id + "']");
            option.attr('data-price', price);
            option.attr('data-shipping', shipping);
            option.attr('data-description', desc);
            option.text("$" + (Number(price) + Number(shipping)) + " - $" + price + "/$" + shipping + " " + desc);
        } else {
            $("#prices")
                .append($("<option></option>")
                .attr('data-id', '0')
                .attr('data-price', price)
                .attr('data-shipping', shipping)
                .attr('data-description', desc)
                .text("$" + (Number(price) + Number(shipping)) + " - $" + price + "/$" + shipping + " " + desc));
        }
        modal.modal('hide');
    });

    </script>

    </body>
</html>
