<?
require_once $_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php";
CModule::IncludeModule("sale");

$resultUserPropsId = CSaleOrderUserProps::GetList(array(), array("USER_ID" => $USER->GetID()));
$userPropsId = $resultUserPropsId->Fetch();

if($userPropsId){
	$resultUserProp = CSaleOrderUserPropsValue::GetList(array("ID" => "ASC"), Array("PROP_CODE" => 'PHONE', 'USER_PROPS_ID'=> $userPropsId['ID']));
	$arUserProp = $resultUserProp->Fetch();

	if(isset($arUserProp['ID'])){
		$result = CSaleOrderUserPropsValue::Update(
			(int) $arUserProp['ID'],
			array('ID' => (int) $arUserProp['ID'], 'USER_PROPS_ID' => $arUserProp['USER_PROPS_ID'], 'VALUE' => $_POST['phone'])
		);
		echo json_encode(array('status' => 1));
		exit;
	}
}
echo json_encode(array('status' => 0));
?>