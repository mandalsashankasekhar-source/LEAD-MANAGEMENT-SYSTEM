<?php
include 'lib/api_helper.php';
session_start();
include('config/db.php');
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['id'];

//print_r($_SESSION);exit;
$row2Summary = awsapi(
        [            
            "user_id" => $user_id
        ],
        "dashboard"
    );
//echo "<pre>";print_r($row2Summary);exit;

$result2 = awsapi(
        [            
            "user_id" => $user_id
        ],
        "dashboard_chart"
    );

$result = awsapi(
        [            
            "user_id" => $user_id
        ],
        "dashboard_table"
    );
 
//echo "<pre>";print_r($result2);exit;
$datastring = "";
foreach ($result2 as $key => $row2) {

$datastring .= "{ name: '".$row2['priority']."', y: ".$row2['percentage']." },";

}
$datastring = rtrim($datastring,",");


// $sql_summary ="SELECT  COUNT(0) as total,
// SUM(`priority`='High') as 'high_priority',
// SUM(`priority`='Medium') as 'medium_priority',
// SUM(`priority`='Low') as 'low_priority'
// FROM `leads` 
// WHERE `sales_person_id`='" . $user_id . "'";
// //echo $sql_summary;exit;
// $resultSummary = $conn->query($sql_summary);
// $row2Summary = $resultSummary->fetch_assoc();


//echo $datastring;exit;
?>

<!DOCTYPE html>
<html>
<head>
  <title>Dashboard</title>
  <link href="Assets/CSS/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.highcharts.com/highcharts.js"></script>
  <script src="https://code.highcharts.com/modules/exporting.js"></script>
  <script src="https://code.highcharts.com/modules/export-data.js"></script>
  <script src="https://code.highcharts.com/modules/accessibility.js"></script>
  <script src="https://code.highcharts.com/themes/adaptive.js"></script>
</head>

<body  >
  <div class="chart mt-3 container"> 
     <div class="row">
        
        <div class="col-4">
           <h1>Dashboard</h1>
        </div>
        <div class="col-4"><?php echo $_SESSION['username']; ?>(<?php echo $_SESSION['role']; ?>)</div>
        <div class="col-4 text-end">
            <a href="manage_lead.php" class="btn btn-danger">Manage Lead</a>
          <a href="logout.php" class="btn btn-danger">Logout</a>
        </div> 
    </div>

  <div class="row mb-6">
      <div class="col-3">
        <div class="card p-4 bg-danger text-white">
          <h3>Total Priority: <?php echo $row2Summary[0]['total']; ?></h3>
          <p><?php echo round(($row2Summary[0]['total']/$row2Summary[0]['total'])*100); ?>% Performance</p> 

        </div>
        
      </div>
      <div class="col-3">
         <div class="card p-4 bg-primary text-white">
          <h3>High Priority: <?php echo $row2Summary[0]['high_count']; ?></h3>
          <p><?php echo round(($row2Summary[0]['high_count']/$row2Summary[0]['total'])*100); ?>% Performance</p>

        </div>
      </div>
      <div class="col-3">
         <div class="card p-4 bg-success text-white">
          <h4>Medium Priority: <?php echo $row2Summary[0]['medium_count']; ?></h4>
          <p><?php echo round(($row2Summary[0]['medium_count']/$row2Summary[0]['total'])*100); ?>% Performance</p>

        </div>
        
      </div>
      <div class="col-3">
         <div class="card p-4 bg-warning text-white">
          <h3>Low Priority: <?php echo $row2Summary[0]['low_count']; ?></h3>
          <p><?php echo round(($row2Summary[0]['low_count']/$row2Summary[0]['total'])*100); ?>% Performance</p>

        </div>
        
      </div>
  </div>

    <div class="row mt-4">

    <!-- Bar Chart -->
    <div class="col-md-4">
        <div id="barchart"></div>
    </div>

    <!-- Pie Chart -->
    <div class="col-md-4">
        <div id="piechart"></div>
    </div>

    <!-- Line Chart -->
    <div class="col-md-4">
        <div id="linechart"></div>
    </div>

</div>

