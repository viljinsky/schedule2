<?php
    session_start();

    if (isset($_SESSION['current_week'])){
        $current_week = $_SESSION['current_week']+1;
        $_SESSION['current_week']=$current_week;
    }

    $calback = $_SERVER['HTTP_REFERER'];
    header('Location: '.$calback);
