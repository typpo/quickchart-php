<?php
  require_once('../QuickChart.php');

  $qc = new QuickChart();
  $qc->setConfig("{
    type: 'bar',
    data: {
      labels: ['Q1', 'Q2', 'Q3', 'Q4'],
      datasets: [{
        label: 'Users',
        data: [50, 60, 70, 180]
      }]
    }
  }");

  echo $qc->toFile('/tmp/chart.png');
?>
