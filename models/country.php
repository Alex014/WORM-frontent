<?php
namespace models;

require_once '../lib/db.class.php';
require_once '../config/db.php';

class Country {
    public function get()
    {
        return \DB::query("SELECT * FROM country");
    }

    public function saveParam(int $param)
    {
        $_SESSION['paramCountry'] = $param;
    }

    public function restoreParam(): int
    {
        if (isset($_SESSION['paramCountry'])) {
            return $_SESSION['paramCountry'];
        } else {
            return 0;
        }
        
    }
}
 