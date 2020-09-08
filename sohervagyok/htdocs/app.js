const api = './service.php';
var token = '';
var version = 10;
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

    var list = '';
    var balance = 0;
    for (var i = result.items.length - 1; i >= 0; i--) {
        balance += parseInt(result.items[i].amount);
        result.items[i].balance = balance;
    }

    var rows = result.items.length;
    if(rows > 10) {
        rows = 10;
    }

    for (var i = 0; i < rows; i++) {
        var amount = new Intl.NumberFormat().format(result.items[i].amount);
        var balance = new Intl.NumberFormat().format(result.items[i].balance);
        list += '<li class="w3-bar">'
            + '<div class="w3-row">'
            + '<div class="w3-quarter w3-container w3-right-align">' + amount + '</div>'
            + '<div class="w3-quarter w3-container">' + result.items[i].comment + '</div>'
            + '<div class="w3-quarter w3-container">' + result.items[i].date + '</div>'
            + '<div class="w3-quarter w3-container w3-right-align">' + balance + '</div>'
            + '</div>'
            + '</li>';
    }

    f('list').innerHTML = list;

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