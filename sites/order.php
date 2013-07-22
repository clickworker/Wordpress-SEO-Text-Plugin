<script type="text/javascript">

jQuery(document).ready(function(){
	if(jQuery('#cw_seotext_topic').val() == 'Enter topic here'){jQuery('#cw_seotext_topic').attr('style',"color: #c0c0c0; font-wight: normal; width: 400px;");}
	else {jQuery('#cw_seotext_topic').attr('style',"color: #000000; font-wight: bold; width: 400px;");}
	if(jQuery('#cw_seotext_keywords').val() == 'Enter up to three comma separated keywords'){jQuery('#cw_seotext_keywords').attr('style',"color: #c0c0c0; font-wight: normal; width: 400px;");}
	else {jQuery('#cw_seotext_keywords').attr('style',"color: #000000; font-wight: bold; width: 400px;");}
	if(jQuery('#cw_seotext_keywords_min').val() == '1'){jQuery('#cw_seotext_keywords_min').attr('style',"color: #c0c0c0; font-wight: normal; width: 50px;");}
	else {jQuery('#cw_seotext_keywords_min').attr('style',"color: #000000; font-wight: bold; width: 50px;");}
	if(jQuery('#cw_seotext_keywords_max').val() == '5'){jQuery('#cw_seotext_keywords_max').attr('style',"color: #c0c0c0; font-wight: normal; width: 50px;");}
	else {jQuery('#cw_seotext_keywords_max').attr('style',"color: #000000; font-wight: bold; width: 50px;");}
		
});

function pretty_value_onfocus(obj, value, str){
	if(obj.value == str) { 
		obj.value = ''; 
		obj.style.color='#c0c0c0'; 
		obj.style.fontWeight='normal'; 
	}else{
		obj.style.color='#000000'; 
		this.style.fontWeight='bold';
	}
}

function pretty_value_onblur(obj, value, str){
	if(obj.value == '') { 
		obj.value = str; 
		obj.style.color='#c0c0c0'; 
		obj.style.fontWeight='normal'; 
	}else{
		obj.style.color='#000000'; 
		obj.style.fontWeight='bold';
	}
}
</script>

