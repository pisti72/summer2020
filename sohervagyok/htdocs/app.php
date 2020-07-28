<?php
require 'connect.php';

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
    <link rel="stylesheet" type="text/css" href="mystyle.css">
    <meta charset="UTF-8">
    <script>var hashcode = '<?php echo $hashcode; ?>';</script>
</head>

<body onload="onload2()">
    <header class="w3-container w3-teal">
        <h1>Expenses</h1>
    </header>

    <div id="form" class="w3-container w3-margin">

        <div id="success" class="w3-panel w3-green w3-display-container">
            <span onclick="closeSuccess()" class="w3-button w3-large w3-display-topright">×</span>
            <h3>Success</h3>
            <p>You are logged in</p>
        </div>

        <div class="container" id="expense">
            <div class="w3-card">

                <div class="w3-container w3-orange">
                    <h2>Expense</h2>
                </div>

                <div class="w3-container w3-padding w3-margin">
                    <label>Amount</label>
                    <input class="w3-input w3-border" type="number" id="amountExpense">
                    <label>Comment</label>
                    <input class="w3-input w3-border" type="text" id="commentExpense">
                    <button class="w3-btn w3-blue w3-margin" onclick="expense()">Send</button>
                </div>

            </div>

            <div class="w3-container w3-margin">
                <button class="w3-btn w3-green" onclick="openIncome()">Income</button>
            </div>
        </div>

        <div class="container" id="income">
            <div class="w3-container w3-margin">
                <button class="w3-btn w3-orange" onclick="openExpense()">Expense</button>
            </div>
            <div class="w3-card">

                <div class="w3-container w3-green">
                    <h2>Income</h2>
                </div>

                <div class="w3-container w3-padding w3-margin">
                    <label>Amount</label>
                    <input class="w3-input w3-border" type="number" id="amountIncome">
                    <label>Comment</label>
                    <input class="w3-input w3-border" type="text" id="commentIncome">
                    <button class="w3-btn w3-blue w3-margin" onclick="income()">Send</button>
                </div>

            </div>

        </div>

        <ul class="w3-card w3-ul" id="list">
        </ul>

    </div>

    <div id="error" class="w3-container w3-margin">

        <div class="w3-panel w3-red w3-display-container">
            <span onclick="closeError()"
                class="w3-button w3-large w3-display-topright">×</span>
            <h3>Error</h3>
            <p>You are not logged in</p>
        </div>

    </div>

    <div id="loader">
        <div id="circle"></div>
    </div>

    <footer class="w3-container w3-dark-grey">
        <p id="version"></p>
    </footer>

    <script src="app.js"></script>
</body>

</html>