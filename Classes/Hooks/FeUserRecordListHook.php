<?php
namespace BoergenerWebdesign\BwFeAdmin\Hooks;

use BoergenerWebdesign\BwCourse\Domain\Model\FrontendUser;
use BoergenerWebdesign\BwFeAdmin\Utilities\FrontendUriBuilderUtility;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository;
use TYPO3\CMS\Extbase\Object\Exception;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Recordlist\RecordList\RecordListHookInterface;

class FeUserRecordListHook implements RecordListHookInterface {
    /** @var UriBuilder  */
    protected UriBuilder $uriBuilder;
    /** @var IconFactory  */
    protected IconFactory $iconFactory;
    /** @var ObjectManager  */
    protected ObjectManager $objectManager;
    /** @var FrontendUserRepository  */
    protected FrontendUserRepository $frontendUserRepository;

    /**
     * FeUserRecordListHook constructor.
     */
    public function __construct() {
        $this -> iconFactory = GeneralUtility::makeInstance(IconFactory::class);
        $this -> uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $this -> objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this -> frontendUserRepository = $this -> objectManager -> get(FrontendUserRepository::class);
    }

    /**
     * @param string $table
     * @param array $row
     * @param array $cells
     * @param object $parentObject
     * @return array
     */
    public function makeControl($table, $row, $cells, &$parentObject) : array {
        if($table == "fe_users") {
            // FE-User einlesen
            $user = $this -> getFrontendUser($row['uid']);
            // Root-Page Uid ermitteln
            $rootPage = $this -> getFrontendUsersRoot($user);
            // Weiterleitungs URL finden
            $uri = $this -> buildFrontendUri($user, $rootPage);

            $params = 'data[' . $table . '][' . $row['uid'] . '][confirmed]=0';
            $cells['primary']['confirmed'] = '<a class="btn btn-default t3js-record-confirm" target="_blank" href="'.$uri.'"'
                . ' title="Login in FE">'
                . $this -> iconFactory -> getIcon('feadmin-switch-to-user', Icon::SIZE_SMALL) -> render() . '</a>';
        }
        return $cells;
    }

    /**
     * @param string $table
     * @param array $currentIdList
     * @param array $headerColumns
     * @param object $parentObject
     * @return array
     */
    public function renderListHeader($table, $currentIdList, $headerColumns, &$parentObject) : array {
        return $headerColumns;
    }

    /**
     * @param string $table
     * @param array $currentIdList
     * @param array $cells
     * @param object $parentObject
     * @return array
     */
    public function renderListHeaderActions($table, $currentIdList, $cells, &$parentObject) : array {
        return $cells;
    }

    /**
     * @param string $table
     * @param array $row
     * @param array $cells
     * @param object $parentObject
     * @return array
     */
    public function makeClip($table, $row, $cells, &$parentObject) : array {
        return $cells;
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

    /**
     * Ermittelt die Seite, auf der der Nutzer eingeloggt werden kann.
     * @param FrontendUser $frontendUser
     * @return int
     */
    private function getFrontendUsersRoot(FrontendUser $frontendUser) : int {
        $rootline = BackendUtility::BEgetRootLine($frontendUser -> getPid());
        return $rootline[1]["uid"];
    }

    /**
     * Erzeugt eine FrontendUri.
     * @param FrontendUser $user
     * @param int $pageUid
     * @return string
     * @throws Exception
     */
    private function buildFrontendUri(FrontendUser $user, int $pageUid = 0) : string {
        /** @var SiteFinder $siteFinder */
        $siteFinder = GeneralUtility::makeInstance(SiteFinder::class);

        $site = $siteFinder ->getSiteByPageId($pageUid);
        return "//".$site -> getBase()."?feadmin_uid=".$user -> getUid();
    }

}