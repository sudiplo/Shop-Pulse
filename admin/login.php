<?php
include 'connect.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../customer/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" 
        integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" 
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Login</title>
</head>
<body>
    <div class="container" id="main">
        <!-- Sign Up Form -->
        <div class="sign-up">
            <form method="POST" action="">
                <!-- <h1>Create Account</h1>
                <div>    
                    <input type="text" name="name" placeholder="Name" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit" name="sign-up">Register</button> -->
            </form>
        </div>

        <div class="sign-in">
            <form method="POST" action="">
                <h1>Admin Login</h1>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <p class="switch">
                    <a href="../customer/login.php">Customer Login</a>
                </p>
                <button type="submit" name="sign-in">Login</button>
            </form>
        </div>

        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-left">
                    <h1>Welcome Back!</h1>
                    <p>Already have an account?</p>
                    <button id="signIn">Login</button>
                </div>
                <div class="overlay-right">
                    <h1>Namaste, Welcome!</h1>
                    <!-- <p>Don't have an account?</p>
                    <button id="signUp">Register</button> -->
                </div>
            </div>
        </div>
    </div>
<!-- php  -->
 <?php 
                if(isset($_POST['sign-up'])){
                    $Name=$_POST['name'];
                    $email=$_POST['email'];
                    $password=$_POST['password'];
                    $password=md5($password);
                  

                    $checkEmail="SELECT * From admins where email='$email'";
                    $result=$conn->query($checkEmail);
                    if($result->num_rows>0){
                        // echo "Email Address Already Exists !";
                        echo "<script> alert('Email Address Already Exists !');window.location.href='login.php';</script>";
                    }
                    else{
                        $insertQuery="INSERT INTO admins(name,email,password)
                                    VALUES ('$Name','$email','$password')";
                            if($conn->query($insertQuery)==TRUE){
                                header("location: login.php");
                            }
                            else{
                                echo "Error:".$conn->error;
                            }
                    }
                

                }

                if(isset($_POST['sign-in'])){
                $email=$_POST['email'];
                $password=$_POST['password'];
                $password=md5($password) ;
                
                $sql="SELECT * FROM admins WHERE email='$email' and password='$password'";
                $result=$conn->query($sql);
                if($result->num_rows>0){
                    session_start();
                    $row=$result->fetch_assoc();
                    $_SESSION['name']=$row['name'];
                    $_SESSION['email']=$row['email'];
                    $_SESSION['id']=$row['id'];
                    
                    header("Location: admin_dashboard.php");
                    exit();
                }
                else{
                     echo "<script> alert('Not Found, Incorrect Email or Password !');window.location.href='login.php';</script>";
                }

                }
            ?>
<!--  -->
    <script type="text/javascript">
        const signUpButton = document.getElementById('signUp');
        const signInButton = document.getElementById('signIn');
        const main = document.getElementById('main');

        signUpButton.addEventListener('click', () => {
            main.classList.add("right-panel-active");
        });

        signInButton.addEventListener('click', () => {
            main.classList.remove("right-panel-active");
        });
    </script>
</body>
</html>
