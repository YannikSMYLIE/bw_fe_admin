<?php

namespace BoergenerWebdesign\BwFeAdmin\Authentication;

use Doctrine\DBAL\Exception;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Authentication\Mfa\MfaRequiredException;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

class MiddlewareAuthentication extends FrontendUserAuthentication {
    /** @var array  */
    protected array $userData = [];

    /**
     * @param ServerRequestInterface|null $request
     * @param int $userUid
     * @return void
     * @throws Exception
     * @throws MfaRequiredException
     */
    public function start(ServerRequestInterface $request = null, int $userUid = -1) {
        $this -> readUserData($userUid);
        if($this -> userData) {
            parent::start($request);
        }
    }

    /**
     * Gewünschte:n Benutzer:in anmelden.
     * @param ServerRequestInterface|null $request
     * @return void
     */
    public function checkAuthentication(ServerRequestInterface $request = null) {
        $this -> userSession = $this -> createUserSession($this -> userData);
        $this->user = array_merge($this -> userData, $this->user ?? []);
        $this->loginSessionStarted = true;
    }

    /**
     * Reads user data given by uid.
     * @param int $userUid
     * @return void
     * @throws Exception
     */
    private function readUserData(int $userUid) : void {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('fe_users');
        $result = $queryBuilder
            -> select('*')
            -> from("fe_users")
            -> where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($userUid))
            )
            -> executeQuery();
        $users = $result -> fetchAllAssociative();

        if(count($users) == 1 && $users[0]) {
            $this -> userData = $users[0];
        }
    }

}