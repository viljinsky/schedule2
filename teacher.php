<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php include_once './proc.php'; ?>         

<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <style>
            .teable{padding: 20px;}
            .leftside{float: left;width:20%;background: #f0f0f0;}
            .rightside{margin-left: 21%;background: #f0f0f0;}
            
            table {margin-bottom: 10px; background: #fff; border-collapse: collapse;border-color: lightgray;}
            th {background: lightslategrey;color: white;}
        </style>
    </head>
    <body>
        <?php 
            if (isset($_SERVER['HTTP_REFERER'])){
                $callback = $_SERVER['HTTP_REFERER'];
                echo $callback;
            }
        ?>

        <?php 
            include './nav.html';
            include './week.php';
        ?>
        
        
        <div class="teable">
            <div class="leftside">
                <?php  echo getTeacherList(); ?>
            </div>
            
            <div class="rightside">
        <?php   
            $teacher_id = filter_input(INPUT_GET, 'teacher_id');
            if (!empty($teacher_id)){
                echo getTeacherSchedule($teacher_id);
            }
        ?>
                
            </div>
        </div>
        
        
    </body>
</html>
