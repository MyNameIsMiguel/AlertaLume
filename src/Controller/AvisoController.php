<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Form\AvisoType;
use App\Entity\Aviso;

class AvisoController extends AbstractController
{
	/**
	 * @Route("/avisos", name="avisos")
	 */ 
 public function avisosAction()
    {
		$avisos = $this->getDoctrine()
		->getRepository(Aviso::class)
		->findAll();
        return $this->render('aviso/avisos.html.twig', [
            'avisos' => $avisos
        ]);
    }
    /**
     * @Route("/avisos/new", name="novo_aviso")
     */
    public function newAction(Request $request)
    {
		$aviso = new Aviso();
		$form = $this->createForm(AvisoType::class, $aviso);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			/** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
			//$file = $aviso->getFoto();
			$file = $form->get('foto')->getData();
			 //return new Response (var_dump($aviso));
			
			$fileName = $this->generaNombreFichero().'.'.$file->guessExtension();
			try {
				$file->move(
				$this->getParameter('dir_imagenes'),
				$fileName
				);
				
			}catch (FileException $e) {
				// ...
			}
			$aviso->setFoto($fileName);
			
			$em  = $this ->getDoctrine() -> getManager();
			$em -> persist($aviso);
			$em -> flush();
			  
			  return $this->redirectToRoute('avisos');
			 
		}
		
		
        return $this->render('aviso/new.html.twig', 
            array ('form' => $form->createView())
        );
    }
    
    private function generaNombreFichero () : string
    {
		// md5 recude a similutide dos nomes de fichero xeraos
		// por uniqid(), que está baseado en timestamps.
		return md5(uniqid());
	}
    
}

