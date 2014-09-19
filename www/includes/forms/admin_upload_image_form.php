<?php

// form require <button type="button" id="button_upload_image" class="button-css-blue">Upload image</button>

?>

<div class="popup-window" id="popup_image_uploader">
    <div class="popup-window-content">
        <form class="file-uploader-form" id="file_uploader_form" method="POST">
            <div name="upload_status"></div>
            <div id="upload_errors"></div>
            <div id="upload_warnings"></div>
            <input style="display: none;" type="file" name="file_browse" />
            <div class="cropper-container">
                <div class="cropper-img"></div>
                <div class="cropper-data">
                    Preview&nbsp;
                    <span class="cropper-rendered-data"></span>
                    <div class="extra-preview"></div>
                    <br />
                    <button type="button" name="cropper_reset">Reset size</button>
                    <button type="button" name="cropper_done">Done</button>
                    <button type="button" name="cancel">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

