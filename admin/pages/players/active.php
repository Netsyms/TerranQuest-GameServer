<?php
if (IN_ADMIN !== true) {
    die("Error.");
}

$update_success = 0;

// Handle updating
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !is_empty($_POST['msg']) && !is_empty($_POST['uuid'])) {
    if (!$database->has("players", ['uuid' => $_POST['uuid']])) {
        $update_success = -1;
    } else {
        $database->update("players", ["kick" => $_POST['msg']], ["uuid" => $_POST['uuid']]);
        $update_success = 1;
    }
}
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Active Sessions <span class="pull-right"><small><?php echo date('h:i:s a'); ?></small></span></h1>
    </div>
</div>

<?php
if ($update_success == -1) {
    ?>
    <div class="alert alert-dismissable alert-danger" id="kickerrormsg">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <i class="fa fa-times"></i> The user does not exist.
    </div>
    <?php
} else if ($update_success == 1) {
    ?>
    <div class="alert alert-dismissable alert-success" id="kicksuccessmsg">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <i class="fa fa-check"></i> User kicked from the server.
    </div>
    <?php
}
?>

<div class="row">
    <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="player-list">
        <thead>
            <tr>
                <th>Name</th>
                <th>Kick</th>
                <th>Level</th>
                <th>Team</th>
                <th>Ping</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $players = $database->select("players", "*", ['lastping[>]' => date('Y-m-d H:i:s', strtotime('-1 minute'))]);
            foreach ($players as $player) {
                $lastping = date('Y-m-d h:i:s A', strtotime($player['lastping']));
                echo "\n"
                . "            <tr>\n"
                . "                <td>" . $player['nickname'] . "</td>\n"
                . "                <td>\n"
                . "                    <form method='POST'>"
                . "                        <input type='text' class='form-control' name='msg' placeholder='Enter a kick message.' value='" . $player['kick'] . "' />\n"
                . "                        <input type='hidden' name='uuid' value='" . $player['uuid'] . "' />\n"
                . "                        <input type='submit' class='btn btn-warning' value='Kick' />\n"
                . "                    </form>\n"
                . "                </td>\n"
                . "                <td>" . $player['level'] . "</td>\n"
                . "                <td>" . getTeamNameFromId($player['teamid']) . "</td>\n"
                . "                <td>" . $lastping . "</td>\n"
                . "            </tr>\n";
            }
            ?>
        </tbody>
    </table>
</div>
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/dataTables.bootstrap.min.js"></script>
<script>
    $(document).ready(function () {
        $('#player-list').dataTable();
    });
</script>