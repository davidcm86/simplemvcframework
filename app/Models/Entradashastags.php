<?php 
namespace Models;

use Core\Model;

class Entradashastags extends Model 
{    
    function __construct(){
        parent::__construct();
    }

    public function getRelacion($entrada_id, $tag_id){
		return $this->db->select("SELECT * FROM entradashastags WHERE entrada_id =:entrada_id and tag_id =:tag_id",array(':entrada_id' => $entrada_id,':tag_id' => $tag_id));
	}

	public function getIdsTags($entrada_id){
		return $this->db->select("SELECT id,tag_id FROM entradashastags WHERE entrada_id =:entrada_id",array(':entrada_id' => $entrada_id));
	}
	
    public function insertTags($data) {
    	return $this->db->insert('entradashastags', $data);
	}

	public function deleteEntrada($id) {
    	$this->db->delete('entradashastags', array('id' => $id));
	}
	
}