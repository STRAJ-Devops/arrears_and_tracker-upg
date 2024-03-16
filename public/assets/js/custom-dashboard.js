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
        }
    },
    title: {
        display: true,
        text: 'Sales and Targets'
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
    title: {
        display: true,
        text: 'Sales and Targets'
    },
};


new Chart(document.getElementById('branch-sales-targets'), config);