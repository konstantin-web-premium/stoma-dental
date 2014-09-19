<?php
interface iAdminModule{
    /**
     * @return string - module name
     */
    public static function getTitle();

    /**
     * @param $address
     */
    public function init($address);

    /**
     * @return array
     */
    public function getData();

    /**
     * @param $prop
     * @return mixed
     */
    public function getProp($prop);

    /**
     * @return array (strings)
     */
    public function getRequiredJSFilenames();
}
?>