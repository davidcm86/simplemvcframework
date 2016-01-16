<?php 
namespace Models;

use Core\Model;

class Tags extends Model 
{    
    function __construct(){
        parent::__construct();
    }

    public function getTags($id){
		return $this->db->select("SELECT * FROM tags WHERE id =:id",array(':id' => $id));
	}

	public function getTag($tag){
		return $this->db->select("SELECT * FROM tags WHERE tag =:tag",array(':tag' => $tag));
	}
	
    public function insertTag($data) {
    	return $this->db->insert('tags', $data);
	}
	
}