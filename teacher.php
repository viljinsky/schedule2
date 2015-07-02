<!DOCTYPE html>
<?php include_once './proc.php'; ?>         
<html>
    <head>
        <meta charset="UTF-8">
        <title>Преподаватели</title>
        <link rel="stylesheet" href="week.css">
    </head>
    
    <body>
        
        <?php include_once './proc.php';
            include './nav.html'; 
            scheduleAttr();
            include './week.php'; 
         ?>
        
        
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
        
    </body>
</html>
