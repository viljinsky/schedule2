<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        
    </head>
    <body>
        <h1>Пример распсания</h1>
        <?php include './nav.html'; ?>
        
        <?php
        date_default_timezone_set('Europe/Moscow');
        
//        setlocale(LC_TIME, ); 
//        print strftime('%A');        
        
        $d1 = new DateTime();
        date_sub($d1, new DateInterval('P3D'));
        for ($k=0;$k<7;$k++){
            echo print_r($d1).' '.$d1->format("Y M D w").'<br>';
            date_add($d1,new DateInterval('P1D'));
        }
        
        
        ?>        
        
        <h1>Расчёт дня</h2>

        <?php
        $now = date_create('2015-07-01');
        $w = $now->format('w');
        $days = array(
            0=>'Восресенье',
            1=>'Понедельник',
            2=>'Вторник',
            3=>'Среда',
            4=>'Четверг',
            5=>'Пятница',
            6=>'Суббота'
            );
        echo $days[$w].' '.$w.'<br>';
        
        date_sub($now, new DateInterval('P'.--$w.'D'));
        echo 'Понедельник '.  date_format($now, 'Y M d').'<br>';
        echo 'Расчёт<br>';
        
        for ($n=0;$n<7;$n++){
            echo date_format($now, 'Y m d D').' ['.$days[date_format($now, 'w')].']<br>';
            date_add($now, new DateInterval('P1D'));
        }
        ?>
        
    </body>
</html>