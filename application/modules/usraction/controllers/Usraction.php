<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Usraction extends MX_Controller
{

    private $aAkses;

    function Usraction()
    {
        parent::__construct();
        $this->load->helper('utility');
        $this->load->helper('menunavigasi');
        $this->load->library('mypagination');

        $this->load->model('data_model', 'dataaction');

        if (!$this->auth->is_logged_in()) {
            redirect('login', 'refresh');
        }

        $this->aAkses = akses("usraction", $this->session->userdata('s_access'));

        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');

    }


    function index()
    {
        $this->indexlist(0);
    }

    function indexlist($idlevel = 0)
    {

        $this->session->set_userdata('menu', '33');
        $data['menu'] = '33';

        $uri_segment = 3;
        $offset = 0;
        $SQLcari = " AND id_level=$idlevel ORDER BY id_action asc";
        $query = $this->dataaction->getDaftar(1, 10, $offset, null, $SQLcari);
        $jum_data = $this->dataaction->getDaftar(0, null, null, null, $SQLcari);
        $this_url = site_url('usraction/pagging/');
        $data2 = $this->mypagination->getPagination($jum_data->num_rows(), 10, $this_url, $uri_segment);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['jum_data'] = $jum_data->num_rows();
        $data['result'] = $query->result();
        $data['order'] = 'id_action';
        $data['typeorder'] = 'sorting';
        $data['lvlID'] = $idlevel;
        $this->template->load('template', 'display', $data);
    }

    public function pagging($page = 0)
    {
        $cr = $this->input->post('cari');
        $limited = $this->input->post('lmt');
        $idlevel = $this->input->post('idlevel');
        $limited = ((isset($limited) && ($limited != '' || $limited != null)) ? $limited : 10);
        $offset = ((isset($page) && ($page != '' || $page != null)) ? $page : 0);

        $SQLcari = "";
        if ($cr == 'cri') {
            $data['caridata'] = '';
        } else {
            $data['caridata'] = str_replace('%20', ' ', $cr);
            $SQLcari .= " AND ( menu_desc LIKE '%" . str_replace('%20', ' ', $cr) . "%'  ) ";
        }

        $SQLcari .= " AND id_level=$idlevel ";

        $data['order'] = $this->input->post('order');
        $typeorder = $this->input->post('sorting');

        if ($typeorder != '' && $typeorder != null) {
            if ($typeorder == 'sorting') {
                $sorting = 'ASC';
                $data['typeorder'] = 'sorting_asc';
            } else if ($typeorder == 'sorting_asc') {
                $sorting = 'ASC';
                $data['typeorder'] = 'sorting_asc';
            } else {
                $sorting = 'DESC';
                $data['typeorder'] = 'sorting_desc';
            }
        } else {
            $sorting = 'ASC';
            $data['typeorder'] = 'sorting_asc';
        }

        if ($data['order'] != '' && $data['order'] != null) {
            $SQLcari .= " ORDER BY " . $data['order'] . " " . $sorting;
        } else {
            $SQLcari .= " ORDER BY id_action ASC ";
        }

        $query = $this->dataaction->getDaftar(1, $limited, $offset, null, $SQLcari);
        $jum_data = $this->dataaction->getDaftar(0, null, null, null, $SQLcari);
        $this_url = site_url("usraction/pagging");
        $data2 = $this->mypagination->getPagination($jum_data->num_rows(), $limited, $this_url, 3);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['limit_display'] = $limited;
        $data['jum_data'] = $jum_data->num_rows();
        $data['result'] = $query->result();
        $this->load->view('list', $data);
    }

    public function edit($noId = 0)
    {
        $sql = "select grup_action.*,menu_desc 
                FROM grup_action
          INNER JOIN menu_new b ON b.menu_id=grup_action.id_menu
          where id_action=$noId";

        echo json_encode($this->db->query($sql)->row_array());
    }

    public function save()
    {
        $idx = is_numeric($this->input->post('iddata')) ? intval($this->input->post('iddata')) : 0;
        $idlvl = is_numeric($this->input->post('idlvl')) ? intval($this->input->post('idlvl')) : 0;
        $t1 = $this->input->post('txt1');
        $t2 = $this->input->post('flagadd');
        $t3 = $this->input->post('flagedit');
        $t4 = $this->input->post('flagdelete');
        $t5 = $this->input->post('flagprint');

        $dataIn['id_menu'] = $t1;
        $dataIn['id_level'] = $idlvl;
        $dataIn['flagadd'] = $t2;
        $dataIn['flagedit'] = $t3;
        $dataIn['flagdelete'] = $t4;
        $dataIn['flagprint'] = $t5;

        if ($idx == 0) {
            $this->db->select("id_menu");
            $this->db->from("grup_action");
            $this->db->where("id_menu", $t1);
            $this->db->where("id_level", $idlvl);
            $this->db->limit(1);
            $sql1 = $this->db->get();

            if ( $sql1->num_rows()>0)
            {
                createLog("Membuat privileges " . $t1, "Gagal");
                $data['msg'] = 'Maaf, Akses menu tidak ada..';
                $data['status'] = 'error';
            } else {
                $dataIn['create_by'] = $this->session->userdata('s_username');
                $insert = $this->db->insert('grup_action', $dataIn);
                if (!$insert) {
                    createLog("Membuat privileges " . $t1, "Gagal");
                    $data['msg'] = 'Maaf, Tidak bisa menyimpan data..';
                    $data['status'] = 'error';

                } else {
                    createLog("Membuat privileges " . $t1, "Sukses");
                    $data['msg'] = 'Data berhasil disimpan..';
                    $data['status'] = 'succes';
                }
            }
        } else {
            $this->db->where('id_action', $idx);
            $data = $this->db->get("grup_action")->row_array();
            log_history("edit", "grup_action", $data);

            $dataIn['modif_date'] = date("Y-m-d H:i:s");
            $dataIn['modif_by'] = $this->session->userdata('s_username');
            $this->db->where('id_action', $idx);
            $update = $this->db->update('grup_action', $dataIn);
            if (!$update) {
                createLog("Merubah privileges " . $t1, "Gagal");
                $data['msg'] = 'Maaf, Tidak bisa merubah data..';
                $data['status'] = 'error';

            } else {
                createLog("Merubah privileges " . $t1, "Sukses");
                $data['msg'] = 'Data berhasil dirubah..';
                $data['status'] = 'succes';
            }
        }
        die(json_encode($data));
    }

    public function hapus()
    {
        $id = $this->input->post('id');
        for ($i = 0; $i < count($id); $i++) {
            $nId = is_numeric($id[$i]) ? $id[$i] : 0;

            $this->db->where('id_action', $nId);
            $query = $this->db->get("grup_action");
            $datas = $query->row_array();
            log_history("delete", "grup_action", $datas);

            $this->db->where('id_action', $nId);
            $this->db->delete('grup_action');

            if (isset($datas)) {
                createLog("Menghapus Akses user  " . $nId, "Sukses");
            }
        }

        $data['msg'] = 'Data berhasil dihapus..';
        $data['status'] = 'succes';

        echo json_encode($data);
    }


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */