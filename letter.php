<?php
$email = $_POST['email'];

$to = "quickcabsservices@gmail.com";
$subject = "Newsletter Subscribed";

$message = '
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>' . $subject . '</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 0;
    }

    .container {
      width: 100%;
      max-width: 600px; /* Adjust this value according to your design */
      margin: auto;
      overflow: hidden;
    }

    header {
      background: #ffffff;
      color: #333;
      padding: 10px 0;
      border-bottom: 1px solid #e5e5e5;
    }

    main {
      padding: 20px 0;
    }
    
    p{
       font-size :14px;
        line-height: 1.5;
    }
    }
    footer {
      background: #ffffff;
      color: #333;
      padding: 10px 0;
      border-top: 1px solid #e5e5e5;
      text-align: center;
    }

    footer a {
      text-decoration: none;
    }

   
    
     /* Styles for mobile devices */
    @media only screen and (max-width:450px)   {
        img {
            width:100px;
            height: 30px !important;
            display: inline !important;
            margin-bottom:-20px;
        }
    }
  </style>

  

</head>
<body style="text-align:center;padding:5px;border:1px solid black;">
  <div class="container">
    <header>
      <h2>' . $subject . '</h2>
    </header>
    <main> <br>
      <p>Below Email ID has been subscribed to your newsletter.</p>
      <p>Email: <strong>' . $email . '</strong></p>
    </main> <br><br><br><hr>
    <footer>
      <p style="line-height: 1.5;">Powered by <a href="http://www.syncsolution.co.in/" style="margin-bottom: -20px !important;"><img src="https://vyavsaay.co.in/vdpl/img/logo-vyavsaay.png" style="width: 100px;height:auto;margin-bottom: -6px !important;padding-left: 6px;" alt="Company Logo">
      </a></p>
    </footer>
  </div>
</body>
</html>
';

$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type: text/html\r\n";
$headers .= "From: noreply@website.com\r\n";

if ($email != NULL) {
  mail($to, $subject, $message, $headers);
}

// Redirect to
echo '<script type="text/javascript">
       alert("Thanks for subscribing to our Newsletter!");
       window.location = "index.html";
  </script>';
?>
