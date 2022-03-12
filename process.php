<?php
session_start();
require('new_connection.php');

// Condition
if(isset($_POST['action']) && $_POST['action'] == 'register'){
    register_user($_POST); // Use the actual post
}

else if(isset($_POST['action']) && $_POST['action'] == 'login'){
    login_user($_POST); // Use the actual post
}
else if(isset($_POST['action']) && $_POST['action'] == 'forgot'){
    // login_user($_POST); // PAPALITAN NATIN TOHHHH
    forgot_password($_POST);
}
else{ //Malicious navigation to process.php or someone is trying to log off
    session_destroy();
    header('location: index.php');
}

function register_user($post){ //just a parameter called post
    $_SESSION['errors'] = array();
    //----------------Begin validation check--------------//
    if(empty($post['first_name'])){
        $_SESSION['errors'][] = "First name can't be blank!";
    }
    if(strlen($post['first_name']) < 2 && !empty($post['last_name'])){
        $_SESSION['errors'][] = "First name should contain atleast 2 characters!";
    }
    if(!ctype_alpha($post['first_name'])){
        $_SESSION['errors'][] = "First name must be letters only!";
    }
    if(!ctype_alpha($post['last_name'])){
        $_SESSION['errors'][] = "Last name must be letters only!";
    }
    if(strlen($post['last_name']) < 2 && !empty($post['first_name'])){
        $_SESSION['errors'][] = "Last name should contain atleast 2 characters!";
    }
    if(empty($post['last_name'])){
        $_SESSION['errors'][] = "Last name can't be blank!";
    }
    if(empty($post['password'])){
        $_SESSION['errors'][] = "Password can't be blank!";
    }
    if($post['password'] !== $post['confirm_password']){
        $_SESSION['errors'][] = "Password must match!";
    }
    if(empty($post['email'])){
        $_SESSION['errors'][] = "Email can't be blank!";
    }
    if(!filter_var($post['email'], FILTER_VALIDATE_EMAIL)) {
        $_SESSION['errors'][] = "Email must be valid!";
    }
    if (!preg_match('/^[0-9]*$/', $post['contact'])) {
        // Error
        $_SESSION['errors'][] = "Phone number must be valid!";
      }
    //----------End validation check--------------------//
    if(count($_SESSION['errors']) > 0){  //If i have any errors at all
        echo "Try die()";
        die();
        header('location: index.php');
    }
    else{ //Now you need to insert the data in your database
        $password = md5($_POST['password']);
       $query = "INSERT INTO table1 (first_name, last_name, email, password, created_at, updated_at, contact_num) 
       VALUES('{$post['first_name']}' ,'{$post['last_name']}', '{$post['email']}', '{$password}', NOW(), NOW(), '{$post['contact']}')";
       echo $query;
       run_mysql_query($query);
       $_SESSION['success_message'] = "User succesfully created";
       header('location: index.php');
    }
}

function login_user($post){ //just a parameter called post
    $password = md5($_POST['password']);
    $query = "SELECT * FROM table1 WHERE table1.password ='{$password}' AND table1.email = '{$post['email']}'";
    $user = fetch_all($query); //go and attempt to grab user with above credentials
    if(count($user) > 0){
        $_SESSION['user_id'] = $user[0]['id'];
        $_SESSION['first_name'] = $user[0]['first_name'];
        $_SESSION['logged_in'] = TRUE;
        header('location: success.php');
    }
    else{
        $_SESSION['errors'] = array();
        $_SESSION['errors'][]= "can't find user with these credentials";
        header('location: index.php');
    }
}

function forgot_password($post){ //just a parameter called post
    $new_password = md5('Village88');
    $query = "UPDATE table1
    SET password='$new_password'
    WHERE contact_num= '{$post['contacts']}'";
    echo $query."<br>";
    $user = run_mysql_query($query);
    var_dump($user);
    die();
    if(count($user) > 0){
        $_SESSION['user_id'] = $user[0]['id'];
        // $_SESSION['first_name'] = $user[0]['first_name'];
        // $_SESSION['logged_in'] = TRUE;
        header('location: success.php');
    }
    else{
        $_SESSION['errors'] = array();
        $_SESSION['errors'][]= "can't find user with these credentials";
        header('location: index.php');
    }
}

?>