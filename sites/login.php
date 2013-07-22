<?php
//Get saved options
$devOptions = getOptions();

//display api warnings
display_warnings();
?>

<br/><br/> 

<div id="normal-sortables">
    <div id="poststuff" class="postbox" style="width: 50%;">
        <div class="handlediv" title="Zum umschalten klicken"><br></div>
        <h3 class="hndle"><span>Create Your Clickworker Account<span style="position:relative;"><a href="http://localhost:8888/wp-admin/admin.php?page=fb-like-settings#editorwidget"></a></span></span></h3>
        <div class="inside">
            <br/>
            Please register as a client at Clickworker in order to get your password and to place an order.
            <div class="clear"></div>
            <div class="submit">
                <a target="_new" href="https://<?php echo CW_SERVER; ?>/marketplace/customers/new">
                <input type="submit" name="save" accesskey="p" id="adminOptionsSubmit" value="Register now" class="button-primary" name="adminOptionsSubmit" value="','" />
                </a>
            </div>
            <div class="clear"></div>
        </div>
    </div>
</div>

<div id="normal-sortables" class="meta-box-sortables ui-sortable">
    <div id="poststuff" class="postbox" style="width: 50%;">
        <div class="handlediv" title="Zum umschalten klicken"><br></div>
        <h3 class="hndle"><span>Login<span style="position:relative;"><a href="http://localhost:8888/wp-admin/admin.php?page=fb-like-settings#editorwidget"></a></span></span></h3>
        <div class="inside">
            <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
            	<?php 
            		if(CW_SERVER == "localhost") 
            			$protocol = "http"; 
            		else 
            			$protocol = "https";
            	?>
                This is version <?php echo VERSION; ?> | <a href="<?php echo "$protocol://".CW_SERVER."/marketplace/payments/new"; ?>" target="_new">Charge your Account</a><br/>

                <?php
                if (!empty($customer)) {
                    echo 'Status: <span style="color: green;">Active</span><br />';
                }
                ?>
                <br/>
                
                <strong>Username:</strong><br/>
                <input type="text" id="clickworker_username" autocomplete="off" name="clickworker_username" value="<?php if(!empty($devOptions['clickworker_username'])){ echo $devOptions['clickworker_username']; }else{echo "";}?>" /><br/>
                <strong>Password:</strong><br/>
                <input type="password" id="clickworker_password" autocomplete="off" name="clickworker_password" value="<?php if(!empty($devOptions['clickworker_username'])){echo $devOptions['clickworker_password'];}else{echo "";} ?>" /><br/>
                <input type="checkbox" id="clickworker_lowcredits" name="clickworker_lowcredits" value="true" <?php
                if ($devOptions["clickworker_lowcredits"] == 'true') {
                    echo "checked";
                }
                ?>/>
                Alert me when credits are low<br/>

                <br/>
                You don’t have an account yet? <a href="https://<?php echo CW_SERVER; ?>/marketplace/customers/new" target="_new">Register now.</a><br/>
                <br/>
                <input type="submit" name="adminOptionsSubmit" accesskey="p" id="adminOptionsSubmit"  class="button-primary" name="adminOptionsSubmit" value="Login" />
            </form>
            <div class="clear"></div>
        </div>
    </div>
</div>
<?php
?>
