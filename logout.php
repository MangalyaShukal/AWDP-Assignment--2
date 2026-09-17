<?php require 'includes/functions.php';session_unset();session_destroy();session_start();flash('success','You have been logged out.');go('index.php');
