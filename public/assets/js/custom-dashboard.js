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
                formatter: function (value, context) {
                    //return the percentage and append the percentage sign
                    const percentage = (value / (withArrears + withoutArrears)) * 100;
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
                    console.log(value, totalTargets, totalSales);
                    const percentage = (value / (Number(totalTargets) + Number(totalSales))) * 100;
                    console.log(percentage);
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


new Chart(document.getElementById('targets-sales-chart'), config);
