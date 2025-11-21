<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends CI_Controller {

	function Ajax(){
		parent::__construct();
		$this->load->helper('utility');
        $this->load->helper('string');

	}

    public function kalangkabut(){
        $data['image'] = random_string("numeric", 6);
        $this->session->set_userdata('mycapture', $data['image']);
        echo $data['image'];
    }

    function getUnitKerja()
    {
        $this->load->view('unkerja');
    }

    function child_unkerja($param=1,$lvl=1)
    {
        $data['param'] = $param;
        $data['lvl'] = $lvl;
        $this->load->view('next_unkerja',$data);
    }

    function getArea()
    {
        $this->load->view('area');
    }

    function child_area($param=1,$lvl=1)
    {
        $data['param'] = $param;
        $data['lvl'] = $lvl;
        $this->load->view('next_area',$data);
    }

    function getUnitKerjaN()
    {
        $this->load->view('unkerja2');
    }

    function child_unkerjaN($param=1,$lvl=1)
    {
        $data['param'] = $param;
        $data['lvl'] = $lvl;
        $this->load->view('next_unkerja2',$data);
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */