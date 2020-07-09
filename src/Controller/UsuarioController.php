<?php
// src/Controller/UsuarioController

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use App\Form\UsuarioType;
use App\Entity\Usuario;

class UsuarioController extends AbstractController
{

	 
    /**
     * @Route("/usuarios", name="usuarios")
     * @IsGranted("ROLE_USER_EDIT")
     */
    public function usuariosAction()
    {
		$usuarios = $this->getDoctrine()
		->getRepository(Usuario::class)
		->findAll();
        return $this->render('security/usuarios.html.twig', [
            'usuarios' => $usuarios
        ]);
    }
    /**
	 * @Route("/usuarios/new", name="novo_usuario")
	 *  @IsGranted("ROLE_USER_EDIT")
	 */
	public function newAction(Request $request,UserPasswordEncoderInterface $encoder ) {
		//$this->denyAccessUnlessGranted('ROLE_USER_ADMIN', null, 'acceso denegado a esta paxina');
		 $usuario =  new Usuario();
		 $form = $this->createForm(UsuarioType::class, $usuario);
		 $form -> handleRequest($request);
		 if ($form -> isSubmitted() && $form ->isValid()) {
			 
			   // $password= $this->get('security.password_encoder')
			  // ->encodePassword($u    suario, $usuario->getPassword());
			   $password = $encoder->encodePassword($usuario, $usuario->getPassword());
			   
			  $usuario-> setPassword($password);
			  //$user ->setIsActive($form->get('isActive')->getData());
			  $usuario -> setRoles([$form->get('rol')->getData()]);
			  $em  = $this ->getDoctrine() -> getManager();
			  $em -> persist($usuario);
			  $em -> flush();
			  
			  return $this->redirectToRoute('usuarios');
		 }
		 return $this->render(
		    'security/new.html.twig', 
		    array ('form' => $form->createView())
		 );
	}
	/**
	 * @Route("/usuarios/del/{id}", name="borra_usuario") 
     */
     public function delAction($id){
		 $entityManager = $this->getDoctrine()->getManager();
		 $usuario = $entityManager->getRepository(Usuario::class)
		 ->find($id);
		 if (!$usuario) {
			 throw $this->createNotFoundException('non se atopou usuario con id: '.$id);
		 }
		 $entityManager->remove($usuario);
		 $entityManager->flush();
		 
        return $this->redirectToRoute('usuarios');
	 }
    
}
