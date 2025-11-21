<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Apikey extends MX_Controller {

    function Apikey(){
        parent::__construct();

        $this->load->library('form_validation');
        $this->load->helper('utility');
        $this->load->helper('menunavigasi');
        $this->load->library('mypagination' );
        if( !$this->auth->is_logged_in() ){
            redirect('login', 'refresh');
        }
        $this->load->model('key_m');
    }

    function index_listsdsdsd()
    {

        $this->session->set_userdata('menu','33');
        $data['menu'] = '33';
            
        // API Keys
        if ($this->input->post('key_key') AND $this->input->post('key_note')) {
            $this->key_m->update_keys($this->input->post('key_key'), $this->input->post('key_note'));
        }
        if ($this->input->post('new_key')) {
            $this->key_m->insert_keys($this->input->post('new_key'), $this->input->post('new_key_note'));
        }

        unset($_POST['key_key'], $_POST['key_note'], $_POST['new_key'], $_POST['new_key_note']);

        $data["api_keys"] = $this->key_m->get_all();
        
        //$this->template->build('index');
        $this->template->load('template','display',$data);
    }


    function killSession()
    {
    }

    function index()
    {
        $this->killSession();
        $this->index_list();
    }

    function index_list()
    {
        $this->session->set_userdata('menu','33');
        $data['menu'] = '33';

        $uri_segment=3;
        $offset = 0;
        $SQLcari = " ORDER BY id desc";
        $query = $this->key_m->getDaftar(1,10,$offset,null,$SQLcari);
        $jum_data = $this->key_m->getDaftar(0,null,null,null,null);
        $this_url = site_url('harilbr/pagging/');
        $data2 = $this->mypagination->getPagination($jum_data->num_rows(),10,$this_url,$uri_segment);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['order'] = 'id';
        $data['typeorder'] = 'sorting_desc';
        $data['jum_data'] = $jum_data->num_rows();
        $data['result'] = $query->result();

        $this->template->load('template','display',$data);
    }

    public function pagging($page=0)
    {
        $cr = $this->input->post('cari');
        $limited = $this->input->post('lmt');
        $limited=((isset($limited) && ($limited!='' || $limited!=null))?$limited:10);
        $offset = ((isset($page) && ($page!='' || $page!=null))?$page:0);

        $SQLcari="";
        if($cr=='cri'){
            $data['caridata']='';
        }else{
            $data['caridata']= str_replace('%20',' ',$cr);
            $SQLcari .= " AND ( info LIKE '%".str_replace('%20',' ',$cr)."%' 
                        or startdate LIKE '%".str_replace('%20',' ',$cr)."%' ) ";
        }

        $data['order'] = $this->input->post('order');
        $typeorder = $this->input->post('sorting');

        if($typeorder!='' && $typeorder!=null){
            if($typeorder=='sorting'){
                $sorting = 'ASC';
                $data['typeorder'] = 'sorting_asc';
            }else if($typeorder=='sorting_asc'){
                $sorting = 'ASC';
                $data['typeorder'] = 'sorting_asc';
            }else{
                $sorting = 'DESC';
                $data['typeorder'] = 'sorting_desc';
            }
        }else{
            $sorting = 'DESC';
            $data['typeorder'] = 'sorting_desc';
        }

        if($data['order']!='' && $data['order']!=null){
            $SQLcari .= " ORDER BY ".$data['order']." ".$sorting;
        }else{
            $SQLcari .= " ORDER BY id Desc ";
        }
        $query = $this->key_m->getDaftar(1,$limited,$offset,null,$SQLcari);
        $jum_data = $this->key_m->getDaftar(0,null,null,null,$SQLcari);
        $this_url = site_url("apikey/pagging");
        $data2 = $this->mypagination->getPagination($jum_data->num_rows(),$limited,$this_url,3);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['limit_display'] = $limited;
        $data['jum_data'] = $jum_data->num_rows();
        $data['result'] = $query->result();
        $this->load->view('list',$data);
    }

    public function edit($noId=0)
    {
        $this->db->from("kunci_wbsvc");
        $this->db->where("id",$noId);
        echo json_encode($this->db->get()->row_array());
    }

    public function save()
    {
        $idx = is_numeric($this->input->post('iddata'))? intval($this->input->post('iddata')):0;
        $t1 = $this->input->post('txt1');
        $t2 = $this->input->post('txt2');
        $t3 = $this->input->post('txt3');

        $dataIn['expired'] =  $t1;
        $dataIn['key'] = $t2;
        $dataIn['note'] =  $t3;

        if ($idx==0)
        {
            $insert = $this->db->insert('kunci_wbsvc', $dataIn);
            if (!$insert) {

                createLog("Membuat api key ".$t1,"Gagal");
                $data['msg'] = 'Maaf, Tidak bisa menyimpan data..';
                $data['status'] = 'error';

            } else {
                createLog("Membuat api key ".$t1,"Sukses");
                $data['msg'] = 'Data berhasil disimpan..';
                $data['status'] = 'succes';
            }
        }
        else
        {
            $this->db->where('id', $idx);
            $data=$this->db->get("kunci_wbsvc")->row_array();
            log_history("edit","kunci_wbsvc",$data);

            $this->db->where('id', $idx);
            $update = $this->db->update('kunci_wbsvc',$dataIn);
            if(!$update){
                createLog("Merubah api key ".$t1,"Gagal");
                $data['msg'] = 'Maaf, Tidak bisa merubah data..';
                $data['status'] = 'error';
            }else{
                createLog("Merubah apikey ".$t1,"Sukses");
                $data['msg'] = 'Data berhasil dirubah..';
                $data['status'] = 'succes';

            }
        }
        die(json_encode($data));
    }

    public function hapus()
    {
        $id = $this->input->post('id');
        for($i=0;$i<count($id);$i++){
            $nId = is_numeric($id[$i])?$id[$i]:0;

            $this->db->where('id', $nId);
            $query=$this->db->get("kunci_wbsvc");
            $datas = $query->row_array();
            log_history("delete","kunci_wbsvc",$datas);

            $this->db->where('id',$nId);
            $this->db->delete('kunci_wbsvc');

            if (isset($datas)) {
                createLog("Menghapus api key " . $datas["id"] , "Sukses");
            }
        }

        $data['msg'] = 'Data berhasil dihapus..';
        $data['status'] = 'succes';

        echo json_encode($data);
    }

}