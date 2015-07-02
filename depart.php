<!DOCTYPE html>
<?php    include_once './proc.php'; ?>

<html lang="ru">
    
    <head>
        <meta charset="UTF-8">
        <title>Классы</title>
        <link rel="stylesheet" href="week.css">
        
    </head>
    
    <body>
        
        <?php include_once './proc.php';
            include './nav.html'; 
            scheduleAttr();
            include './week.php'; 
         ?>
        
        
        <div class="leftside">    
            <?php
                echo getDepartList();
            ?>
        </div>

        <div class="rightside">
            <?php
                $depart_id =  filter_input(INPUT_GET, 'depart_id');
                if (!empty($depart_id)){
                    echo getDepartSchedule($depart_id, date_create('2015-09-01'));
                }
            ?>
        </div>    
        
    </body>
</html>
