var chartData = $('.hide_chart').data('chart');
var chartNameData = $('.hide_chart').data('nameChart');
var chartPieData = $('.hide_chart').data('pieChart');
var fontSizeChart = $('.hide_chart').data('fontSize');
var isHasImage = $('.hide_chart').data('hasImage');

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
            text: ''
        },

        tooltip: {
            useHTML: true,
            pointFormat: '<b style="color: red; font-size: 13px">{point.y}</b><br/>'
        },

        xAxis: {
            categories: chartNameData,
            min: 0,
            labels: {
                useHTML: true,
                style: {
                    fontSize: fontSizeChart + 'px'
                },
                overflow: 'justify'
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
            data: chartData,
            color: 'darkcyan'
        }],

        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            },
            series: {
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    format: '{point.y}'
                }
            }
        },

    });

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
            data: chartPieData
        }]
    });
});

$(document).ready(function () {
    var width = $(window).width();
    pieChart = $('#chart_div').highcharts();
    columChart = $('#chart').highcharts();
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
    columChart.setSize(columnSize);

    $(window).resize(function(){
        var chart = $('#chart_div').highcharts();
        var w = $('#chart_div').closest("#pieChart").width();

        chart.setSize(
            w,w * (3/4),false
        );
    });
});
