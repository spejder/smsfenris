<?php

ini_set('display_errors', 'on');
error_reporting(E_ALL);

require_once 'init.php';

$conn = DBConnection::get();
$res = $conn->query("select stemme, count(stemme) as antal from stemmer where afstemningid = (select id from afstemning where afsluttet IS NULL order by id desc limit 1) group by stemme");

if (!$res) die("Der opstod en fejl!");

$stemmer = array();
while ($obj = $res->fetch_object()) {
    $stemmer[$obj->stemme] = intval($obj->antal);
}

Logger::info(print_r($stemmer, true));

$data = array();
$pie = array();

foreach ($stemmer as $stemme => $antal) {
    $dataObj = new stdClass();
    $pieObj = array();

    $dataObj->type = 'column';
    $dataObj->data = array($antal);
    $dataObj->name = $stemme;

    $pieObj[0] = $stemme;
    $pieObj[1] = $antal;

    $data[] = $dataObj;
    $pie[] = $pieObj;
}

$totalVotes = array_sum($stemmer);


//$kategorier = json_encode(array_keys($stemmer));
//$antal = json_encode(array_values($stemmer));
//$jsonData = json_encode($data);

require_once 'pagehead.php';
?>
<style type="text/css">


</style>

<script type="text/javascript">
    $(function () {

        $('#container').highcharts({
            chart: {
                type: 'column'
            },
            labels: {
                style: {
                    fontSize: '15pt',
                    fontWeight: 'bold'
                }
            },
            title: {
                text: 'Afstemning'
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                categories: [''],
                labels: {
                    style: {
                        fontSize: '12pt',
                        fontWeight: 'bold'
                    }
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Antal stemmer'
                },
                tickInterval: 1,
                labels: {
                    style: {
                        fontSize: '18pt',
                        fontWeight: 'bold'
                    }
                }
            },
            legend: {
                itemStyle: {
                    fontSize: '16pt',
                    fontWeight: 'bold'
                }
            },
            credits: {
                enabled: false
            },

            tooltip: {
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>{point.y}</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0,
                    dataLabels: {
                        enabled: true,
                        style: {
                            fontSize: '16pt',
                            fontWeight: 'bold'
                        }
                    }
                }
            },
            series: <?php echo json_encode($data); ?>
        });


    $('#containerPie').highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false
        },
        title: {
            text: 'Stemmeandel'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        credits: {
            enabled: false
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    color: '#000000',
                    connectorColor: '#000000',
                    format: '{point.percentage:.1f} %'
                }
            }
        },
        series: [{
            type: 'pie',
            name: 'Stemmeandel',
            data: <?php echo json_encode($pie); ?>
        }]
    });

    });

</script>
</head>
<body>
<script src="js/highcharts.js"></script>
<script src="js/modules/exporting.js"></script>

<div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>

<br /><br />

<div id="containerPie" style="min-width: 310px; height: 400px; margin: 0 auto"></div>


</body>
</html>
