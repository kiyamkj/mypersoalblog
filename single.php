<?php
$post_id = 1;
$errors = array();

if(filter_has_var(INPUT_GET, 'post_id')){
    $post_id = (int) filter_input(INPUT_GET, 'post_id', FILTER_SANITIZE_NUMBER_INT);
} else {
    die("No post found!");
}

if(isset($_POST['submit'])){
    $query_insert = "INSERT INTO tbl_comment (POST_ID, date, USER_ID, parent, content, name)
                            VALUES (?, NOW(), ?, 0, ?, ?)";

    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING);

    if(strlen(trim($comment)) == 0){
        $errors[] = "Awesome comment... Please try again, this time with a bit more content.";
    } else {

        $user_id = null;
        if(is_user_logged_in()){
            $user_id = $_SESSION['user_id'];
        }

        $prepare_insert = $db->prepare($query_insert);
        $prepare_insert->bind_param('iiss', $post_id, $user_id, $comment, $name);

        if(!$prepare_insert->execute()){
            $errors[] = "Your comment could not be submitted. Please inform an administrator.";
        } else {
            header("Location: " . $_SERVER['REQUEST_URI']);
        }
    }
}

$query = "SELECT p.*, u.name, u.email, 
            (SELECT COUNT(*) FROM tbl_comment as c WHERE c.POST_ID = p.ID) AS comment_count 
          FROM `tbl_post` AS p
          LEFT JOIN tbl_user AS u ON (p.USER_ID = u.ID)
          WHERE p.ID = $post_id";

$result_post = $db->query($query); 

if($result_post->num_rows==0){
    die("Post does not exsist");
}
$post = $result_post->fetch_assoc();      
$time_single = strtotime($post['date']);
$datetime_single = date('l jS \of F Y h:i:s A', $time_single);
?>
<!-- Post Content -->
<div class="post-preview">
    <a href="#">
        <h2 class="post-title">
        <?php echo $post['title']; ?>
        </h2>
        <h3 class="post-subtitle">
        <?php echo $post['sub_title']; ?>
        </h3>
    </a>
    <p class="post-meta">Posted by <a href="#"><?php echo $post['name']; ?></a> <?php echo $datetime_single; ?></p>
    <p> <?php echo $post['content']; ?></p>
    
</div>
<hr>

<!-- Blog Comments -->
<!-- Comments Form -->
<div class="well">
    <?php
    if(count($errors) > 0){
    ?>
        <div class="bg-danger" style="padding: 10px 5px 2px 0px; margin-bottom: 10px;">
            <ul>
                <?php
                foreach($errors as $error){
                    echo "<li>$error</li>";
                }
                ?>
            </ul>
        </div>
    <?php   
    }
    ?>

    <h4>Leave a Comment:</h4>
    <form role="form" action="index.php?action=single&amp;post_id=<?php echo $post_id;?>" method="POST">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="<?php echo (is_user_logged_in() ? $_SESSION['user_name'] : ''); ?>" class="form-control">
        </div>
        <div class="form-group">
            <label for="comment">Comment</label>
            <textarea class="form-control" name="comment" id="comment" rows="3"></textarea>
        </div>
        <button type="submit" class="btn btn-primary" name="submit">Submit</button>
    </form>
</div>
<hr>
<!-- Posted Comments -->
<!-- Comment -->
<?php

$query_comment = "SELECT * FROM tbl_comment AS c
                    WHERE c.POST_ID= $post_id
                    ORDER BY date DESC";
$result_comment = $db->query($query_comment);

while($row = $result_comment->fetch_assoc()){

$time = strtotime($row['date']);
$datetime = date('l jS \of F Y h:i:s A', $time);
?>
<div class="media" style="min-height: 70px;">
    <a class="pull-left" href="#">
        <img class="media-object" src="http://placehold.it/64x64" alt="">
    </a>
    <div class="media-body" >
        <h4 class="media-heading"><?php echo $row['name'];?>
        <small><?php echo $datetime; ?></small>
        </h4>
        <?php echo $row['content']; ?>
    </div>
</div>
<?php
}
?>
<!-- Comment -->
