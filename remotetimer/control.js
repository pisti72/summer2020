const api = './api.php';
const tick = 30;
var counter = 0;
hide('play');
update();

function checkServer() {
    if (counter % tick == 0) {
        fetch(api + '?timer=' + id).then(function (response) {
            return response.json()
        }).then(function (json) {
            f('name').innerHTML = json.name;
            f('timestring').innerHTML = json.timestring;
            if(json.result == 'success'){
                hide('connection');
            }else{
                visible('connection');
            }
            
            //console.log(json.command);
            if(json.command != 'NOPE'){
                visible('message');
                f('message').innerHTML = 'Sending command...';
                if(json.command == 'SETONEHOUR'){
                    f('message').innerHTML = 'Setting one hour..';
                }else if(json.command == 'DECFIVEMINS'){
                    f('message').innerHTML = 'Decreasing 5 mins...';
                }else if(json.command == 'ADDFIVEMINS'){
                    f('message').innerHTML = 'Adding 5 mins...';
                }else if(json.command == 'SETTOZERO'){
                    f('message').innerHTML = 'Stopping...';
                }else if(json.command == 'PAUSED'){
                    f('message').innerHTML = 'Pausing...';
                }else if(json.command == 'CONTINUE'){
                    f('message').innerHTML = 'Playing...';
                }
            }else{
                hide('message');
            }
        }).catch(function (error) {
            console.log(error);
            visible('connection');
        })
    }
}

function setOneHour() {
    fetch(api + '?command=SETONEHOUR&token=' + id).then().catch(function (error) {
        console.log(error)
    });
    visible('pause');
    hide('play');
}

function decFiveMins() {
    fetch(api + '?command=DECFIVEMINS&token=' + id).then().catch(function (error) {
        console.log(error)
    });
    visible('pause');
    hide('play');
}

function addFiveMins() {
    fetch(api + '?command=ADDFIVEMINS&token=' + id).then().catch(function (error) {
        console.log(error)
    });
    visible('pause');
    hide('play');
}

function setToZero() {
    fetch(api + '?command=SETTOZERO&token=' + id).then().catch(function (error) {
        console.log(error)
    });
    visible('pause');
    hide('play');
}

function playPressed() {
    hide('play');
    visible('pause');
    fetch(api + '?command=CONTINUE&token=' + id).then().catch(function (error) {
        console.log(error)
    });
}

function pausePressed() {
    visible('play');
    hide('pause');
    fetch(api + '?command=PAUSED&token=' + id).then().catch(function (error) {
        console.log(error)
    });
}

function update() {
    counter++;
    checkServer();
    requestAnimationFrame(update);
}

function visible(n) {
    f(n).style.display = 'inline-block';
}

function hide(n) {
    f(n).style.display = 'none';
}

function f(n) {
    return document.getElementById(n);
}
