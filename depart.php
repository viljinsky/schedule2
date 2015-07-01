<!DOCTYPE html>

<?php
    $db = new SQLite3("../example.db");
    date_default_timezone_set('Europe/Moscow');
    

    function getDepartList(){
        global $db;
        $result = '';
        $curriculumList = $db->query("select * from curriculum");
        while ($curriculum=$curriculumList->fetchArray()){
            $result.= $curriculum['caption']."<br>";

            $departList = $db->query("select * from v_depart where curriculum_id=".$curriculum['id']." order by curriculum_id,skill_id ");
            while ($depart=$departList->fetchArray()){
                $result.="<a href='/schedule2/depart.php/?depart_id="
                        .$depart['depart_id']
                        ."'>"
                        .$depart['depart_label']
                        ."</a><br>";
            }
        }
        return $result;
    }

    // Возвращает таблиу <table> расписание класса на неделю
    function getDepartSchedule($depart_id,$data=NULL){
        global  $db;
        
        if (empty($data)){
            $data = new DateTime();
            $dow  = date_format($data, 'w');
            $interval = new DateInterval('P'.$dow.'D');
            date_sub($data, $interval);
        }
        $interval = new DateInterval('P1D');

        $queryDayList ="select distinct day_list.day_no,day_list.day_caption from day_list ,depart ,shift_detail
                        where depart.shift_id=shift_detail.shift_id and day_list.day_no=shift_detail.day_id
                        and depart.id=";
        $queryBellList="select distinct bell_list.bell_id,bell_list.time_start from bell_list,depart,shift_detail
                        where depart.shift_id=shift_detail.shift_id 
                        and shift_detail.bell_id=bell_list.bell_id
                        and depart.id=";

        $result = '';
        $dayList = $db->query($queryDayList.$depart_id);
        $bellList = $db->query($queryBellList.$depart_id);
        $result .='<table border="1">';
        while ($day=$dayList->fetchArray()){
            date_add($data, $interval);
            $sdata = date_format($data, 'd M');
            $result .='<tr><th colspan="4">'.$sdata.' '.$day['day_caption'].'</th></tr>';
            $day_id = $day['day_no'];
            while ($bell=$bellList->fetchArray()){
                echo '<tr>';
                    $result .='<td>'.$bell['time_start']."</td>";
                    $bell_id=$bell['bell_id'];

                    $q = $db->query("select * from v_schedule where depart_id=".$depart_id." and day_id=".$day_id." and bell_id=".$bell_id);
                    $r = $q->fetchArray();
                    if (empty($r)){
                        $result .='<td>&nbsp;</td><td>&nbsp;</td><td></td></tr>';
                    } else {
                        $subject_name = $r['subject_name'];
                        $result .='<td>'
                                .$subject_name
                                .'</td><td>'
                                .$r['group_label']
                                .'</td><td>'
                                .$r['room']
                                .'</td></tr>';
                        while ($r=$q->fetchArray()){
                            if ($r['subject_name']==$subject_name){
                                $s="&nbsp;";
                            } else {
                                $s=$subject_name;
                            }
                            $subject_name=$r['subject_name'];
                            $result .='<tr><td>&nbsp</td><td>'
                                    .$s
                                    .'</td><td>'
                                    .$r['group_label']
                                    .'</td><td>'
                                    .$r['room']
                                    .'</td></tr>';
                        }
                    }
                }
            }
        $result .='</table>';
        return $result;

    }  
        
?>

<html lang="ru">
    
    <head>
    
    <style>
        .leftside {float: left;width:20%;background: #f0f0f0;}
        .rightside {margin-left: 21%;background: #f0f0f0;}
        th {background: lightslategrey;color:whitesmoke;}
    </style>
    </head>
    <body>
      
        <?php include './nav.html'; 
        ?>
        
        
    <div class="leftside">    
        <?php
            echo getDepartList();
        ?>
    </div>
        
    <div class="rightside">
        <?php
//            $data = new DateTime();
//            date_add($data, new DateInterval('P1M10D'));
            
            $depart_id=  filter_input(INPUT_GET, 'depart_id');
            if (!empty($depart_id)){
                echo getDepartSchedule($depart_id,  date_create('2015-09-01'));
            }
        ?>
        
    </div>    
        
    </body>
</html>
