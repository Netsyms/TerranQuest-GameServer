<?php
require_once 'required.php';

if (is_empty(ADMIN_USER) || is_empty(ADMIN_PASS)) {
    die("Admin panel disabled.  Please set a username and password in settings.php.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !is_empty($_POST['username']) && !is_empty($_POST['password'])) {
    if ($_POST['username'] === ADMIN_USER && $_POST['password'] === ADMIN_PASS) {
        $_SESSION['admin'] = true;
    }
}

$page = "login";

if ($_SESSION['admin'] === true && is_empty($_GET['page'])) {
    $page = "dashboard";
} else if ($_SESSION['admin'] === true) {
    $page = $_GET['page'];
} else if ($_SESSION['admin'] !== true) {
    require "login.php";
    die();
}

if ($page == "logout") {
    $_SESSION['admin'] = false;
    session_unset();
    session_destroy();
    require "login.php";
    die();
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

        <title>TerranQuest Admin Panel</title>

        <!-- Bootstrap Core CSS -->
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link href="css/bootstrapXL.css" rel="stylesheet">

        <!-- Custom CSS -->
        <link href="css/sb-admin-2.css" rel="stylesheet">

        <!-- Components -->
        <link href="css/leaflet.css" rel="stylesheet">
        <link href="css/leaflet.markercluster.css" rel="stylesheet">
        <link href="css/metisMenu.min.css" rel="stylesheet">
        <link href="css/dataTables.bootstrap.css" rel="stylesheet">
        <link href="css/dataTables.responsive.css" rel="stylesheet">

        <!-- Custom Fonts -->
        <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css">

        <!-- jQuery -->
        <script src="js/jquery.min.js"></script>
        <!-- Bootstrap Core JavaScript -->
        <script src="js/bootstrap.min.js"></script>
        <!-- Metis Menu Plugin JavaScript -->
        <script src="js/metisMenu.min.js"></script>
        <!-- Custom Theme JavaScript -->
        <script src="js/sb-admin-2.js"></script>
        
        <script src="js/bootstrap3-typeahead.min.js"></script>
        <script src="js/leaflet.js"></script>
        <script src="js/leaflet.markercluster.js"></script>
    </head>

    <body>

        <div id="wrapper">
            <!-- Navigation -->
            <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="./">TerranQuest Admin Panel</a>
                </div>

                <div class="navbar-default sidebar" role="navigation">
                    <div class="sidebar-nav navbar-collapse">
                        <ul class="nav" id="side-menu">
                            <!--<li class="sidebar-search">
                                <div class="input-group custom-search-form">
                                    <input type="text" class="form-control" placeholder="Search by username">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="button">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </span>
                                </div>
                            </li>-->
                            <li>
                                <a href="./?page=dashboard"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                            </li>
                            <li>
                                <a href="#"><i class="fa fa-users fa-fw"></i> Players<span class="fa arrow"></span></a>
                                <ul class="nav nav-second-level">
                                    <li>
                                        <a href="./?page=players">Manage Players</a>
                                    </li>
                                    <li>
                                        <a href="./?page=players&sub=active">Manage Active Sessions</a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="./?page=locations"><i class="fa fa-map-marker fa-fw"></i> Locations</a>
                            </li>
                            <li>
                                <a href="./?page=badges"><i class="fa fa-star fa-fw"></i> Badges</a>
                            </li>
                            <!--<li>
                                <a href="#"><i class="fa fa-cubes fa-fw"></i> Items<span class="fa arrow"></span></a>
                                <ul class="nav nav-second-level">
                                    <li>
                                        <a href="./?page=items">Items</a>
                                    </li>
                                    <li>
                                        <a href="./?page=items&sub=inventory">Inventory</a>
                                    </li>
                                    <li>
                                        <a href="./?page=items&sub=barcodes">Barcodes</a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="#"><i class="fa fa-comments fa-fw"></i> Chat<span class="fa arrow"></span></a>
                                <ul class="nav nav-second-level">
                                    <li>
                                        <a href="./?page=chat&sub=global">Global Log</a>
                                    </li>
                                    <li>
                                        <a href="./?page=chat&sub=regional">Regional Log</a>
                                    </li>
                                </ul>
                            </li>-->
                            <li>
                                <a href="./?page=logout"><i class="fa fa-sign-out fa-fw"></i> Log out</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <div id="page-wrapper">
                <?php
                switch ($page) {
                    case "dashboard":
                        require("pages/dashboard.php");
                        break;
                    case "players":
                        require("pages/players.php");
                        break;
                    case "locations":
                        require("pages/locations.php");
                        break;
                    case "badges":
                        require("pages/badges.php");
                        break;
                    default:
                        require("pages/404.php");
                        break;
                }
                ?>
            </div>

        </div>
    </body>

</html>
