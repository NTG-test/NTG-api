<?php
    function GET() {
        if (0) {
        } else {
            $result = mysqli_query($GLOBALS['db'], "SELECT * FROM quranSurah ");
            $i=1;
            while ($quranSurah = mysqli_fetch_array($result)) {
                $response -> $i = (object) ['name'=>$quranSurah['name'], 'period'=>$quranSurah['period']];
                $i++;
            }
        }
        done(200, json_encode($response, JSON_UNESCAPED_UNICODE));
    }
