<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/* load the MX_Loader class */
require APPPATH."third_party/MX/Loader.php";

class MY_Loader extends MX_Loader {
    // public function model($model, $name = '', $db_conn = FALSE) {
    //     if (empty($model)) return; // Jangan lanjut jika model kosong

    //     if (is_array($model)) {
    //         foreach ($model as &$m) {
    //             if (is_string($m)) { 
    //                 $m = ucfirst(strtolower($m));
    //             }
    //         }
    //     } elseif (is_string($model)) {
    //         $model = ucfirst(strtolower($model));
    //     }
    //     parent::model($model, $name, $db_conn);
    // }
}