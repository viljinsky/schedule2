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
                if (isset($_SESSION['weekno'])){
                    $weekno=$_SESSION['weekno'];
                    $date = date_create("2015-1-1");
                    $dow = date_format($date,"w");
                    $n=($weekno-1)*7 - $dow+1;
                    date_add($date, new DateInterval("P".$n."D"));
                }
                
                if (!empty($teacher_id)){
                    echo getTeacherSchedule($teacher_id,$date);
                }
            ?>                
        </div>
        
    </body>
</html>
