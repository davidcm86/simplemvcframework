<?php 
namespace Models;

use Core\Model;

class Entradas extends Model 
{    
    function __construct(){
        parent::__construct();
    }

    public function getEntradas() {
    	return $this->db->select('SELECT * FROM entradas order by id desc');
	}
	
	public function getEntrada($id){
		return $this->db->select("SELECT * FROM entradas WHERE id =:id",array(':id' => $id));
	}
	
    public function insertEntrada($data) {
    	return $this->db->insert('entradas', $data);
	}
	
	public function updateEntrada($data, $where) {
		return $this->db->update('entradas', $data, $where);
	}
	
	public function deleteEntrada($id) {
    	$this->db->delete('entradas', array('id' => $id));
	}
	
}