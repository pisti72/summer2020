<?php
$servername = "sql207.epizy.com";
$username = "epiz_26088689";
$password = "O4jqF15BOj";
$dbname = "epiz_26088689_soher";
$message = "";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
$message .= "Connected successfully<br>";

// sql to create table
$sql = "CREATE TABLE IF NOT EXISTS Users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(30) NOT NULL,
    password VARCHAR(64) NOT NULL,
    email VARCHAR(50),
    hashcode VARCHAR(64) NOT NULL,
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";

if ($conn->query($sql) === TRUE) {
    //$message .= "Table Users created successfully <br>";
  } else {
    //echo "Error creating table: " . $conn->error;
  }

if ($_POST["name"] and $_POST['password'] and $_POST['submit'] == 'Login') {
    //echo '<br>Check login';
    $name = $_POST['name'];
    $password = md5($_POST['password']);
    $sql = "SELECT hashcode FROM Users WHERE name='$name' AND password='$password'";
    //echo $sql;
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $hashcode = $row["hashcode"];
        }
    } else {
    }
}

if ($_POST["name"] and $_POST['password'] == $_POST['passwordagain'] and $_POST['email'] and $_POST['submit'] == 'Register') {
    //echo '<br>Register users';
    $name = $_POST['name'];
    $password = md5($_POST['password']);
    $email = $_POST['email'];
    $hashcode = md5($name.$password.$email);
    //Check does user exist
    //Insert user
    $sql = "INSERT INTO Users (name, password, email, hashcode) VALUES ('$name', '$password', '$email', '$hashcode')";
    if ($conn->query($sql) === TRUE) {
        $message .= "User added<br>";
    } else {
        $message .= "User not added<br>";
    }
}



mysqli_close($conn); 
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Expenses</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <meta charset="UTF-8">
        <script>var hashcode ='<?php echo $hashcode; ?>';</script>
    </head>
    
    <body>
        <header class="w3-container w3-teal">
            <h1>Kiadások</h1>
        </header>

        <div id="form" class = "w3-container">

        <div class="w3-panel w3-green w3-display-container">
            <span onclick="this.parentElement.style.display='none'" class="w3-button w3-large w3-display-topright">×</span>
            <h3>Success</h3>
            <p>You are logged in</p>
        </div>

        </div>

        <div id="error" class = "w3-container">

        <div class="w3-panel w3-red w3-display-container">
            <span onclick="this.parentElement.style.display='none'" class="w3-button w3-large w3-display-topright">×</span>
            <h3>Error</h3>
            <p>You are not logged in</p>
        </div>

        </div>


        <script src="app.js"></script>
    </body>
</html>
