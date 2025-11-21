<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Author: Abdi-Iwan
 * Date: 6/13/2017 2:50 PM
 * Build For : kemendagri.go.id
 */
class My404 extends CI_Controller {

    function My404(){
		parent::__construct();
	}
	
	public function index(){
	    die("Page Not Found");
	}

}
