<?php
    $db = new SQLite3("../example.db");
    date_default_timezone_set('Europe/Moscow');
    
    function scheduleAttr(){
        global $db;
        $data = $db->query('select * from attr');
        $result = array();
        while ($row=$data->fetchArray()){
            $param_name=$row['param_name'];
            $param_value=$row['param_value'];
            $result[$param_name]=$param_value;
//            echo $param_name.' '.$param_value.'<br>';
            
        }
        $date_begin = date_create($result['date_begin']);
        $date_end =  date_create($result['date_end']);
        echo '<h2>'.$result['educational_institution'].'</h2>';
        echo '<h1>'.$result['schedule_span'].'</h1>';
        echo '<h2>'.$result['schedule_title'].'</h2>';
        echo '<b> c '.  substRusDate($date_begin, 'd M').' по '. substRusDate($date_end,'d M Y г.').'</b>';
        
        return $result;
    }
    
    /*
     * Список преподавателей
     */
    function teacherList(){
        global $db;
        
        $sql1 = 'select * from profile where profile_type_id=1';
        $data1 = $db->query($sql1);
        
        $sql2 = 'select * from v_teacher where profile_id=:profile_id';
        $stmt2 = $db->prepare($sql2);
        $result = 'Список перподавателей<br>';
        while ($row1=$data1->fetchArray()){
            $result.='<b>'.$row1['profile_name'].'</b><br>';
            $stmt2->bindParam('profile_id', $row1['id']);
            $data2 = $stmt2->execute();
            while ($row2=$data2->fetchArray()){
                $result.='<a href="teacher.php?teacher_id='.$row2['id'].'">'.$row2['teacher_fio'].'</a><br>';
            }
        }
        
//        $result = "";
//        $data = $db->query("select * from v_teacher");
//        while ($row=$data->fetchArray()){
//            $result.= "<a href='./teacher.php?teacher_id=".$row['id']."'>".$row['teacher_fio']."</a><br>";
//        }
        return $result;
    } 
            
    /*
     * Расписание преподавателя
     */
    
    
    function dateNavigator($week_no=null){
        if (empty($week_no)){
            $week_no=  date_format(create_date(), 'W');
        }
        
        return '<table border="1px" align="center" cellpadding="5px>" style="font:bold;background:#ccc"'
                . '<tr><td><a href="week.php?week=prior">Предыдущая'.($week_no-1).'</a></td>'
                . '<td><a href="week.php?week=current">Текущая'.($week_no).'</a></td>'
                . '<td><a href="week.php?week=next">Следующая'.($week_no+1).'</a></td>'
                . '</tr>'
                . '</table>';
        
    }
    
    function substRusDate($date,$format){
        $trans = array(
            'Monday'=>'Понедельник',
            'Tuesday'=>'Вторник',
            'Wednesday'=>'Среда',
            'Thursday'=>'Четверг',
            'Friday'=>'Пятница',
            'Saturday'=>'Суббота',
            'Sunday'=>'Восресенье',
            'Jan'=>'Январь',
            'Feb'=>'Февраль',
            'Mar'=>'Март',
            'Apr'=>'Апрель',
            'May'=>'Май',
            'Jun'=>'Июнь',
            'Jul'=>'Июль',
            'Aug'=>'Август',
            'Sep'=>'Сентябрь',
            'Oct'=>'Октябрь',
            'Nov'=>'Ноябрь',
            'Dec'=>'Декабрь',
            );
        return strtr(date_format($date, $format),$trans);
    }
    
    function teacherSchedule($teacher_id,$data=NULL){
        global $db;
        
        if (empty($data)){
            $data = date_create("2015-1-1");
        }
        
        $sql = 'select * from v_teacher where id='.$teacher_id;
        $query = $db->query($sql);
        $row= $query->fetchArray();
        $teacher_name=$row['teacher_fio'];
        $teacher_profile=$row['profile_name'];
        
        $result="";
        $dayList = $db->query("select * from day_list where (select count(*) from schedule where day_id=day_list.day_no and teacher_id=".$teacher_id.")>0");

        
        $result.='<div>';
        $result.='Преподаватель <b>'.$teacher_name.'</b> ('.$teacher_profile.')';
        $result.= dateNavigator(date_format($data,'W'));
        
        while ($day=$dayList->fetchArray()){
            $day_id = $day['day_no'];
            $schedule = $db->query("select * from v_schedule where day_id=".$day_id." and teacher_id=".$teacher_id);
            
            $result.= '<table border="1px" width="100%">';
            
            $d = date_create(date_format($data,'Y-m-d'));
            date_add($d,new DateInterval("P".($day_id-1)."D"));
            
//            $result.= '<tr><th colspan="4">'.date_format($d, 'l (d M Y)')."</th></tr>";
            $result.= '<tr><th colspan="4">'.  substRusDate($d, 'l (d M Y)')."</th></tr>";
            
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
        $result.='</div>';
        return $result;
    }
            
    
    /*
     *                 Список классов
     */

    function departList(){
        global $db;
        $sql1 = 'select id as curriculum_id,caption from curriculum';
        $query1 = $db->query($sql1);
        
        $sql2 = 'select * from v_depart where curriculum_id=:curriculum_id order by skill_id';
        $stmt = $db->prepare($sql2);
        
        $result = 'Список классов<br>';
        while ($row1=$query1->fetchArray()){
            $result.='<b>'.$row1['caption'].'</b><br>';
            $stmt->bindParam("curriculum_id", $row1['curriculum_id']);
            $query2=$stmt->execute();
            while ($row2=$query2->fetchArray()){
                $result.='<a href="depart.php?depart_id='.$row2['depart_id'].'">'.$row2['depart_label'].'</a><br>';
            }
        }
        return $result;
    }

    //------------------------------------------------------------------------//
    // Возвращает таблиу <table> расписание класса на неделю                  //
    //------------------------------------------------------------------------//
    function departCell($data2){
        $result = '';
        $rowcout = 0;
        $d2 = $data2->fetchArray();
        if (!empty($d2)){
            do{ 
                $rowcout+=1;
                $result.='<td>'.$d2['subject_name'].'</td>';
                $result.='<td>'.(empty($d2['room'])?'каб.?':$d2['room']).'</td>';
                $result.='<td>'.$d2['group_label'].'</td>';
                $result.='</tr>';
         } while ($d2=$data2->fetchArray());
       } else {
            $result.='<td>&nbsp;</td>';
            $result.='<td>&nbsp;</td>';
            $result.='<td>&nbsp;</td>';
            $result.='</tr>';
        }
        
        
        return array('rowcount'=>$rowcout,'str'=>$result);
    }
    
    /**
     * Расписание занятий класса на день
     * @global SQLite3 $db
     * @param type $depart_id
     * @param type $day
     * @return string
     */
    function departDayGrid($depart_id,$day){
        global $db;

        // список дней
        $sql = 'select distinct c.* from depart a inner join shift_detail b '
        .' on a.shift_id=b.shift_id'
        .' inner join bell_list c on c.bell_id=b.bell_id	'
        .' where a.id=:depart_id';
        $stmt = $db->prepare($sql);
        $stmt->bindValue('depart_id', $depart_id);
        $data = $stmt->execute();
        
        // расписание
        $sql2 = 'select * from v_schedule '
                . 'where depart_id=:depart_id'
                . ' and day_id=:day_id'
                . ' and bell_id=:bell_id';
        
        $stmt2 = $db->prepare($sql2);
        $stmt2->bindParam('depart_id', $depart_id);
        $stmt2->bindParam('day_id', date_format($day, 'N'));

        $result ='';
        $result.='<table border="1px" width="100%">';
        $result.='<tr>';
        $result.='<th colspan="4">&nbsp'. substRusDate($day,'l (d M Y)').'</th>';
        $result.='</tr>';
        while ($d=$data->fetchArray()){
            
            $stmt2->bindParam("bell_id", $d['bell_id']);
            $data2 = $stmt2->execute();
            $a= departCell($data2);
            $rowspan=$a['rowcount'];
            
            $result.='<tr>';
            if ($rowspan<2){
                $result.='<td >'.$d['time_start'].'</td>';
            }
            else {
                $result.='<td rowspan="'.$rowspan.'">'.$d['time_start'].'</td>';                
            }
            $result .=$a['str'];
        }
        $result.='</table>';
        return $result;
    }
    
    /**
     * Расписание занятий класса на неделю
     * @global SQLite3 $db
     * @param type $depart_id
     * @param type $first_date
     * @return string
     */
    function departSchedule($depart_id,$first_date=null){
        global $db;
        
        $sql = 'select label from depart where id=:depart_id';
        $stmt = $db->prepare($sql);
        $stmt->bindParam('depart_id', $depart_id);
        $query = $stmt->execute();
        if ($row=$query->fetchArray()){
            $depart_label = $row['label'];
        }
        
        
        $sqlDayList = 'select distinct b.day_id '
                . 'from depart a '
                . 'inner join shift_detail b on a.shift_id=b.shift_id '
                . 'where a.id=:depart_id';
        $stmt2 = $db->prepare($sqlDayList);
        $stmt2->bindParam('depart_id', $depart_id);
        $dayList=$stmt2->execute();
        
        if (empty($first_date)){
            $first_date=date_create();
            $p = date_format($first_date, 'N') -1;
            date_sub($first_date,new DateInterval('P'.$p.'D'));
        }
        $result = '';
        $result.='<table width="100%">';
        $dayno = 0;
        $colCount = 3;
        $rowCount = 3;
        $dayCount = 7;
        $result.="<tr>";
        $result.="<td colspan=3>$depart_label &nbsp ".dateNavigator(date_format($first_date,'W'))."</td>";                
        $result.="</tr>";
        
        $r = $dayList->fetchArray();
        for ($row=0;$row<3;$row++){
            $result.='<tr valign="top">';
            for ($col=0;$col<3;$col++){
                if (!empty($r)){
                    $dayno=$r['day_id']-1;
                    $day = date_create(date_format($first_date,'Y-m-d'));
                    date_add($day,new DateInterval('P'.$dayno.'D'));
                    $result.='<td>'.departDayGrid($depart_id,$day).'</td>';
                    $r=$dayList->fetchArray();
                } else {
                    $result.='<td>&nbsp;</td>';
                }
            }
            $result.='</tr>';
            if (empty($r)){
                break;
            }
        }
        $result.='</table>';
        return $result;
    }
    //------------------------------------------------------------------------//
    


