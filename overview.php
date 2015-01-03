<?php
$page = 1;
$category = null;
$year = null;

if(filter_has_var(INPUT_GET, 'page')){
    $page = (int) filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT);
}

if(filter_has_var(INPUT_GET, 'category')){
    $category = (int) filter_input(INPUT_GET, 'category', FILTER_SANITIZE_NUMBER_INT);
}

if(filter_has_var(INPUT_GET, 'year')){
    $year = (int) filter_input(INPUT_GET, 'year', FILTER_SANITIZE_NUMBER_INT);
}

$where = "WHERE 1 = 1";

if($category != null){
    $where .= " AND p.Category_ID = " .$category;
}

if($year != null){
    $where .= " AND YEAR(p.date) = " .$year;
}

$per_page = 2;

$query_total_entries = "SELECT COUNT(p.ID) AS count FROM `tbl_post` AS p " .$where;
$result_total_entries = $db->query($query_total_entries);

$total_entries = (int) $result_total_entries->fetch_assoc()['count'];
$total_pages = ceil($total_entries / $per_page);

if($page > $total_pages){
    $page = $total_pages;
} else if($page < 1){
    $page = 1;
}

$start = max($page - 1, 0) * $per_page;

$query = "SELECT p.*, u.name, u.email, 
            (SELECT COUNT(*) FROM tbl_comment as c WHERE c.POST_ID = p.ID) AS comment_count 
          FROM `tbl_post` AS p
          LEFT JOIN tbl_user AS u ON (p.USER_ID = u.ID) " .$where ."
          ORDER BY p.date DESC
          LIMIT ?, ?";

$prepared_statement = $db->prepare($query);
$prepared_statement->bind_param('ii', $start, $per_page);
$prepared_statement->execute();

$result = $prepared_statement->get_result();

while($row = $result->fetch_assoc()){

$time = strtotime($row['date']);
$datetime = date('l jS \of F Y h:i:s A', $time);
?>
    <div class="post-preview">
        <a href="index.php?action=single&amp;post_id=<?php echo $row['ID']; ?>">
            <h2 class="post-title">
            <?php echo $row['title']; ?>
            </h2>
            <h3 class="post-subtitle">
            <?php echo $row['sub_title']; ?>
            </h3>
        </a>
        <p class="post-meta">Posted by <a href="#"><?php echo $row['name']; ?></a> on <?php echo $datetime; ?></p>
        <p><?php 
            echo text_ellipsis(
                    $row['content'], 
                    ' ... <a href="index.php?action=single&post_id=' . $row['ID'] . '">more</a>',
                    500
            ); 
        ?></p>
        <a class="btn btn-primary" href="index.php?action=single&amp;post_id=<?php echo $row['ID']; ?>">
            Comments <span class="badge"><?php echo $row['comment_count']; ?></span>
        </a>
    </div>
    <hr>
<?php
}
?>

<nav>
    <ul class="pagination">
        <?php
        for($i = 1; $i <= $total_pages; $i++){
            echo '<li class="' . ($i == $page ? 'active' : '') . '"><a href="index.php?action=overview&page='.$i.'">'.$i.'</a></li>';
        }
        ?>
    </ul>
</nav>