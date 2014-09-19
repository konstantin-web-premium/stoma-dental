<?php
$mModule = G::$pageData->getCurrentAdminModule();
$categories = $mModule->getProp("categoriesTree");

function renderElement($data, $separator){
    if ($data["type"] > 1){
        return;
    }
    $len = count($data["children"]) | 0;
    // title
    echo "<option value='$data[label]'>$separator$data[title]</option>'n";
    if ($len){
        $separator .= " - ";
        foreach($data["children"] as $child){
            renderElement($child, $separator);
        }
    }
}

?>
<div class="errors-block"></div>

<form id="edit_category_form">
    <input type="hidden" name="id" />

    <div class="input-block">
        Label
        <br />
        <input type="text" name="label" value="" />
    </div>

    <div class="input-block">
        Title
        <br />
        <input type="text" name="title" />
    </div>

    <div class="input-block">
        Parent
        <br />
        <select>
            <option>-Root-</option>
            <?php

            if ($categories && count($categories) > 0){
                $separator = "";
                foreach ($categories["children"] as $category) {
                    renderElement($category, $separator);
                }
            }

            ?>
        </select>
    </div>

    <button type="submit">Save</button>
    <button type="button" name="cancel"> Cancel</button>
</form>