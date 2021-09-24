<?php
    function GET() {
        if ($_GET['id']=='') done(400, 'langCodeIsNotSet');

        $allowedLang = array('enUS', 'ar', 'fa');
        if (!in_array($_GET['id'], $allowedLang)) done(404,'langCodeNotFound');

        $result = mysqli_query($GLOBALS['db'], "SELECT * FROM ntgLanguage ");
        $i=1;
        while ($quranSurah = mysqli_fetch_array($result)) {
            $response -> $i = (object) ['selector'=>$quranSurah['selector'], 'attribute'=>$quranSurah['attribute'], 'trans'=>$quranSurah[$_GET['id']]];
            $i++;
        }
        done(200, json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
