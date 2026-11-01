<?php
include 'lib/api_helper.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = $_POST['email'] ?? '';
    $password = $_POST['pswd'] ?? '';
    //echo "<pre>";print_r($_POST);exit;
    $user = awsapi(
        [
            "email" => $email,
            "password" => $password
        ],
        "login"
    );
    //echo "<pre>";print_r($user);exit;

    if ($user) {

        $_SESSION['id'] = $user['0']['id'];
        $_SESSION['username'] = $user['0']['name'];
        $_SESSION['email'] = $user['0']['email'];
        $_SESSION['role'] = $user['0']['role'];

        header("Location: dashboard.php");
        exit();

    } else {

        header("Location: login.php?invalidlogin=1");
        exit();

    }
}
?>