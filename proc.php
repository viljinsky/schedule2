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
        echo '<h1>'.$result['schedule_span'].'</h1>';
        echo '<h2>'.$result['schedule_title'].'</h2>';
        echo '<h2>'.$result['educational_institution'].'</h2>';
        
        return $result;
    }
    
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
        $cr="\n\r";
        $curriculumList = $db->query("select * from curriculum");
        while ($curriculum=$curriculumList->fetchArray()){
            $result.= $curriculum['caption']."<br>".$cr;

            $departList = $db->query("select * from v_depart where curriculum_id=".$curriculum['id']." order by curriculum_id,skill_id ");
            while ($depart=$departList->fetchArray()){
                $result.="<a href='./depart.php?depart_id=".$depart['depart_id']."'>"
                        .$depart['depart_label']."</a><br>".$cr;
            }
        }
        return $result;
    }

    //------------------------------------------------------------------------//
    // Возвращает таблиу <table> расписание класса на неделю                  //
    //------------------------------------------------------------------------//
    function getSubjectCell($data2){
        $result = '';
        $rowcout = 0;
        $d2 = $data2->fetchArray();
        if (!empty($d2)){
            do{ 
                $rowcout+=1;
                $result.='<td>'.$d2['subject_name'].'</td>';
                $result.='<td>'.$d2['room'].'</td>';
                $result.='<td>&nbsp;</td>';
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
    
    function getTable($depart_id,$day){
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
        $result.='<td colspan="4">&nbsp'.  date_format($day, "Y-m-d N l").'</td>';
        $result.='</tr>';
        while ($d=$data->fetchArray()){
            
            $stmt2->bindParam("bell_id", $d['bell_id']);
            $data2 = $stmt2->execute();
            $a= getSubjectCell($data2);
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
    
    function getDepartSchedule2($depart_id,$first_date=null){
        if (empty($first_date)){
            $first_date=date_create();
            $p = date_format($first_date, 'N') -1;
            date_sub($first_date,new DateInterval('P'.$p.'D'));
        }
        $result = '';
        $result.='<table border="1px">';
        $dayno = 0;
        $colCount = 3;
        $rowCount = 3;
        $dayCount = 7;
        $result.="<tr>";
        $result.="<td colspan=3>$depart_id</td>";                
        $result.="</tr>";
        
        for ($row=0;$row<3;$row++){
            $result.='<tr valign="top">';
            for ($col=0;$col<3;$col++){
                $dayno=$row*$colCount+$col;
                if ($dayno<$dayCount){
                    $day = date_create(date_format($first_date,'Y-m-d'));
                    date_add($day,new DateInterval('P'.$dayno.'D'));
                    $result.='<td>'. getTable($depart_id,$day).'</td>';
                } else {
                    $result.='<td>&nbsp;</td>';
                }
            }
            $result.='</tr>';
        }
        $result.='</table>';
        return $result;
    }
    //------------------------------------------------------------------------//
    
//    function getDepartSchedule($depart_id,$data=NULL){
//        global  $db;
//        
//        if (empty($data)){
//            $data = new DateTime();
//            $dow  = date_format($data, 'N');
//            $interval = new DateInterval('P'.--$dow.'D');
//            date_sub($data, $interval);
//        }
//        $interval = new DateInterval('P1D');
//
//        $queryDayList ="select distinct day_list.day_no,day_list.day_caption from day_list ,depart ,shift_detail
//                        where depart.shift_id=shift_detail.shift_id and day_list.day_no=shift_detail.day_id
//                        and depart.id=";
//        $queryBellList="select distinct bell_list.bell_id,bell_list.time_start from bell_list,depart,shift_detail
//                        where depart.shift_id=shift_detail.shift_id 
//                        and shift_detail.bell_id=bell_list.bell_id
//                        and depart.id=";
//
//        $result = '';
//        $dayList = $db->query($queryDayList.$depart_id);
//        $bellList = $db->query($queryBellList.$depart_id);
//        $cr = "\n\r";
//        while ($day=$dayList->fetchArray()){
//            date_add($data, $interval);
//            $sdata = date_format($data, 'd M');
//            
//            $result .='<table border="1px" width="300px">'.$cr;
//            
//            $result .="<tr>".$cr."<th colspan='4' align='left'>".$day['day_caption'].' '.$sdata."</th>".$cr."</tr>".$cr;
//            $day_id = $day['day_no'];
//            while ($bell=$bellList->fetchArray()){
//                $result.= '<tr>'.$cr;
//                    $result .='<td>'.$bell['time_start']."</td>";
//                    $bell_id=$bell['bell_id'];
//
//                    $q = $db->query("select * from v_schedule where depart_id=".$depart_id." and day_id=".$day_id." and bell_id=".$bell_id);
//                    $r = $q->fetchArray();
//                    if (empty($r)){
//                        $result .="<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>\n\r</tr>".$cr;
//                    } else {
//                        $subject_name = $r['subject_name'];
//                        $result .='<td>'.$subject_name.'</td>'
//                                . '<td>'.$r['group_label'].'</td>'
//                                . '<td>'.$r['room'].'<br>'.$r['teacher']."</td>".$cr
//                                . "</tr>".$cr;
//                        while ($r=$q->fetchArray()){
//                            if ($r['subject_name']==$subject_name){
//                                $s="&nbsp;";
//                            } else {
//                                $s=$subject_name;
//                            }
//                            $subject_name=$r['subject_name'];
//                            $result .='<tr>'
//                                    . '<td>&nbsp</td>'
//                                    . '<td>'.$s.'</td>'
//                                    . '<td>'.$r['group_label'].'</td>'
//                                    . '<td>'.$r['room'].'<br>'.$r['teacher']."</td>".$cr
//                                    . "</tr>".$cr;
//                        }
//                    }
//                }
//                $result .="</table>".$cr;                
//            }
//        return $result;
//
//    }  


