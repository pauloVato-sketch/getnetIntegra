<?php
namespace Listeners;

use Doctrine\ORM\EntityManager;

use Zeedhi\Framework\DTO\Request;

class PreDispatcher extends \Zeedhi\Framework\Events\PreDispatch\Listener {

    const authRoutes = array(
        '/OperatorRepository',
        '/UtilitiesTest',
        '/FiliaisLogin',
        '/CaixasLogin',
        '/VendedoresLogin',
        '/OrderLoginUserRepository',
        '/lib_buildMenu',
        '/OperatorLogout',
        '/auth'
    );

    private $entityManager;
    private $sessionService;

    public function __construct(EntityManager $entityManager, \Helpers\Environment $sessionService){
        $this->entityManager = $entityManager;
        $this->sessionService = $sessionService;
    }

    public function preDispatch(Request $request) {
        $route = $request->getRoutePath();
        if (self::isNotAuthRoute($route)){
            $this->sessionService->handleSessionLifetime();
            $consumerId = $this->sessionService->getUserInfo();
            if ($consumerId == NULL) {
                throw new \Exception("Sessão expirada. É necessário refazer o Login no sistema.", 1);
            }
        }
    }

    private function isNotAuthRoute($route) {
        return !in_array($route, self::authRoutes);
    }
}