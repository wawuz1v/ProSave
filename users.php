<?php
require_once 'db/db_connect.php';


class Users
{
    private $chatID;

    public function __construct($chatID)
    {
        $this->chatID = $chatID;

        if($this->chatID != ''){
            if (!$this->isUserSet()){
                $this->makeUser();
            }
        }

    }



    function setPage($page)
    {

        return $this->setKeyValue('page', $page);

    }

    function getPage()
    {

        return $this->getKeyValue('page');

    }




    private function isUserSet()
    {

        global $db;

        $chatID = $db->real_escape_string($this->chatID);


        $result = $db->query("select * from `u4142_prosave`.`users` where chatID='$chatID' LIMIT 1");

        $myArray = (array)($result->fetch_array());

        if (!empty($myArray)) return true;

        return false;

    }

    private function makeUser()
    {

        global $db;

        $chatID = $db->real_escape_string($this->chatID);


        $query = "insert into `u4142_prosave`.`users`(chatID) values('{$chatID}')";



        if (!$db->query($query))

            die("пользователя создать не удалось");

    }

    function setKeyValue($key, $value)
    {

        global $db;

        $chatID = $db->real_escape_string($this->chatID);

        $value = base64_encode($value);

        if (!$this->isUserSet()) {

            $this->makeUser(); // если каким-то чудом этот пользователь не зарегистрирован в базе

        }

        $data = $this->getData();

        $data[$key] = $value;

        $data = json_encode($data, JSON_UNESCAPED_UNICODE);

        return $db->query("update `u4142_prosave`.`users` SET data_json = '{$data}' WHERE chatID = '{$chatID}'"); // обновляем запись в базе

    }

    function getKeyValue($key)
    {

        $data = $this->getData();

        if (array_key_exists($key, $data)) {

            return base64_decode($data[$key]);

        }

        return "";


    }

    public function getData()
    {

        global $db;

        $res = array();

        $chatID = $db->real_escape_string($this->chatID);

        $result = $db->query("select * from `u4142_prosave`.`users` where chatID='$chatID'");

        $arr = $result->fetch_assoc();

        if (isset($arr['data_json'])) {

            $res = json_decode($arr['data_json'], true);

        }


        return $res;

    }

    public function getData1()
    {

        global $db;

        $res = array();

        $chatID = $db->real_escape_string($this->chatID);

        $result = $db->query("select count(chatID) from `u4142_prosave`.`users`");

        return $result->fetch_assoc();

    }



}
