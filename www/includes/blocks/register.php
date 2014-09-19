<?php

include(PATH_INCLUDES . "forms/registration_form.php");

echo "<div id=\"reg_errors\">\n";

if (isset($_POST['submit'])){
    if (count($user->getErrors()) > 0){
        foreach($user->getErrors() as $error){
            echo RenderUtils::renderError($error);
        }
    }else{
        echo ">>> reg is successfull ! <br />";
    }
}

echo "</div>\n";

?>
