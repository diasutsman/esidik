<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class No_akses extends MX_Controller {
	function No_akses(){
		parent::__construct();
	}
	
	function index()
	{
		echo '<div style="margin-bottom: 4em;margin-top: 4em;text-align: center;">
            <h1 style="font-size: 120px;line-height: 1em;margin-bottom: 0.5em;">Oops!</h1>
            <div style="font-size: 16px;margin-bottom: 1.5em;">
                Maaf, Anda tidak diizinkan mengakses halaman ini!<br>
                <a href="'.site_url('home').'">Kembali Ke Home</a>
            </div>
        </div>';
	}
}
