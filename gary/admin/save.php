<?php include '../inc/functions.php'?>
<table>
<?php 

// save form data
    adminSave_manageItem();

echo "<table>";
echo "<tr><td colspan=\"2\">Form Post Data</td></tr>";
    foreach ($_POST as $key => $value) {
        echo "<tr>";
        echo "<td>";
        echo $key;
        echo "</td>";
        echo "<td>";
        if ($key == "prices") {
         foreach ($value as $item)
            {
                echo "$item<br/>";
            }   
        } else {
            echo $value;
        }        
        echo "</td>";
        echo "</tr>";
    }
echo "</table>"




?>
</table>