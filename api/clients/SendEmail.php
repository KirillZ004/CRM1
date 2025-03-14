<?php
$header = $_POST['header'];
$main = $_POST['main'];
$footer = $_POST['footer'];
echo "  
<!DOCTYPE html>
<html lang='ru'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Важное сообщение для наших клиентов</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image:url('../../img/backround.jpg');
            background-size: cover;
            background-attachment: fixed;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.8);
            border: 1px solid #555;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
        }
        .header {
            background-color: #333;
            color: #fff;
            padding: 10px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            padding: 20px;
            color: #fff;
        }
        .button {
            background-color: #4CAF50;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .button:hover {
            background-color: #3e8e41;
        }
        .footer {
            background-color: #333;
            color: #fff;
            padding: 10px;
            text-align: center;
            border-radius: 0 0 10px 10px;
        }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h2>$header</h2>
        </div>
        <div class='content'>
            <p>Уважаемые клиенты,</p>
            <p>$main</p>

        </div>
        <div class='footer'>
            <p>&copy; $footer</p>
        </div>
    </div>
</body>
</html>


";


?>
            <!-- <p>Чтобы узнать больше о наших новостях и акциях, нажмите на кнопку ниже:</p>
            <a href='https://вашсайт.рф' class='button'>Перейти на сайт</a>
            <p>Благодарим за доверие и ждём вас на нашем сайте!</p> -->