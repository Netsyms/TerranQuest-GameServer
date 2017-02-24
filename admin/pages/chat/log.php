<?php
if (IN_ADMIN !== true) {
    die("Error.");
}

$update_success = 0;


// Handle updating
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !is_empty($_POST['id'])) {
    if (!$database->has("messages", ['id' => $_POST['id']])) {
        $update_success = -1;
    } else {
        if ($_POST['action'] == "delete") {
            $database->delete("messages", ['id' => $_POST['id']]);
            $update_success = 1;
        }
    }
}
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Chat Log <span class="pull-right"><small><?php echo date('h:i:s a'); ?></small></span></h1>
    </div>
</div>

<?php
if ($update_success == -1) {
    ?>
    <div class="alert alert-dismissable alert-danger">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <i class="fa fa-times"></i> The message does not exist.
    </div>
    <?php
} else if ($update_success == 1) {
    ?>
    <div class="alert alert-dismissable alert-success">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <i class="fa fa-check"></i> Message deleted.
    </div>
    <?php
}
?>

<div class="row">
    <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="chat-list">
        <thead>
            <tr>
                <th>User</th>
                <th>Message</th>
                <th>Time</th>
                <th>Location</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            //$msgs = $database->select("messages", "*");
            $msgs = $database->select('messages', ["[>]players" => ["uuid" => "uuid"]], ['messages.id', 'messages.uuid', 'messages.message', 'messages.time', 'players.nickname', 'messages.lat', 'messages.long'], [
                "ORDER" => "messages.time ASC",
                "LIMIT" => 500
            ]);
            foreach ($msgs as $msg) {
                $time = date('Y-m-d h:i:s A', strtotime($msg['time']));
                echo "\n"
                . "            <tr>\n"
                . "                <td>" . (is_null($msg['nickname']) ? "SYSTEM" : $msg['nickname']) . "</td>\n"
                . "                <td>" . $msg['message'] . "</td>\n"
                . "                <td>" . $time . "</td>\n"
                . "                <td>" . ((is_null($msg['lat']) || is_null($msg['long'])) ? "Global" : $msg['lat'] . ', ' . $msg['long']) . "</td>\n"
                . "                <td>\n"
                . "                    <form method='POST'>"
                . "                        <input type='hidden' name='id' value='" . $msg['id'] . "' />\n"
                . "                        <input type='hidden' name='action' value='delete' />\n"
                . "                        <button type='submit' class='btn btn-danger' ><i class='fa fa-times'></i> Delete</button>\n"
                . "                    </form>\n"
                . "                </td>\n"
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
        $('#chat-list').dataTable({
            order: [2, "desc"]
        });
    });
</script>