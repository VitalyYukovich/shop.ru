<?
require_once $_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php";
require_once '../../class.php';

$obj = new COneClickOrder();
$result = $obj->handlerAjaxRequest($_POST['phone']);

echo json_encode($result);