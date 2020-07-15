<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
use \Bitrix\Main\Loader;
function pr($var){
    echo '<pre>'; var_dump($var); echo '</pre>';
}
class COneClickOrder extends CBitrixComponent
{
    public $order;
    protected $authorize;
    protected $errors = [];

    function __construct($component = null)
    {
        parent::__construct($component);

        if(!Loader::includeModule('sale')){
            $this->errors[] = 'Модуль "Корзина" не подключен';
        }

        if(!Loader::includeModule('catalog')){
            $this->errors[] = 'No catalog module';
        }
        $this->authorize = false;
    }
    //обработка массива arParams
    public function onPrepareComponentParams($arParams)
    {
        $arParams = array(
            "CACHE_TYPE" => $arParams["CACHE_TYPE"],
            "CACHE_TIME" => isset($arParams["CACHE_TIME"]) ?$arParams["CACHE_TIME"]: 36000000,
        );
        return $arParams;
    }

    //Метод проверяющий авторизацию. Если пользователь не авторизирован, создаем нового пользователя по номеру телефону и авторизируем его
    protected function checkAuthorize($phone){
        global $USER;
        if(!$USER->IsAuthorized()){

            $login = preg_replace("#[^0-9]#", "", $phone);
            $email = $login ."@manao.by";

            $ID = CUser::GetByLogin($login)->fetch()['ID'];
            if(!$ID){
                $user = new CUser;
                $arFields = Array(
                    "EMAIL"           => $email,
                    "LOGIN"           => $login,
                    "NAME"            => $login,
                    "PERSONAL_PHONE"  => $phone,
                    "PASSWORD"        => '123456qwerty',
                    "CONFIRM_PASSWORD"=> '123456qwerty'
                );
                $ID = $user->Add($arFields);
            }
            $USER->Authorize($ID);
        }else
            $this->authorize = true;
    }

    //Создание заказа и заполнение его товарами из корзины
    protected function createVirtualOrder()
    {
        global $USER;

        try {
            $siteId = \Bitrix\Main\Context::getCurrent()->getSite();

            $basketItems = \Bitrix\Sale\Basket::loadItemsForFUser(
                \CSaleBasket::GetBasketUserID(),
                $siteId
            )
                ->getOrderableItems();
            $this->order = \Bitrix\Sale\Order::create($siteId, $USER->GetID());
            $this->order->setPersonTypeId(1);
            $this->order->setBasket($basketItems);
        } catch (\Exception $e) {
            $this->errors[] = $e->getMessage();
        }
    }

    //Установка свойст заказа значение массива из параметров
    protected function setOrderProps($valueProps)
    {
        global $USER;
        $arUser = $USER->GetByID(intval($USER->GetID()))->Fetch();
        foreach ($this->order->getPropertyCollection() as $prop) {
            switch($prop->getField('CODE')){
                case 'PHONE':
                    $prop->setValue($valueProps['PHONE']);
                    break;
                case 'FIO':
                    $prop->setValue($arUser['NAME'] . ' ' . $arUser['LAST_NAME']);
                    break;
                case 'EMAIL':
                    $prop->setValue($arUser['EMAIL']);
                    break;
            }
        }
    }
    public function handlerAjaxRequest($phone){
        global $USER;
        if($phone){
            $this->checkAuthorize($phone);
            $this->createVirtualOrder();
            $this->setOrderProps(array('PHONE' => $phone));
            $result = $this->order->save();
            $status = $result->isSuccess();
        }else{
            $status = false;
        }
        if(!$this->authorize)  //Завершение сеанса, если пользователь не был авторизован вначале
            $USER->Logout();
        return $status;
    }
    public function executeComponent()
    {
        $this->includeComponentTemplate();
    }
}?>