<?php
namespace BoergenerWebdesign\BwFeAdmin\Middleware;

use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

class SwitchUserMiddleware implements MiddlewareInterface {
    /** @var ObjectManager  */
    protected $objectManager;
    /** @var FrontendUserRepository  */
    protected $frontendUserRepository;

    /**
     * AjaxController constructor.
     */
    public function __construct() {
        $this -> objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        /** @var FrontendUserRepository $accountRepository */
        $this -> frontendUserRepository = $this -> objectManager->get(FrontendUserRepository::class);
    }


    /**
     * Bearbeitet die Anfrage.
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws \Exception
     */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        // Prüfen ob Parameter vorhanden ist und ein Login durchgeführt werden soll.
        if(!$this -> checkQueryParams($request -> getQueryParams())) {
            return $handler->handle($request);
        }

        // Prüfen ob aktueller BE-User Administrator*in ist
        $this -> checkIfBeUserIsAdmin();
        // Prüfen ob Benutzer*in existiert
        $user = $this -> getFrontendUser($request -> getQueryParams()["feadmin_uid"]);

        // Einloggen!
        $userArray = $GLOBALS['TSFE']->fe_user->getRawUserByName($user -> getUsername());

        $userAuth = $this->objectManager->get(FrontendUserAuthentication::class);
        $userAuth->checkPid = false;
        $GLOBALS['TSFE']->fe_user->forceSetCookie = TRUE;
        $GLOBALS['TSFE']->fe_user->dontSetCookie = false;
        $GLOBALS['TSFE']->fe_user->start();
        $GLOBALS['TSFE']->fe_user->createUserSession($userArray);
        $GLOBALS['TSFE']->fe_user->setAndSaveSessionData('dummy', TRUE);
        $GLOBALS['TSFE']->fe_user->loginUser = 1;

        $GLOBALS['TSFE']->fe_user->user = $GLOBALS['TSFE']->fe_user->fetchUserSession();
        $GLOBALS['TSFE']->fe_user->fetchGroupData();
        $GLOBALS['TSFE']->loginUser = true;

        return $handler->handle($request);
    }

    /**
     * Prüft ob der BE-User Administrator*in ist.
     */
    private function checkIfBeUserIsAdmin() : void {
        if(!$GLOBALS['BE_USER'] || !$GLOBALS['BE_USER'] -> isAdmin()) {
            throw new \Exception("Der BE-User muss Administrator sein um diese Funktion nutzen zu können!", 1611618644);
        }
    }

    /**
     * Prüft ob die Query Parameter ausreichend sind.
     * @param array|null $queryParams
     * @throws \Exception
     */
    private function checkQueryParams(?array $queryParams) : bool {
        return $queryParams && key_exists("feadmin_uid", $queryParams);
    }

    /**
     * Ermittelt den FrontendUser.
     * @param int $uid
     * @return FrontendUser
     * @throws \Exception
     */
    private function getFrontendUser(int $uid) : FrontendUser {
        $user = $this -> frontendUserRepository -> findByUid($uid);
        if(!$user) {
            throw new \Exception("Der gewünschte Benutzer konnte nicht gefunden werden!", 1611618646);
        }
        return $user;
    }
}