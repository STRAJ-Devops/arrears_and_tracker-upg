//graph for product sales and targets
var productLabels = productLabels;
var data = {
    labels: productLabels,
    datasets: [
        {
            label: 'Sales',
            backgroundColor: 'green',
            borderColor: 'green',
            data: productSales
        },
        {
            label: 'Target',
            backgroundColor: 'red',
            borderColor: 'red',
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
                color: 'white',
                formatter: function (value, context) {
                    return "";
                },
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
            backgroundColor: 'green',
            borderColor: 'green',
            data: branchSales
        },
        {
            label: 'Target',
            backgroundColor: 'red',
            borderColor: 'red',
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
            datalabels: {
                color: 'white',
                formatter: function (value, context) {
                    return "";
                },
            }
        }
    },
};


new Chart(document.getElementById('branch-sales-targets'), config);


//pie chart for arrears
var data = {
    labels: ['Outstanding Principal', 'Principal in Arrears'],
    datasets: [
        {
            label: 'Arrears',
            backgroundColor: ['green', 'red'],
            borderColor: ['green', 'red'],
            data: [outstandingPrincipal, PrincipalInArrears]
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
                text: 'A pie chart showing the outstanding principal and principal in arrears'
            },
            datalabels: {
                color: 'white',
                // display percentage with two decimal points
                formatter: function (value, context) {
                    //return the percentage and append the percentage sign
                    const percentage = (value / (Number(outstandingPrincipal) + Number(PrincipalInArrears))) * 100;
                    return percentage.toFixed(2) + '%';
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
            label: 'Arrears',
            backgroundColor: ['red', 'green'],
            borderColor: ['red', 'green'],
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
                // display percentage with two decimal points
                formatter: function (value, context) {
                    var percentage = 0;
                    if (context.dataIndex === 0) {
                        percentage = ((totalSales / totalTargets) * 100).toFixed(2);
                    } else {
                        percentage = (((totalTargets - totalSales) / totalTargets) * 100).toFixed(2);
                    }
                    return percentage + '%';
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
