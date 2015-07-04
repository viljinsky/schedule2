<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <title>Расписание 2</title>
    </head>
    <body>
        <?php include_once './proc.php';
            include './nav.html'; 
            scheduleAttr();
//            include './week.php'; 
         ?>
        
        <h1>Списки</h1>
        
        <div id="depart_list" style="position: absolute;width:50%;background: #ccc;">
             <?php echo getDepartList(); ?> 
        </div>    
        <div id="teacher_pist" style="margin-left: 51%;background: #ccc;">
            <?php echo getTeacherList(); ?>
        </div>    
        
        
            
        <h1>Расчёт дня</h2>
        <footer>Ильинский В.В.</footer>

        
    </body>
</html>