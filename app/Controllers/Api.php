<?php
namespace Controllers;

use Core\View;
use Core\Controller;


/**
 * Sample controller showing a construct and 2 methods and their typical usage.
 */
class Api extends Controller
{
    private $Entradas;
    private $Tags;
    private $Entradashastags;

    /**
     * Call the parent construct
     */
    public function __construct()
    {
        parent::__construct();
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->entradas = new \Models\Entradas();
        $this->tags = new \Models\Tags();
        $this->entradashastags = new \Models\Entradashastags();    
    }

    /**
     * Inicio de la Api el cual solo responde que está viva
     */
    public function api()
    {
        $xmlResponse = '<div>Bienvenido a la API!</div>';
        header('Content-Type: text/xml; charset=utf-8');
        echo <<<EOF
<?xml version="1.0" encoding="utf-8"?>
<api>
<response>
{$xmlResponse}
</response>
</api>
EOF;

    }
    
    /**
     * Gestionamos todas las peticiones que llegan de entradas
     */
    public function entradas()
    {
    	// dependiendo del método que recibamos...
        switch($this->method){
            case 'GET':
                $entradas = $this->entradas->getEntradas();
                foreach ($entradas as $entrada) {
                    $xmlResponse .= <<<EOF
<list-entrada>
    <name>{$entrada->name}</name>
    <description>{$entrada->description}</description>
</list-entrada>
EOF;
                }
                break;
            case 'POST':
                $xmlResponse = $this->apiAddEntrada($_POST);
                $xmlResponse .= <<<EOF
<add-entrada>
    <div>Entrada creada correctamente</div>
</add-entrada>
EOF;
                break;
            case 'PUT':
                $xmlResponse = $this->apiEditEntrada($_GET);
                $xmlResponse .= <<<EOF
<edit-entrada>
    <div>Entrada editada correctamente</div>
</edit-entrada>
EOF;
                break;
            case 'DELETE':
                $xmlResponse = $this->apiDeleteEntrada($_GET['id']);
                $xmlResponse .= <<<EOF
<edit-entrada>
    <div>Entrada borrada correctamente</div>
</edit-entrada>
EOF;
                break;
            default:
                $xmlResponse = '<div>Nothing found</div>';
        }
        header('Content-Type: text/xml; charset=utf-8');
        echo <<<EOF
<?xml version="1.0" encoding="utf-8"?>
<api>
<response>
{$xmlResponse}
</response>
</api>
EOF;
        
    }
	
    /**
     * Añadimos una entrada
     */
	public function apiAddEntrada($datos)
	{
	    $name = $_POST['name'];
		$description = $_POST['description'];
		if(empty($name)){
			$error[] = 'Introduzca un nombre';
		}
		if(empty($description)){
			$error[] = 'Introduzca una descripción';
		}
		if(!isset($error)){
			$postdata = array(
				'name' => $name,
				'description' => $description
			);
			$idEntrada = $this->entradas->insertEntrada($postdata);
			if($idEntrada) {
				$this->guardarTags($description, $idEntrada);
				return true;
			}
		}
		return $error;
	}
	
	/**
     * Editamos una entrada
     */
	public function apiEditEntrada($datos)
	{
	    var_dump($datos);
	    $id = $datos['id'];
		$name = base64_decode($datos['name']);
		$description = base64_decode($datos['description']);
		if(empty($name)){
			$error[] = 'Introduzca un nombre';
		}
		if(empty($description)){
			$error[] = 'Introduzca una descripción';
		}
		if(!isset($error)){
			$postdata = array(
				'name' => $name,
				'description' => $description
			);
			$where = array('id' => $id);
			$this->entradas->updateEntrada($postdata, $where);
			$this->guardarTags($description, $id);
			echo \Helpers\Url::redirect($string);
		}
		
		$data['title'] = 'Editar entrada';
		$data['row'] = $this->entradas->getEntrada($id);

		$tagsRecuperados = $this->entradashastags->getIdsTags($id);
		foreach ($tagsRecuperados as $tagRecuperado) {
			$tags[] = $this->tags->getTags($tagRecuperado->tag_id);
		}
		$data['tags'] = $tags;
		$this->view->rendertemplate('header',$data);
		$this->view->render('entradas/edit',$data,$error);
		$this->view->rendertemplate('footer',$data);	
	}

    /**
     * Borramos una entrada
     */
	public function apiDeleteEntrada($id)
	{
		$this->borrarRelacionTags($id);
		$this->entradas->deleteEntrada($id);
		return true;
	}

    /**
     * Guardamos los tags separando la descripción por espacios
     */
	public function guardarTags($description, $idEntrada)
	{
		$this->borrarRelacionTags($idEntrada);
		// borramos relaciones para meterla de nuevo sin necesidad de mirar si sobra o falta alguna
		$this->entradashastags->deleteEntrada($idEntrada);
		// separamos la cadena para crear los tags
		$tags = explode(" ", $description);
		foreach ($tags as $tag) {
			// verificamos si ya existe el tag
			$idTag = $this->tags->getTag($tag);
			if (!$idTag['0']->tag) {
				// no existe el tag, lo creo
				$data = array(
					'tag' => $tag
				);
				$idTag = $this->tags->insertTag($data);
				if ($idTag) {
					// si existe la relación no lo creamos otra vez
					$relacion = $this->entradashastags->getRelacion($idEntrada,$idTag['0']->id);
					if (empty($relacion['0']->id)) {
						$postdata = array(
							'entrada_id' => $idEntrada,
							'tag_id' => $idTag
						);
						$this->entradashastags->insertTags($postdata);
					}
				}
			} else {
				// si existe la relación no lo creamos otra vez
				$relacion = $this->entradashastags->getRelacion($idEntrada,$idTag['0']->id);
				if (empty($relacion['0']->id)) {
					$postdataInsert = array(
						'entrada_id' => $idEntrada,
						'tag_id' => $idTag['0']->id
					);
					$idRelacionCreada = $this->entradashastags->insertTags($postdataInsert);
				}
			}
		}
		return true;
	}

    /**
     * Boramos la relación entre entrada y tag
     */
	public function borrarRelacionTags($idEntrada)
	{
		$tagsRecuperados = $this->entradashastags->getIdsTags($idEntrada);
		foreach ($tagsRecuperados as $tagRecuperado) {
			$this->entradashastags->deleteEntrada($tagRecuperado->id);
		}
	}
}
