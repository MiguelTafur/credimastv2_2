<?php 
	if($grafica = "prestamosAnio"){
		$prestamosAnio = $data;
 ?>

<script>
    Highcharts.chart('graficaAnioPrestamos', {
        chart: {
            type: 'column'
        },
        title: {
            text: '<?= $prestamosAnio['anio']; ?>'
        },
        subtitle: {
            text: '<b>Total: <?= $prestamosAnio['totalGastos']; ?></b>'
        },
        xAxis: {
            type: 'category',
            labels: {
                autoRotation: [-45, -90],
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'CREDIMAST'
            }
        },
        legend: {
            enabled: false
        },
        tooltip: {
            pointFormat: '{point.y}'
        },
        series: [{
            name: 'Gastos',
            colors: [
                '#9b20d9', '#9215ac', '#861ec9', '#7a17e6', '#7010f9', '#691af3',
                '#6225ed', '#5b30e7', '#533be1', '#4c46db', '#4551d5', '#3e5ccf',
                '#3667c9', '#2f72c3'
            ],
            colorByPoint: true,
            groupPadding: 0,
            data: [
            <?php 
                foreach ($prestamosAnio['meses'] as $mes) {
                echo "['".$mes['mes']."',".$mes['total']."],";
                }
            ?> 
            ],
            dataLabels: {
                enabled: true,
                rotation: -90,
                color: '#FFFFFF',
                inside: true,
                verticalAlign: 'top',
                y: 10, // 10 pixels down from the top
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        }]
    });
</script>
<?php } ?>