<?php
if (isset($_POST['newTaskSubmit']) || isset($_POST['priceCheck'])) { 
	// If the order form has been submitted
    // Step 1: Attempt to submit assignment to Clickworker. Create a Task and Task Template. Return any errors that may exist (not enough credits, invalid credentials, etc). If no errors, proceed to Step 2
    // if the dropdown is not set to the default option: 
    $words = explode("#", $_POST['cw_seotext_words']);
    
    $minWords = $words[0];
    $maxWords = $words[1];
    $keywords = explode(",", $_POST['cw_seotext_keywords']);
    $keyword_a = trim($keywords[0]);
    $keyword_b = isset($keywords[1]) ? trim($keywords[1]) : null;
    $keyword_c = isset($keywords[2]) ? trim($keywords[2]) : null;
    global $warnings;
    $minKeyWords = $_POST['cw_seotext_keywords_min'];
    $maxKeyWords = $_POST['cw_seotext_keywords_max'];
    $vali_error  = false;

    //Form Validation
    if($minWords == "0"){
    	echo "<div class='updated fade'><p><strong>" . __('Attention!') . "</strong> ".
    			"Please select your text length.</p></div>";
    	$vali_error = true;
    }    
    if (empty($_POST['cw_seotext_topic']) || $_POST['cw_seotext_topic'] == "Enter topic here"){
      echo "<div class='updated fade'><p>
                <strong>" . __('Attention!') . "</strong> " .
                "Please enter a topic for your order.".
            "</p></div>";
       $vali_error = true;
    }
     if (empty($keyword_a) || $keyword_a == "Enter up to three comma separated keywords"){
             echo "<div class='updated fade'><p>
                <strong>" . __('Attention!') . "</strong> " .
                "Enter at least one keyword.".
            "</p></div>";
        $vali_error = true;
    }
    if(empty($_POST['cw_seotext_description'])  ){
       echo "<div class='updated fade'><p>
                <strong>" . __('Attention!') . "</strong> " .
                "The field \"Instruction for the author\" can't be blank, Try to explain the task to the Clickworkers as detailed as possible. At least 20 words.".
            "</p></div>";
        $vali_error = true;
    }
    
    
	if($vali_error == false){
	    	
	    $time = time();
	    if (isset($_POST['qualityControl'])) {
	        $quality = "textcreate_proof_yes";
	    } else {
	        $quality = "textcreate_proof_no";
	    }
	
	    $template = array("task_template" =>
	        array("code" => "tpl_text_create_keywords_" . $_POST['cw_language'] . "_" . $time,
	            "name" => "Wordpress APIClient SEO-Text (" . $_POST['cw_language'] . ")",
	            "titles" => array('en' => $_POST['cw_seotext_topic'],'de' => $_POST['cw_seotext_topic']),
	            "descriptions" => array('en' => $_POST['cw_seotext_description'],'de' => $_POST['cw_seotext_description']),
	            "product" => array("link" => array(
	                    "href" => "/api/marketplace/v2/products/TextCreateWithKeywords",
	                    "rel" => "product",
	                    "type" => "application/json"
	                ),
	                "attributes" => array(
	                    array("code" => "textcreatewithkeywords_language",
	                        "value" => $_POST['cw_language']
	                    ),
	                    array("code" => "textcreatewithkeywords_text_length",
	                        "value" => $_POST['cw_seotext_words']
	                    ),
	                    array("code" => "textcreatewithkeywords_proof_read",
	                        "value" => $quality
	                    )
	                )
	            ),
	            "form" => array("elements" => array(
	                    array("type" => "text_field",
	                        "titles" => array("en" => "Title", "de" => "Titel"),
	                        "item_code" => "title",
	                        "is_output" => false,
	                        "is_mandatory" => true,
	                        "options" => array(
	                            array("code" => "min_length", "value" => 5),
	                            array("code" => "max_length", "value" => 100),
	                        )
	                    ),
	                    array("type" => "text_area",
	                        "titles" => array("en" => "Your Text", "de" => "Ihr Text"),
	                        "item_code" => "seotext",
	                        "is_output" => true,
	                        "is_mandatory" => true,
	                        "options" => array(
	                            array("code" => "min_length", "value" => $minWords),
	                            array("code" => "max_length", "value" => $maxWords),
	                        )
	                    ),
	                    array("type" => "keyword",
	                        "titles" => array('en' => "Keyword A",'de' => "Keyword A"),
	                        "item_code" => "keyword_a",
	                        "is_output" => false,
	                        "is_mandatory" => true,
	                        "options" => array(
	                            array("code" => "reference_slug", "value" => 'seotext'),
	                            array("code" => "min_occurrence_ref", "value" => "keyword_a_min"),
	                            array("code" => "max_occurrence_ref", "value" => "keyword_a_max"),
	                        )
	                    )
	                )
	            )
	        )
	    );
	
	    if ($keyword_b != "") {
	
	        $add = array("type" => "keyword",
	            "titles" => array('en' => "Keyword B",'de' => "Keyword B"),
	            "item_code" => "keyword_b",
	            "is_output" => false,
	            "is_mandatory" => true,
	            "options" => array(
	                array("code" => "reference_slug", "value" => 'seotext'),
	                array("code" => "min_occurrence_ref", "value" => "keyword_b_min"),
	                array("code" => "max_occurrence_ref", "value" => "keyword_b_max"),
	            )
	        );
	
	        array_push($template["task_template"]["form"]["elements"], $add);
	    }
	    if ($keyword_c != "") {
	        $add = array("type" => "keyword",
	            "titles" => array('en' => "Keyword C",'en' => "Keyword C"),
	            "item_code" => "keyword_c",
	            "is_output" => false,
	            "is_mandatory" => true,
	            "options" => array(
	                array("code" => "reference_slug", "value" => 'seotext'),
	                array("code" => "min_occurrence_ref", "value" => "keyword_c_min"),
	                array("code" => "max_occurrence_ref", "value" => "keyword_c_max"),
	            )
	        );
	
	        array_push($template["task_template"]["form"]["elements"], $add);
	    }
	
	    if (isset($_POST['priceCheck'])) {
	        $result = cw_command("prices/create/", "POST", json_encode($template));
	        if (!empty($result)) {
	            $arr = json_decode($result, true);
	            $price = ($arr['price_response']['price']);
	        } else {
	            $price = array('after_tax' => 'error', 'currency' => "");
	        }
	    }
	    if (isset($_POST['newTaskSubmit'])) {
	        $result = cw_command("customer/task_templates/", "POST", json_encode($template));
	        if (!empty($result)) {
	            $arr = json_decode($result, true);
	
	            // c) Create a Task to match the Template
	            $template_id = $arr['task_template_response']['task_template']['link'][0]['href'];
	
	            $task = array("task" => array(
	                    "customer_ref" => $_POST['cw_seotext_topic'] . " " . $time,
	                    "template" => array(
	                        "link" => array(
	                            "href" => "/api/marketplace/v2/customer/task_templates/tpl_text_create_keywords_" . $_POST['cw_language'] . "_" . $time,
	                            "rel" => "task_template",
	                            "type" => "application/json"
	                        )
	                    ),
	                    "input" => array(
	                        "items" => array(
	                            array("code" => "title", "content" => $_POST['cw_seotext_topic']),
	                            array("code" => "keyword_a", "content" => $keyword_a),
	                            array("code" => "keyword_a_min", "content" => $minKeyWords),
	                            array("code" => "keyword_a_max", "content" => $maxKeyWords)  
	                        )
	                    ),
	                    "notifications" => array("event" => "CUSTOMER_INPUT_REQUIRED",
	                        "callback_url" => 'http://notification.example.com',
	                        "callback_method" => "POST",
	                        "payload_format" => "JSON")
	                )
	            );
	
	            if ($keyword_b != "") {
	                $add1 = array("code" => "keyword_b", "content" => $keyword_b);
	                $add2 = array("code" => "keyword_b_min", "content" => $minKeyWords);
	                $add3 = array("code" => "keyword_b_max", "content" => $maxKeyWords);
	                array_push($task["task"]["input"]["items"], $add1, $add2, $add3);
	            }
	            if ($keyword_c != "") {
	                $add1 = array("code" => "keyword_c", "content" => $keyword_c);
	                $add2 = array("code" => "keyword_c_min", "content" => $minKeyWords);
	                $add3 = array("code" => "keyword_c_max", "content" => $maxKeyWords);
	                array_push($task["task"]["input"]["items"], $add1, $add2, $add3);
	            }
	
	            $data = cw_command("customer/tasks/", "POST", json_encode($task));
	            if (!empty($data)) {
	                echo "<div id='message' class='updated fade'><p>Order placed!</p></div>";
	            }
	        }
	    }
    }
    //$arr = json_decode($data, true);
    //var_dump( $arr);
    // For debugging purposes: 
    //echo $template . "<br />< br/>";
    //echo $task;
    // Step 2: Set post title to the value of the "Topic" field
    // Step 3: Add a custom field to the post linking it to the Task + Job ID on Clickworker
    // Step 4: Save the post as a draft, notify the user if everything was completed successfully and inform them not to touch anything in the post.
} else {
    // Check if this post has a valid Clickworker Job or Task ID
    // If no, display the form so the user can create a Job or Task
    // If yes, check if the Task is complete (I believe this is done by checking for a Job associated with the Task. The Job should be "signoff")
    // If incomplete, display a message informing the user that the work is still pending
    // If complete, populate the post with the appropriate data, remove metadata. Disable post button, display a form to grade the assignment. Accept will post the post, otherwise post is deleted + refund is given.
}

