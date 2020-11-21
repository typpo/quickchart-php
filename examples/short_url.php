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

  echo $qc->getShortUrl();
  // Prints:
  // https://quickchart.io/chart/render/zf-16815ac3-850f-4f5f-a79c-f6386c688f48
?>

