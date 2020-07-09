<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\SerializerInterface;
//use Symfony\Component\Serializer\SerializerInterface;

use FOS\RestBundle\Controller\Annotations as Rest;
use App\Entity\Aviso;
use App\Entity\Voto;
use App\Entity\AlertaInformacion;

/**
 * Class ApiController
 *
 * @Route("/api")
 */
class ApiController extends AbstractController
{
    /**
     * @Route("/", name="index_api")
     */
    public function index()
    {
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }

    /*GESTION DE AVISOS*/
    /**
     * @Rest\Get("/aviso", name="obtener_avisos")
     */
    public function getAllAvisos(SerializerInterface $serializer){
        $manejador = $this->getDoctrine()->getManager();
        $avisos =[];

        
        $avisos = $manejador->getRepository("App:Aviso")->findAll();
        
        $code = 200;
        $error = false;
        $response = [
        	'code' => $code,
        	'error' => $error,
        	'data' => $avisos,
        ];
    return new Response($serializer->serialize($response, "json"),200,["Content-Type"=>"application/json"]);
    }

    /**
     * @Rest\Get("/aviso/{id}", name="aviso_id")
     */
    public function getAviso(SerializerInterface $serializer, $id){
        $manejador = $this->getDoctrine()->getManager();
        $aviso = [];
      
       
        $aviso = $manejador->getRepository("App:Aviso")->find($id);
        
        $code = 200;
        $error = false;
        $response = [
        	'code' => $code,
        	'error' => $error,
        	'data' =>$aviso ,
        ];
        return new Response($serializer->serialize($response, "json"),200,["Content-Type"=>"application/json"]);
    }

    /**
     * @Rest\Post("/aviso/new", name="crear_aviso")
     */
    public function newAviso(SerializerInterface $serializer, Request $request){
    	$manejador = $this->getDoctrine()->getManager();
        $aviso = new Aviso();
        $mensaje = "";
        	$latitud = $request->get("latitud", null);
        	$longitud = $request->get("longitud", null);
        	$ip = $request->get("ip", null);
        	$foto = $request->get("foto", null);
        	$comentario = $request->get("comentario", null);
        	$tipo = $request->get("tipo", null);
        	$codconcello = $request->get("codconcello", null);

    		$aviso->setLatitud($latitud)->setLongitud($longitud)->setIp($ip)->setFoto($foto)->setComentario($comentario)->setTipo($tipo)->setCodConcello($codconcello);
    		/*Buscamos las alertas que tienen al aviso dentro.*/
    		$alertasInteresadas = $this->getAlertasDeAviso($aviso);
    		foreach($alertasInteresadas as $alerta){
    			$aviso->addAlertasInformacion($alerta);
    		}
    		$manejador->persist($aviso);
    		$manejador->flush();
      
        $response = [
        	'code' => 201,
        	'error' => false,
        	'data' => $aviso,
        ];

        return new Response($serializer->serialize($response, "json"),201,["Content-Type"=>"application/json"]);
    }
     /**
     * @Rest\Put("/aviso/edit/{id}", name="editar_aviso")
     */
    public function editAviso(SerializerInterface $serializer, $id, Request $request){
    	$manejador = $this->getDoctrine()->getManager();
        

        $aviso = $manejador->getRepository("App:Aviso")->find($id);
        $foto = $request->get("foto", null);
        if($foto!=null)  
        	$aviso->setFoto($foto) ;
        $comentario = $request->get("comentario", null);
        if($comentario!=null)  
        	$aviso->setComentario($comentario) ;
        $tipo = $request->get("tipo", null);
        if($tipo!=null) 
        	$aviso->setTipo($tipo) ;
    	$manejador->persist($aviso);
    	$manejador->flush();


        $response = [
        	'code' => 201,
        	'error' => false,
        	'data' => $aviso ,
        ];

           return new Response($serializer->serialize($response, "json"),201,["Content-Type"=>"application/json"]);
    }
    /**
     * @Rest\Delete("/aviso/del/{id}", name="borrar_aviso")
     */
    public function deleteAviso(SerializerInterface $serializer, $id){
    	$manejador = $this->getDoctrine()->getManager();
        $mensaje = "";
        
        $aviso = $manejador->getRepository("App:Aviso")->find($id);
    	$manejador->remove($aviso);
    	$manejador->flush();
        
        $response = [
        	'code' => 201,
        	'error' => false,
        	'data' => $aviso,
        ];

       return new Response($serializer->serialize($response, "json"),201,["Content-Type"=>"application/json"]);
    }



    /*GESTION DE VOTOS*/
    /**
     * @Rest\Get("/voto", name="obtener_votos")
     */
    public function getAllVotos(SerializerInterface $serializer){
        $manejador = $this->getDoctrine()->getManager();
        $votos =[];

        $votos = $manejador->getRepository("App:Voto")->findAll();
       
        $response = [
        	'code' => 200,
        	'error' => false,
        	'data' => $votos,
        ];

        return new Response($serializer->serialize($response, "json"),200,["Content-Type"=>"application/json"]);
    }

