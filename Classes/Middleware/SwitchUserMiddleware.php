<?php
namespace BoergenerWebdesign\BwFeAdmin\Middleware;

use BoergenerWebdesign\BwFeAdmin\Authentication\MiddlewareAuthentication;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

class SwitchUserMiddleware implements MiddlewareInterface {
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

        // Session erzeugen
        /** @var FrontendUserAuthentication $frontendUser */
        $frontendUser = GeneralUtility::makeInstance(MiddlewareAuthentication::class);
        $frontendUser -> start($request, $request -> getQueryParams()["feadmin_uid"]);

        // Login in Session speichern
        $response = new RedirectResponse($this -> getRedirectUri($request), 302);
        $frontendUser->storeSessionData();
        return $frontendUser->appendCookieToResponse($response);;
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
     * Returns the current URI without parameters.
     * @param ServerRequestInterface $request
     * @return string
     */
    private function getRedirectUri(ServerRequestInterface $request) : string {
        return $request -> getUri() -> getScheme()."://".$request -> getUri() -> getHost().$request -> getUri() -> getPath();
    }
}