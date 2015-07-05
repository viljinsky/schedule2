<?php
        session_start();
        date_default_timezone_set('Europe/Moscow');

        // текущая неделя
        if (!isset($_SESSION['weekno'])){
            $weekno = date_format(date_create(), "W");
            $_SESSION['weekno']=$weekno;
        } else {
            $weekno = $_SESSION['weekno'];
        }
        // колбек
        if (isset($_SERVER['HTTP_REFERER'])){
            $callback=$_SERVER['HTTP_REFERER'];
        }
        
        function nextW(){
            global $weekno,$callback;
            $_SESSION['weekno']=$weekno+1;
            header('Location: '.$callback);
        }
        
        function priorW(){
            global $weekno,$callback;
            $_SESSION['weekno']=$weekno-1;
            header('Location: '.$callback);            
        }
        
        function currentW(){
            global $callback;
            $_SESSION['weekno']=  date_format(date_create(), 'W');
            header('Location: '.$callback);            
            
        }
        /*
         * Определение даты понедельника-воскресения по номеру недели в году
         */
        function getSchedulePeriod($year,$weekno){
            $a = array();
            $data = date_create($year.'-1-1');
            $size = ($weekno-1)*7 ;
            $dow = date_format($data, 'N')-1;
            date_add($data, new DateInterval('P'.$size.'D'));
            date_sub($data, new DateInterval('P'.$dow.'D'));
            
            $a['date_begin'] = $data;
            
            $data1 = date_create(date_format($data, 'Y-m-d'));
            date_add($data1, new DateInterval('P6D'));
            
            $a['date_end']=$data1;
            return $a;
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

        $year = date_format(date_create(), 'Y');
        $a = getSchedulePeriod($year, $weekno);

        echo '<p>текущая неделя '. $weekno
             .' ( с '.date_format($a['date_begin'],'d m Y')
             .'  по '.date_format($a['date_end'],'d m Y').')</p>';

    ?>    
<!--    <nav>
        <a href="week.php?week=prior">Предыдущая</a>
        <a href="week.php?week=current">Текущая</a>
        <a href="week.php?week=next">Следующая</a>
    </nav>-->
