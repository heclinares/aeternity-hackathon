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
    $digests             = openssl_get_md_methods();

    // User exist?
    if ($user !== null) {
      // Create new token
      $token = bin2hex(openssl_random_pseudo_bytes(32));
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
    
    // We expect to receive the token provided by /login-request but signed using ECDSA algorithm
    $token  = $request->query->get('token');
    $em     = $this->getDoctrine()->getManager();
    $user   = $em->getRepository('App:User')->findOneByUuid($uuid);
    
    // User exist?
    if ($user !== null) {
      // Check signature
      $sig = openssl_verify($user->getToken(), pack("H*", $token), $user->getPubkey(), OPENSSL_ALGO_SHA256);
      print_r($sig);
      exit;
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
   * @Route("/local-login-request-test", name="public_login_request_test")
   */
  public function loginRequestTest(Request $request)
  {
    // Init & get query parameters
    $uuid   = $request->query->get('uuid');
    
    // We expect to receive the token provided by /login-request but signed using ECDSA algorithm
    $token  = $request->query->get('token');
    $em     = $this->getDoctrine()->getManager();
    $user   = $em->getRepository('App:User')->findOneByUuid($uuid);
    
    // User exist?
    if ($user !== null) {
      // Create new token
      $token = bin2hex(openssl_random_pseudo_bytes(32));
      $user->setToken($token);
      
      // Sign the token
      $cert = file_get_contents(__DIR__ . '/../../prime256v1-key.pem');
      $prkey = openssl_pkey_get_private($cert);
      $signature = '';
      $signed = openssl_sign($token, $signature, $prkey, OPENSSL_ALGO_SHA256);
      openssl_free_key($prkey);
      
      print_r('Token: ' . $token . "<br>");
      print_r('Signed: ');
      print_r(bin2hex($signature));exit;
      
      // Send token in the response
      $this->response['response'] = new \stdClass();
      $this->response['response']->token = $token;
      $this->response['response']->signed = bin2hex($signature);
      
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
