<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <style>
            .teable{padding: 20px;}
            .leftside{float: left;width:20%;background: #f0f0f0;}
            .rightside{margin-left: 21%;background: #f0f0f0;}
        </style>
    </head>
    <body>

        <?php include './nav.html'; ?>
        
        
        <div class="teable">
            <div class="leftside">
        <?php
            $db = new SQLite3("../example.db");
            $data = $db->query("select * from v_teacher");
            while ($row=$data->fetchArray()){
                echo "<a href='./teacher.php?teacher_id=".$row['id']."'>".$row['teacher_fio']."</a><br>";
            }
        ?>
                
            </div>
            <div class="rightside">
        <?php    
            $teacher_id = filter_input(INPUT_GET, 'teacher_id');
            if (!empty($teacher_id)){
                $dayList = $db->query("select * from day_list where (select count(*) from schedule where day_id=day_list.day_no and teacher_id=".$teacher_id.")>0");
                
                while ($day=$dayList->fetchArray()){
                    $day_id = $day['day_no'];
                    echo $day['day_caption']."<br>";
                    $schedule = $db->query("select * from v_schedule where day_id=".$day_id." and teacher_id=".$teacher_id);
                    echo '<table>';
                    while ($subject=$schedule->fetchArray()){
                        echo '<tr>';
                        echo '<td>'
                                .$subject['lesson_time']
                                .'</td><td>'
                                .$subject['subject_name']
                                .'</td><td>'.$subject['depart_label']
                                .'</td><td>'.$subject['room']
                                ."</td>";
                        echo '</tr>';
                    }
                    echo '</table>';
                }
                
                
                
            }
        ?>
                
            </div>
        </div>
        
        
    </body>
</html>
