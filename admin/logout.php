<?php require '../includes/functions.php';unset($_SESSION['admin']);flash('success','Admin logged out.');go('admin-login.php');