?>

<div id="normal-sortables" class="meta-box-sortables ui-sortable">

<?php display_warnings(); ?>
    <div id="poststuff" class="postbox" style="width: 50%;">

        <div class="handlediv" title="Zum umschalten klicken"><br></div>

        <h3 class="hndle"><span>Order SEO Text<span style="position:relative;"><a href="http://localhost:8888/wp-admin/admin.php?page=fb-like-settings#editorwidget"></a></span></span></h3>

        <div class="inside">

            <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">

<?php

if (!isset($_POST['priceCheck']) || $vali_error) {

	$language = isset($_POST['cw_language']) == true ? $_POST['cw_language']: "en";

?>
                    <div style="width: 500px; ">
                        <strong>Language:</strong><br/>
                        <select id="cw_langage" name="cw_language" style="width: 400px; color: #000000; font-weight: bold;">
                            <option value="en" <?php echo is_selected($language, "en");?>>English</option>
                            <option value="de" <?php echo is_selected($language, "de");?>>German</option>
                            <option value="fr" <?php echo is_selected($language, "fr");?>>French</option>                       
                            <option value="es" <?php echo is_selected($language, "es");?>>Spanish</option>
                            <option value="pl" <?php echo is_selected($language, "pl");?>>Polish</option>
                            <option value="pt" <?php echo is_selected($language, "pt");?>>Portuguese</option>
                            <option value="tr" <?php echo is_selected($language, "tr");?>>Turkish</option>
                            <option value="it" <?php echo is_selected($language, "it");?>>Italian</option>
                            <option value="cs" <?php echo is_selected($language, "cs");?>>Czech</option>
                            <option value="nl" <?php echo is_selected($language, "nl");?>>Dutch</option>
                            <option value="ru" <?php echo is_selected($language, "ru");?>>Russian</option>
                            <option value="hu" <?php echo is_selected($language, "hu");?>>Hungarian</option>
                            <option value="ro" <?php echo is_selected($language, "ro");?>>Romanian</option>
                        </select>
                    </div>

                    <br/>
                    <strong>Enter topic of the text:</strong><br/>

                    <div style="width: 500px;">
                    <?php 
                    	$topic = 'Enter topic here';
                    	if(isset($_POST['cw_seotext_topic']) && trim($_POST['cw_seotext_topic']) != "")
                    		$topic = $_POST['cw_seotext_topic'];                    	
                    ?>
                        <input type="text" name="cw_seotext_topic" id="cw_seotext_topic" value="<?php echo $topic; ?>" onfocus="pretty_value_onfocus(this, '<?php echo $topic; ?>', 'Enter topic here');" onblur="pretty_value_onblur(this, '<?php echo $topic; ?>', 'Enter topic here');" onload="pretty_value_onload(this, '<?php echo $topic; ?>', 'Enter topic here');" style="width: 400px;" />
                    </div>
                    <br/>

                    <strong>Select your text length:</strong><br/>

                    <div style="width: 500px;">
					<?php 
						$length = "0";
						if(isset($_POST['cw_seotext_words']) && trim($_POST['cw_seotext_words']) != "") 
							$length = $_POST['cw_seotext_words']; 
					?>
                        <select id="cw_seotext_words" name="cw_seotext_words" style="width: 400px; color: #000000; font-weight: bold;">
                            <option value="0" <?php echo is_selected($length, "0"); ?>>Length of Text</option>
                            <option value="10#55" <?php echo is_selected($length, "10#55"); ?>>&lt; 50 words</option>
                            <option value="50#110" <?php echo is_selected($length, "50#110"); ?>>51 - 100 words</option>
                            <option value="100#160" <?php echo is_selected($length, "100#160"); ?>>101 - 150 words</option>
                            <option value="151#205" <?php echo is_selected($length, "151#205"); ?>>151 - 200 words</option>
                            <option value="201#255" <?php echo is_selected($length, "201#255"); ?>>201 - 250 words</option>
                            <option value="251#305" <?php echo is_selected($length, "251#305"); ?>>251 - 300 words</option>
                            <option value="301#355" <?php echo is_selected($length, "301#355"); ?>>301 - 350 words</option>
                            <option value="351#405" <?php echo is_selected($length, "351#405"); ?>>351 - 400 words</option>
                            <option value="451#505" <?php echo is_selected($length, "451#505"); ?>>451 - 500 words</option>
                            <option value="501#555" <?php echo is_selected($length, "501#555"); ?>>501 - 550 words</option>
                            <option value="551#605" <?php echo is_selected($length, "551#605"); ?>>551 - 600 words</option>
                            <option value="601#710" <?php echo is_selected($length, "601#710"); ?>>601 - 700 words</option>
                            <option value="701#810" <?php echo is_selected($length, "701#810"); ?>>701 - 800 words</option>
                            <option value="801#910" <?php echo is_selected($length, "801#910"); ?>>801 - 900 words</option>
                            <option value="901#1010" <?php echo is_selected($length, "901#1010"); ?>>901 - 1000 words</option>
                            <option value="1001#1210" <?php echo is_selected($length, "1001#1210"); ?>>1001 - 1200 words</option>
                        </select>
                    </div>

                    <br/>
                    <strong>Keywords for this text (separated by commas):</strong><br/>

                    <div style="width: 500px; margin-top: 10px;">
                    <?php 
                    	$def_keywords = "Enter up to three comma separated keywords";
                    	if (isset($_POST['cw_seotext_keywords']) && trim($_POST['cw_seotext_keywords']) != ""){
                    		$def_keywords = trim($_POST['cw_seotext_keywords']);}                    		
                    ?>
                        <input type="text" name="cw_seotext_keywords" id="cw_seotext_keywords" value="<?php echo $def_keywords; ?>" onfocus="pretty_value_onfocus(this, '<?php echo $def_keywords; ?>', 'Enter up to three comma separated keywords');" onblur="pretty_value_onblur(this, '<?php echo $def_keywords; ?>', 'Enter up to three comma separated keywords');" style="width: 400px;" />
                    </div>

                    <br/>
                    <strong>Keyword frequency for your text:</strong><br/>

                    <div style="width: 500px;">
                    <?php 
                    	$keyword_min = "1"; $keyword_max = "5";
						if(isset($_POST['cw_seotext_keywords_min']) && trim($_POST['cw_seotext_keywords_min']) != "")
							$keyword_min = trim($_POST['cw_seotext_keywords_min']);
						if(isset($_POST['cw_seotext_keywords_max']) && trim($_POST['cw_seotext_keywords_max']) != "")
							$keyword_max = trim($_POST['cw_seotext_keywords_max']);						
                    ?>
                        <input type="text" name="cw_seotext_keywords_min" id="cw_seotext_keywords_min" value="<?php echo $keyword_min; ?>" onfocus="pretty_value_onfocus(this, '<?php echo $keyword_min; ?>', '1');" onblur="pretty_value_onblur(this, '<?php echo $keyword_min; ?>', '1');" style="width: 50px;" />
                        up to 
                        <input type="text" name="cw_seotext_keywords_max" id="cw_seotext_keywords_max" value="<?php echo $keyword_max; ?>" onfocus="pretty_value_onfocus(this, '<?php echo $keyword_max; ?>', '5');" onblur="pretty_value_onblur(this, '<?php echo $keyword_max; ?>', '5');" style="width: 50px;" />
                    </div>

                    <br/>
                    <strong>Instruction for the author:</strong><br/>

                    <div style="width: 500px;">
                    <?php 
						$descript = "";
						if(isset($_POST['cw_seotext_description']) && trim($_POST['cw_seotext_description']) != "")
							$descript = trim($_POST['cw_seotext_description']);
                    ?>
                        <textarea name="cw_seotext_description" id="cw_seotext_description" style="width: 400px; color: #000000; font-weight: bold;"><?php echo $descript; ?></textarea>
                    </div>

                    <div style="width: 500px; margin-top: 10px;">
                    <?php 
                    	$control = 1;
                    	if(isset($_POST['qualityControl']) && trim($_POST['qualityControl']))
                    		$control = $_POST['qualityControl'];
                    ?>
                        <input type="checkbox" id="qualityControl" checked="checked" name="qualityControl" value="<?php echo $control; ?>" /> Enable Quality Control
                    </div>

                    <br/>
                    <div style="width: 500px; margin-top: 10px;">
                        <input type="submit" id="priceCheck" class="button-primary" name="priceCheck" value="<?php _e('Check Price and place order', 'ClickworkerSEOText'); ?>" />
                    </div>
<?php } 