    /**
     * @Rest\Get("/voto/{id}", name="voto_id")
     */
    public function getVoto(SerializerInterface $serializer, $id){
        $manejador = $this->getDoctrine()->getManager();
        $voto = [];

        $voto = $manejador->getRepository("App:Voto")->find($id);
        
        $response = [
        	'code' => 200,
        	'error' => false,
        	'data' => $voto ,
        ];

       return new Response($serializer->serialize($response, "json"),200,["Content-Type"=>"application/json"]);
    }

    /**
     * @Rest\Post("/voto/new", name="crear_voto")
     */
    public function newVoto(SerializerInterface $serializer, Request $request){
    	$manejador = $this->getDoctrine()->getManager();
        $voto = new Voto();

        $confirmado = $request->get("confirmado", null);
        $ip = $request->get("ip", null);
        $comentario = $request->get("comentario", null);
        $avisoID = $request->get("avisoId", null);
        $voto->setConfirmado($confirmado)->setIp($ip)->setComentario($comentario);
        //Recuperamos el aviso y le añadimos el voto.
        $aviso = $manejador->getRepository("App:Aviso")->find($avisoID);
        $voto->setAviso($aviso);
        $manejador->persist($voto);
    	$manejador->flush();
       	//$aviso->addVoto($voto);
       	//$manejador->persist($aviso);
        $response = [
        	'code' => 201,
        	'error' => false,
        	'data' => $voto,
        ];

    	return new Response($serializer->serialize($response, "json"),201,["Content-Type"=>"application/json"]);
    }
     /**
     * @Rest\Put("/voto/edit/{id}", name="editar_voto")
     */
    public function editVoto(SerializerInterface $serializer, $id, Request $request){
    	$manejador = $this->getDoctrine()->getManager();
        
        
        $voto = $manejador->getRepository("App:Voto")->find($id);
        $confirmado = $request->get("confirmado", null);
        if($confirmado!=null)  
        	$voto->setConfirmado($confirmado) ;
        $comentario = $request->get("comentario", null);
        if($comentario!=null)  
        	$voto->setComentario($comentario) ;
    	$manejador->persist($voto);
    	$manejador->flush();
        
        $response = [
        	'code' => 201,
        	'error' => false,
        	'data' => $voto ,
        ];

        return new Response($serializer->serialize($response, "json"),201,["Content-Type"=>"application/json"]);
    }
    /**
     * @Rest\Delete("/voto/del/{id}", name="borrar_voto")
     */
    public function deleteVoto(SerializerInterface $serializer, $id){
    	$manejador = $this->getDoctrine()->getManager();
       
        $voto = $manejador->getRepository("App:Voto")->find($id);
    	$manejador->remove($voto);
    	$manejador->flush();
       
        $response = [
        	'code' => 201,
        	'error' => false,
        	'data' => $voto,
        ];

        return new Response($serializer->serialize($response, "json"),201,["Content-Type"=>"application/json"]);
    }


/*GESTION DE ALERTASINFORMACION*/


    /**
     * @Rest\Get("/alertainformacion", name="obtener_alertas")
     */
    public function getAllAlertas(SerializerInterface $serializer){
        $manejador = $this->getDoctrine()->getManager();
        $alertas =[];
       
        $alertas = $manejador->getRepository("App:AlertaInformacion")->findAll();
        
        $response = [
        	'code' => 200,
        	'error' => false,
        	'data' => $alertas ,
        ];

       return new Response($serializer->serialize($response, "json"),200,["Content-Type"=>"application/json"]);
    }

    /**
     * @Rest\Get("/alertainformacion/{id}", name="alerta_id")
     */
    public function getAlerta(SerializerInterface $serializer, $id){
        $manejador = $this->getDoctrine()->getManager();
        $alerta = [];
     
        $alerta = $manejador->getRepository("App:AlertaInformacion")->find($id);
       
        $response = [
        	'code' => 200,
        	'error' => false,
        	'data' => $alerta ,
        ];
        return new Response($serializer->serialize($response, "json"),200,["Content-Type"=>"application/json"]);
    }


    /**
     * @Rest\Post("/alertainformacion/new", name="crear_alerta")
     */
    public function newAlerta(SerializerInterface $serializer, Request $request){
    	$manejador = $this->getDoctrine()->getManager();
        $alerta = new AlertaInformacion();
        
        $codConcello =$request->get("codConcello", null);
        $areaString = $request->get("area", null);
        if($codConcello!=null){
        		$alerta = $manejador->getRepository("App:AlertaInformacion")->findByCodConcello($codConcello);
        }else{
    		$alerta->setArea($areaString);
    		//Buscamos los avisos que existan en ese area. 
        	$avisos = $this->getAvisosEnArea(json_decode($areaString,true));
        	foreach($avisos as $aviso){
    		$alerta->addAviso($aviso);
    	}
        }

    		//$alerta->addUsuario($this->getUser());
    	$manejador->persist($alerta);
    	$manejador->flush();
    
        $response = [
        	'code' => 201,
        	'error' => false,
        	'data' =>$alerta,
        ];

          return new Response($serializer->serialize($response, "json"),201,["Content-Type"=>"application/json"]);
    }