<br>
<br>
<div>
    <table class="table table-bordered w-100">
      <thead>
        <tr>
          <th>Contact Name</th>
          <th>Phone</th>
          <th>Email</th>
          <th>Priority</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($result as $key => $row) { ?>
          <tr>
            <td><?php echo $row['contact_name']; ?></td>
            <td><?php echo $row['phone']; ?></td>
            <td><?php echo $row['email']; ?></td>
            <td>
              <?php $priority = $row["priority"];
              if ($priority == "High") {
                echo '<span class="badge rounded-pill bg-danger px-3 py-2">High</span>';
              } elseif ($priority == "Medium") {
                echo '<span class="badge rounded-pill bg-warning px-3 py-2">Medium</span>';

              } else {
                echo '<span class="badge rounded-pill bg-success px-3 py-2">Low</span>';
              }
        }

        ?>
          </td>
        </tr>
        <?php {
        } ?>


      </tbody>
    </table>
    </div>
  </div>




</body>

<script>
  // Data retrieved from https://netmarketshare.com/
// Make monochrome colors
const colors = Highcharts.getOptions().colors.map((c, i) =>
    // Start out with a darkened base color (negative brighten), and end
    // up with a much brighter color
    Highcharts.color(Highcharts.getOptions().colors[0])
        .brighten((i - 3) / 7)
        .get()
);

// Build the chart
Highcharts.chart('piechart', {
    chart: {
        plotBackgroundColor: 'black',
        plotBorderWidth: null,
        plotShadow: false,
        type: 'pie'
    },
    title: {
        text: ''
    },
    credits: {
      enabled: false
  },
    tooltip: {
        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
    },
    accessibility: {
        point: {
            valueSuffix:'%'
        }
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            colors,
            borderRadius: 5,
            dataLabels: {
                enabled: true,
                format: '<b>{point.name}</b><br>{point.percentage:.1f} %',
                distance: -50,
                filter: {
                    property: 'percentage',
                    operator: '>',
                    value: 4
                }
            }
        }
    },
    series: [{
        name: 'Share',
        data: [ <?php echo $datastring; ?>
            // { name: 'Chrome', y: 74.03 },
            // { name: 'Edge', y: 12.66 },
            // { name: 'Firefox', y: 4.96 },
            // { name: 'Safari', y: 2.49 },
            // { name: 'Internet Explorer', y: 2.31 },
            // { name: 'Other', y: 3.398 }
        ]
    }]
});
Highcharts.chart('barchart', {
    chart: {
        type: 'column'
    },
    title: {
        text: ''
    },
    subtitle: {
        text: '<a href="http://statcounter.com" target="_blank"></a>'
    },
    accessibility: {
        announceNewData: {
            enabled: true
        }
    },
    xAxis: {
        type: 'category'
    },
    yAxis: {
        title: {
            text: ''
        }
    },
    legend: {
        enabled: false
    },
    plotOptions: {
        series: {
            borderWidth: 0,
            dataLabels: {
                enabled: true,
                formatter: function() {
                    // Sum up all values in the chart dynamically
                    let total = this.series.data.reduce((sum, point) => sum + point.y, 0);
                    // Calculate the percentage share for this specific bar
                    let percentage = (this.y / total) * 100;
                    return percentage.toFixed(1) + '%';
                }
            }
        }
    },
    tooltip: {
        headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
        pointFormatter: function() {
            // Sum up all values in the chart dynamically for the tooltip
            let total = this.series.data.reduce((sum, point) => sum + point.y, 0);
            let percentage = (this.y / total) * 100;
            return '<span style="color:' + this.color + '">' + this.name + '</span>: <b>' + percentage.toFixed(1) + '%</b><br>';
        }
    },
    series: [{
        name: 'Leads',
        colorByPoint: true,
        data: [<?php echo $datastring; ?>]
    }]
});

Highcharts.chart('linechart', {
    chart: {
        type: 'line'
    },
    title: {
        text: ''
    },
    accessibility: {
        announceNewData: {
            enabled: true
        }
    },
    xAxis: {
        type: 'category'
    },
    yAxis: {
        title: {
            text: ''
        }
    },
    legend: {
        enabled: false
    },
    plotOptions: {
        series: {
            borderWidth: 0,
            dataLabels: {
                enabled: true,
                formatter: function() {
                    // Sum up all values dynamically across the line points
                    let total = this.series.data.reduce((sum, point) => sum + point.y, 0);
                    // Calculate percentage share
                    let percentage = (this.y / total) * 100;
                    return percentage.toFixed(1) + '%';
                }
            }
        }
    },
    tooltip: {
        headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
        pointFormatter: function() {
            let total = this.series.data.reduce((sum, point) => sum + point.y, 0);
            let percentage = (this.y / total) * 100;
            return '<span style="color:' + this.color + '">\u25CF</span> ' + this.name + ': <b>' + percentage.toFixed(1) + '%</b><br>';
        }
    },
    series: [{
        name: 'leads',
        data: [<?php echo $datastring; ?>]
    }]
});
</script>
</html>