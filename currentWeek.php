<?php

    session_start();
    date_default_timezone_set('Europe/Moscow');
    $now =  new DateTime();
    $_SESSION['current_week']=  date_format($now,'W');
    
    $calback = $_SERVER['HTTP_REFERER'];
    header('Location: '.$calback);
            
