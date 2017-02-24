<?php
if (IN_ADMIN !== true) {
    die("Error.");
}

$update_success = 0;


// Handle updating
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !is_empty($_POST['msg'])) {
    // Validate input
    if (is_empty($VARS['lat']) || is_empty($VARS['long'])) {
        $database->insert('messages', ['#time' => 'NOW()', 'message' => $_POST['msg'], 'uuid' => null]);
        $update_success = 1;
    } else {
        if (!preg_match('/-?[0-9]{1,3}\.[0-9]{2,}/', $VARS['lat'])) {
            $update_success = -1;
            $update_msg = "Latitude (lat) is in the wrong format.";
        }
        if (!preg_match('/-?[0-9]{1,3}\.[0-9]{2,}/', $VARS['lon'])) {
            $update_success = -1;
            $update_msg = "Longitude (long) is in the wrong format.";
        }
        if ($update_success == 0) {
            $database->insert('messages', ['#time' => 'NOW()', 'message' => $_POST['msg'], 'lat' => $VARS['lat'], 'long' => $VARS['lon'], 'uuid' => null]);
            $update_success = 1;
        }
    }
}
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Server Alerts <span class="pull-right"><small><?php echo date('h:i:s a'); ?></small></span></h1>
    </div>
</div>

<?php
if ($update_success == -1) {
    ?>
    <div class="alert alert-dismissable alert-danger">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <i class="fa fa-times"></i> An error occurred: <?php echo $update_msg; ?>
    </div>
    <?php
} else if ($update_success == 1) {
    ?>
    <div class="alert alert-dismissable alert-success">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <i class="fa fa-check"></i> Action Successful.
    </div>
    <?php
}
?>

<div class="row">
    <div class="panel panel-default">
        <form method="POST">
            <div class="panel-body">
                <label for="msg">Message</label>
                <input type="text" name="msg" class="form-control" required="yes" /><br />
                <label for="lat">Latitude/Longitude (optional, message will be broadcast globally if one or both are empty)</label>
                <input type="text" name="lat" class="form-control" placeholder="Latitude" /><br />
                <input type="text" name="lon" class="form-control" placeholder="Longitude" /><br />
            </div>
            <div class="panel-footer">
                <input type="submit" class="btn btn-primary" value="Send Broadcast"/>
            </div>
        </form>
    </div>
</div>