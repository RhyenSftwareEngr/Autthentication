<?php
session_start();
echo "Howdy! {$_SESSION['first_name']}!";
echo "<a href='process.php'> Log off </a>";
?>