const api = './service.php';
var token = '';
var version = 11;
function onload2() {

    f('version').innerHTML = version;

    if (hashcode == '') {
        hide('form')
    } else {
        hide('error');
        history();
    }

    hide('income');

}

function closeSuccess() {
    hide('success');
}

function closeError() {
    hide('error');
}

async function history() {
    let data = {
        token: hashcode,
        command: 'history',
    }

    show('loader');

    let response = await fetch(api, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
    });

    let result = await response.json();

    
    var maxrows = 10;
    var list = f('list');
    var row = f('balance_row');
    
    var counter = {}
    var balanceDaily = 0;
    var balanceRow = 0;
    var labels = [];
    var dataBalance = [];
    var dataCashflow = [];
    
    for (var i = result.items.length - 1; i >= 0; i--) {
        var date = result.items[i].date;
        var day = new Date(date).toLocaleDateString();
        var amount = parseInt(result.items[i].amount);
        var comment = result.items[i].comment;

        balanceRow += parseInt(result.items[i].amount);
        result.items[i].balance = balanceRow;

        if (i == result.items.length - 1) {
            counter.day = day;
            counter.sum = amount
            balanceDaily = amount;
        } else {
            
            if (day == counter.day) {
                counter.sum += amount;
            } else {
                balanceDaily += counter.sum;
                labels.push(counter.day);
                dataCashflow.push(counter.sum);
                dataBalance.push(balanceDaily);
                counter.sum = amount;
                counter.day = day;
            }
        }
    }
    
    /**
     * List
     */

    list.innerHTML="";

    var row_counter = 0;
    for (var i = 0; i < result.items.length; i++) {
        var cln = row.cloneNode(true);
        var amount = new Intl.NumberFormat().format(result.items[i].amount);
        var balance = new Intl.NumberFormat().format(result.items[i].balance);
        var comment = result.items[i].comment;
        var date = result.items[i].date;
        if(++row_counter < maxrows) {
            cln.getElementsByTagName('div')[0].getElementsByTagName('div')[0].innerHTML = amount;
            cln.getElementsByTagName('div')[0].getElementsByTagName('div')[1].innerHTML = comment;
            cln.getElementsByTagName('div')[0].getElementsByTagName('div')[2].innerHTML = date;
            cln.getElementsByTagName('div')[0].getElementsByTagName('div')[3].innerHTML = balance;
            list.appendChild(cln);
        }
    }

    /**
     * Chart
     */

    var ctx = f('myChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Balance by day',
                data: dataBalance,
                fill: false,
                borderColor: '#f00',
                lineTension: 0.1
            }
                /*            
                            ,{
                            label: 'Spending by day',
                            data: dataCashflow,
                            fill:false,
                            borderColor: '#0f0',
                            lineTension: 0.1
                            }
                */
            ]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        callback: function (value, index, values) {
                            return new Intl.NumberFormat().format(value);
                        }
                    }
                }]
            }
        }
    });

    hide('loader');
}

async function expense() {
    hide('success');
    var amount = f('amountExpense').value;
    f('amountExpense').value = '';
    var comment = f('commentExpense').value;
    f('commentExpense').value = '';
    let data = {
        token: hashcode,
        amount: amount,
        command: 'expense',
        comment: comment
    };
    show('loader');

    let response = await fetch(api, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
    });

    let result = await response.json();

    history();
    hide('loader');

}

async function income() {
    hide('success');
    var amount = f('amountIncome').value;
    f('amountIncome').value = '';
    var comment = f('commentIncome').value;
    f('commentIncome').value = '';
    let data = {
        token: hashcode,
        amount: amount,
        command: 'income',
        comment: comment
    };
    show('loader');

    let response = await fetch(api, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
    });

    let result = await response.json();

    history();

    hide('loader');
}

function openIncome() {
    show('income');
    hide('expense');
}

function openExpense() {
    show('expense');
    hide('income');
}

function f(n) {
    return document.getElementById(n);
}

function hide(n) {
    f(n).style.display = 'none';
}

function show(n) {
    f(n).style.display = 'block';
}