if (isset($_POST['priceCheck']) && !$vali_error) {

?>

                    <strong>Language</strong><br/>
                    <div style="width: 500px; ">
                        <input type="text" readonly="readonly" id="cw_langage_temp" name="cw_language_temp" value="<?php echo get_language($_POST['cw_language']); ?>" />
                        <input type="hidden" id="cw_langage" name="cw_language" value="<?php echo $_POST['cw_language']; ?>" />
                    </div>

                    <br/>
                    <strong>Topic</strong><br/>

                    <div style="width: 500px;">
                        <input type="text" name="cw_seotext_topic" id="cw_seotext_topic" value="<?php echo $_POST['cw_seotext_topic'] ?>" readonly="readonly" style="width: 400px; color: #c0c0c0; font-weight: bold;" />
                    </div>
                    <br/>

                    <strong>Select your text length:</strong><br/>

                    <div style="width: 500px;">
                        <input type="hidden" id="cw_seotext_words" name="cw_seotext_words" value="<?php echo $_POST['cw_seotext_words'] ?>" readonly="readonly" />
                        <?php echo get_text_length($_POST['cw_seotext_words']); ?>
                    </div>

                    <br/>
                    <strong>Keywords for this text (separated by commas):</strong><br/>

                    <div style="width: 500px; margin-top: 10px;">
                        <input  type="text" name="cw_seotext_keywords" id="cw_seotext_keywords" value="<?php echo $_POST['cw_seotext_keywords'] ?>" readonly="readonly" style="width: 400px; color: #c0c0c0; font-weight: bold;" />
                    </div>

                    <br/>
                    <strong>Keyword frequency for your text:</strong><br/>

                    <div style="width: 500px;">
                        <input readonly="readonly"  type="text" name="cw_seotext_keywords_min" id="cw_seotext_keywords_max" value="<?php echo $_POST['cw_seotext_keywords_min'] ?>" />
                        up to 
                        <input readonly="readonly"  type="text" name="cw_seotext_keywords_max" id="cw_seotext_topic" value="<?php echo $_POST['cw_seotext_keywords_max'] ?>"/>
                    </div>

                    <br/>
                    <strong>Instruction for the author:</strong><br/>

                    <div style="width: 500px;">
                        <textarea readonly="readonly"  name="cw_seotext_description" id="cw_seotext_description" style="width: 400px;"><?php echo $_POST['cw_seotext_description']; ?></textarea>
                    </div>

                    <div style="width: 500px; margin-top: 10px;">
                     <strong> Enable Quality Control:     </strong><br/>
                     <?php if (isset($_POST['qualityControl'])) {
                            echo 'yes';
                            echo '<input type="hidden" id="qualityControl" name="qualityControl" value="1" />';
                        }  else {
                            echo 'no';
                        } ?> 
                    </div>    
                        
                    <hr/>
                    <div class="price">Total:<?php echo $price['after_tax'] . " " . $price['currency']; ?></div>

                    <br/>

                    <div style="width: 500px; margin-top: 10px;">
                        <input type="submit" id="newTaskSubmit" class="button-primary" name="newTaskSubmit" value="<?php _e('Place Order', 'ClickworkerSEOText'); ?>" />
                    </div>
<?php } ?>
            </form>
        </div>
    </div>
