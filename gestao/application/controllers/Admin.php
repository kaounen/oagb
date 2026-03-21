<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->database();
		$this->load->helper('url');

		$this->load->library('grocery_CRUD');
	}

	public function _example_output($output = null)
	{
		$this->load->view('backend.php',(array)$output);
	}

	/*public function offices()
	{
		$output = $this->grocery_crud->render();

		$this->_example_output($output);
	}
*/
	public function index()
	{
		$this->_example_output((object)array('output' => '' , 'js_files' => array() , 'css_files' => array()));
	}

	

	
public function noticias_()
{
		
			$crud = new grocery_CRUD();

			//$crud->set_theme('datatables');
			$crud->set_table('noticias');

		
			$crud->set_subject('Noticias');

			$crud->display_as('titulo','Titulo');
			
			$crud->display_as('foto1','Fotografia 1');
			$crud->set_field_upload('foto1','assets/uploads/files');
			$crud->display_as('legendaFoto1','Legenda Foto 1');
			$crud->display_as('foto2','Fotografia 2');
			$crud->set_field_upload('foto2','assets/uploads/files');
			$crud->display_as('legendaFoto2','Legenda Foto 2');
			$crud->display_as('foto3','Fotografia 3');
			$crud->set_field_upload('foto3','assets/uploads/files');
			$crud->display_as('legendaFoto3','Legenda Foto 3');

			$crud->display_as('foto4','Fotografia 4');
			$crud->set_field_upload('foto4','assets/uploads/files');
			$crud->display_as('legendaFoto4','Legenda Foto 4');

			$crud->display_as('foto5','Fotografia 5');
			$crud->set_field_upload('foto5','assets/uploads/files');
			$crud->display_as('legendaFoto5','Legenda Foto 5');


			//$crud->display_as('resumo','Resumo');
			$crud->display_as('data','Data');
			$crud->display_as('corpo','Corpo da noticia');

			$crud->display_as('fotosFlickr','Fotos Flickr');
			$crud->display_as('legendaFlickr','Legenda fotos Flickr');
			$crud->display_as('video-youtube','Video Youtube');
			$crud->display_as('legendaVideo','Legenda Video Youtube');
			
			$crud->display_as('ficheiro1','Ficheiro 1');
			$crud->set_field_upload('ficheiro1','assets/uploads/files');
			$crud->display_as('legendaFicheiro1','Legenda Ficheiro 1');

			$crud->display_as('ficheiro2','Ficheiro 2');
			$crud->set_field_upload('ficheiro2','assets/uploads/files');
			$crud->display_as('legendaFicheiro2','Legenda Ficheiro 2');
			
			$crud->display_as('ficheiro3','Ficheiro 3');
			$crud->set_field_upload('ficheiro3','assets/uploads/files');
			$crud->display_as('legendaFicheiro3','Legenda Ficheiro 3');


			//$crud->unset_texteditor('titulo','resumo','legendaFoto1','legendaFoto2','legendaFoto3','fotosFlickr','legendaFlickr','video-youtube','legendaVideo','legendaFicheiro1','legendaFicheiro2','legendaFicheiro3');
			
			$crud->required_fields('titulo','foto1','data');

	public function carousel()
	{
		$crud = new grocery_CRUD();
		$crud->set_table('carousel_slides');
		$crud->set_subject('Slides do Carousel');
		$crud->required_fields('titulo', 'imagem');
		$crud->set_field_upload('imagem', 'assets/uploads/files');
		
		$output = $crud->render();
		$this->_example_output($output);
	}

	public function agenda()
	{
		$crud = new grocery_CRUD();
		$crud->set_table('agenda');
		$crud->set_subject('Eventos da Agenda');
		$crud->required_fields('titulo', 'data_evento');
		$crud->set_field_upload('imagem_destaque', 'assets/uploads/files');
		
		$output = $crud->render();
		$this->_example_output($output);
	}

	public function pareceres()
	{
		$crud = new grocery_CRUD();
		$crud->set_table('pareceres_deliberacoes');
		$crud->set_subject('Pareceres e Deliberações');
		$crud->required_fields('tipo', 'numero_documento', 'titulo', 'data_documento');
		$crud->set_field_upload('arquivo_pdf', 'assets/uploads/files');
		
		$output = $crud->render();
		$this->_example_output($output);
	}

	public function comunicados()
	{
		$crud = new grocery_CRUD();
		$crud->set_table('comunicados');
		$crud->set_subject('Comunicados');
		$crud->required_fields('titulo', 'data_publicacao');
		
		$output = $crud->render();
		$this->_example_output($output);
	}

	public function advogados()
	{
		$crud = new grocery_CRUD();
		$crud->set_table('advogados');
		$crud->set_subject('Advogados');
		$crud->required_fields('numero_registo', 'nome_completo', 'regiao', 'data_inscricao');
		$crud->set_field_upload('foto', 'assets/uploads/files');
		
		$output = $crud->render();
		$this->_example_output($output);
	}

	public function noticias()
	{
		$crud = new grocery_CRUD();
		$crud->set_table('noticias');
		$crud->set_subject('Notícias');
		$crud->required_fields('titulo', 'slug', 'conteudo');
		$crud->set_field_upload('imagem_destaque', 'assets/uploads/files');
		
		$output = $crud->render();
		$this->_example_output($output);
	}
}
