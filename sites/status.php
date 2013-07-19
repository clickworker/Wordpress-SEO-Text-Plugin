<?php
if (isset($_POST['delete'])) { // If the user is attempting to delete a Task
    // Get task details so we can locate the template
    $data = cw_command($_POST['delete'], "GET");

    $arr = json_decode($data, true);

    // Extract the task template from the task info above

    $template = $arr['task_response']['task']['template']['link'][0]['href'];

    // Delete the task 

    $data = cw_command($_POST['delete'], "DELETE");
    $arr = json_decode($data, true);
    

    // Delete the task template (must be done last as a Task Template can't be deleted unless it isn't in use)

    $data = cw_command($template, "DELETE", "", true);

    if (!empty($data) && !is_wp_error($data)) {
          $arr = json_decode($data, true);
    }
}

if (isset($_POST['accept']) || isset($_POST['reject'])) {

    if (isset($_POST['accept'])) {
        $jobContent = "yes";
        $accepted = 1;
        $cw_post_title = $_POST['post_title'];
        $cw_body_content = $_POST['post_content'];

        // Create Wordpress Post
        // post_status can be changed if we want to have it start out as a draft instead of publishing directly

        global $user_ID;
        $new_post = array(
            'post_title' => $cw_post_title,
            'post_content' => $cw_body_content,
            'post_status' => 'pending',
            'post_date' => date('Y-m-d H:i:s'),
            'post_author' => $user_ID,
            'post_type' => 'post',
            'post_category' => array(0)
        );
        $post_id = wp_insert_post($new_post);
        
       
        
    } else {
        $jobContent = "no";
        $accepted = 0;
    }

    $job_template = array("job" =>
        array("input" =>
            array("items" =>
                array(
                    array("code" => "signoff", "content" => "yes"),
                    array("code" => "accepted", "content" => $accepted),
                    array("code" => "spelling", "content" => $_POST['spelling_score']),
                    array("code" => "style", "content" => $_POST['style_score']),
                    array("code" => "topic", "content" => $_POST['topic_score']),
                    array("code" => "comment", "content" => $_POST['comment'])
                )
            )
        )
    );
   
    // For debug purposes:
    //  echo $job_template . "<br /><br />" . $cw_post_title . "<br /><br />" . $cw_body_content . "<br /><br />" . $job . "<br /><br />";

     $data = cw_command($_POST['job_id'], "PUT", json_encode($job_template), true);
     
     if (isset($_POST['accept'])) {
         wp_redirect( get_edit_post_link($post_id,"&" )); exit();
     }else{
         require_once(ABSPATH . 'wp-admin/admin-header.php');         
     }     
     
}  
     
display_warnings();
?>          

<br/><br/>

<h2>Order Status</h2>
<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]?>&noheader=true" >
<table class="wp-list-table widefat fixed posts" cellspacing="0"  style="width: 50%;">
    <thead>
        <tr>
            <th scope="col" id="title" class="author column-title">Title</th>
            <th scope="col" id="status" class="author column-author" >Status</th>
			<!-- <th scope="col" id="status" class="author column-author" style="width:100px;">Action</th> -->
        </tr>
    </thead>

    <tbody id="cw_price">
<?php
$call = cw_command("customer/tasks/", "GET");

if (!empty($call)) {
    $arr = json_decode($call, true);
    $tasks = $arr['tasks_response']['tasks'];
    if (count($tasks) > 0) {
        foreach ($tasks as $theTask) {
     
            $link =  $theTask['link'][0]['href'];
            $task_id = get_id_from_link($link);

            echo '<tr id="post-7553" class="alternate author-other status-publish format-default iedit" valign="top">';
            echo "<tr>";
            
            echo '<td class="post-title page-title column-title"><strong>' . $theTask['customer_ref'] . "</strong>".
            	"<div style='display: none;' id='task_" . $task_id . "'>" . get_link_for_state($theTask['state'], $task_id) . "</div>" .
            	"</td>";
            echo '<td class="author column-author">' . get_only_link_for_state($theTask['state'], $task_id) . "</td>";
            
            //echo '<td class="author column-author">' . get_download_link_or_blank($theTask['state'], $link) . '</td>';
            
            echo "</tr>";            
        }
    }
} else {
    echo "Error while retrieving API data";
}
?>
    </tbody>
</table>
</form>

<script type="text/javascript">
/*
function download_file(link, id){
	var ifrm = document.getElementById("frame_" + id);
    ifrm.src = path;
}
*/
</script>