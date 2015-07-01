<?php
        session_start();
        date_default_timezone_set('Europe/Moscow');
        $now = new DateTime();
        if (!isset($_SESSION['current_week'])){
            $_SESSION['current_week']= date_format($now, 'W');
        }
        $weekno = $_SESSION['current_week']; 
        if (isset($_SERVER['HTTP_REFERER'])){
            $callback=$_SERVER['HTTP_REFERER'];
        }
        
        function nextW(){
            global $weekno,$callback;
            $_SESSION['current_week']=$weekno+1;
            header('Location: '.$callback);
        }
        
        function priorW(){
            global $weekno,$callback;
            $_SESSION['current_week']=$weekno-1;
            header('Location: '.$callback);            
        }
        
        function currentW(){
            global $now,$callback;
            $_SESSION['current_week']=  date_format($now, 'W');
            header('Location: '.$callback);            
            
        }
        
        $week = filter_input(INPUT_GET, 'week');
        if (!empty($week)){
            switch ($week){
                case 'next':nextW();break;
                case 'prior':priorW();break;
                case 'current':currentW();break;
            }
            return;
        }

        $dow = date_format($now, 'w');
        $size = ($weekno-1)*7-$dow;
        $year = date_format($now,'Y');

        $data = date_create($year.'-1-1');
        date_add($data, new DateInterval('P'.$size.'D'));
        $data1 = date_create(date_format($data,'Y').'-'.date_format($data,'m').'-'.date_format($data,'d'));
        date_add($data1, new DateInterval('P6D'));


        echo '<p>текущая неделя -'. $weekno
             .' size    -'.  $size
             .' неделя с -'.date_format($data,'d m Y')
             .'  по -'.date_format($data1,'d m Y').'</p>';
    ?>    
    <nav>
        <a href="week.php?week=prior">Предыдущая</a>
        <a href="week.php?week=next">Следующая</a>
        <a href="week.php?week=current">Текущая</a>
    </nav>
