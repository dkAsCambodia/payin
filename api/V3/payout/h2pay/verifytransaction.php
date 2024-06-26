<?php
if(!empty($_POST['transId']) && !empty($_POST['key'])){
    return "True";
}elseif(!empty($_GET['transId']) && !empty($_GET['key'])){
    return "True";
}else{
    echo "working no have data";
    return false;
}
