<?php
if (IN_ADMIN !== true) {
    die("Error.");
}

$update_success = 0;

// Handle updating
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !is_empty($_POST['uuid'])) {
    if (!$database->has("players", ['nickname' => $_POST['owner']])) {
        $update_success = -1;
    } else {
        $owneruuid = $database->select("players", ['uuid'], ['nickname' => $_POST['owner']])[0]['uuid'];
        $database->update("locations", ["currentlife" => $_POST['life'], "maxlife" => $_POST['maxlife'], "teamid" => $_POST['team'], "owneruuid" => $owneruuid], ["locationid" => $_POST['uuid']]);
        $update_success = 1;
    }
}

// Handle address lookups
if (!is_empty($_GET['addr'])) {
    // If we searched for this address already, just use the same info
    if ($_GET['addr'] == $_SESSION['geo_addr_cached']) {
        $geolatitude = $_SESSION['geo_addr_lat'];
        $geolongitude = $_SESSION['geo_addr_lng'];
    } else {
        $geocode = json_decode(file_get_contents("http://www.mapquestapi.com/geocoding/v1/address?key=" . MAPQUEST_KEY . "&outFormat=json&maxResults=1&thumbMaps=false&location=" . urlencode($_GET['addr'])), TRUE);
        $geolatitude = $geocode['results'][0]['locations'][0]['latLng']['lat'];
        $geolongitude = $geocode['results'][0]['locations'][0]['latLng']['lng'];
        // Cache the results a little
        $_SESSION['geo_addr_lat'] = $geolatitude;
        $_SESSION['geo_addr_lng'] = $geolongitude;
        $_SESSION['geo_addr_cached'] = $_GET['addr'];
    }
    echo "<script>var latitude = $geolatitude; var longitude = $geolongitude; var zoomlevel = 10;</script>\n";
} else {
    echo "<script>var latitude = 0.00000; var longitude = 0.00000; var zoomlevel = 1;</script>\n";
}
?>

<div class="modal fade" tabindex="-1" role="dialog" id="editplace-modal">
    <div class="modal-dialog" role="document">
        <form class="modal-content form-inline" action="" method="POST">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Edit <span id="place-name"></span></h4>
            </div>
            <div class="modal-body">
                <label>Life:</label>
                <div class="input-group">
                    <input type="number" step=".01" class="form-control" id="place-life" name="life" placeholder="Current Life" />
                    <span class="input-group-addon"> / </span>
                    <input type="number" step=".01" class="form-control" id="place-maxlife" name="maxlife" placeholder="Max Life" />
                </div>
                <br />
                <label>Team:</label> <select name="team" id="place-team" class="form-control">
                    <option value="0" selected>No Team</option>
                    <option value="1">Water</option>
                    <option value="2">Fire</option>
                    <option value="3">Earth</option>
                    <option value="4">Wind</option>
                    <option value="5">Light</option>
                    <option value="6">Dark</option>
                </select>
                <br />
                <label>Owner:</label>
                <input type="text" autocomplete="off" class="form-control" id="place-owner" name="owner" placeholder="Owner Name" />
                <input type="hidden" id="place-uuid" name="uuid" value="" />
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
        <h1 class="page-header">Locations <span class="pull-right"><small><?php echo date('h:i:s a'); ?></small></span></h1>
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
        <i class="fa fa-times"></i> Update failed: No such username exists.
    </div>
    <?php
}
?>

<div class="row">
    <div class="col-lg-12 col-xl-5">
        <form action="" method="GET">
            <input type="hidden" name="page" value="locations" />
            <div class="input-group">
                <input type="text" name="addr" class="form-control" placeholder="Zoom to Address" value="<?php echo (is_empty($_GET['addr']) ? "" : urldecode($_GET['addr'])); ?>"/>
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Search</button>
                </span>
            </div>
        </form>
        <div id="place-map" style="width: 100%; height: 500px;"></div>
    </div>
    <div class="col-lg-12 col-xl-7">
        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="place-list">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Location</th>
                    <th>Life</th>
                    <th>Team</th>
                    <th>Owner</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/dataTables.bootstrap.min.js"></script>
<script>
    function editPlace(uuid, name, life, maxlife, team, owner) {
        $('#place-name').text(name);
        $('#place-life').val(life);
        $('#place-maxlife').val(maxlife);
        $('#place-team').val(team);
        $('#place-owner').val(owner);
        $('#place-uuid').val(uuid);
        $('#editplace-modal').modal();
    }

    $('#place-owner').typeahead({
        source: function (query, process) {
            $.getJSON("playersearch.php", {q: query}, function (data) {
                process(data);
            });
        }
    });
</script>
<script src="js/location-map.js"></script>