<?php
namespace Controllers;

use Core\View;
use Core\Controller;


/**
 * Sample controller showing a construct and 2 methods and their typical usage.
 */
class Entradas extends Controller
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
        $this->entradas = new \Models\Entradas();
        $this->tags = new \Models\Tags();
        $this->entradashastags = new \Models\Entradashastags();    
    }

    /**
     * Mostramos las entradas disponibles
     */
    public function entradas()
    {
        $data['entradas'] = $this->entradas->getEntradas();
        $data['title'] = 'Listado de entradas';

        View::renderTemplate('header', $data);
        View::render('entradas/entradas', $data);
        View::renderTemplate('footer', $data);
    }
    
    /**
     * Añadimos una entrada
     */
    public function add()
    {
		if(isset($_POST['submit'])){
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
				}
				echo \Helpers\Url::redirect($string);
			}
		}
		$data['title'] = 'Añadir Entrada';
		$this->view->rendertemplate('header',$data);
		$this->view->render('entradas/add',$data,$error);
		$this->view->rendertemplate('footer',$data);	
	}
	
	/**
     * Editamos una entrada
     */
	public function edit($id)
	{
		if(isset($_POST['submit'])){
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
				$where = array('id' => $id);
				$this->entradas->updateEntrada($postdata, $where);
				$this->guardarTags($description, $id);
				echo \Helpers\Url::redirect($string);
			}
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
	public function delete($id)
	{
		$this->borrarRelacionTags($id);
		$this->entradas->deleteEntrada($id);
		echo \Helpers\Url::redirect($string);
	}

    /**
     * Guardamos los tags separando la descripción por espacios
     */
	public function guardarTags($description, $idEntrada)
	{
		// borramos relaciones para meterla de nuevo sin necesidad de mirar si sobra o falta alguna
		$this->borrarRelacionTags($idEntrada);
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
