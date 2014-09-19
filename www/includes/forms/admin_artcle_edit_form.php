<?php
// id, title, robots, description, keywords, author, copyright, author_url, publisher_url,
// og_title, og_url, og_image, og_description, og_site_name, og_type, og_locale
// pages - id, access, blocks_id, meta_id, label, content
$blocks = G::$pageData->getBlocksList();
?>
<div id="edit_article_errors"></div>

<form class="page-edit-form" id="page_edit_form" method="POST">
    <h3 name="form_title">Edit: </h3>

    <input type="hidden" name="id" value="0"/>

    <div>

        <div class="input-label-block">
            Label
            <span class="required-star">*</span>
            <br >
            <span class="grey-text">http://stoma-dental.com/<?php echo ""; ?></span>
            <input type="text" name="label" />
        </div>

        <div class="input-block" style="display: inline-block">
            Access
            <br />
            <span class="grey-text">show this page to user with seniority <b>equal or higher</b> than</span>
            <select name="access">
                <option value="1">Administrator</option>
                <option value="2">Moderator</option>
                <option value="3">Authorized user</option>
                <option value="4" selected="selected">Guest</option>
            </select>
        </div>

        <div class="input-block">
            Title
            <span class="required-star">*</span>
            <br />
            <input type="text" name="title" />
        </div>

    </div>

    <!-- meta TAGS ------------------------------------------------------ -->
    <div class="marked-block" spoiler="true" title="Meta">
        <h3>Meta tags</h3>

        <input type="hidden" name="meta_id" value="" />

        <div class="input-block">
            robots
            <br />
            <select id="select_robots" name="robots">
                <option>index,follow</option>
                <option>index,nofollow</option>
                <option>noindex,follow</option>
                <option>noindex,nofollow</option>
            </select>
            <span id="select_robots_hint"></span>
        </div>

        <div class="input-block">
            description
            <br />
            <input type="text" name="description" />
        </div>

        <div class="input-block">
            keywords
            <br />
            <input type="text" name="keywords" />
        </div>

        <div class="input-block">
            author
            <br />
            <input type="text" name="author" />
        </div>

        <div class="input-block">
            copyright
            <br />
            <input type="text" name="copyright" />
        </div>

        <div class="input-block">
            author_url
            <br />
            <input type="text" name="author_url" />
        </div>

        <div class="input-block">
            publisher_url
            <br />
            <input type="text" name="publisher_url" />
        </div>

    </div>

    <div class="marked-block" spoiler="true" title="Socials">
        <h3>Socials tags</h3>

        <div class="input-block">
            og:title
            <br />
            <input type="text" name="og_title" />
        </div>

        <div class="input-block">
            og:url
            <br />
            <input type="text" name="og_url" />
        </div>

        <div class="input-block">
            og:image
            <br />
            <input type="text" name="og_image" hint="http://..." />
        </div>

        <div class="input-block">
            og:description
            <br />
            <input type="text" name="og_description" />
        </div>

        <div class="input-block">
            og:site_name
            <br />
            <input type="text" name="og_site_name" />
        </div>

        <div class="input-block">
            og:type
            <br />
            <input type="text" name="og_type" hint="website|article" />
        </div>

        <div class="input-block">
            og:locale
            <br />
            <input type="text" name="og_locale" hint="ru_RU|uk_RU|us_EN|..." />
        </div>

    </div>

    <div class="marked-block" spoiler="true" title="Blocks">
        <h3>Show blocks</h3>

        <ul class="blocks-list">
        <?php

        foreach($blocks as $block){
            $checked = G::$pageData->isVisibleBlock($block["label"]);
            //echo "<li><input type='checkbox' name=\"block_$block[id]\" />&nbsp;$block[title]: <span class=\"grey-text\">$block[description] - ( $block[label] )</span></li>\n";
            echo "<li><div checkbox=\"true\" name=\"block_$block[id]\"></div>&nbsp;$block[title]: <span class=\"grey-text\">$block[description] - ( $block[label] )</span></li>\n";
        }

        ?>
        </ul>
    </div>

    <div class="input-block">
        Content
        <br />
        <textarea type="text" rows="20" cols="100" name="content"></textarea>
    </div>

    <button type="submit" class="button-css-blue">Save</button>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <button type="reset" class="button-css-blue">Reset</button>

</form>
