<?php

$response = array();

global $mysqli;
//$mysqli = new mysqli("c199um.forpsi.com", "b14304", "H3MFdnN","b14304","3306");
$mysqli = new mysqli("localhost", "root", "", "remotetimer", "3306");

/* check connection */
if (mysqli_connect_errno()) {
    //printf("Connect failed: %s\n", mysqli_connect_error());
    $response['result'] = 'failed';
    exit();
} else {
    //printf("Connected");
}

/*
Request timer
called by timer
 */
$response['result'] = 'failed';

//called by control and timer at 1st
if (isset($_GET['timer'])) {
    $token = $_GET['timer'];
    $query = "SELECT name, command, timestring, style FROM remotetimer_timers WHERE token = '$token'";
    if ($result = $mysqli->query($query)) {
        /* fetch object array */
        if ($row = $result->fetch_row()) {
            $response['result'] = 'success';
            $response['name'] = $row[0];
            $response['command'] = $row[1];
            $response['timestring'] = $row[2];
            $response['style'] = $row[3];
            $result->close();
        } else {
            $response['result'] = 'failed';
        }
    }
}
//called by timer
if (isset($_GET['timestring'], $_GET['receivedcommand'], $_GET['token'])) {
    $token = $_GET['token'];
    $timestring = $_GET['timestring'];
    $command = $_GET['receivedcommand'];
    $query = "SELECT command FROM remotetimer_timers WHERE token = '$token'";
    $ip = $_SERVER['REMOTE_ADDR'];
    if ($result = $mysqli->query($query)) {
        if ($row = $result->fetch_row()) {
            $response['result'] = 'success';
            if ($row[0] == $command) {
                $query = "UPDATE remotetimer_timers SET timestring = '$timestring', command='NOPE', client = '$ip' WHERE token ='$token'";
                $success = $mysqli->query($query);
                if ($success) {
                    $response['result'] = 'success';
                } else {
                    $response['result'] = 'failed';
                }
            } else {
                $response['result'] = 'failed';
            }
            $result->close();
        } else {
            $response['result'] = 'failed';
        }
    }
}

//called by control
if (isset($_GET['command'], $_GET['token'])) {
    $token = $_GET['token'];
    $command = $_GET['command'];
    $query = "UPDATE remotetimer_timers SET command = '$command' WHERE token ='$token'";
    $success = $mysqli->query($query);
    if ($success) {
        $response['result'] = 'success';
    } else {
        $response['result'] = 'failed';
    }
}

//called by user registration
$request_body = file_get_contents('php://input');
$data = json_decode($request_body);
if (isset($data)) {
    //new user
    if (isset($data->name, $data->email, $data->password)) {
        $token = substr(md5($data->name . $data->email . $data->password), 0, 16);
        $password = md5($data->password);
        $email = $data->email;
        $name = $data->name;
        $query = "INSERT INTO remotetimer_users (name,password,email,token)VALUES('$name','$password','$email','$token')";
        //$response['sql'] = $query;
        $success = $mysqli->query($query);
        if ($success) {
            $response['result'] = 'success';
        } else {
            $response['result'] = 'failed';
        }
        //$response['name'] = $data->name;
    }
    //login user
    if (isset($data->name, $data->password)) {
        $password = md5($data->password);
        $name = $data->name;
        $id = 9999;
        $query = "SELECT id, name, token FROM remotetimer_users WHERE (name = '$name' OR email = '$name') AND password='$password'";
        //$response['sql'] = $query;
        if ($result = $mysqli->query($query)) {
            if ($row = $result->fetch_row()) {
                $response['result'] = 'success';
                $id = $row[0];
                $response['name'] = $row[1];
                $response['token'] = $row[2];
            }
            $tokens = array();
            $names = array();
            $query = "SELECT name, token FROM remotetimer_timers WHERE user_id = '$id'";
            if ($result = $mysqli->query($query)) {
                while ($row = $result->fetch_row()) {
                    array_push($names, $row[0]);
                    array_push($tokens, $row[1]);
                }
                //$response['timers'] = json_encode($tokens);
                $response['timers'] = $tokens;
                $response['names'] = $names;
            }
        } else {
            $response['result'] = 'failed';
        }
        //$response['name'] = $data->name;
    }
}

/* send back the result */
echo json_encode($response);

/* close connection */
$mysqli->close();
