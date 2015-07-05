<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <title>Пример расписания</title>
        <link rel="stylesheet" href="./week.css">
    </head>
    <body>
        <header>
        <?php include_once './proc.php';
            include './nav.html'; 
            scheduleAttr(); ?>
        </header>
        
        <article>
            
        <h1>Списки</h1>
        <div style="border:1px solid black;overflow: hidden; background: #f0f0f0;">
            <div id="depart_list" style="float:left;width:50%;padding:20px;">
                 <?php echo departList(); ?> 
            </div>    
            <div id="teacher_pist" style="margin-left: 51%;padding:20px">
                <?php echo teacherList(); ?>
            </div>    
        </div>
        
        </article>
            
        <footer>
           <?php  include './footer.html' ;?>
        </footer>

        
    </body>
</html>