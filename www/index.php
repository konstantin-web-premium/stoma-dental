<?php
// secure access through index only
define("A", true);

include $_SERVER["DOCUMENT_ROOT"] . "/bin/Main.php";
?>
<!DOCTYPE html>
<html>
<head>
    <?php

    G::render(BLOCK_HEAD); // -------------------------------------------------

    ?>
</head>
<body>
<div class="page-wrapper">
    <?php

    G::render(BLOCK_OVERTOP); // -------------------------------------------------

    ?>
    <div class="header-wrapper">
        <!-- header_block _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ -->
        <div class='wrapper-inner-container'>
            <div class='header-content'>
                <div class="top-menu">
                    <?php

                    G::render(BLOCK_TOP_MENU); // -------------------------------------------------

                    ?>
                </div>
                <div class="header-middle-container">
                    <img src="/images/header/logo.png" class="header-logo" alt="logo" />
                    <div class="header-title-container">
                        <img src="/images/header/sd_title.png" alt="Stoma-Dental"/>
                        <h1><?php echo G::$language->getText("common", "under_logo_text");?></h1>
                    </div>
                    <?php

                    G::render(BLOCK_SEARCH); // -------------------------------------------------

                    ?>
                    <?php

                    G::render(BLOCK_SCART); // -------------------------------------------------

                    ?>
                </div>
                <?php

                G::render(BLOCK_MIDDLE_MENU); // -------------------------------------------------

                ?>
            </div>
        </div>
    </div>

    <?php

    G::render(BLOCK_CAROUSEL); // -------------------------------------------------

    ?>


    <div class="wrapper-inner-container">
        <div class="body">
            <div class="clear">
            </div>
            <?php

            G::render(BLOCK_PRE_CONTENT); // -------------------------------------------------

            ?>
            <!-- content_block - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
            <div class="content-block-container">
                <?php

                G::render(BLOCK_SIDEBAR_LEFT); // -------------------------------------------------

                ?>

                <div class="content-block">
                    <!-- CONTENT -->
                    <?php

                    G::render(BLOCK_CONTENT); // -------------------------------------------------

                    ?>
                </div>
                <!-- // -->
                <?php

                G::render(BLOCK_SIDEBAR_RIGHT); // -------------------------------------------------

                ?>
            </div>
        </div>
        <!-- footer_block _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ -->

    </div>

    <div class="footer-top-wrapper">
        <div class="wrapper-inner-container">
            <div class="footer-top">
                <?php

                G::render(BLOCK_FOOTER); // -------------------------------------------------

                ?>
            </div>
        </div>
    </div>
    <div class="footer-bottom-wrapper">
        <div class="wrapper-inner-container">
            <div class="footer-bottom">
                <?php

                $text_company = G::$language->getText("common","company_name_full");
                echo "$text_company&#160;&#169;&#160;2013-" . date("Y") ;

                ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>
