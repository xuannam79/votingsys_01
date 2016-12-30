<div class="col-lg-12">
    <!-- bar chart -->
    <script type="text/javascript">
        $(function () {
            /**
             * BAR CHART
             */
            var myChart = Highcharts.chart('chart', {
                chart: {
                    type: 'bar',
                    width: 750,
                    marginLeft: 250,
                    marginTop: 100
                },

                credits: {
                    enabled: false
                },

                legend: {
                    enabled: false
                },

                title: {
                    text: '',
                },

                tooltip: {
                    useHTML: true,
                    positioner: function () {
                        return { x: 300, y: 10 };
                    },
                    pointFormat: '<b style="color: red; font-size: 20px">{point.y}</b><br/>',
                },
                xAxis: {
                    categories: {!! $chartNameData !!},
                    labels: {
                        useHTML: true,
                        style: {
                            fontSize: fontSizeChart + 'px'
                        }
                    }
                },
                yAxis: {
                    tickInterval: 1,
                    title: {
                        text: ""
                    }
                },
                series: [{
                    name:'',
                    data: {!! $optionRateBarChart !!},
                    color: 'darkcyan'
                }],
            });
        });
    </script>
<div id="chart"></div>
</div>
