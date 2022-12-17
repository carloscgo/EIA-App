<?php
class ContactsController
{

    public static $CONFIG;
    protected static $DB = null;
    private static $table = "contacts"; //nombre de la tabla principal de la base de datos a la que se referira el script
    public static $tableID = "id"; // nombre del campo identificador unico de registros de la tabla
    public static $data = array();

    public function __construct()
    {
        self::$DB = new DB_Class();
        self::$CONFIG = json_decode(json_encode(unserialize(CONFIG), JSON_FORCE_OBJECT));
    }

    public function __destruct()
    {
        // destructor de la clase
    }

    private static function getContacts()
    {
        $status = false;
        $message = '';
        $row = array();

        $sql = "SELECT id, firstname, lastname, email, phone, created_at, updated_at
				FROM " . self::$table . "
				WHERE deleted_at IS NULL";

        $rs = self::$DB->DB_Consulta($sql);

        if (!$row = self::$DB->DB_fetch_array($rs)) {
            $message = self::$CONFIG->msj_no_con;
            $row = array();
        }

        echo json_encode(array(
            'status' => $status,
            'message' => $message,
            'data'     => $row
        ));
    }
}
