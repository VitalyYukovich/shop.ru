<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?
	$actionPath = str_replace('\\', '/', __FILE__);
	$actionPath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $actionPath);
	$actionPath = str_replace('template.php', 'handler.php', $actionPath);
?>

<form id='oneclickorder' action ='<?=$actionPath;?>'>
	<label for="phone">Введите номер телефона</label>
	<input type='text' name='phone'>
	<input type="submit" value="Оформить заказ">
</form>