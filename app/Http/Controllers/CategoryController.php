<?php

namespace App\Http\Controllers;

use App\Categories;
use Illuminate\Http\Request;
use \Firebase\JWT\JWT;
use App\Users;

class CategoryController extends Controller
{
    public function index()
   {
        $headers = getallheaders();

        if ($headers["Authorization"] != null){

            $parameters = JWT::decode($headers["Authorization"], $this-> key, array('HS256'));
            $id = Users::where('email', $parameters->email)->first()->id;
            $categories = Categories::where('users_id', $id)->get();

            foreach ($categories as $key => $category)
            {
                return $this->success("MyCategories", $categories);
            }
        }

        else
        {
            return response()->json(["Message" =>403, "No tienes suficientes permisos"]);
        }    
    }

    public function create()
    {
        
    }

    public function store(Request $request)
    {
        $headers = getallheaders();
        $token = $headers['Authorization'];
        $key = $this->key;
        $userData = JWT::decode($token, $key, array('HS256'));
        $categoryName = $_POST['categoryName'];

        $id = Users::where('email', $userData->email)->first()->id;
        $categories = Categories::where('users_id', $id)->get();
        
        foreach ($categories as $category) 
        {
            if ($category->name == $categoryName) 
            {
                return $this->error(400, 'El nombre de la categoria ya existe'); 
            }
        }

        if (!preg_match("/^[a-zA-Z ]*$/",$categoryName)) {
            return $this->error(400, 'El nombre de la categoria solo puede contener caracteres sin espacios en blanco'); 
        }

        if (empty($categoryName)) {
            return $this->error(400, 'Por favor introduce un nombre para la categoria');
        } 

        if ($this->checkLogin($userData->email , $userData->password)) 
        { 
            $category = new Categories();
            $category->name = $categoryName; 
            $category->users_id = $id;
            $category->save();

            return $this->success('Categoria creada', $request->categoryName);

        }
        else
        {
            return $this->error(401, "No tienes permisos");
        }
     }

     public function deleteCategory()
    {
        $headers = getallheaders();
        $token = $headers['Authorization'];
        $key = $this->key;
        $userData = JWT::decode($token, $key, array('HS256'));
        $id_user = Users::where('email', $userData->email)->first()->id;
        $id_category = $_POST['idCategory'];
        $id = $id_category;
        
        $category = Categories::find($id);
        if (is_null($category)) {
            return $this->error(400, 'Esa categoria no existe');
        }else{
            $category_name = Categories::where('id', $id_category)->first()->name;
            Categories::destroy($id);

        return $this->success('Categoria Borrada', $category_name);
        }
    }

    public function updateCategory()
    {
        $headers = getallheaders();
        $token = $headers['Authorization'];
        $key = $this->key;
        $userData = JWT::decode($token, $key, array('HS256'));
        $id_category = $_POST['idCategory'];
        $newName = $_POST['newName'];

        $id = $id_category;
        $category = Categories::find($id);



        if (is_null($category)) {
            return $this->error(400, 'Esa categoria no existe');
        }

        if (!empty($_POST['newName']) ) {
            $category->name = $newName;
        }else{
            return $this->error(400, 'No puede estar el nombre vacío');
        }

       
            $category->save();
        return $this->success('Categoria actualizada');
        

    }

    
    public function show()
    {
        
    }

    
    public function edit(Category $category)
    {
        
    }

    
    public function update(Request $request, Category $category)
    {
        
    }

    
    public function destroy(Category $category)
    {
        
    }
}
