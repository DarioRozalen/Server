<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Passwords;
use \Firebase\JWT\JWT;
use App\Categories;
use App\Users;

class PasswordController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $headers = getallheaders();

        if ($headers["Authorization"] != null){

            $parameters = JWT::decode($headers["Authorization"], $this-> key, array('HS256'));

            $id = Users::where('email', $parameters->email)->first()->id;

            $passwords = Passwords::where('users_id', $id)->get();

            foreach ($passwords as $key => $password)
            {
                return $this->success("MyPasswords", $passwords);
            }


        }


        else
        {
            return response()->json(["Message" =>403, "No tienes suficientes permisos"]);
        }
        
    }


    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $headers = getallheaders();
        $token = $headers['Authorization'];

        $key = $this->key;
        $userData = JWT::decode($token, $key, array('HS256'));

        //$categoryName = $_POST['categoryName'];
        $passwordTitle = $_POST['passwordTitle'];
        $passwordPass = $_POST['passwordPass'];
        $idCategory = $_POST['idCategory'];

        //$id_passwords = Passwords::where()


        $id = Users::where('email', $userData->email)->first()->id;

        $passwords = Passwords::where('users_id', $id)->get();

        foreach ($passwords as $password) 

        {
            if ($password->title == $passwordTitle) 
            {
                return $this->error(400, 'El titulo de la password ya existe'); 
            }
        }

        if (!preg_match("/^[a-zA-Z ]*$/",$passwordTitle)) {
            return $this->error(400, 'El titulo de la password solo puede contener caracteres sin espacios en blanco'); 
        }

        if (empty($passwordTitle)) {
            return $this->error(400, 'Por favor introduce un titulo para la password');
        }
        if (empty($passwordPass)) {
            return $this->error(400, 'Por favor introduce una password para la password');
        } 

        $idCategories = Categories::where('id', $idCategory)->get();


        if ($idCategories->isEmpty() && !empty($idCategory)) { 

            return $this->error(400, "La categoria no existe, por favor introduce una existente");

        }


        if ($this->checkLogin($userData->email , $userData->password)) 
        { 
            
        	$password = new Passwords();
            $password->title = $passwordTitle;
            $password->password = encrypt($passwordPass);
            if (!empty($idCategory)) {
            	 $password->category_id = $idCategory;
            }
            
            $password->users_id = $id;
            $password->save();

            return $this->success('Password creada', $request->passwordTitle);

        }
        else
        {
            return $this->error(401, "No tienes permisos");
        }
     
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Rols  $rols
     * @return \Illuminate\Http\Response
     */
    public function show(Password $password)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Rols  $rols
     * @return \Illuminate\Http\Response
     */
    public function edit(Password $password)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Rols  $rols
     * @return \Illuminate\Http\Response
     */
    public function deletePassword()
    {
        $headers = getallheaders();
        $token = $headers['Authorization'];
        $key = $this->key;
        $userData = JWT::decode($token, $key, array('HS256'));
        $id_user = Users::where('email', $userData->email)->first()->id;
        $id_password = $_POST['idPassword'];
        $id = $id_password;
        
        $password = Passwords::find($id);
        if (is_null($password)) {
            return $this->error(400, 'Esa password no existe');
        }else{
            $password_title = Passwords::where('id', $id_password)->first()->title;
            Passwords::destroy($id);

        return $this->success('Password Borrada', $password_title);
        }
    }


    public function updatePassword()
    {
        $headers = getallheaders();
        $token = $headers['Authorization'];
        $key = $this->key;
        $userData = JWT::decode($token, $key, array('HS256'));
        $id_password = $_POST['idPassword'];
        $newTitle = $_POST['newTitle'];
        $newPass = $_POST['newPass'];
        $newCategory = $_POST['newCategory'];


        $id = $id_password;
        $password = Passwords::find($id);


        $category = Categories::find($newCategory);



        if (is_null($password)) {
            return $this->error(400, 'Esa password no existe');
        }

        if (empty($_POST['newTitle']) && empty($_POST['newPass']) && empty($_POST['newCategory']))
            {
                return $this->error(400, 'Para editar rellena al menos un campo');
            }

        if (!empty($_POST['newTitle']) ) {
            $password->title = $newTitle;
        }
        if (!empty($_POST['newPass']) ) {
            $password->password = encrypt($newPass);
        }
        if (!empty($_POST['newCategory']) ) 
        {
        	if (is_null($category)) {
            return $this->error(400, 'Esa categoria no existe');
        }
            $password->category_id = $newCategory;
        }


            $password->save();
        return $this->success('Password actualizada');
        

    }

    public function update(Request $request, Password $password)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Rols  $rols
     * @return \Illuminate\Http\Response
     */
    public function destroy(Password $password)
    {
        //
    }
}
