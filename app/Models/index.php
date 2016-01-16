<?php 
namespace Models;

use Core\Model;

class Entradas extends Model 
{    
    function __construct(){
        parent::__construct();
    }

    public function getEntradas() {
    	return $this->db->select('SELECT * FROM entradas order by id');
	}
	
    public function insertEntradas() {
    	return $this->db->select('SELECT * FROM entradas');
	}
	
	public function updateEntradas() {
    	return $this->db->select('SELECT * FROM entradas');
	}
	
	public function deleteEntradas() {
    	return $this->db->select('SELECT * FROM entradas');
	}
	
}