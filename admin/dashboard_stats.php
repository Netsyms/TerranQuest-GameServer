<?php
require_once "required.php";


if (!isAdmin()) {
    die("Unauthorized.");
}
?>
<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header" style="margin-top: -15px;">System Overview <span class="pull-right"><small><?php echo date_tz('h:i:s a'); ?></small></span></h3>
    </div>
</div>
<div class="row">
    <div class="col-xl-2 col-lg-4 col-sm-6" style="min-width: 200px;">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-users fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge">
                            <?php
                            echo $database->count("players", ['lastping[>]' => date('Y-m-d H:i:s', strtotime('-1 minute'))]);
                            ?>
                        </div>
                        <div>Active Players</div>
                    </div>
                </div>
            </div>
            <a href="./?page=players&sub=active">
                <div class="panel-footer">
                    <span class="pull-left">Manage Sessions</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-sm-6" style="min-width: 200px;">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-globe fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?php echo $database->count("players"); ?></div>
                        <div>Total Players</div>
                    </div>
                </div>
            </div>
            <a href="./?page=players">
                <div class="panel-footer">
                    <span class="pull-left">Manage Players</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-sm-6" style="min-width: 200px;">
        <div class="panel panel-green">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-map-marker fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?php echo $database->count("locations", ['owneruuid[!]' => null]); ?></div>
                        <div>Captured Locations</div>
                    </div>
                </div>
            </div>
            <a href="./?page=locations">
                <div class="panel-footer">
                    <span class="pull-left">Manage Locations</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-sm-6" style="min-width: 200px;">
        <div class="panel panel-yellow">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-star fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?php echo $database->count("player_badges"); ?></div>
                        <div>Badges Given</div>
                    </div>
                </div>
            </div>
            <a href="./?page=badges">
                <div class="panel-footer">
                    <span class="pull-left">Manage Badges</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-sm-6" style="min-width: 200px;">
        <div class="panel panel-black">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-cubes fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?php echo $database->count("inventory"); ?></div>
                        <div>Inventory Items</div>
                    </div>
                </div>
            </div>
            <a href="./?page=items&sub=inventory">
                <div class="panel-footer">
                    <span class="pull-left">Manage Inventory</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-sm-6" style="min-width: 200px;">
        <div class="panel panel-black">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-qrcode fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?php echo $database->count("claimedcodes"); ?></div>
                        <div>Scanned Barcodes</div>
                    </div>
                </div>
            </div>
            <a href="./?page=items&sub=barcodes">
                <div class="panel-footer">
                    <span class="pull-left">Manage Barcodes</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
</div>