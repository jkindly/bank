<?php

namespace App\Security;

use App\Entity\LoginLogs;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;

    private $userRepository;
    private $router;
    private $csrfTokenManager;
    private $passwordEncoder;
    private $user;
    private $em;
    private $username;
    private $isSuccess;

    public function __construct(EntityManagerInterface $em, UserRepository $userRepository, RouterInterface $router, CsrfTokenManagerInterface $csrfTokenManager,
                                UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->userRepository = $userRepository;
        $this->router = $router;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->em = $em;
    }

    public function supports(Request $request)
    {
        return $request->attributes->get('_route') === 'app_login'
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        $credentials = [
          'username' => $request->request->get('username'),
          'password' => $request->request->get('password'),
          'csrf_token' => $request->request->get('_csrf_token'),

        ];

        $request->getSession()->set(
          Security::LAST_USERNAME,
            $credentials['username']
        );

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);

        if(!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $this->user = $this->userRepository->findOneBy(['username' => $credentials['username']]);
        $this->username = $credentials['username'];

        return $this->user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $this->isSuccess = true;

        $this->loginLogsFlush();

        if($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->router->generate('app_account'));
    }

    protected function getLoginUrl()
    {
        return $this->router->generate('app_login');
    }
    
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        if ($this->user != NULL) {
            $this->isSuccess = false;
            $this->loginLogsFlush();
        }
        return parent::onAuthenticationFailure($request, $exception);
    }

    public function loginLogsFlush() {
        $loginLogs = new LoginLogs();

        $loginLogs
            ->setUsername($this->username)
            ->setUser($this->user)
            ->setIsSuccess($this->isSuccess)
        ;

        $this->em->persist($loginLogs);
        $this->em->flush();
    }
}
