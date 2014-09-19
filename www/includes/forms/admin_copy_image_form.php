<?php

$module = G::$pageData->getCurrentAdminModule();
$action = $module->getAction();
//hidden form

?>
<form method="POST" id="copy_image_form" accept-charset="utf-8">
    <table class="nodes_list_table">
        <thead>
            <tr>
                <th>&nbsp;</th>
                <th>New image</th>
                <th>Image file name</th>
                <th>Press the buttons to upload the image</th>
                <th>Upload status</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td width="30px">#</td>
                <td width="50px"><img src="/images/catalogue/medium/nophoto.jpg" class="admin_preview_mini" name="preview" alt="" /></td>
                <td width="250px"><input type="text" name="filename" style="width:90%;" />.JPG</td>
                <td>
                    <button type="button" id="button_upload_image">Upload NEW image</button>
                    &nbsp;
                    <button type="submit">Apply</button>
                </td>
                <td name="errors_field"></td>
            </tr>
        </tbody>
    </table>
    <input type="hidden" name="img_filename" />
    <input type="hidden" name="image_type" value="<?php echo $action;?>"/>
</form>