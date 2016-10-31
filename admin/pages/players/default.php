<?php
if (IN_ADMIN !== true) {
    die("Error.");
}
?>
<div class="modal fade" tabindex="-1" role="dialog" id="editplayer-modal">
    <div class="modal-dialog" role="document">
        <form class="modal-content" action="editplayer.php" method="POST">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Edit Player <span id="player-name"></span></h4>
            </div>
            <div class="modal-body">
                Level: <input type="number" step="0.00001" class="form-control" id="player-level" name="level" placeholder="Level" /><br />
                Life: <input type="number" class="form-control" id="player-life" name="life" placeholder="Current Life" /><br />
                Credits: <input type="number" class="form-control" id="player-credits" name="credits" placeholder="Credits" /><br />
                Team: <select name="team" id="player-team" class="form-control">
                    <option value="0" selected>No Team</option>
                    <option value="1">Water</option>
                    <option value="2">Fire</option>
                    <option value="3">Earth</option>
                    <option value="4">Wind</option>
                    <option value="5">Light</option>
                    <option value="6">Dark</option>
                </select>
                <input type="hidden" id="player-uuid" name="uuid" value="" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </form><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Players <span class="pull-right"><small><?php echo date_tz('h:i:s a'); ?></small></span></h1>
    </div>
</div>

<?php
if ($_GET['msg'] == 'success') {
    ?>
    <div class="alert alert-dismissable alert-success">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <i class="fa fa-check"></i> Update successful.
    </div>
    <?php
}
?>

<div class="row">
    <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="player-list">
        <thead>
            <tr>
                <th>Name</th>
                <th>Level</th>
                <th>Team</th>
                <th>Life</th>
                <th>Credits</th>
                <th>Ping</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $players = $database->select("players", "*");
            foreach ($players as $player) {
                $lastping = date_tz('Y-m-d h:i:s A', $player['lastping']);
                echo "\n"
                . "            <tr>\n"
                . "                <td>"
                . "<a onclick=\"editPlayer('" . $player['uuid'] . "', '" .
                $player['nickname'] . "', '" . $player['level'] . "', '" .
                $player['energy'] . "', '" . $player['credits'] . "', '" .
                $player['teamid'] . "');\">" . $player['nickname'] . "</a></td>\n"
                . "                <td>" . $player['level'] . "</td>\n"
                . "                <td>" . getTeamNameFromId($player['teamid']) . "</td>\n"
                . "                <td>" . $player['energy'] . "/" . $player['maxenergy'] . "</td>\n"
                . "                <td>" . $player['credits'] . "</td>\n"
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

    function editPlayer(uuid, name, level, life, credits, team) {
        $('#player-name').text(name);
        $('#player-level').val(level);
        $('#player-life').val(life);
        $('#player-credits').val(credits);
        $('#player-team').val(team);
        $('#player-uuid').val(uuid);
        $('#editplayer-modal').modal();
    }
</script>