<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/main.css">
    <title>Document</title>
</head>

<body>
    <form
        action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"
        method="post">

        <input type="number" name="num01" placeholder="Number 1">
        <select name="operator">
            <option value="add">+</option>
            <option value="minus">-</option>
            <option value="multiply">*</option>
            <option value="divide">/</option>
        </select>
        <input type="number" name="num02" placeholder="Number 2">
        <button>Calculate</button>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        $num01 = (float) filter_input(INPUT_POST,"num01",FILTER_SANITIZE_NUMBER_FLOAT);
        $num02 = (float) filter_input(INPUT_POST,"num02",FILTER_SANITIZE_NUMBER_FLOAT);
        $operator = (string) htmlspecialchars($_POST["operator"]);
    }
    ?>
</body>

</html>