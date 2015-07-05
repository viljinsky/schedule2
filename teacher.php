<!DOCTYPE html>
<?php include_once './proc.php'; ?>         
<html>
    <head>
        <meta charset="UTF-8">
        <title>Преподаватели</title>
        <link rel="stylesheet" href="week.css">
    </head>
    
    <body>
        <header>
        <?php include_once './proc.php';
            include './nav.html'; 
            scheduleAttr();
            include './week.php'; 
         ?>
        </header>   
        
        <article>
        <div style="border:1px solid black;overflow: hidden">
        <div class="leftside">
            <?php  echo teacherList(); ?>
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
                    echo teacherSchedule($teacher_id,$date);
                }
            ?>                
        </div>
        </div>    
        </article>
        <footer>
            <?php include 'footer.html'; ?>
        </footer>
        
    </body>
</html>
