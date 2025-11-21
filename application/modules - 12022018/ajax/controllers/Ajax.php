<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use ElephantIO\Client;
use ElephantIO\Engine\SocketIO\Version1X;


class Ajax extends CI_Controller {

	function Ajax(){
		parent::__construct();
		$this->load->helper('utility');
        $this->load->helper('string');

	}

    public function kalangkabut(){
        $data['image'] = randomString(6);
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

    function pushSock()
    {
        try {
            $name = $this->generateRandomString(5);
            $deptname = $this->generateRandomString(10);
            $data[] = ["name" => $name, "deptname" => $deptname, "checktime" => ymdToIna(date("Y-m-d H:i:s"))];
            $client = new Client(new Version1X($this->config->item('servernode')));
            $client->initialize();
            $client->emit('broadcast', $data);

            $client->close();
            echo "Send to front End ";
        }
        catch (Exception $e)
        {
            var_dump($e);
        }

    }

    function pushSockPost()
    {
        try {
            $name = $this->input->post('name');
            $deptname = $this->input->post('deptname');
            $tglskrg = $this->input->post('checktime');
            //$checktime= ymdToIna(date("Y-m-d H:i:s", strtotime($date))) ;
            $data[] = ["name" => $name, "deptname" => $deptname, "checktime" => $tglskrg];
            $client = new Client(new Version1X($this->config->item('servernode')));
            $client->initialize();
            $client->emit('broadcast', $data);

            $client->close();
            echo "Send to front End with post data";
        }
        catch (Exception $e)
        {
            echo "Cannot send to front End ";
        }
    }

    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */