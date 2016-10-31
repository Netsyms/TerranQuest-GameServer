var placetab;

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

function getRadiusForZoom(zoom) {
    switch (zoom) {
        case 18:
            return .3;
        case 17:
            return .5;
        case 16:
            return .75;
        case 15:
            return 1;
        case 14:
            return 1;
        case 13:
            return 1.5;
        case 12:
            return 3;
        case 11:
            return 8;
        case 10:
            return 15;
        case 9:
            return 20;
        case 8:
            return 50;
        case 7:
            return 50;
        case 6:
            return 100;
        case 5:
            return 150;
        case 4:
            return 200;
        case 3:
            return 300;
        case 2:
            return 1000;
        case 1:
            return 5000;
    }
}
var bounds = new L.LatLngBounds(new L.LatLng(-90.0, -180.0), new L.LatLng(90.0, 180.0));
var placemap = L.map('place-map', {maxBounds: bounds, maxBoundsViscosity: 1.0}).setView([latitude, longitude], zoomlevel);
var tileurl = "//stamen-tiles.a.ssl.fastly.net/terrain/{z}/{x}/{y}.jpg";
placemap.addLayer(new L.tileLayer(tileurl, {minZoom: 1, maxZoom: 18, attribution: 'Map tiles by <a href="http://stamen.com">Stamen Design</a>, under <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0</a>. Data by <a href="http://openstreetmap.org">OpenStreetMap</a>, under <a href="http://www.openstreetmap.org/copyright">ODbL</a>.'}));
L.control.scale().addTo(placemap);

function onPlaceTap(feature, layer) {
    layer.on('click', function (e) {
        //openPlace(feature);
    });
}

//var placecluster = L.markerClusterGroup({zoomToBoundsOnClick: false});

var placeLayer = L.geoJson(
        {"name": "Places", "type": "FeatureCollection", "features": []},
        {
            onEachFeature: onPlaceTap,
            pointToLayer: function (feature, latlng) {
                var teamcolor = "#" + getTeamColorFromId(feature.properties.gameinfo.teamid);
                return L.circleMarker(latlng, {
                    radius: 14,
                    fillColor: teamcolor,
                    color: "#000",
                    weight: 1,
                    opacity: 1,
                    fillOpacity: 0.6
                }).bindPopup("<b>" + feature.properties.name + "</b><br />Team: " + getTeamNameFromId(feature.properties.gameinfo.teamid) + "<br />Life: " + feature.properties.gameinfo.currentlife + " / " + feature.properties.gameinfo.maxlife + "<br />Owner: " + feature.properties.gameinfo.nickname + "<br /><span class=\"btn btn-sm btn-default\" onclick=\"editPlace('" + feature.properties.gameinfo.locationid + "', '" + feature.properties.name + "', '" + feature.properties.gameinfo.currentlife + "', '" + feature.properties.gameinfo.maxlife + "', '" + feature.properties.gameinfo.teamid + "', '" + feature.properties.gameinfo.nickname + "');\">Edit</span>");
            }
        });

//placecluster.addLayer(placeLayer);
//placemap.addLayer(placecluster);
placemap.addLayer(placeLayer);

function loadPlaces(lat, long) {
    var url = "../../places.php?lat=" + lat.toFixed(5) + "&long=" + long.toFixed(5) + "&radius=" + getRadiusForZoom(placemap.getZoom());
    try {
        $.getJSON(
                url,
                function (data) {
                    if (data.type === 'FeatureCollection') {
                        placeLayer.clearLayers();
                        placetab.clear();
                        data.features.forEach(function (item) {
                            if (item.properties.gameinfo.nickname == undefined) {
                                item.properties.gameinfo.nickname = "";
                            }
                            placeLayer.addData(item);
                            var pname = "<a onclick=\"editPlace('" + item.properties.gameinfo.locationid + "', '" + item.properties.name + "', '" + item.properties.gameinfo.currentlife + "', '" + item.properties.gameinfo.maxlife + "', '" + item.properties.gameinfo.teamid + "', '" + item.properties.gameinfo.nickname + "');\">" + item.properties.name + "</a>";
                            placetab.row.add([pname, item.geometry.coordinates[1] + ", " + item.geometry.coordinates[0], item.properties.gameinfo.currentlife + " / " + item.properties.gameinfo.maxlife, getTeamNameFromId(item.properties.gameinfo.teamid), item.properties.gameinfo.nickname]).draw();
                            /*placetab.row.add({
                             'name': item.properties.name,
                             'location': item.geometry.coordinates.latitude + ", " + item.geometry.coordinates.longitude,
                             'life': item.properties.gameinfo.currentlife + " / " + item.properties.gameinfo.maxlife,
                             'team': getTeamNameFromId(item.properties.gameinfo.teamid),
                             'owner': item.properties.gameinfo.nickname
                             }).draw();*/
                        });
                    }
                }
        );
    } catch (ex) {
        alert("Couldn't load: " + ex);
    }
}

$(document).ready(function () {
    placetab = $('#place-list').DataTable();
    loadPlaces(latitude, longitude);
});

placemap.on('moveend', function (e) {
    loadPlaces(placemap.getCenter().lat, placemap.getCenter().lng);
});

function reloadPlaces() {
    loadPlaces(placemap.getCenter().lat, placemap.getCenter().lng);
}
