google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawPieChart);
google.charts.setOnLoadCallback(drawBarChart);
var chartData = $('.hide_chart').data('chart');

function drawPieChart() {
    if (typeof chartData !== "undefined" && chartData != null && chartData.length != 0) {
        // Create the data table.
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Topping');
        data.addColumn('number', 'Slices');
        data.addRows(chartData);
        var options = {
            'width': 850,
            'height': 450,
            is3D: true,
            forceIFrame: true,
            pieSliceTextStyle: {
                fontSize: '20px'
            }
        };
        var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
        chart.draw(data, options);
    }
}

function drawBarChart() {
    if (typeof chartData !== "undefined" && chartData != null && chartData.length != 0) {

        // Create the data table.
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Topping');
        data.addColumn('number', '');
        data.addRows(chartData);
        var options = {
            'width': 750,
            'height': 450,
            chartArea: {
                left: 250
            },
            colors: ['darkcyan'],
            hAxis: {
                gridlines: {
                    count: 4
                }
            }
        };
        var chart = new google.visualization.BarChart(document.getElementById('chart'));
        chart.draw(data, options);
    }
}


