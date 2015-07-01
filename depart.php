<!DOCTYPE html>

<?php
    include_once './proc.php';
?>

<html lang="ru">
    
    <head>
    
    <style>
        .leftside {float: left;width:20%;background: #f0f0f0;}
        .rightside {margin-left: 21%;background: #f0f0f0;}
        table {background: #fff;border-collapse: collapse; border-color: lightgrey;}
        th {background: lightslategrey;color:white}
    </style>
    </head>
    <body>
        
        <?php 
            if (isset($_SERVER['HTTP_REFERER'])){
                $callback = $_SERVER['HTTP_REFERER'];
                echo $callback;
            }
            if (isset($_SERVER['QUERY_STRING'])){
                echo '?'.$_SERVER['QUERY_STRING'];
                
            }
        ?>
        
      
        <?php 
            include './nav.html'; 
            include './week.php';
        ?>
        
        
    <div class="leftside">    
        <?php
            echo getDepartList();
        ?>
    </div>
        
    <div class="rightside">
        <?php
            $depart_id=  filter_input(INPUT_GET, 'depart_id');
            if (!empty($depart_id)){
                echo getDepartSchedule($depart_id,  date_create('2015-09-01'));
            }
        ?>
        
    </div>    
        
    </body>
</html>
