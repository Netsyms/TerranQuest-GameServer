<?php
if (IN_ADMIN !== true) {
    die("Error.");
}
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Dashboard</h1>
    </div>
</div>

<!-- Quick Status -->
<div id="quick-stats">
    <?php
    require 'dashboard_stats.php';
    ?>
</div>
<!-- End Quick Status -->

<!-- Maps -->
<div class="row">
    <div class="col-lg-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-map fa-fw"></i> Player Map <small>Last 2 days</small>
                <div class="pull-right">
                    <div class="btn-group">
                        <button type="button" onclick="loadPlayers()" class="btn btn-default btn-xs">
                            <i class="fa fa-refresh fa-fw"></i> Refresh
                        </button>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div id="player-map" style="width: 100%; height: 500px;"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-map fa-fw"></i> Location Map
                <div class="pull-right">
                    <div class="btn-group">
                        <button type="button" onclick="reloadPlaces()" class="btn btn-default btn-xs">
                            <i class="fa fa-refresh fa-fw"></i> Refresh
                        </button>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div id="place-map" style="width: 100%; height: 500px;"></div>
            </div>
        </div>
    </div>
</div>
<script src="js/player-map.js"></script>
<script src="js/place-map.js"></script>
<!-- End Maps -->

<script>
                            setInterval(function () {
                                $.get("dashboard_stats.php", function (data) {
                                    $('#quick-stats').html(data);
                                });
                            }, 5000);
</script>