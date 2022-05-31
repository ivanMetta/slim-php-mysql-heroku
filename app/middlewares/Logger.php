<?php
use Slim\Psr7\Response;
class Logger
{
    public static function LogOperacion($request, $response, $next)
    {
        $retorno = $next($request, $response);
        return $retorno;
    }
    public static function VerificarCredenciales($request, $handler)
    {
        $requestType = $request->getMethod();
        $response = new response();
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);

        try {
            AutentificadorJWT::verificarToken($token);
            $esValido = true;
          } catch (Exception $e) {
            $payload = json_encode(array('error' => $e->getMessage()));
          }

        if($esValido)
        {
            if($requestType == "GET")
            {
                $response->getBody()->write('Metodo ' . $requestType .'no verifica' );

            }elseif ($requestType == "POST"){

                $response->getBody()->write('Metodo ' . $requestType .' verifica' );
                $dataParseada= $request->getParsedBody();
                $nombre = $dataParseada['nombre'];
                $perfil = $dataParseada['perfil'];
                if($perfil == "admin")
                {
                    $response->getBody()->write('Bienvenido ' . $nombre );
                    $handler->handle($request);                
                }
                else{
                    $response->getBody()->write('Usuario no autorizado ' . $nombre . 'perfil: ' . $perfil);              
                    $response->withStatus(302);
                }           

            }
        }
        return $response;
    }
}
