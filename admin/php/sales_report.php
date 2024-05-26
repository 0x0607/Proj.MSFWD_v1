<?php
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) exit;
$INCLUDE_JS[] = "/javascripts/get_chart.js";
$DATA = json_encode(["action" => "GET_TOTAL_AMOUNTS"]);
// $res = apiRespondJsonData($DATA, SERVER_API_URL);
// echo $res;
