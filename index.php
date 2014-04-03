<?php

// Make sure you set the database connection on the next line:
$mysqli = new mysqli("localhost", "root", "root", "TemperatureSensor");

/* check connection */
if ($mysqli->connect_errno) {
    header("HTTP/1.0 500 Internal Server Error");
    exit();
}

$lastTemps = getDerniereTemperature($mysqli);
$xCategories = getXCategories($mysqli);
$datas = getData($mysqli);

function getDerniereTemperature($mysqli) {

    $sql = 'SELECT temperature, temperatureDate FROM tempHistory ORDER BY temperatureDate DESC LIMIT 1';
    $res = $mysqli->query($sql);
    $row = $res->fetch_assoc();
    return $row['temperature'];
}


function getXCategories($mysqli){

    $sql = 'SELECT temperatureDate FROM tempHistory ORDER BY temperatureDate DESC';
    $res = $mysqli->query($sql);

    /* Récupère un tableau associatif */
    $values = array();
    while ($row = $res->fetch_assoc()) {
        $date = $row["temperatureDate"];
        array_push($values, '"' . $date . '"');
    }

    /* Libération des résultats */
    $res->free();
    return implode(", ", $values);
}

function getData($mysqli){
    $sql = 'SELECT temperature FROM tempHistory ORDER BY temperatureDate DESC';
    $res = $mysqli->query($sql);

    /* Récupère un tableau associatif */
    $values = array();
    while ($row = $res->fetch_assoc()) {
        $date = $row["temperature"];
        array_push($values, $date);
    }

    /* Libération des résultats */
    $res->free();
    return implode(", ", $values);
}

?>

<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Température intérieure</title>

    <script type="text/javascript" src="js/jquery.1.8.3.min.js"></script>
    <script type="text/javascript">
        $(function () {
            $('#graph').highcharts({
                title: {
                    text: 'Courbe de température',
                    x: -20 //center
                },
                xAxis: {
                    categories: [<?php echo $xCategories; ?>]
                },
                yAxis: {
                    title: {
                        text: 'Temperature (°C)'
                    },
                    plotLines: [
                        {
                            value: 0,
                            width: 1,
                            color: '#808080'
                        }
                    ]
                },
                tooltip: {
                    valueSuffix: '°C'
                },
                legend: {
                    layout: 'vertical',
                    align: 'right',
                    verticalAlign: 'middle',
                    borderWidth: 0
                },
                series: [
                    {
                        name: 'Rennes',
                        data: [<?php echo $datas; ?>]
                    }
                ]
            });

            $('#container').highcharts({

                chart: {
                    type: 'gauge',
                    plotBackgroundColor: null,
                    plotBackgroundImage: null,
                    plotBorderWidth: 0,
                    plotShadow: false
                },

                title: {
                    text: 'Température'
                },

                pane: {
                    startAngle: -150,
                    endAngle: 150,
                    background: [
                        {
                            backgroundColor: {
                                linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                                stops: [
                                    [0, '#FFF'],
                                    [1, '#333']
                                ]
                            },
                            borderWidth: 0,
                            outerRadius: '109%'
                        },
                        {
                            backgroundColor: {
                                linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                                stops: [
                                    [0, '#333'],
                                    [1, '#FFF']
                                ]
                            },
                            borderWidth: 1,
                            outerRadius: '107%'
                        },
                        {
                            // default background
                        },
                        {
                            backgroundColor: '#DDD',
                            borderWidth: 0,
                            outerRadius: '105%',
                            innerRadius: '103%'
                        }
                    ]
                },

                // the value axis
                yAxis: {
                    min: 0,
                    max: 40,

                    minorTickInterval: 'auto',
                    minorTickWidth: 1,
                    minorTickLength: 10,
                    minorTickPosition: 'inside',
                    minorTickColor: '#666',

                    tickPixelInterval: 30,
                    tickWidth: 2,
                    tickPosition: 'inside',
                    tickLength: 10,
                    tickColor: '#666',
                    labels: {
                        step: 2,
                        rotation: 'auto'
                    },
                    title: {
                        text: '°'
                    },
                    plotBands: [
                        {
                            from: 0,
                            to: 10,
                            color: '#0040FF' // green
                        },
                        {
                            from: 10,
                            to: 25,
                            color: '#FFFF00' // yellow
                        },
                        {
                            from: 25,
                            to: 40,
                            color: '#DF5353' // red
                        }
                    ]
                },

                series: [
                    {
                        name: 'Dernière température',
                        data: [<?php echo $lastTemps; ?>],
                        tooltip: {
                            valueSuffix: '°'
                        }
                    }
                ]

            });
        });

    </script>
</head>
<body>



<script src="Highcharts-3.0.10/js/highcharts.js"></script>
<script src="Highcharts-3.0.10/js/highcharts-more.js"></script>

<div id="container" style="min-width: 310px; max-width: 400px; height: 300px; margin: 0 auto"></div>
<div id="graph" style="min-width: 310px; height: 400px; margin: 0 auto"></div>


</body>
</html>
