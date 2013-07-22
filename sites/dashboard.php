<?php
if (!empty($customer)) {
    $balance = $customer['customer_response']['customer']['balance_amount'];
    $currency = $customer['customer_response']['customer']['currency_code'];
    $username = $customer['username'];
}

display_warnings();
?>

<h2>Dashboard</h2>

<div id="normal-sortables">

    <div id="poststuff" class="postbox" style="width: 50%;">



        <div class="handlediv" title="Zum umschalten klicken"></div>

        <h3 class="hndle"><span>My Account<span style="position:relative;"><a href="http://localhost:8888/wp-admin/admin.php?page=fb-like-settings#editorwidget"></a></span></span></h3>

        <div class="inside">

            <label for="current_balance"> 

                <strong>Account: </strong><?php echo $username; ?><br/>

                <strong>Status: </strong><?php
                    if (!empty($customer)) {
                        echo '<span style="color: green;">Active</span><br />';
                    } else {
                        echo '<span style="color: red;">Inactive</span><br />';
                    }
                    ?>

                <strong>Current balance: <?php
                if (!empty($customer)) {
                    echo " " . $customer['customer_response']['customer']['balance_amount'] . " " . $customer['customer_response']['customer']['currency_code'];
                }
?> </strong> <a href="<?php echo "https://".CW_SERVER."/en/marketplace/payments/new"; ?>" target="_new">Charge your Account</a><br/>

            </label>

        </div>

    </div>

</div>





<div id="normal-sortables">

    <div id="poststuff" class="postbox" style="width: 50%;">



        <div class="handlediv" title="Zum umschalten klicken"><br></div>

        <h3 class="hndle"><span>Order SEO Texts Now<span style="position:relative;"><a href="<?php echo $_SERVER["REQUEST_URI"]; ?>_order"></a></span></span></h3>

        <div class="inside">

            <br/>

            First click on "order now" to get to the order form. Here you can put your order online in just a few steps. In each order you can have one SEO text written, with up to three keywords in dozens of different languages.The texts will be written according to your exact specifications by a qualified freelance author from our Crowd and screened for duplicates. The text will also be quality controlled if requested. 

            <div class="clear"></div>

            <div class="submit">
                <a href="<?php echo $_SERVER["REQUEST_URI"]; ?>_order">
                    <input type="submit" name="save" accesskey="p" id="adminOptionsSubmit" value="Order now" class="button-primary" name="adminOptionsSubmit" value="','" />
                </a>
            </div>

            <div class="clear"></div>

        </div>

    </div>

</div>



<div id="normal-sortables">

    <div id="poststuff" class="postbox" style="width: 50%;">



        <div class="handlediv" title="Zum umschalten klicken"><br></div>

        <h3 class="hndle"><span>Our Price Table<span style="position:relative;"><a href="<?php echo $_SERVER["REQUEST_URI"]; ?>_prices"></a></span></span></h3>

        <div class="inside">

            <br/>

            Please find an overview of our current prices for SEO Texts on our price table. 

            <div class="clear"></div>

            <div class="submit">
                <a href="http://www.clickworker.com/en/unique-seo-content/" target="_new">
                    <input type="submit" name="save" accesskey="p" id="adminOptionsSubmit" value="See Prices" class="button-primary" name="adminOptionsSubmit" value="','" />
                </a>
            </div>

            <div class="clear"></div>

        </div>

    </div>

</div>



<div id="normal-sortables">

    <div id="poststuff" class="postbox" style="width: 50%;">



        <div class="handlediv" title="Zum umschalten klicken"><br></div>

        <h3 class="hndle"><span>Charge your Account<span style="position:relative;"><a href="<?php echo $_SERVER["REQUEST_URI"]; ?>_charge"></a></span></span></h3>

        <div class="inside">

            <br/>

            You need to have sufficient funds available in your Clickworker account to submit projects. 
<br/>
             The account can be recharged in your customer area under the menu item "Account" / "Charge Account"
<br/>
            Clickworker supports payment using wire transfer, credit card, direct debit and PayPal.


            <div class="clear"></div>

            <div class="submit">
                <a href="<?php echo $_SERVER["REQUEST_URI"]; ?>_charge">
                    <input type="submit" name="save" accesskey="p" id="adminOptionsSubmit" value="Charge Account" class="button-primary" name="adminOptionsSubmit" value="','" />
                </a>
            </div>

            <div class="clear"></div>

        </div>

    </div>

</div>



<div id="normal-sortables">

    <div id="poststuff" class="postbox" style="width: 50%;">



        <div class="handlediv" title="Zum umschalten klicken"><br></div>

        <h3 class="hndle"><span>Order Status<span style="position:relative;"><a href="<?php echo $_SERVER["REQUEST_URI"]; ?>_status"></a></span></span></h3>

        <div class="inside">

            <br/>

            You will get a notice via e-mail as soon as the text is done. Every text ordered is shown under "Order Status Overview" with its actual order status. Texts with the order status “finished” are ready to be accepted by you and linked to a new wordpress page. 

            <div class="clear"></div>

            <div class="submit">
                <a href="<?php echo $_SERVER["REQUEST_URI"]; ?>_status">
                    <input  type="submit" name="save" accesskey="p" id="adminOptionsSubmit" value="See Order Status" class="button-primary" name="adminOptionsSubmit" value="','" />
                </a>
            </div>

            <div class="clear"></div>

        </div>

    </div>

</div>

<?php ?>