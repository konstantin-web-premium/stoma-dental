<?php
class PopupManager{
    const ADMIN_EDIT_CAT = "editAdminCategory";
    const ADMIN_ADD_CAT = "addAdminCategory";
    const ADMIN_GET_IMAGES_TABLE = "getImagesTable";

    private $type; // (string)

    public function __construct($type){
        $this->type = $type;
    }

// -----------------------------------------------------------
// PRIVATE ----------------------------------------------------
// -----------------------------------------------------------
    private function validateData($value, $as){
        switch ($as){
            default:
                return null;
            case "id":
                return intval($value);
        }
    }

// -----------------------------------------------------------
// PUBLIC ----------------------------------------------------
// -----------------------------------------------------------
    public function renderPopupContent(){
        switch($this->type){
            /*
            case self::ADMIN_EDIT_CAT:
                $this->data = $this->validateData($this->data, "id");
                PageUtils::getPageNode();
            case self::ADMIN_ADD_CAT:
                $this->data = $this->validateData($this->data, "id");
                $_POST["id"] = $this->data;
                if (G::$user->isORhigher(U_MODERATOR)){
                    include ROOT . PATH_INCLUDES . "forms/admin_edit_category_form.php";
                }
                break;
            */
            case self::ADMIN_GET_IMAGES_TABLE:
                G::$pageData->imagesType = $_POST["data"];
                if (G::$user->isORhigher(U_MODERATOR)){
                    include ROOT . PATH_INCLUDES . "admin/popup_images_list.php";
                }
                break;
        }
    }
}
?>