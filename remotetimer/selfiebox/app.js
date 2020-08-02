const url = 'api.php';
const STATE_WELCOME = 0;
const STATE_COUNTDOWN = 1;
const STATE_SHOT = 2;
const STATE_PREVIEW = 3;
const STATE_SEND = 4;
const STATE_SENT = 5;
var counter = 0;
var countdown_number = 0;
var shot_number = 0;
var state = STATE_WELCOME;
var canvas = [f('canvas0'), f('canvas1'), f('canvas2'), f('canvas3')];
var video = f('video');
var imageToSend = {};
var promise = navigator.mediaDevices.getUserMedia({ video: true })
    .then(function (stream) {
        /* use the stream */
        video.srcObject = stream;
        video.onloadedmetadata = function (e) {
            video.play();
        };
    })
    .catch(function (err) {
        /* handle the error */
        console.log(err.name + ": " + err.message);
    });

f('container').addEventListener("click", function () {
    if (state == STATE_WELCOME) {
        toCountdown();
    }
});

addKeyboard();

setInterval(function () { animation() }, 1000);

function shot(n) {
    console.log('shot: ' + n);
    canvas[n].width = video.videoWidth;
    canvas[n].height = video.videoHeight;
    ctx = canvas[n].getContext('2d');
    ctx.drawImage(video, 0, 0);
    ctx.font = "30px Arial";
    ctx.fillStyle = "#fff";
    ctx.fillText("YOUR LOGO", 10, 40);
}

function save() {
    download("my_picture.jpg", imageToSend);
}

function download(filename, image) {
    var element = document.createElement('a');
    //element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
    element.setAttribute('href', image);
    element.setAttribute('download', filename);

    element.style.display = 'none';
    document.body.appendChild(element);
    element.click();
    document.body.removeChild(element);
}

function animation() {
    counter++;
    if (state == STATE_WELCOME) {
        if (counter % 2 == 0) {
            f('title_txt').innerHTML = texts.en.title;
            f('touch_txt').innerHTML = texts.en.touch;
        } else {
            f('title_txt').innerHTML = texts.hu.title;
            f('touch_txt').innerHTML = texts.hu.touch;
        }
    } else if (state == STATE_COUNTDOWN) {
        if (countdown_number >= texts.en.countdown.length - 1) {
            toShot();
        }
        f('countdown').innerHTML = texts.en.countdown[countdown_number];
        countdown_number++;
    } else if (state == STATE_SHOT) {
        if (counter % 4 == 0) {
            shot(shot_number);
            f('countdown').innerHTML = '&#128247;';
            setInterval(function () {
                f('countdown').innerHTML = '';
            }, 500);
            shot_number++;
            if (shot_number >= canvas.length) {
                state = STATE_PREVIEW;
                hide('shot');
                show('preview');
            }
        }
    }
}

function toCountdown() {
    state = STATE_COUNTDOWN;
    hide('welcome');
    hide('preview');
    show('shot');
    countdown_number = 0;
    counter = 0;
}

function toShot() {
    state = STATE_SHOT;
    shot_number = 0;
    counter = 0;
}

function toWelcome() {
    state = STATE_WELCOME;
    hide('send');
    hide('sent');
    hide('preview');
    show('welcome');
    countdown_number = 0;
    counter = 0;
}

function toSending() {
    state = STATE_SENT;
    hide('send');
    show('sent');
}

function select(n) {
    imageToSend = canvas[n].toDataURL('image/jpeg', 0.8);
    if (state == STATE_PREVIEW) {
        state = STATE_SEND;
        hide('preview');
        show('send');
    }
}

function addKeyboard() {
    var s = '';
    var layout = [
        ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0', 'DEL'],
        ['Q', 'W', 'E', 'R', 'T', 'Y', 'U', 'I', 'O', 'P'],
        ['A', 'S', 'D', 'F', 'G', 'H', 'J', 'K', 'L', '@GMAIL.COM'],
        ['Z', 'X', 'C', 'V', 'B', 'N', 'M', '_', '-', '.', '@']
    ];
    for (var j = 0; j < layout.length; j++) {
        var row = layout[j];
        s += "<div>";
        for (var i = 0; i < row.length; i++) {
            s += "<button onclick=\"typing('" + row[i] + "')\">" + row[i] + "</button>\n";
        }
        s += "</div>";
    }
    f('keyboard').innerHTML = s;
}

function typing(n) {
    var email = f('email').value;
    if (n == 'DEL') {
        if (email.length >= 1) {
            email = email.substring(0, email.length - 1);
        }

    } else {
        email += n;
    }
    f('email').value = email;
}

function send() {
    var email = f('email').value;
    sendmail(email, imageToSend);
}

function sendmail(email, image) {
    var blob = image.replace('data:image/jpeg;base64,', '');
    var d = new Date();
    var filename = 'party_' + d.getTime() + '.jpg';
    var data = {
        email: email,
        filename: filename,
        image: blob
    };

    fetch(url, {
        method: 'POST',
        body: JSON.stringify(data),
        headers: {
            'Content-Type': 'application/json'
        }
    }).then(function (response) {
        toSending();
        f('message').innerHTML = 'Sending...';
        return response.json()
    }).then(function (json) {
        if (json.result == 'success') {
            f('message').innerHTML = 'Mail sent';
        } else {
            f('message').innerHTML = 'Mail did not send';
        }
        f('email').value = '';
        setTimeout(
            function () {
                f('message').innerHTML = '';
                toWelcome();
            }, 4000);
    }).catch(function (error) {
        console.log(error);
    })
}

function hide(n) {
    f(n).style.display = 'none';
}

function show(n) {
    f(n).style.display = 'block';
}

function f(n) {
    return document.getElementById(n);
}
