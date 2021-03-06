<?php  

namespace App\Controllers;
use App\Form;
use App\Password;
use App\Auth;

class RegisterController extends Controller{
    
    public function __construct()
    {
        parent::__construct();
        $this->model->setTable("cliente");
        // $this->model->addTable("Comic");
    }
    
    public function index()
    {
		$data['title'] = "Registre-se";
        $this->view->loadPage("register",$data);
    }

    public function registrar()
    {

        $fields =  array(
            'nome',
            'email',
            'senha',
            'repetir-senha',
            'cpf',
            'telefone',
            'endereco'
        );

        $filters = array( 'nome'=>FILTER_SANITIZE_STRING,
            'email'=>FILTER_SANITIZE_STRING,
            'senha'=>FILTER_SANITIZE_STRING,
            'repetir-senha'=>FILTER_SANITIZE_STRING,
            'cpf'=>FILTER_SANITIZE_STRING,
            'telefone'=>FILTER_SANITIZE_STRING,
            'endereco'=>FILTER_SANITIZE_STRING
        );

        $this->form_manager = new Form($fields,$filters);
        $form_data = $this->form_manager->getFilteredData();

       

        if(!$this->verifyIfEmailIsRegistred($form_data['email'])) {

            if($form_data['senha'] !== $form_data['repetir-senha']) {
                die('Senhas não conferem!');
            } 

            $data = [ 'nome'=>$form_data['nome'],
                'cpf'=>$form_data['cpf'],
                'endereco'=>$form_data['endereco'],
                'telefone'=>$form_data['telefone'],
                'email'=>$form_data['email'],
                'senha'=>Password::hashPassword($form_data['senha'], PASSWORD_DEFAULT, 10),
                'deletado'=>0
            ];

            if($this->model->insert($data)->run("rowCount", $data)) {

                $this->auth->newUserLogin(['username'=>$data['nome'], 'email'=>$data['email'], 'password'=>$data['senha']]);

                echo true;
            } else {
                echo false;
            }

        } else {
            echo "Usuário já cadastrado!";
        }

    }

    private function verifyIfEmailIsRegistred($email)
    {
        return $this->model->select()->where('email', $email)->run("rowCount");
    }
    
}