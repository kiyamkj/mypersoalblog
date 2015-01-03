<?php
session_start();

include("database.php");
include("utils.php");

if(isset($_GET['action']) && $_GET['action'] == "logout"){
    unset($_SESSION['logged_in']);
    unset($_SESSION['user_id']);
    unset($_SESSION['user_name']);
    unset($_SESSION['user_email']);
    unset($_SESSION['user_rights']);

    session_destroy();
    if (!empty($_SERVER['HTTP_REFERER'])){
        header("Location: ".$_SERVER['HTTP_REFERER']);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>Not another boring blog</title>
        <!-- Bootstrap Core CSS -->
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <!-- Custom CSS -->
        <link href="css/blog-post.css" rel="stylesheet">
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <!-- Navigation -->
        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <div class="container">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    </button>
                </div>
                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        <li>
                            <a href="index.php">Home</a>
                        </li>
                        <li><?php
                                if(is_user_logged_in()){
                                    echo '<a href="index.php?action=logout">Logout</a>';
                                } else {
                                    echo '<a href="index.php?action=login">Login / Register</a>';
                                }
                            ?></li>
                        <li>
                            <a href="#">Contact</a>
                        </li>
                    </ul>
                </div>
                <!-- /.navbar-collapse -->
            </div>
            <!-- /.container -->
        </nav>
        <!-- Page Content -->
        <div class="container">
            <div class="row">
                <!-- Blog Post Content Column -->
                <div class="col-lg-12">
                    <!-- Blog Post -->
                    <!-- Title -->
                    <h1>Not Another Boring Blog!</h1>
                    <!-- Author -->
                    <p class="lead">
                    <?php
                        if(is_user_logged_in()){
                            echo "Welcome, " . $_SESSION['user_name'] . "!";
                        } else {
                    ?>
                        by <a href="#">Kiyalicious</a>
                    <?php
                    }
                    ?>
                    </p>
                    <hr>
                    <!-- Preview Image -->
                    <img class="img-responsive" src="http://placehold.it/1200x300" alt="">
                </div>
            </div>
            <hr>
            <div class= "row">
                <div class= "col-lg-8">
                   <!--- ACTION CONTENT -->
                   <?php
                        $action = "overview";
                        if(isset($_GET['action'])){
                            $action = $_GET['action'];
                        }

                        switch($action){
                            case "single":
                                include("single.php");
                                break;
                            case "login":
                                include("login.php");
                                break;
                            case "overview":
                            default:
                                include("overview.php");
                                break;
                        }
                   ?>
                </div>
                <!-- Blog Sidebar Widgets Column -->
                <div class="col-md-4">
                    <!-- Blog Search Well -->
                    <div class="well">
                        <h4>Blog Search</h4>
                        <div class="input-group">
                            <input type="text" class="form-control">
                            <span class="input-group-btn">
                            <button class="btn btn-default" type="button">
                            <span class="glyphicon glyphicon-search"></span>
                            </button>
                            </span>
                        </div>
                        <!-- /.input-group -->
                    </div>
                    <!-- Blog Categories Well -->
                    <div class="well">
                        <h4>Blog Categories</h4>
                       
                        <div class="row">
                            <div class="col-lg-6">
                                <ul class="list-unstyled">
                                    <?php
                                    $query_category = "SELECT cat.* FROM tbl_category AS cat";
                                    $result_category = $db->query($query_category);

                                    while($row = $result_category->fetch_assoc()){
                                    ?>  
                                        <li><a href="index.php?action=overview&amp;category=<?php echo $row['ID'];?>"><?php echo $row['Name'];?></a></li>
                                    <?php
                                    }
                                    ?>
                                </ul>
                            </div>
                            <div class="col-lg-6">
                                <ul class="list-unstyled">
                                    <?php
                                    $query_post_years = "SELECT YEAR(p.date) as year FROM tbl_post AS p GROUP BY YEAR(p.date) ORDER BY p.date DESC";
                                    $result_post_years = $db->query($query_post_years);

                                    while($row_years = $result_post_years->fetch_assoc()){
                                    ?>
                                        <li><a href="index.php?action=overview&amp;year=<?php echo $row_years['year'];?>">Year <?php echo $row_years['year']; ?></a></li>
                                    <?php
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    <!-- /.row -->
                    </div>
                    <!-- Side Widget Well -->
                    <div class="well">
                        <h4>Popular Posts</h4>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Inventore, perspiciatis adipisci accusamus laudantium odit aliquam repellat tempore quos aspernatur vero.</p>
                    </div>
                </div>
            </div>
            <!-- /.row -->
            <hr>
            <!-- Footer -->
            <footer>
                <div class="row">
                    <div class="col-lg-12">
                        <p>Copyright &copy; Your Website 2014</p>
                    </div>
                </div>
            <!-- /.row -->
            </footer>
        </div>
        <!-- /.container -->
        
        <!-- jQuery -->
        <script src="js/jquery.min.js"></script>
        <!-- Bootstrap Core JavaScript -->
        <script src="js/bootstrap.min.js"></script>
    </body>
</html>

<?php
$db->close();
?>