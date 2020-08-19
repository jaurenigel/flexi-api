<?php

namespace App;

class MailTempate
{
    public function getName($name)
    {
       return $name;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MAIL</title>
</head>
<body>
    <?php
        $cl = new MailTempate;
        echo $cl->getName;
    ?>
</body>
</html>
