<!DOCTYPE html>
<?php    include_once './proc.php'; ?>

<html lang="ru">
    
    <head>
        <meta charset="UTF-8">
        <title>Классы</title>
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
                <?php
                    echo departList();
                ?>
            </div>

            <div class="rightside">
                <?php
                    $depart_id =  filter_input(INPUT_GET, 'depart_id');
                    $data = NULL;
                    if (isset($_SESSION['weekno'])){
                        $weekno=$_SESSION['weekno'];
                        $data = date_create('2015-1-1');
                        $dn = date_format($data,'N')-1;
                        $n = ($weekno-1)*7-$dn;
                        date_add($data,new DateInterval('P'.$n.'D'));
                    }
                    if (!empty($depart_id)){
                        echo departSchedule($depart_id,$data);
                    }
                ?>
            </div>    
        </div>  
        </article>    
        <footer>
            <?php include './footer.html';?>
        </footer>
        
    </body>
</html>
