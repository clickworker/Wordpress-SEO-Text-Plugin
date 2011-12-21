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

<h2>Assignments</h2>
<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]?>&noheader=true" >
<table class="wp-list-table widefat fixed posts" cellspacing="0"  style="width: 50%;">



    <thead>

        <tr>

            <th scope="col" id="title" class="manage-column column-title sortable desc" style=""><a href="http://localhost:8888/wp-admin/edit.php?orderby=title&amp;order=asc"><span>titel</span><span class="sorting-indicator"></span></a></th>

            <th scope="col" id="id" class="manage-column column-categories sortable desc" style=""><a href="http://localhost:8888/wp-admin/edit.php?orderby=id&amp;order=asc"><span>Link</span><span class="sorting-indicator"></span></a></th>

            <th scope="col" id="status" class="manage-column column-author sortable desc" style="width:200px;"><a href="http://localhost:8888/wp-admin/edit.php?orderby=author&amp;order=asc"><span>status</span><span class="sorting-indicator"></span></a></th>



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

            echo '<tr id="post-7553" class="alternate author-other status-publish format-default iedit" valign="top">';
            echo "<tr>";
            echo '<td class="post-title page-title column-title"><strong>' . $theTask['customer_ref'] . "</strong></td>";
            echo '<td class="categories column-categories"><a href="'.$link.'">Link</a></td>';
            echo '<td  class="author column-author">' . $theTask['state'] . "</td>";
            echo "</tr>";
        }
    }
} else {
    echo "Error while retrieving API data";
}
?>

    </tbody>

</table>

<br/>
<h2>Customer Jobs</h2>
<?php
$call = cw_command("customer/jobs/", "GET");
if (!empty($call)) {
    $arr = json_decode($call, true);
    $jobs = $arr['jobs_response']['jobs'];
    $divNumber = 0;
    if (count($jobs) > 0) {
        foreach ($jobs as $job) {      
            $call = cw_command($job['link'][0]['href'], "GET","",true);
            $arr = json_decode($call, true);
            ?>
            <br />
            <a  onclick="jQuery('#main<?php echo $divNumber; ?>').toggle();" id="xmain<?php echo $divNumber; ?>">[+]</a> <strong><?php echo $arr['job_response']['job']['items'][0]['content']; ?></strong>
            <div id="main<?php echo $divNumber;
            ++$divNumber; ?>" style="margin-left:1em; display: none;">
                <h3><?php echo $arr['job_response']['job']['items'][0]['content']; ?></h3>
                <span class="margin"><?php echo $arr['job_response']['job']['items'][1]['content']; ?></span><br /><br />
                    <input type="hidden" name="job_id" id="job_id" value="<?php echo $arr['job_response']['job']['link'][0]['href']; ?>" />
                    <input type="hidden" name="post_title" id="post_title" value="<?php echo $arr['job_response']['job']['items'][0]['content']; ?>" />
                    <input type="hidden" name="post_content" id="post_content" value="<?php echo $arr['job_response']['job']['items'][1]['content']; ?>" />
                    <label for="spelling_score">Spelling/Grammar:</label>
                    <select name="spelling_score" id="spelling_score">
                        <option value="0">poor</option>
                        <option value="1">acceptable</option>
                        <option value="2" selected="selected">good</option>                     
                    </select><br />
                    <label for="style_score">Style and Structure:</label>
                    <select name="style_score" id="style_score">
                        <option value="0">poor</option>
                        <option value="1">acceptable</option>
                        <option value="2" selected="selected">good</option>                   
                    </select><br />
                    <label for="topic_score">Topic met?</label>
                    <select name="topic_score" id="topic_score">
                        <option value="0">no</option>
                        <option value="1">more or less</option>
                        <option value="2" selected="selected">yes</option>                  
                    </select><br />
                    <label for="comment">Comment:</label>
                    <textarea id="comment" name="comment"></textarea>
                    <br />                    
                    <input type="submit" name="accept" id="accept" value="Accept" />
                    <input type="submit" name="reject" id="reject" value="Reject" />
                
            </div>

            <?php
        }
    }
} else {
    echo "Error while retrieving API Data";
}



/*



  <table class="wp-list-table widefat fixed posts" cellspacing="0"  style="width: 50%;">



  <thead>

  <tr>

  <th scope="col" id="title" class="manage-column column-title sortable desc" style=""><a href="http://localhost:8888/wp-admin/edit.php?orderby=title&amp;order=asc"><span>Titel</span><span class="sorting-indicator"></span></a></th>

  <th scope="col" id="author" class="manage-column column-author sortable desc" style=""><a href="http://localhost:8888/wp-admin/edit.php?orderby=author&amp;order=asc"><span>Status</span><span class="sorting-indicator"></span></a></th>

  <th scope="col" id="categories" class="manage-column column-categories sortable desc" style=""><a href="http://localhost:8888/wp-admin/edit.php?orderby=author&amp;order=asc"><span>Datum</span><span class="sorting-indicator"></span></a></th>

  <th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input type="checkbox"></th>

  </tr>

  </thead>





  <tbody id="cw_price">

  <tr id="post-7553" class="alternate author-other status-publish format-default iedit" valign="top">

  <td class="post-title page-title column-title"><strong>Task Name Lorem Ipsum</strong></td>

  <td class="author column-author">running</td>

  <td class="categories column-categories">10. June 2011</td>

  <th scope="row" class="check-column"><input type="checkbox" name="post[]" value="7553"></th>

  </tr>

  <tr id="post-7553" class="author-other status-draft format-default iedit" valign="top">

  <td class="post-title page-title column-title"><strong>Task Name Lorem Ipsum</strong></td>

  <td class="author column-author">running</td>

  <td class="categories column-categories">10. June 2011</td>

  <th scope="row" class="check-column"><input type="checkbox" name="post[]" value="7553"></th>

  </tr>

  <tr id="post-7553" class="alternate author-other status-publish format-default iedit" valign="top">

  <td class="post-title page-title column-title"><strong>Task Name Lorem Ipsum</strong></td>

  <td class="author column-author">running</td>

  <td class="categories column-categories">10. June 2011</td>

  <th scope="row" class="check-column"><input type="checkbox" name="post[]" value="7553"></th>

  </tr>

  <tr id="post-7553" class="author-other status-draft format-default iedit" valign="top">

  <td class="post-title page-title column-title"><strong>Task Name Lorem Ipsum</strong></td>

  <td class="author column-author">running</td>

  <td class="categories column-categories">10. June 2011</td>

  <th scope="row" class="check-column"><input type="checkbox" name="post[]" value="7553"></th>

  </tr>

  </tbody>



  </table>
 */
?></form>