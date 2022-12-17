<?php
class ContactController
{

    public static $CONFIG;
    protected static $DB      = NULL;
    private static $mensaje     = ""; //inicializar la variable que se encarga de los mensajes
    private static $tabla     = "album"; //nombre de la tabla principal de la base de datos a la que se referira el script
    public static $cod_tabla = "id_album"; //nombre del campo identificador unico de registros de la tabla
    private static $permisos = "albums"; // variable encargada de la permisologia

    public static $permits = array();
    public static $data = array();

    private static function pathAlbum()
    {
        return $_SERVER['DOCUMENT_ROOT'] . DIR . '/public/files/albums/';
    }

    public function __construct()
    {
        self::$DB = new DB_Class();
        self::$CONFIG = json_decode(json_encode(unserialize(CONFIG), JSON_FORCE_OBJECT));

        self::$permits['1'] = consultar_permisos(self::$permisos, 1, self::$DB); // verificar si el usuario puede consultar
        self::$permits['2'] = consultar_permisos(self::$permisos, 2, self::$DB); // verificar si el usuario puede insertar
        self::$permits['3'] = consultar_permisos(self::$permisos, 3, self::$DB); // verificar si el usuario puede modificar
        self::$permits['4'] = consultar_permisos(self::$permisos, 4, self::$DB); // verificar si el usuario puede eliminar
    }

    public function __destruct()
    {
        //destructor de la clase
    }

    public function viewQuery($where)
    {
        $sql = "SELECT al.id_album, al.id_artist, CONCAT(ar.last_name, ' ', ar.first_name) AS artist, al.name, al.slug, al.image, al.soundcloud_id, al.youtube, al.price, al.fee, al.shipping, al.active, al.date_c, al.date_m,
					CASE WHEN al.active = '1' THEN 'Yes'
						ELSE 'No'
					END AS status
				FROM album AS al
				INNER JOIN artist AS ar ON al.id_artist = ar.id_artist
				WHERE " . $where . "
				ORDER BY al.name ASC";

        return $sql;
    }

    public function getRecords()
    {
        if (isset(self::$data['searchPhrase'])) {
            $search = trim(self::$data['searchPhrase']);
            $where .= " AND (
							lower(name) LIKE lower('" . $search . "%')
							OR lower(artist) LIKE lower('%" . $search . "%')
							OR lower(date_c) LIKE lower('" . $search . "%')
							OR lower(date_m) LIKE lower('" . $search . "%')
							OR lower(status) LIKE lower('" . $search . "')
						) ";
        }

        $sql = "SELECT *
				FROM (
					" . self::viewQuery("al.id_artist = " . self::$data['id_artist']) . "
				) AS t
				WHERE true
					" . $where;

        $data = general::getRecords($sql, 'name', self::$data['sort'], self::$data['rowCount'], (self::$data['current'] == '' ? 1 : self::$data['current']));

        $data = json_decode($data, true);

        $records = array();

        foreach ($data['rows'] as $r) {
            $file_zip = self::pathAlbum() . md5($r['id_album']) . '/' . str_replace(' ', '_', $r['name']) . '.zip';

            if (file_exists($file_zip)) {
                $r['zip'] = true;
            } else {
                $r['zip'] = false;
            }

            $records[] = $r;
        }

        header('Content-Type: application/json');

        $data = array(
            "current"     => $data['current'],
            "rowCount"     => $data['rowCount'],
            "rows"         => $records,
            "total"     => $data['total']
        );

