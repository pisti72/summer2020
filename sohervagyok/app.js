if (hashcode == '') {
    hide('form')
} else {
    hide('error');
}

function f(n) {
    return document.getElementById(n);
}

function hide(n) {
    f(n).style.display = 'none';
}

function show(n) {
    f(n).style.display = 'default';
}