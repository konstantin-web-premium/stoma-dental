<?php
$text_exit = G::$language->getText("common", "exit_text");
$text_enter = G::$language->getText("common", "enter_text")
?>
<div class="basket-and-auth">
    <div class="monitor-block">
        <p>
            <?php

            if (G::$user->isAuthorized()){

            ?>
            <a href="/cabinet" title="<?php echo G::$user->data["seniority"]; ?>"><?php echo G::$user->data["name"]; ?></a><br />
            <div class="exit-link">
            [&nbsp;<a href="/logout" ><?php echo $text_exit; ?></a>&nbsp;]
            </div>
            <?php

            }else{

            ?>
                <div class="nonauth-enter">
                    <a href="javascript:popUpShow()">
                        <?php echo $text_enter;?>&nbsp;<img src="/images/icons/key_icon.png" />
                    </a>
                </div>

                <div class="login-popup" id="popup1">
                    <div class="login-popup-content">

                        <div class="close-popup-button">
                            <form action="javascript:popUpHide()">
                                <button type="submit" class="button-css-blue">X</button>
                            </form>
                        </div>

                        <?php include($_SERVER["DOCUMENT_ROOT"] . PATH_INCLUDES . "forms/login_form.php"); ?>
                    </div>
                </div>

            <?php
                //include (PATH_INCLUDES."forms/login_form.php");
            }

            ?>
        </p>
        <hr align="center" noshade />
        <?php

        if (G::$pageData->isVisibleBlock(SCART)){

            #TODO CART

         ?>
            Cart's empty
            <!--div class="order-link">
                <a href="">Make order</a>
            </div-->
            <?php

        }

        # TODO s-cart-block

        ?>
    </div>

    <!--div class="s-cart-block">
        There are<br />
        2 items<br />
        32 000 000 UAH
    </div-->
</div>