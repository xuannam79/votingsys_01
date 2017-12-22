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
                    options3d: {
                        enabled: true,
                        alpha: 45,
                        beta: 0
                    },
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
                        depth: 15,
                        dataLabels: {
                            enabled: true,
                            formatter: function() {
                                return this.percentage.toFixed(2) + ' %';
                            }
                        },
                        showInLegend: true
                    },
                },

                responsive: {
                    rules: [{
                        condition: {
                            maxWidth: 500
                        },
                        chartOptions: {
                            chart: {
                                height: 400
                            },
                            subtitle: {
                                text: null
                            },
                            navigator: {
                                enabled: false
                            }
                        }
                    }]
                },

                legend: {
                    useHTML: true,
                    align: 'left',
                    minHeight: 100,
                    labelFormatter: function () {
                        if (isHasImage) {
                            return (this.name.length > 154) ? this.name.substring(0, 200) + "..." : this.name;
                        }

                        return  (this.name.length > 100) ? this.name.substring(0, 100) + "..." : this.name;
                    },
                },
                tooltip: {
                    enabled: true
                },
                series: [{
                    data:  {!! $optionRatePieChart !!}
                }]
            });
        });

        $(document).ready(function () {
            var width = $(window).width();
            console.log()
            pieChart = $('#chart_div').highcharts();
            var pieSize = 0;
            var columnSize = 0;
            if (width < 768) {
                pieSize = width - (width * 0.03);
            }

            if (width == 768) {
                pieSize = width - (width * 0.21);
            }

            if (width >= 1024) {
                pieSize = width - (width * 0.3);
                columnSize = width - (width * 0.35);
            }

            if (width >= 1850) {
                pieSize = width - (width * 0.51);
                columnSize = width - (width * 0.55);
            }

            pieChart.setSize(pieSize);

            $(window).resize(function(){
                var chart = $('#chart_div').highcharts();
                var w = $('#chart_div').closest("#pieChart").width();

                chart.setSize(
                    w,w * (3/4),false
                );
            });
        });
    </script>
    <div id="chart_div"></div>
</div>