     /**
     * @Rest\Post("/alertainformacion/newAyuntamiento", name="crear_ayuntamiento")
     */
    public function newAyuntamiento(SerializerInterface $serializer, Request $request){
    	$manejador = $this->getDoctrine()->getManager();
        $alerta = new AlertaInformacion();
      
        $codConcello =$request->get("codConcello", null);
        $areaString = $request->get("area", null);
        $alerta->setArea($areaString);
        $alerta->setCodConcello($codConcello); 
    		//Buscamos los avisos que existan en ese area. Se podria reducir si se añade directamente en el getAvisosEnArea.
        $avisos = $this->getAvisosEnArea(json_decode($areaString,true));
        foreach($avisos as $aviso){
    		$alerta->addAviso($aviso);
    	}
        	

    	//$alerta->addUsuario($this->getUser());
    	$manejador->persist($alerta);
    	$manejador->flush();
       
        $response = [
        	'code' => 201,
        	'error' => false,
        	'data' =>$alerta,
        ];

          return new Response($serializer->serialize($response, "json"),201,["Content-Type"=>"application/json"]);
    }

    

     /**
     * @Rest\Put("/alertainformacion/edit/{id}", name="editar_alerta")
     */
    public function editAlerta(SerializerInterface $serializer, $id, Request $request){
    	$manejador = $this->getDoctrine()->getManager();
        
        	$alerta = $manejador->getRepository("App:AlertaInformacion")->find($id);
        	if($alerta->getCodConcello==null){//No pueden modificar los que tengan codigo, estan blindados.
        		$areaJson = $request->get("area", null);
        		//Comprobar usuario para mayor seguridad.
    			//$alerta->setArea(json_encode($areaJson));
    			if($areaJson != null){
    				$avisos = $alerta->getAvisos();
    				foreach ($avisos as $aviso) {
    				    $aviso->removeAlertasInformacion($alerta);
    				}    				
    				$alerta->setArea($areaJson);
    				$avisos = $this->getAvisosEnArea(json_decode($areaJson,true));
        			foreach($avisos as $aviso){
    					$alerta->addAviso($aviso);
    				}
    			}
        		

        	}
        	
    		$manejador->persist($alerta);
    		$manejador->flush();
     
        $response = [
        	'code' => 201,
        	'error' => false,
        	'data' => $alerta ,
        ];

       return new Response($serializer->serialize($response, "json"),201,["Content-Type"=>"application/json"]);
    }
    /**
     * @Rest\Delete("/alertainformacion/del/{id}", name="borrar_alerta")
     */
    public function deleteAlerta(SerializerInterface $serializer, $id){
    	$manejador = $this->getDoctrine()->getManager();
     
        $alerta = $manejador->getRepository("App:AlertaInformacion")->find($id);
        if($alerta->getCodConcello!=null){
        	//  if($alerta->getUsuarios[0]->getId==$this->getUser()->getId())
        	
        	$manejador->remove($alerta);
    		$manejador->flush();
        }else{//Si es un concello simplemente eliminamos al usuario de su lista
        		//$alerta->removeUsuario($this->getUser());
        		//$manejador->persist($alerta);
        		
        }
    	$manejador->flush();
      
        $response = [
        	'code' => 201,
        	'error' => false,
        	'data' =>  $alerta,
        ];

       return new Response($serializer->serialize($response, "json"),201,["Content-Type"=>"application/json"]);
    }

    private function getAvisosEnArea($poligono){
        $manejador = $this->getDoctrine()->getManager();
        $avisos =[];
        $avisosArea =[];
        
        $avisos = $manejador->getRepository("App:Aviso")->findAll();
            foreach($avisos as $aviso){
                if($this->buscarAviso($aviso->getLatitud(), $aviso->getLongitud(), $poligono)){
                    array_push($avisosArea, $aviso);
                }
            }
       return $avisosArea;
    }

    private function getAlertasDeAviso($aviso){
    	   $manejador = $this->getDoctrine()->getManager();
        $alertas =[];
        $alertasDelAviso =[];
        
        $alertas = $manejador->getRepository("App:AlertaInformacion")->findAll();
            foreach($alertas as $alerta){
            	$poligono=json_decode($alerta->getArea(),true);
                if($this->buscarAviso($aviso->getLatitud(), $aviso->getLongitud(), $poligono )){
                    array_push($alertasDelAviso, $alerta);
                }
            }
       return $alertasDelAviso;
    }
    //Importante que el array de puntos sea indexado por lat y lon
    private function buscarAviso($latitud, $longitud, $polyPoints) {
        $inside = false;
        $x = $latitud; $y = $longitud;
            for ($i = 0, $j = (count($polyPoints) - 1); $i < count($polyPoints); $j = $i++) {
                $xi = $polyPoints[$i]["lat"]; $yi = $polyPoints[$i]["lon"];
                $xj = $polyPoints[$j]["lat"]; $yj = $polyPoints[$j]["lon"];

                $intersect = (($yi > $y) != ($yj > $y))
                && ($x < ($xj - $xi) * ($y - $yi) / ($yj - $yi) + $xi);
                if ($intersect) $inside = !$inside;
            }
        return $inside;
    }
}
