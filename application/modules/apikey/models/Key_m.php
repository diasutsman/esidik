<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Key_m extends My_Model{
	/**
	 * @var string	The name of the settings table
	 */
	protected $table = 'kunci_wbsvc';

	/**
	 * @var bool	Tells the model to skip auto validation
	 */
	protected $skip_validation = TRUE;
	
	public function update_keys($keys, $notes) {
		$this->db->trans_begin();

		foreach ($keys as $id => $key) {
			// Missing key
			if ( ! $key) {
				$this->db->delete($this->table, array('id' => $id));
			}
			
			$this->db->where('id', $id)->update($this->table, array(
				'key' => $key,
				'note' => $notes[$id], 
			));
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return FALSE;
		}

		$this->db->trans_commit();
		return TRUE;
	}

	public function insert_keys($keys, $notes) {
		$this->db->trans_begin();

		foreach ($keys as $id => $key) {
			if ($key) {
				$this->db->insert($this->table, array(
					'key' => $key,
					'note' => $notes[$id],
					'level' => 0,
					'date_created' => now(),
				));
			}
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return FALSE;
		}

		$this->db->trans_commit();
		return TRUE;
	}

    public function getDaftar($dipaging=0,$limit=10,$offset=1,$id=null,$cari=null,$count=0)
    {
        $sql =  "SELECT ".((isset($count) && $count ==1)?'count(*) as total':'*')." 
		 from kunci_wbsvc
		Where (1=1)";
        if($id!='' && $id !=null) $sql .= " AND id='".$id."' ";
        if($cari!='' && $cari !=null) $sql .= " $cari ";

        if (isset($dipaging) && $dipaging ==1) {
            $sql .= " limit $offset, $limit ";
        }
        //echo $sql;
        return $this->db->query($sql);
    }
}