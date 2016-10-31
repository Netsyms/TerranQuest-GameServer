<?php
if (IN_ADMIN !== true) {
    die("Error.");
}

$update_success = 0;

// Handle updating
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !is_empty($_POST['player']) && !is_empty($_POST['sid'])) {
    if (!$database->has("players", ['nickname' => $_POST['player']]) || !$database->has("badges", ['badgesid' => $_POST['sid']])) {
        $update_success = -1;
    } else if ($_POST['delete'] == '1') {
        $uuid = $database->select("players", ['uuid'], ['nickname' => $_POST['player']])[0]['uuid'];
        $badgeid = $database->select("badges", ['badgeid'], ['badgesid' => $_POST['sid']])[0]['badgeid'];
        $database->delete("player_badges", ["AND" => ["badgeid" => $badgeid, "playeruuid" => $uuid]]);
        $update_success = 1;
    } else if (!is_empty($_POST['date'])) {
        $uuid = $database->select("players", ['uuid'], ['nickname' => $_POST['player']])[0]['uuid'];
        $origuuid = $database->select("players", ['uuid'], ['nickname' => $_POST['origplayer']])[0]['uuid'];
        $badgeid = $database->select("badges", ['badgeid'], ['badgesid' => $_POST['sid']])[0]['badgeid'];
        $origbadgeid = $database->select("badges", 'badgeid', ['badgesid' => $_POST['origsid']])[0]['badgeid'];
        $date = date("Y-m-d", strtotime($_POST['date']));
        if ($_POST['newrow'] == "1") {
            $database->insert("player_badges", ["badgeid" => $badgeid, "playeruuid" => $uuid, "gotdate" => $date]);
        } else {
            $database->update("player_badges", ["badgeid" => $badgeid, "playeruuid" => $uuid, "gotdate" => $date], ["AND" => ["badgeid" => $origbadgeid, "playeruuid" => $origuuid]]);
        }
        $update_success = 1;
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'POST' && !is_empty($_POST['name']) && !is_empty($_POST['sid']) && !is_empty($_POST['desc'])) {
    $database->insert("badges", ["badgesid" => $_POST['sid'], "badgename" => $_POST['name'], "badgedesc" => $_POST['desc']]);
    $update_success = 1;
}
?>

<div class="modal fade" tabindex="-1" role="dialog" id="addbadge-modal">
    <div class="modal-dialog" role="document">
        <form class="modal-content" action="" method="POST">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add New Badge</h4>
            </div>
            <div class="modal-body">
                Name: <input type="text" class="form-control" id="add-badge-sid" name="name" placeholder="Name" /><br />
                Description: <input type="text" id="add-badge-desc" name="desc" placeholder="Description" /><br />
                ID: <input type="text" class="form-control" id="add-badge-id" name="sid" placeholder="ID" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Add</button>
            </div>
        </form><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" tabindex="-1" role="dialog" id="editplayerbadge-modal">
    <div class="modal-dialog" role="document">
        <form class="modal-content" action="" method="POST">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Edit Badge</h4>
            </div>
            <div class="modal-body">
                Badge: <input type="text" autocomplete="off" class="form-control" id="badge-sid" name="sid" placeholder="Badge" /><br />
                Player: <input type="text" autocomplete="off" class="form-control" id="badge-player" name="player" placeholder="Player" /><br />
                Date: <input type="text" class="form-control" id="badge-date" name="date" placeholder="Date" />
                <input type="hidden" name="origplayer" id="badge-origplayer" value="" />
                <input type="hidden" name="origsid" id="badge-origsid" value="" />
                <input type="hidden" name="newrow" id="badge-newrow" value="0" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </form><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" tabindex="-1" role="dialog" id="delplayerbadge-modal">
    <div class="modal-dialog" role="document">
        <form class="modal-content" action="" method="POST">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Delete Badge?</h4>
            </div>
            <div class="modal-body">
                Badge: <span id="del-badge-sid"></span><br />
                Player: <span id="del-badge-player"></span><br />
                <input type="hidden" name="player" id="del-badge-player-inp" />
                <input type="hidden" name="sid" id="del-badge-sid-inp" />
                <input type="hidden" name="delete" value="1" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-danger">Delete</button>
            </div>
        </form><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Badges <span class="pull-right"><small><?php echo date_tz('h:i:s a'); ?></small></span></h1>
    </div>
</div>

<?php
if ($update_success == 1) {
    ?>
    <div class="alert alert-dismissable alert-success">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <i class="fa fa-check"></i> Update successful.
    </div>
    <?php
} else if ($update_success == -1) {
    ?>
    <div class="alert alert-dismissable alert-danger">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <i class="fa fa-times"></i> Update failed: Invalid username or badge ID.
    </div>
    <?php
}
?>

<div class="row">
    <div class="col-lg-6">
        <h3 class="page-header" style="margin-top: -15px;">All Badges <span class="pull-right"><button class="btn btn-primary btn-sm" onclick="addbadge();"><i class="fa fa-plus"></i> Add</button></span></h3>
        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="badge-list">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>ID</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $badges = $database->select("badges", "*");
                foreach ($badges as $badge) {
                    echo "\n"
                    . "            <tr>\n"
                    . "                <td>" . $badge['badgename'] . "</td>\n"
                    . "                <td>" . $badge['badgedesc'] . "</td>\n"
                    . "                <td>" . $badge['badgesid'] . "</td>\n"
                    . "            </tr>\n";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="col-lg-6">
        <h3 class="page-header" style="margin-top: -15px;">Player Badges <span class="pull-right"><button class="btn btn-primary btn-sm" onclick="addplayerbadge();"><i class="fa fa-plus"></i> Add</button></span></h3>
        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="player-badge-list">
            <thead>
                <tr>
                    <th>Player</th>
                    <th>Badge Name</th>
                    <th>Badge ID</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $pbadges = $database->select("player_badges", ["[>]badges" => "badgeid", "[>]players" => ["playeruuid" => "uuid"]], ["player_badges.badgeid", "player_badges.gotdate", "players.nickname", "badges.badgesid", "badges.badgename"]);
                foreach ($pbadges as $badge) {
                    echo "\n"
                    . "            <tr>\n"
                    . "                <td>" . $badge['nickname'] . "</td>\n"
                    . "                <td>" . $badge['badgename'] . "</td>\n"
                    . "                <td>" . $badge['badgesid'] . "</td>\n"
                    . "                <td>" . $badge['gotdate'] . "</td>\n"
                    . "                <td><button class='btn btn-sm btn-success btn-inline' onclick=\"editplayerbadge('" . $badge['badgesid'] . "', '" . $badge['nickname'] . "', '" . $badge['gotdate'] . "')\"><i class='fa fa-pencil'></i> Edit</button> <button class='btn btn-sm btn-danger btn-inline' onclick=\"delplayerbadge('" . $badge['badgesid'] . "', '" . $badge['nickname'] . "')\"><i class='fa fa-times'></i> Delete</button></td>\n"
                    . "            </tr>\n";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/dataTables.bootstrap.min.js"></script>
<script>
            $(document).ready(function () {
                $('#badge-list').dataTable();
                $('#player-badge-list').dataTable();
            });

            function addbadge() {
                $('#add-badge-sid').val("");
                $('#add-badge-name').val("");
                $('#add-badge-desc').val("");
                $('#addbadge-modal').modal();
            }

            function editplayerbadge(badgesid, nickname, gotdate) {
                $('#badge-sid').val(badgesid);
                $('#badge-player').val(nickname);
                $('#badge-origsid').val(badgesid);
                $('#badge-origplayer').val(nickname);
                $('#badge-date').val(gotdate);
                $('#badge-newrow').val("0");
                $('#editplayerbadge-modal').modal();
            }

            function addplayerbadge() {
                $('#badge-sid').val("");
                $('#badge-player').val("");
                $('#badge-date').val("");
                $('#badge-newrow').val("1");
                $('#editplayerbadge-modal').modal();
            }

            function delplayerbadge(badgesid, nickname) {
                $('#del-badge-sid').text(badgesid);
                $('#del-badge-player').text(nickname);
                $('#del-badge-sid-inp').val(badgesid);
                $('#del-badge-player-inp').val(nickname);
                $('#delplayerbadge-modal').modal();
            }

            $('#badge-player').typeahead({
                source: function (query, process) {
                    $.getJSON("playersearch.php", {q: query}, function (data) {
                        process(data);
                    });
                }
            });

            $('#badge-sid').typeahead({
                source: function (query, process) {
                    $.getJSON("badgesearch.php", {q: query}, function (data) {
                        process(data);
                    });
                }
            });
</script>