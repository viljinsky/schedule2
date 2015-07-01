<?php
    $db = new SQLite3("../example.db");
    date_default_timezone_set('Europe/Moscow');
    
    /*
     * Список преподавателей
     */
    function getTeacherList(){
        global $db;
        $result = "";
        $data = $db->query("select * from v_teacher");
        while ($row=$data->fetchArray()){
            $result.= "<a href='./teacher.php?teacher_id=".$row['id']."'>".$row['teacher_fio']."</a><br>";
        }
        return $result;
    } 
            
    /*
     * Расписание преподавателя
     */
    function getTeacherSchedule($teacher_id,$data=NULL){
        global $db;
        
        if (empty($data)){
            $data = date_create();
        }
        
        $result="";
        $dayList = $db->query("select * from day_list where (select count(*) from schedule where day_id=day_list.day_no and teacher_id=".$teacher_id.")>0");

        while ($day=$dayList->fetchArray()){
            $day_id = $day['day_no'];
            $schedule = $db->query("select * from v_schedule where day_id=".$day_id." and teacher_id=".$teacher_id);
            $result.= '<table border="1px">';
            $result.= '<tr><th colspan="4">'.$day['day_caption']." ". date_format($data, 'Y m d')."</th></tr>";
            
            while ($subject=$schedule->fetchArray()){
                $result.= '<tr>'
                        . '<td>'.$subject['lesson_time'].'</td>'
                        . '<td>'.$subject['depart_label'].'</td>'
                        . '<td>'.$subject['subject_name'].'</td>'
                        . '<td>'.$subject['room'].'</td>'
                        . '</tr>';
            }
            $result.= '</table>';
        }
        return $result;
    }
            
    
    /*
     *                 Список классов
     */

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
            $dow  = date_format($data, 'N');
            $interval = new DateInterval('P'.--$dow.'D');
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
        
        while ($day=$dayList->fetchArray()){
            date_add($data, $interval);
            $sdata = date_format($data, 'd M');
            
            $result .='<table border="1px" width="300px">';
            
            $result .='<tr><th colspan="4" align="left">'.$day['day_caption'].' '.$sdata.'</th></tr>';
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
                                .$r['room'].'<br>'.$r['teacher']
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
                                    .$r['room'].'<br>'.$r['teacher']
                                    .'</td></tr>';
                        }
                    }
                }
                $result .='</table>';
                
            }
        return $result;

    }  


