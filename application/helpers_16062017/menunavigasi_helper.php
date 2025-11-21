<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	function make_menu(){
		$CI =& get_instance();
		$menunav = '';
		$menu = $CI->session->userdata('menu');
		$user_id = $CI->session->userdata('s_access');
		if($user_id==1){
			$sql = "select * from menu_new where menu_parent=0 and menu_status=1 GROUP BY menu_id order by menu_sort asc";
		}else{
			$sql = "SELECT menu_new.*,users.id FROM users 
                    INNER JOIN menu_level ON menu_level.menu_level_user_level=users.user_level_id
                    INNER JOIN menu_new ON menu_new.menu_id = menu_level.menu_level_menu
					WHERE user_level_id = '".$user_id."' and menu_parent=0 and menu_status=1 
					GROUP BY menu_id ORDER BY menu_sort";
		}
		
		$query = $CI->db->query($sql);
		foreach($query->result_array() as $row)
		{
			if(toogle($row['menu_id'],$user_id) > 0){
				$class = ($row['menu_id']==$menu)?'active':'';
				$menunav .= "<li class='dropdown ".$class."'>";
				$menunav .= '<a href="#" class="dropdown-toggle" data-toggle="dropdown">
								<span>'.$row['menu_desc'].'</span>
								<b class="caret"></b>
							</a>';
				$menunav .=	formatTree($row['menu_id'],$user_id);
				$menunav .= "</li>";
			}else{
				$class = ($row['menu_id']==$menu)?'active':'';
				$menunav .= "<li class='".$class."'>";
				$menunav .= '<a href="'.site_url($row['menu_link']).'">
								<span>'.$row['menu_desc'].'</span>
							</a>';
				$menunav .= "</li>";
			}
		}
		
		echo $menunav;
	}		
	
	function formatTree($id_parent,$user_id){
		$CI =& get_instance();
		if($user_id==1){
			$sql = "select * from menu_new where menu_parent='".$id_parent."' and menu_status=1  GROUP BY menu_id ORDER BY menu_sort asc";
		}else{
			$sql = "SELECT menu_new.*,users.id FROM users 
                    INNER JOIN menu_level ON menu_level.menu_level_user_level=users.user_level_id
                    INNER JOIN menu_new ON menu_new.menu_id = menu_level.menu_level_menu
					WHERE user_level_id = '".$user_id."' AND menu_parent = '".$id_parent."' and menu_status=1  
					GROUP BY menu_id ORDER BY menu_sort";
		}
		
		$query = $CI->db->query($sql);
		$menunav = "<ul class='dropdown-menu'>";
        foreach($query->result_array() as $item){
			if(toogle($item['menu_id'],$user_id) > 0){
				$menunav .= "<li class='dropdown'>";
				$menunav .= '<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">'.$item['menu_desc'].' <b class="caret"></b></a>';
				$menunav.= formatTree($item['menu_id'],$user_id);
				$menunav.= "</li>";		
			}else{
				$menunav .= "<li>";
				$menunav .= '<a href="'.site_url($item['menu_link']).'">'.$item['menu_desc'].'</a>';
				$menunav.= "</li>";	
			}
        }

      $menunav.= "</ul>";
	  return $menunav;
    }
	
	function toogle($id_parent,$user_id){
		$CI =& get_instance();
		if($user_id==1){
			$sql = "select * from menu_new where menu_parent='".$id_parent."' and menu_status=1  GROUP BY menu_id ORDER BY menu_sort asc";
		}else{
			$sql = "SELECT menu_new.*,users.id FROM users 
                    INNER JOIN menu_level ON menu_level.menu_level_user_level=users.user_level_id
                    INNER JOIN menu_new ON menu_new.menu_id = menu_level.menu_level_menu
					WHERE user_level_id = '".$user_id."' AND menu_parent = '".$id_parent."' and menu_status=1  
					GROUP BY menu_id ORDER BY menu_sort";
		}
		$query = $CI->db->query($sql);
		return $query->num_rows();
    }

    function makeBreadcrumb($id=null) {
        $CI =& get_instance();
        $urlme = $CI->uri->segment(1);

        if ($id==null) {
            $s = "select * from menu_new where menu_link='$urlme'";
        } else
        {
            $s = "SELECT * FROM menu_new WHERE menu_id = $id";
        }
        $qry = $CI->db->query($s);
        if ($qry->num_rows() >0) {
            $row = $qry->row_array();
            $name = $row['menu_desc'];
            $selname = $row['menu_link'] == $urlme ? "class='active'" : "";
            if ($row['menu_parent'] == 0) {
                return "<li $selname>" . $name . "</li>";
            } else {
                return makeBreadcrumb($row['menu_parent']) . "<li $selname>" . $name . "</li>";
            }
        } else
        {
            return "";
        }
}
?>