</div>   



<?php 
function is_selected($value, $option)
{
	$str = "";
	
	if($value == $option) $str = 'selected="selected"';
	
	return $str;
}

function get_text_length($str){
	$res = "";
	switch($str){
		case "10#55": $res = "&lt; 50 words"; break;
		case "50#110": $res = "51 - 100 words"; break;
		case "100#160": $res = "101 - 150 words"; break;
		case "151#205": $res = "151 - 200 words"; break;
		case "201#255": $res = "201 - 250 words"; break;
		case "251#305": $res = "251 - 300 words"; break;
		case "301#355": $res = "301 - 350 words"; break;
		case "351#405": $res = "351 - 400 words"; break;
		case "451#505": $res = "451 - 500 words"; break;
		case "501#555": $res = "501 - 550 words"; break;
		case "551#605": $res = "551 - 600 words"; break;
		case "601#710": $res = "601 - 700 words"; break;
		case "701#810": $res = "701 - 800 words"; break;
		case "801#910": $res = "801 - 900 words"; break;
		case "901#1010": $res = "901 - 1000 words"; break;
		case "1001#1210": $res = "1001 - 1200 words"; break;
	}
	return $res;
}

function get_language($str){
	$language = "";
	
	switch($str){
		case "en": $language = "English"; break;
		case "de": $language = "German"; break;
		case "fr": $language = "French"; break;
		case "es": $language = "Spanish"; break;
		case "pl": $language = "Polish"; break;
		case "pt": $language = "Portuguese"; break;
		case "tr": $language = "Turkish"; break;
		case "it": $language = "Italian"; break;
		case "cs": $language = "Czech"; break;
		case "nl": $language = "Dutch"; break;
		case "ru": $language = "Russian"; break;
		case "hu": $language = "Hungarian"; break;
		case "ro": $language = "Romanian"; break;
	}
	
	return $language;
}
?>
