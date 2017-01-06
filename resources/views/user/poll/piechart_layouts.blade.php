<div class="col-lg-12">
    <div class="hide_chart" data-has-image="{{ $isHaveImages }}"></div>
    <!-- pie chart -->
    <script type="text/javascript">
        var isHasImage = $('.hide_chart').data('hasImage');
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
                    align: 'left',
                    labelFormatter: function () {
                        if (isHasImage) {
                            return (this.name.length > 154) ? this.name.substring(0, 200) + "..." : this.name;
                        }

                        return  (this.name.length > 100) ? this.name.substring(0, 100) + "..." : this.name;
                    }
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