        echo json_encode($data);
    }

    public static function searchRecord($cod)
    {
        $status = false;
        $mensaje = '';
        $row    = array();

        $sql = "SELECT id_album, id_artist, artist, name, image, soundcloud_id, youtube, price, fee, shipping, active, date_c, date_m
				FROM (
					" . self::viewQuery("al.id_artist = " . self::$data['id_artist']) . "
				) AS tabla
				WHERE " . self::$cod_tabla . " = " . $cod;

        $rs = self::$DB->DB_Consulta($sql);
        if (!($row = self::$DB->DB_fetch_array($rs))) {
            $mensaje = self::$CONFIG->msj_no_con;
            $row     = array();
        }

        return array(
            'status' => $status,
            'message' => $mensaje,
            'data'     => $row
        );
    }

    public static function getNameArtist($id_artist)
    {
        $row = NULL;

        $sql = "SELECT name
				FROM artist
				WHERE id_artist = " . $id_artist;

        $rs = self::$DB->DB_Consulta($sql);
        $row = self::$DB->DB_fetch_array($rs);

        return $row['name'];
    }

    public static function addRecord($data)
    {
        $sql = "SELECT name
                FROM " . self::$tabla . "
                WHERE lower(name) LIKE lower('" . $data['name'] . "')
					AND id_artist = " . self::$data['id_artist'] . "";

        $rs = self::$DB->DB_Consulta($sql);

        if (self::$DB->DB_num_rows($rs) < 1) {
            $into  = "id_artist, id_user, name, slug, image, soundcloud_id, youtube, price, fee, shipping, active";
            $values = $data['id_artist'] . ", " . $_SESSION['id_user'] . ", '" . trim($data['name']) . "', '" . slug($data['name']) . "', '" . basename($data['image']) . "', '" . trim($data['soundcloud_id']) . "', '" . trim($data['youtube']) . "', " . $data['price'] . ", " . $data['fee'] . ", " . $data['shipping'] . ", '" . ($data["active"] == '' ? '0' : $data["active"]) . "'";

            $rs_ins = self::$DB->DB_Insertar(self::$tabla, $into, $values);
            $mensaje = ($rs_ins ? self::$CONFIG->msj_si_insert : self::$CONFIG->msj_no_insert);

            if ($rs_ins) {
                mkdir(self::pathAlbum() . md5(self::$DB->DB_Insert_ID()));
            }
        } else {
            $mensaje = "There is already a Album with that name for the Artist";
            $rs_ins  = '0';
        }

        return array(
            'status' => $rs_ins,
            'message' => $mensaje
        );
    }

    public static function upRecord($data)
    {
        $precio = to_number($data['precio']);

        $sql = "SELECT *
                FROM " . self::$tabla . "
                WHERE lower(name) LIKE lower('" . $data['name'] . "')
					AND id_artist = " . self::$data['id_artist'] . "
                    AND " . self::$cod_tabla . " != " . $data['cod'];

        $rs = self::$DB->DB_Consulta($sql);

        if (self::$DB->DB_num_rows($rs) < 1) {
            /* Registro de modificacion del usuario en la bitacora */
            self::$DB->DB_add_log_usuarios($_SESSION['id_user'], 2, 'Previous Data:' . self::$DB->DB_show_data(self::$tabla, self::$cod_tabla . " = " . $data['cod']));

            $set = "name = '" . trim($data['name']) . "',
					slug = '" . slug($data['name']) . "',
					image = '" . basename($data['image']) . "',
					soundcloud_id = '" . trim($data['soundcloud_id']) . "',
					youtube = '" . trim($data['youtube']) . "',
					price = " . $data['price'] . ",
					fee = " . $data['fee'] . ",
					shipping = " . $data['shipping'] . ",
					active = '" . ($data["active"] == '' ? '0' : $data["active"]) . "'";

            $rs_mod = self::$DB->DB_Modificar(self::$tabla, $set, self::$cod_tabla . " = " . $data['cod']);
            $mensaje = ($rs_mod ? self::$CONFIG->msj_si_edit : self::$CONFIG->msj_no_edit);

            if ($rs_mod && !file_exists(self::pathAlbum() . md5($data['cod']))) {
                mkdir(self::pathAlbum() . md5($data['cod']));
            }
        } else {
            $mensaje = "There is already a Album with that name for the Artist";
            $rs_mod  = '0';
        }

        return array(
            'status' => $rs_mod,
            'message' => $mensaje
        );
    }

    public static function deleteRecord($cod)
    {
        $sql = "SELECT *
				FROM song
				WHERE " . self::$cod_tabla . " = " . $cod;

        $rs  = self::$DB->DB_Consulta($sql);
        $nhijos = self::$DB->DB_num_rows($rs);

        if ($nhijos < 1) {
            /* Registro de eliminacion del usuario en la bitacora */
            self::$DB->DB_add_log_usuarios($_SESSION['id_user'], 3, 'Previous Data:' . self::$DB->DB_show_data(self::$tabla, self::$cod_tabla . " = " . $cod));

            $rs_del = self::$DB->DB_Eliminar(self::$tabla, self::$cod_tabla . " = " . $cod);
            $mensaje = ($rs_del ? self::$CONFIG->msj_si_del : self::$CONFIG->msj_no_del);

            if ($rs_del) {
                unlink(self::pathAlbum() . md5($cod));
            }
        } else {
            $mensaje = "You can not delete the Album, you have associated Songs";
            $rs_del  = '0';
        }

        return array(
            'status' => $rs_del,
            'message' => $mensaje
        );
    }

    public static function getListTypeahead($params)
    {
        $search = trim($params['search']);
        $result    = array();

        $sql = "SELECT *
				FROM (
					SELECT id_album, name AS album
					FROM " . self::$tabla . "
				) AS tabla
				WHERE lower(album) LIKE lower('" . $search . "%')
				ORDER BY album ASC
				LIMIT 10";

        $rs = self::$DB->DB_Consulta($sql);
        while (($row = self::$DB->DB_fetch_array($rs))) {
            $result[] = array(
                'label' => $row['album'],
                'value' => $row['id_album']
            );
        }

        echo json_encode($result);
    }

    public static function compressZip($cod)
    {
        $status = '0';

        $sql = "SELECT id_album, name
				FROM " . self::$tabla . "
				WHERE " . self::$cod_tabla . " = " . $cod;

        $rs = self::$DB->DB_Consulta($sql);
        if ($row = self::$DB->DB_fetch_array($rs)) {
            $dir       = self::pathAlbum() . md5($row['id_album']) . '/';
            $filename = str_replace(' ', '_', $row['name']) . '.zip';

            $zip = new ZipArchive();

            if ($zip->open($dir . $filename, ZIPARCHIVE::CREATE) === true) {
                $songs = new songs();

                $songs->setData(['id_album' => $row['id_album']]);
                $data = $songs->getRecords(false);

                foreach ($data as $s) {
                    $file = $s['name'] . '.' . explode('.', $s['song'])[1];

                    $zip->addFile('../' . $s['song'], $file);
                }

                $zip->close();

                $status  = '1';
                $mensaje = 'Successfully created zip file.';
            } else {
                $mensaje = 'Error creating zip file.';
            }
        } else {
            $mensaje = 'The album is not found.';
        }

        return array(
            'status' => $status,
            'message' => $mensaje
        );
    }

    public static function removeZip($cod)
    {
        $status = '0';

        $sql = "SELECT id_album, name
				FROM " . self::$tabla . "
				WHERE " . self::$cod_tabla . " = " . $cod;

        $rs = self::$DB->DB_Consulta($sql);
        if ($row = self::$DB->DB_fetch_array($rs)) {
            $file_zip = self::pathAlbum() . md5($row['id_album']) . '/' . str_replace(' ', '_', $row['name']) . '.zip';

            if (file_exists($file_zip)) {
                unlink($file_zip);

                $status  = '1';
                $mensaje = 'Successfully removed zip file.';
            } else {
                $mensaje = 'Error removing zip file.';
            }
        } else {
            $mensaje = 'The album is not found.';
        }

        return array(
            'status' => $status,
            'message' => $mensaje
        );
    }
}
