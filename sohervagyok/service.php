<?php
require 'connect.php';

function getHistory($id, $conn) {
  //Get the list by user
  $sql = "SELECT amount, comment, reg_date FROM Expenses WHERE user_id='$id' ORDER BY reg_date DESC";
  $result = $conn->query($sql);
  $array = array();
  if ($result->num_rows > 0) {
      // output data of each row
      while($row = $result->fetch_assoc()) {
          unset($element);
          $element->amount = $row["amount"];
          $element->comment = $row["comment"];
          $element->date = $row["reg_date"];
          $array[] = $element;
      }
  }
  return $array;
}

$faszom = 1;

function getUserId($token, $conn) {
  //Get user by token
  $sql = "SELECT id FROM Users WHERE hashcode='$token'";
  //echo $sql;
  $result = $conn->query($sql);
  $id = '';
  if ($result->num_rows > 0) {
      // output data of each row
      while($row = $result->fetch_assoc()) {
          $id = $row["id"];
      }
  }
  return $id;
}

$_POST = json_decode(file_get_contents('php://input'), true);

$response->success = FALSE;



// sql to create table
$sql = "CREATE TABLE IF NOT EXISTS Expenses (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    amount VARCHAR(30) NOT NULL,
    comment VARCHAR(64),
    user_id INT(6) NOT NULL,
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";

if ($conn->query($sql) === TRUE) {
  //$message .= "Table Users created successfully <br>";
} else {
  //echo "Error creating table: " . $conn->error;
}

if ($_POST["amount"] and $_POST['token'] and $_POST['comment'] and  $_POST['command'] == 'expense') {
  //echo '<br>Check login';
  $response->success = 'inserted';
  $amount = $_POST['amount'];
  $token = $_POST['token'];
  $comment = $_POST['comment'];
  $user_id = getUserId($token, $conn);

  if($user_id != '') {
    //Insert user
    $sql = "INSERT INTO Expenses (amount, comment, user_id) VALUES ('-$amount', '$comment', '$user_id')";
    if ($conn->query($sql) === TRUE) {
        $message .= "Expense added<br>";
    } else {
        $message .= "Expense not added<br>";
    }
  }
}

if ($_POST["amount"] and $_POST['token'] and $_POST['comment'] and  $_POST['command'] == 'income') {
  //echo '<br>Check login';
  $response->success = 'inserted';
  $amount = $_POST['amount'];
  $token = $_POST['token'];
  $comment = $_POST['comment'];
  $user_id = getUserId($token, $conn);

  if($user_id != '') {
    //Insert user
    $sql = "INSERT INTO Expenses (amount, comment, user_id) VALUES ('$amount', '$comment', '$user_id')";
    if ($conn->query($sql) === TRUE) {
        $message .= "Income added<br>";
    } else {
        $message .= "Income not added<br>";
    }
  }
}

if ($_POST["token"] and $_POST["command"] == "history") {
  $response->success = 'got history';
  $token = $_POST['token'];
  $user_id = getUserId($token, $conn);
  $response->items = getHistory($user_id, $conn);
}

echo json_encode($response);

?>