<?php
namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController{
    public static function login(Router  $router){
        $alertas = [];
        $auth = new Usuario($_POST);
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $auth = new Usuario($_POST);
            $alertas = $auth->validarLogin();
            if(empty($alertas)){
                //Comprobar que exista el usuario
                $usuario = Usuario::where('email',$auth->email);
                if($usuario){
                    //Verificar Password
                    if($usuario->comprobarPasswordAndVerificado($auth->password)){
                        //Autenticar al Usuario
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        //Redireccionamiento
                        if($usuario->admin === '1'){
                            $_SESSION['admin'] = $usuario->admin ?? NULL;
                            header('Location: /admin');
                        }else{
                            header('Location: /cita');
                        }
                    }
                }else{
                    Usuario::setAlerta('error',' El Usuario no existe.');
                }
            }
        }
        $alertas = Usuario::getAlertas();

        $router->render('auth/login', [
            'alertas' => $alertas,
            'auth'    => $auth
        ]);

    }
    public static function logout(Router  $router){
        session_start();
        $_SESSION = [];
        header('Location: /');
    }
    public static function olvide(Router  $router){
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $auth= new Usuario($_POST);
            $alertas = $auth->validarEmail();
            if(empty($alertas)){
                $usuario = Usuario::where('email', $auth->email);
                if($usuario && $usuario->confirmado === '1'){
                    //Generar token
                    $usuario->crearToken();
                    $usuario->guardar();

                    //Enviar el Email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();

                    //Alerta de Exito
                    Usuario::setAlerta('exito','Hemos enviado un email de confirmación a su correo.');
                }
                else{
                    Usuario::setAlerta('error',' El Usuario no existe o no está confirmado.');
               }
            }
        }
        $alertas = Usuario::getAlertas();
        $router->render('auth/olvide-password', [
            'alertas' => $alertas
        ]);
    }
    public static function recuperar(Router  $router){
        $alertas = [];
        $error = false;
        $token = s($_GET['token']);

        //Buscar Usuario
        $usuario = Usuario::where('token', $token);

       if(empty($usuario)){
            //Mostrar mensaje de error
            Usuario::setAlerta('error','Token no válido');
            $error = true;
        }
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            //Leer el nuevo Password y modificarlo
            $password = new Usuario($_POST);
            $alertas = $password->validarPassword();
            if(empty($alertas)){
                $usuario->password = null;
                $usuario->password =  $password->password;
                $usuario->hashPassword();
                $usuario->token = null;

                $resultado = $usuario->guardar();
                if($resultado){
                    header('Location: /');
                }
            }   
        }
        $alertas = Usuario::getAlertas();
        $router->render('auth/recuperar-password', [
            'alertas' => $alertas,
            'error' => $error
        ]);
    }
    public static function crear(Router  $router){
        $usuario = new Usuario;
        //Alertas Vacias
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            //Revisar que alerta este vacio
            if(empty($alertas)){
                //Verificar que el usuario no este registrado
                $resultado = $usuario->existeUsuario();
                if($resultado->num_rows){
                    $alertas = Usuario::getAlertas();
                }else{
                    //Haschear el Password
                    $usuario->hashPassword();

                    //Generar un TOKEN unico
                    $usuario->crearToken();

                    //Enviar Email
                    $email =  new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarConfirmacion();

                    //crear el usuario
                    $resultado = $usuario->guardar();
                    if($resultado){
                        header('Location: /mensaje');
                    }
                }
            }

        }
        $router->render('auth/crear-cuenta', [
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function mensaje(Router  $router){
        $router->render('auth/mensaje', [

        ]);
    }

    public static function confirmar(Router  $router){
        $alertas = [];
        $token = s($_GET['token']);
        $usuario = Usuario::where('token', $token);
        if(empty($usuario)){
            //Mostrar mensaje de error
            Usuario::setAlerta('error','Token no válido');
        }else{
            //Modificar el valor a confirmado
            $usuario->confirmado = '1';
            $usuario->token = NULL;
            $usuario->guardar();
            Usuario::setAlerta('exito','Cuenta comprobada correctamente!');
        }
        //Obtener Alertas
        $alertas = Usuario::getAlertas();
        //Renderizar la vista
        $router->render('auth/confirmar-cuenta', [
            'alertas' => $alertas
        ]);
    }
}