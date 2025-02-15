<?php
// Get data from form 
$name = $_POST['name'];
$mobileno = $_POST['telefon'];
$email = $_POST['email'];
// $datetime = $_POST['datetime'];
// $services = $_POST['services'];
$message = $_POST['message'];

$to = "quickcabsservices@gmail.com";
$subject = "Enquiry from Quick Cab Pune.";

// Format the message using HTML
$txt = "
<html>
<head>
  <title>$subject</title>
  <style>
  body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    background-color: #fff;
    // font-size: 14px;
  }

  .container {
    width: 100%;
    max-width: 600px;
    margin: auto;
    overflow: hidden;
    padding: 10px;
  }

  p {
    margin-bottom: 10px;
  }

  strong {
    color: black;
  }

  header, footer {
    background: #333;
    color: #fff;
    padding: 10px 0;
    text-align: center;
  }

  header h2, footer p {
    text-align: center;
  }

  footer {
    background: #ffffff;
    color: #333;
    padding: 10px 0;
    border-top: 1px solid #e5e5e5;
  }

  footer a {
    text-decoration: none;
    text-align: center;
  }
  
    img {
    width: 100px;
    height: 23px;
    margin-bottom: -6px;
  }

   @media only screen and (max-width: 600px) {
    img {
      margin-bottom: -6px;
      height:60px;
    }
  }

//   img {
//     width: 60px;
//     height: 20px;
//     margin-bottom: -6px;
//   }

//   @media only screen and (max-width: 600px) {
//     img {
//       margin-bottom: -6px;
//       height:30px;
//     }
//   }

  
</style>

</head>
<body style='border:1px solid black; border-radius: 8px;'>
  <div class='container'>
    <header>
      <h2 style='text-align: center;'>Enquiry Form</h2> 
      <br><br>
    </header>
    <div style='padding: 10px;'> 
    <p><strong>Name:</strong> $name</p>
    <p><strong>Mobile No:</strong> $mobileno</p>
    <p><strong>Email:</strong> $email</p>
    <p><strong>Message:</strong> $message</p>
    </div>
    <br>
    <hr> 
   <footer>

    <p style='text-align: center;font-size:14px'>Powered by syncsolution.co.in/</p>
    </footer>
  </div>
</body>
</html>
";

$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= "From: quickcabsservices@gmail.com";

if ($email != NULL) {
    mail($to, $subject, $txt, $headers);
}

// Redirect and show alert
echo '<script type="text/javascript">
    alert("Thanks for submitting details.");
    window.location = "index.html";
</script>';
?>
