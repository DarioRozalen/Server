<?php

namespace App\Http\Controllers;

use App\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use \Firebase\JWT\JWT;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $header = getallheaders();
        $userParams = JWT::decode($header['Authorization'], $this->key, array('HS256'));

        $userList = Users::where('rol_id', 2)->get();
            return $this->success("Lista de usuarios", $userList);

        
    }
    public function register (Request $request)



    {
        if (!isset($_POST['name']) or !isset($_POST['email']) or !isset($_POST['password'])) 
        {
            return $this->error(401, 'Tienes que rellenar todos los campos');
        }

       

        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $users = Users::where('email', $email)->get();


        foreach ($users as $user) 

        {
            if ($user->email == $email) 

            {
                return $this->error(400, 'El email ya existe, por favor utiliza otro'); 
            }

        }


        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

            return $this->error(400, 'Por favor introduzca un email valido'); 
        }

        if (strlen($password) < 8){

            return $this->error(400, 'El password debe tener al  menos 8 caracteres');
        }

    
        
    

        if (!empty($name) && !empty($email) && !empty($password))
        {
            try
            {
                $users = new Users();
                $users->name = $name = str_replace(' ', '',$request->name);
                $users->password = encrypt($password);
                $users->email = $email;
                $users->rol_id = 2;

                $users->save();
            }


            catch(Exception $e)
            {
                return $this->error(2, $e->getMessage());
            }
            
            return $this->success('Usuario registrado correctamente');
        }
        else
        {
            return $this->error(401, 'Debes rellenar todos los campos');
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    protected function login(Request $request)

    {

        $email = $_POST['email'];
        $password = $_POST['password'];

        if (empty($email)){

            return $this->error (401, 'Por favor introduce un email');

        }

        if (empty($password)){

            return $this->error (401, 'Por favor introduce el password');

        }

        $users = Users::where('email', $email)->get();


        if ($users->isEmpty()) { 

            return $this->error(400, "Ese usuario no existe, por favor introduce un email correcto");

        }





       
        $userDecrypt = Users::where('email', $email)->first();


        $passwordHold = $userDecrypt->password;

        $decryptedPassword = decrypt($passwordHold);
        $key = $this->key;
        if (self::checkLogin($email, $password))
        {
            
            $userSave = Users::where('email', $email)->first();


            $array = $arrayName = array
            (
                'id' => $userSave->id,
                'email' => $email,
                'password' => $password,
                'name' => $userSave->name
            );

            
            $token= JWT::encode($array, $key);

            return $this->success("Usuario logueado", $token);

            // return response($token)->header('Access-Control-Allow-Origin', '*');

        }
        else
        {
            return response("Los datos no son correctos", 403)->header('Access-Control-Allow-Origin', '*');
        }
        

    }


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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function updateUser()

    {
        $headers = getallheaders();
        $token = $headers['Authorization'];
        $key = $this->key;
        $userData = JWT::decode($token, $key, array('HS256'));
        $id_user = $_POST['idUser'];
        $newName = $_POST['newName'];
        $newEmail = $_POST['newEmail'];
        $newPassword = $_POST['newPassword'];

        $id = $id_user;
        $user = Users::find($id);
        $userObj = Users::where('email', $userData->email)->first();
        $rol = $userObj->rol_id;

        if ($rol == 1)
        {

            if (is_null($user)) {
                return $this->error(400, 'El usuario no existe');
            }           
            

            if (empty($_POST['newName']) && empty($_POST['newEmail']) && empty($_POST['newPassword']))
            {
                return $this->error(400, 'Para editar rellena al menos un campo');
            }


            if (!empty($_POST['newName']) ) 
            {

                $user->name = $newName;
            }

            if (!empty($_POST['newEmail']))
            {   
                if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL))
                {

                    return $this->error(400, 'Por favor introduce un email valido');

                }


                $user->email = $newEmail;
                //return $this->success(200, 'Email Modificado');
            }

            if(!empty($_POST['newPassword']))
            {
                if(strlen($newPassword) < 8)
                {
                    return $this->error(415, 'El password tiene que tener al menos 8 caracteres');
                    $user->password =encrypt($newPassword);

                }

            }
            // if (!empty($_POST['newPassword']) && strlen($password) < 8 ) {
            //     $user->password =encrypt($newPassword);
            // }             
            $user->save();
            return $this->success(200, 'Usuario Actualizado');

        }else{
            return $this->error(401, 'No tienes permisos para editar');
        }



    }

    

    public function deleteUser()

    {

        $headers = getallheaders();
        $token = $headers['Authorization'];
        $key = $this->key;
        $userData = JWT::decode($token, $key, array('HS256'));
        $id_user = Users::where('email', $userData->email)->first()->id;
        $id_users = $_POST['idUser'];
        $id = $id_users;

        $user = Users::find($id);

        $rolUser = Users::where("email", $userData->email)->first()->rol_id;


        if($rolUser == 1){

        $user_name = Users::where('id', $id_users)->first()->name;
        Users::destroy($id);

        return $this->success('Usuario Borrado', $user_name);

        } 

        else {
            return $this->error(403, "No tienes suficientes permisos");
        }
        
        if (is_null($user)) {
            return $this->error(400, 'El usuario no existe');



        }
    }

    public function destroy(Users $user)
    {
        
   

    }

    

       
}
