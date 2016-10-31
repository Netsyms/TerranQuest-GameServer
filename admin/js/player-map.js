function getTeamInfoFromId(id) {
    id = id + "";
    var team_string = "None";
    var team_color = "FFFFFF";
    switch (id) {
        case "1":
            team_string = "Water";
            team_color = "00BFFF";
            break;
        case "2":
            team_string = "Fire";
            team_color = "FF4000";
            break;
        case "3":
            team_string = "Earth";
            team_color = "D1A000";
            break;
        case "4":
            team_string = "Wind";
            team_color = "96FFFF";
            break;
        case "5":
            team_string = "Light";
            team_color = "FFFF96";
            break;
        case "6":
            team_string = "Dark";
            team_color = "ABABAB";
            break;
        default:
            team_string = "None";
            team_color = "FFFFFF";
            break;
    }
    return {'name': team_string, 'color': team_color};
}

function getTeamNameFromId(id) {
    return getTeamInfoFromId(id)['name'];
}

function getTeamColorFromId(id) {
    return getTeamInfoFromId(id)['color'];
}

var bounds = new L.LatLngBounds(new L.LatLng(-90.0, -180.0), new L.LatLng(90.0, 180.0));
var playermap = L.map('player-map', {maxBounds: bounds, maxBoundsViscosity: 1.0}).setView([0.0, 0.0], 1);
var tileurl = "//stamen-tiles.a.ssl.fastly.net/terrain/{z}/{x}/{y}.jpg";
playermap.addLayer(new L.tileLayer(tileurl, {minZoom: 1, maxZoom: 18, attribution: 'Map tiles by <a href="http://stamen.com">Stamen Design</a>, under <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0</a>. Data by <a href="http://openstreetmap.org">OpenStreetMap</a>, under <a href="http://www.openstreetmap.org/copyright">ODbL</a>.'}));
L.control.scale().addTo(playermap);

function onPlayerTap(feature, layer) {
    layer.on('click', function (e) {
        //openPlace(feature);
    });
}

var playercluster = L.markerClusterGroup();

// {"type": "Feature", "geometry": {"type": "Point", "coordinates": [0, 0]}, "properties": {"uuid": -1, "nickname": "nobody", "level": 0, "energy": 0, "maxenergy": 0, "lastping": 0, teamid: 0, credits: 0}}
var playerLayer = L.geoJson(
        {"name": "Places", "type": "FeatureCollection", "features": []},
        {
            onEachFeature: onPlayerTap,
            pointToLayer: function (feature, latlng) {
                var teamcolor = "#" + getTeamColorFromId(feature.properties.teamid);
                return L.circleMarker(latlng, {
                    radius: 12,
                    fillColor: teamcolor,
                    color: "#000",
                    weight: 1,
                    opacity: 1,
                    fillOpacity: 0.6
                }).bindPopup("<b>" + feature.properties.nickname + "</b><br />Team: " + getTeamNameFromId(feature.properties.teamid) + "<br />Level: " + feature.properties.level + "<br />Life: " + feature.properties.energy + " / " + feature.properties.maxenergy + "<br />Credits: " + feature.properties.credits + "<br />Ping: " + feature.properties.lastping);
            }
        });

playercluster.addLayer(playerLayer);
playermap.addLayer(playercluster);

function loadPlayers() {
    var url = "./players.php";
    try {
        $.getJSON(
                url,
                function (data) {
                    if (data.type === 'FeatureCollection') {
                        playerLayer.clearLayers();
                        playercluster.clearLayers();
                        data.features.forEach(function (item) {
                            playerLayer.addData(item);
                        });
                        playercluster.addLayer(playerLayer);
                    }
                }
        );
    } catch (ex) {
        alert("Couldn't load player map: " + ex);
    }
}

var playercluster = L.markerClusterGroup();
playercluster.addLayer(playerLayer);
playermap.addLayer(playercluster);

setTimeout(loadPlayers, 500);