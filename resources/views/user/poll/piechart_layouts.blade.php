<div class="col-lg-12">
    <!-- pie chart -->
    <script type="text/javascript">
        $(function () {
            /**
             * PIE CHART
             */
            var myPieChart = Highcharts.chart('chart_div', {
                chart: {
                    type: 'pie',
                    width: 800,
                    marginLeft: 100,
                    options3d: {
                        enabled: true,
                        alpha: 45,
                        beta: 0
                    }
                },

                title: {
                    text: '',
                },

                credits: {
                    enabled: false
                },

                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        depth: 50,
                        dataLabels: {
                            enabled: true,
                            formatter: function() {
                                return this.percentage.toFixed(2) + ' %';
                            }
                        },
                        showInLegend: true
                    }
                },

                legend: {
                    useHTML: true,
                    maxHeight: 100,
                },
                tooltip: {
                    useHTML: true,
                    pointFormat: '<b style="color: red; font-size: 20px">{point.y}</b><br/>',
                },
                series: [{
                    data:  {!! $optionRatePieChart !!}
                }]
            });
        });
    </script>
    <div id="chart_div"></div>
</div>
