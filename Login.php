<?php
// Include config file
require_once 'include/config.php';
 
// Define variables and initialize with empty values
$idmember = $password = "";
$idmembererr = $passworderr = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if username is empty
    if(empty(trim($_POST["id_member"]))){
        $idmembererr = 'Please enter id_member.';
    } else{
        $idmember = trim($_POST["id_member"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST['password']))){
        $password_err = 'Please enter your password.';
    } else{
        $password = trim($_POST['password']);
    }
    
    // Validate credentials
    if(empty($idmembererr) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT id_member, password, fullname, ls_admin, photo, bio, first_name ";
        $sql .= "FROM member WHERE id_member = ?";
        
        if($stmt = $mysqli->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_id_member);
            
            // Set parameters
            $param_id_member = $idmember;
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Store result
                $stmt->store_result();
                
                // Check if username exists, if yes then verify password
                if($stmt->num_rows == 1){                    
                    // Bind result variables
                    $stmt->bind_result($idmember, $hashed_password, $fullname, $ls_admin, $photo, $bio, $first_name);
                    if($stmt->fetch()){
                        if(password_verify($password, $hashed_password)){
                            /* Password is correct, so start a new session and
                            save the username to the session */
                            session_start();
                            $_SESSION['id_member'] = $idmember;      
                            $_SESSION['fullname'] = $fullname; 
                            $_SESSION['ls_admin'] = $ls_admin;
                            $_SESSION['photo'] = $photo;
                            $_SESSION['bio'] = $bio;  
                            $_SESSION['first_name'] = $first_name;   
                            
                            // filter untuk jenins hak akses jika user
                            if ($ls_admin)   
                              // Jika user memiliki hak akses admin (1) maka akan di teruskan ke halaman admin
                              header("location: admin/index.php");
                            else
                            // Jika user memiliki hak akses admin (1) maka akan di teruskan ke halaman member
                              header("location: index.php");
                        } else{
                            // Display an error message if password is not valid
                            $password_err = 'The password you entered was not valid.';
                        }
                    }
                } else{
                    // Display an error message if username doesn't exist
                    $idmembererr = 'No account found with that username.';
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        
        // Close statement
        $stmt->close();
    }
    
    // Close connection
    $mysqli->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="style/style.css" type="text/css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <title>Login</title>
</head>
<body>
<div class="row">
      <div class="column center">

      </div>
      
    <div class="column side">

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
      <div class="imgcontainer">
        <!-- Memanggil file nsc.jpg dan menampilkan di halaman -->
        <img src="nsc.jpg" alt="Avatar" class="avatar">
      </div>
      <div class="container">
        <label for="id_member"><b>Oficer ID</b></label>
        <input type="text" placeholder="Enter Username" name="id_member" required>
        <span class="error"><?php echo $idmembererr; ?></span>

        <label for="psw"><b>Password</b></label>
        <input type="password" placeholder="Enter Password" name="password" required>
        <span class="error"><?php echo $password_err; ?></span>
            
        <button class="button button1" type="submit" name="login" >Login</button>
        
      </div>
    </form>
    </div> 
  </div> 
  
</body>
</html>