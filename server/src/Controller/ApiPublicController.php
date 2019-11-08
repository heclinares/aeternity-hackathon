<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
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
   * First step for a "Login with Aeternity" action
   * Client requests a login token, the server generates one, in a safe way and returns it
   * Client must sign the token and send it back to the path /login-complete
   * Receives user UUID by query parameters
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
      $this->response['response'] = $token;
      $this->response['error'] = false;
      
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
   * After signing the token, the client must call this endpoint, sending the signed token
   * The server here checks for validation, granting access or denying it, depending on the test result 
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
      // TODO: Used openssl_verify for a quick proof of concept. We will move all this process to a node.js server and use Aeternity SDK 
      $sig = openssl_verify($user->getToken(), pack("H*", $token), $user->getPubkey(), OPENSSL_ALGO_SHA256);
      print_r($sig);
      // Show the signature result, and exit, to check the process has been completed
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
   * @Route("/download", name="public_download_view")
   * Shows a basic website with a game uploaded to the decentralized shop
   * Includes a download button that should test if the game has been bought by the user or not,
   * and then, grant access to the link
   */
  public function downloadView(Request $request)
  {
    return new Response('
      <html>
        <body style="font-family: \'Open Sans\', sans-serif; padding-top: 200px; background-color: #000; background-image: url(background.jpg); color: #fff; text-align: center; background-size: cover;">
          <h1>Block adventures</h1>
          <p>Block adventures is an awesome fictional game created to test the system.<br/>It\'s already included into the shop contract with the address: <strong>ADDRESS</strong></p>
          <div style="margin-top: 100px">
            <button onclick="window.open(\'/download-file\')" type="button" style="color: #fff; border: none; cursor: pointer;background: rgb(255, 13, 106) none repeat scroll 0% 0%;padding: 14px;border-radius: 0px;">Download Block Adventures</button>
          </div>
        </body>
      </html>
    ');
  }
  
  protected function getUserById($uuid) {
    $em     = $this->getDoctrine()->getManager();
    $user   = $em->getRepository('App:User')->findOneByUuid($uuid);
    
    return $user;
  }
  
  /**
   * @Route("/download-file", name="public_download_file")
   * Shows a basic website with a game uploaded to the decentralized shop
   * Includes a download button that should test if the game has been bought by the user or not,
   * and then, grant access to the link
   */
  public function downloadFile(Request $request)
  {
    // Get user based on UUID
    // In a future version this should be changed by a complete user auth system
    $user = $this->getUserById($request->query->get('user_id'));
    $em = $this->getDoctrine()->getManager();
    // Get contract address by ID
    $game = $em->getRepository('App:Game')->findOneByUuid($request->query->get('game_id'));
    
    // HACK: this is a quick work around for the hackathon, to use the AE JS SDK with PHP
    // We have created a simple nodejs service that includes the SDK and works like a middleware
    // Calling the /gameBalance endpoint via RPC, we can check if the user has access to the game or not
    // Must have node-service running before calling this function
    
    // Curl to node-service asking for balance
    // Generate CURL call
    $ch            = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:3000/gameBalance' );
    curl_setopt($ch, CURLOPT_POST, 0);
    
    // Query parameters
    $query = 'gameAddress=' . $game->getContractAddress() . '&userAddress=' . $user->getPubkey();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
    $resp = curl_exec($ch);
    $resp = json_decode($resp);
    
    print_r($resp);
    exit;
    
    // Need to provide: contract address and user address
    
    // Parse response
    
    // Allowed, send download link
    
    // Not allowed, say it polite
  }
  
  /**
   * @Route("/local-login-request-test", name="public_login_request_test")
   * Test method
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
