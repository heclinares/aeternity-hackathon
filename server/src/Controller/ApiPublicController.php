<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ApiPublicController extends Controller
{
  private $response = [];
  
  public function __construct() {
    $this->response['error'] = true;
    $this->response['response'] = 'Unexpected error';
  }
  
  /**
   * @Route("/login-request", name="public_login_request")
   */
  public function loginRequest(Request $request)
  {
    // Init & get query parameters
    $uuid   = $request->query->get('uuid');
    $em     = $this->getDoctrine()->getManager();
    $user   = $em->getRepository('App:User')->findOneByUuid($uuid);
    
    // User exist?
    if ($user !== null) {
      // Create new token
      $token = openssl_random_pseudo_bytes(32);
      $user->setToken($token);
      
      // Send token in the response
      $this->reponse['response'] = $token;
      
      $em->persist($user);
    } else {
      $this->response['response'] = 'User not found';
    }
    
    // Return response
    return new JsonResponse(
      $this->response
    );
  }
  
  /**
   * @Route("/login-complete", name="public_login_complete")
   */
  public function loginComplete(Request $request)
  {
    // Init & get query parameters
    $uuid   = $request->query->get('uuid');
    $token  = $request->query->get('token');
    $em     = $this->getDoctrine()->getManager();
    $user   = $em->getRepository('App:User')->findOneByUuid($uuid);
    
    // User exist?
    if ($user !== null) {
      // TODO: Check if token is correct
      
      
      $em->persist($user);
    } else {
      $this->response['response'] = 'User not found';
    }
    
    // Return response
    return new JsonResponse(
      $this->response
    );
  }
}
