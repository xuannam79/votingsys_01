var chartData = $('.hide_chart').data('chart');
var chartNameData = $('.hide_chart').data('nameChart');
var chartPieData = $('.hide_chart').data('pieChart');
var fontSizeChart = $('.hide_chart').data('fontSize');

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
            categories: chartNameData,
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
            data: chartData,
            color: 'darkcyan'
        }],
    });

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
            align: 'left',
            maxHeight: 100,
        },
        tooltip: {
            useHTML: true,
            pointFormat: '<b style="color: red; font-size: 20px">{point.y}</b><br/>',
        },
        series: [{
            data: chartPieData
        }]
    });
});
