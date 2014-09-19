<?php
# MCatalog

$mModule = G::$pageData->getCurrentAdminModule();
$categories = $mModule->getProp("categoriesTree");

/*
echo "<pre>";
print_r($categories);
echo "</pre>";
die();
*/


function renderElement($data, $prefix){
    global $pageData;

    if ($data["type"] > 1){
        return;
    }

    $address = $pageData->getAddress();
    $prefix .= "&mdash;&nbsp;";

    ?>
    <option value="<?php echo $data["id"]; ?>">
        <?php echo $prefix . $data["title"]; ?>
    </option>
    <?php echo ($data["children"][0]["children"] ? "" : "(".count($data["children"]).")") ?>
    <?php

    foreach($data["children"] as $child){
        renderElement($child, $prefix);
    }
    return;
}

?>
Add CATEGORY:<br />
<form method="POST">
    Name:
    <input type="text" name="title" /><br />
    Label:
    <input type="text" name="label" /><br />
    Parent category:
    <select name="parent">
        <option value="1">Root</option>
        <?php
        foreach($categories["children"] as $category){
            renderElement($category, "");
        }        ?>
        <option></option>
    </select><br />
    <input type="submit" name="submit" value="Send" />
</form>
