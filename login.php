<!DOCTYPE html>
<html lang="en">
<head>
  <title>Login page</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
 
</head>
<body>
  <style>
    body{
      background-image:url(assets/images/Copilot_20260717_124842.png);
      background-repeat:no-repeat;
      background-size:cover;
      margin-top:10%;
    }
    </style>


<div class="container mt-3">
  <div class="row">
    <div class="col-sm-4">
     
    </div>
    <div style="background-color:transparent" class="col-sm-4 card p-4">
    <div style="text-align: center; margin-bottom: 20px;">
    <img src="assets/images/login2.png" alt="Logo" style="max-width: 150px; height: auto;">
</div>
      <?php
if(isset($_GET['invalid']) AND $_GET['invalid']==1){

      ?>
      <p style="color:red"> Invalid Credential</p>
<?php } ?>
       <form action="action_page.php" onsubmit="return validate();" method="POST">
        <div class="mb-3 mt-3">
          
          <label for="email">Email:</label>
          <input type="email" class="form-control" id="email" placeholder="Enter email" name="email">
        </div>
        <div class="mb-3">
          <label for="pwd">Password:</label>
          <input type="password" class="form-control" id="pwd" placeholder="Enter password" name="pswd">
        </div>
        <p id="msg"></p>
        
        <div style="text-align:center;">
        <button type="submit"  class="btn btn-primary">Sign in</button>
</div>
  </form>
    </div>
    <div class="col-sm-4">
              
    </div>
  </div>

</body>
<script>
function validate(){
    let email=document.getElementById("email").value;
    let pwd=document.getElementById("pwd").value;
    if(email=="")
     {
      document.getElementById("msg").innerHTML="Email field is required";
      document.getElementById("msg").style.color="red";
  return false;
     }
     else if(pwd==""){

    document.getElementById("msg").innerHTML="Password field is required";
      document.getElementById("msg").style.color="red";
      return false;
     }
     else{
      document.getElementById("msg").innerHTML="form submitted successfully";
      document.getElementById("msg").style.color="green";
      return true;
     }


  }
</script>
</html>