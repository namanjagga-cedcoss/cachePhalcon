<?php

declare(strict_types=1);

use Phalcon\Security\JWT\Builder;
use Phalcon\Security\JWT\Signer\Hmac;
use Phalcon\Security\JWT\Token\Parser;
use Phalcon\Security\JWT\Validator;
use Phalcon\Mvc\Controller;



class SignupController extends Controller
{

        public function indexAction()
        {
        }
        public function registerAction()
        {
                $user = new Users();

                // assign value from the form to $user
                $user->assign(
                        $this->request->getPost(),
                        [
                                'name',
                                'email',
                                'password',
                                'role',
                                'status'
                        ],
                );
                // Store and check for errors


                $success = $user->save();

                // passing the result to the view
                $this->view->success = $success;

                if ($success) {
                        $message = "Thanks for registering!";
                        $signer  = new Hmac();

                        $builder = new Builder($signer);

                        $now        = new DateTimeImmutable();
                        $issued     = $now->getTimestamp();
                        $notBefore  = $now->modify('-1 minute')->getTimestamp();
                        $expires    = $now->modify('+1 day')->getTimestamp();
                        $passphrase = 'QcMpZ&b&mo3TPsPk668J6QH8JA$&U&m2';

                        $builder
                                ->setAudience('https://localhost')  // aud
                                ->setContentType('application/json')        // cty - header
                                ->setExpirationTime($expires)               // exp 
                                ->setId('abcd123456789')                    // JTI id 
                                ->setIssuedAt($issued)                      // iat 
                                ->setIssuer('https://phalcon.io')           // iss 
                                ->setNotBefore($notBefore)                  // nbf
                                ->setSubject($_POST['role'])   // sub
                                ->setPassphrase($passphrase)                // password 
                        ;

                        $tokenObject = $builder->getToken();

                        $tokenObject->getToken();
                        if ($tokenObject != null) {
                                $checkToken = 1;
                        } else {
                                echo 'access denied';
                        }
                        $this->response->redirect('addproduct/add?bearer=' . $tokenObject->getToken());


                        // $this->logger2->info('user signup ');
                } else {
                        $message = "Sorry, the following problems were generated:<br>"
                                . implode('<br>', $user->getMessages());
                        // $this->logger2->error('something went wrong');
                }

                // passing a message to the view
                $this->view->message = $message;
        }
}