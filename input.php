<?php
    // if ( isset($_POST['input1']) ) {
    //     $result = base64_encode($_POST['input1']);
    //     echo "<h1>$result</h1>";
    // }

    // if ( isset($_POST['input1']) && isset($_POST['input2']) && isset($_POST['input3'])) {
    //     $result = substr_count($_POST['input1'], $_POST['input2'], $_POST['input3'] );
    //     echo "<h1>$result</h1>";
    // }

    if ( isset($_POST['input1']) && isset($_POST['input2']) ) {
        $result = substr_count($_POST['input1'], $_POST['input2'] );
        echo "<h1>$result</h1>";
    }

?>

<form action="" method="POST">
    <input type="text" name="input1">
    <input type="text" name="input2">
    <input type="text" name="input3">
    <button name="submit">Submit</button>
</form>