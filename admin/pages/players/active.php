<?php
if (IN_ADMIN !== true) {
    die("Error.");
}
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Active Sessions <span class="pull-right"><small><?php echo date_tz('h:i:s a'); ?></small></span></h1>
    </div>
</div>

<div class="alert alert-dismissable alert-success" id="kicksuccessmsg" style="display: none;">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <i class="fa fa-check"></i> User <span id="kickusername">undefined</span> kicked from the server.
</div>

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
            $players = $database->select("players", "*", ['lastping[>]' => date('Y-m-d H:i:s', strtotime('-30 seconds'))]);
            foreach ($players as $player) {
                $lastping = date_tz('Y-m-d h:i:s A', $player['lastping']);
                echo "\n"
                . "            <tr>\n"
                . "                <td>" . $player['nickname'] . "</td>\n"
                . "                <td>\n"
                . "                    <input type='text' class='form-control' id='" . $player['uuid'] . "-kickmsg' placeholder='Enter a kick message.' value='" . $player['kick'] . "' />\n"
                . "                    <a class='btn btn-warning' onclick=\"kickPlayer('" . $player['uuid'] . "', '" . $player['nickname'] . "');\">Kick</a>\n"
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

    function kickPlayer(uuid, name) {
        var kick_reason = $('#' + uuid + "-kickmsg").val();
        $.post("kickplayer.php", {
            uuid: uuid,
            msg: kick_reason
        }, function () {
            $('#kickusername').text(name);
            $('#kicksuccessmsg').css('display', 'block');
        });
    }
</script>