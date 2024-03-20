//graph for product sales and targets
var productLabels = productLabels;
var data = {
    labels: productLabels,
    datasets: [
        {
            label: 'Sales',
            backgroundColor: 'rgba(0, 123, 255, 0.9)',
            borderColor: 'rgba(0, 123, 255, 1)',
            data: productSales
        },
        {
            label: 'Target',
            backgroundColor: 'rgba(255, 193, 7, 0.9)',
            borderColor: 'rgba(255, 193, 7, 1)',
            data: productTargets
        }
    ]
}

var config = {
    type: 'bar',
    data: data,
    options: {
        responsive: true,
        scales: {
            x: {
                stacked: true,
            },
            y: {
                stacked: true
            }
        },
        plugins: {
            title: {
                display: true,
                text: 'Product Sales and Targets'
            },

            datalabels: {
                lables: {
                    value: {

                    }
                }
            }
        }
    },
};


new Chart(document.getElementById('product-sales-targets'), config);


var data = {
    labels: branchLabels,
    datasets: [
        {
            label: 'Sales',
            backgroundColor: 'rgba(0, 123, 255, 0.9)',
            borderColor: 'rgba(0, 123, 255, 1)',
            data: branchSales
        },
        {
            label: 'Target',
            backgroundColor: 'rgba(255, 193, 7, 0.9)',
            borderColor: 'rgba(255, 193, 7, 1)',
            data: branchTargets
        }
    ]
}

var config = {
    type: 'bar',
    data: data,
    options: {
        responsive: true,
        scales: {

        },
        plugins: {
            title: {
                display: true,
                text: 'Branch Sales and Targets'
            },
        }
    },
};


new Chart(document.getElementById('branch-sales-targets'), config);


//pie chart for arrears
var data = {
    labels: ['Arrears', 'No Arrears'],
    datasets: [
        {
            label: 'Arrears',
            backgroundColor: ['red', 'green'],
            borderColor: ['red', 'green'],
            data: [withArrears, withoutArrears]
        }
    ]
}

var config = {
    type: 'pie',
    data: data,
    options: {
        responsive: true,
        plugins: {
            title: {
                display: true,
                text: 'Loans Disbursed with Arrears and Without Arrears'
            },
            datalabels: {
                color: 'white',
                // display percentage with two decimal points
                formatter: function(value, context) {
                    //return the percentage and append the percentage sign
                    return value;
                },
                font: {
                    weight: 'bold',
                    size: 25,
                }
            }
        }
    },
};


new Chart(document.getElementById('arrears-chart'), config);

//pie chart for arrears
var data = {
    labels: ['Targets', 'Actuals'],
    datasets: [
        {
            label: 'Targets vs Actuals',
            backgroundColor: ['rgba(255, 193, 7, 0.9)', 'rgba(0, 123, 255, 0.9)'],
            borderColor: ['rgba(255, 193, 7, 1)', 'rgba(0, 123, 255, 1)'],
            data: [totalTargets, totalSales]
        }
    ]
}

var config = {
    type: 'pie',
    data: data,
    options: {
        responsive: true,
        plugins: {
            title: {
                display: true,
                text: 'Total Sales vs Total Targets'
            },
            datalabels: {
                color: 'white',
                formatter: function(value, context) {
                    console.log(context.chart.data.datasets[0].data[1]);
                },
                font: {
                    weight: 'bold',
                    size: 25,
                }
            }
        }
    },
};


new Chart(document.getElementById('targets-sales-chart'), config);