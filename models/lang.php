<?php
namespace models;

require_once '../lib/db.class.php';
require_once '../config/db.php';

class Lang {
    public function get()
    {
        return \DB::query("SELECT * FROM lang");
    }

    public function saveParam(int $param)
    {
        $_SESSION['paramLang'] = $param;
    }

    public function restoreParam(): int
    {
        if (isset($_SESSION['paramLang'])) {
            return $_SESSION['paramLang'];
        } else {
            return 0;
        }
    }
}
 