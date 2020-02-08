<?php


//SOLV Terminliste in Mysql-Tabelle abspeichern
$year = "2014";
$url = "http://www.o-l.ch/cgi-bin/fixtures?&year=".$year."&csv=1";
$file = $url;
    if ($file>""){
        $handle = fopen($file,"r");    
        //loop through the csv file and insert into database
        $header = 1 ;
        do{
            if ($data[0]) {
                $sql = "INSERT INTO solv_termine (unique_id,date,duration,kind,day_night,national,region,type,event_name,event_link,club,map,location,coord_x,coord_y,deadline,entryportal,last_modification) VALUES
                (
                    '".addslashes($data[0])."',
                    '".addslashes($data[1])."',
                    '".addslashes($data[2])."',
                    '".addslashes($data[3])."',
                    '".addslashes($data[4])."',
                    '".addslashes($data[5])."',
                    '".addslashes($data[6])."',
                    '".addslashes($data[7])."',
                    '".addslashes($data[8])."',
                    '".addslashes($data[9])."',
                    '".addslashes($data[10])."',
                    '".addslashes($data[11])."',
                    '".addslashes($data[12])."',
                    '".addslashes($data[13])."',
                    '".addslashes($data[14])."',
                    '".addslashes($data[15])."',
                    '".addslashes($data[16])."',
                    '".addslashes($data[17])."'
                )
            ";
// 1. Zeile ausschliessen
if ($header==0) echo $sql."<br>";
else $header = 0;
            }
        } while ($data = fgetcsv($handle,1000,";","'"));
    }
?>