<?php

namespace BoergenerWebdesign\BwFeAdmin\EventListener;

use TYPO3\CMS\Backend\RecordList\Event\ModifyRecordListRecordActionsEvent;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Imaging\IconSize;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class ModifyRecordListRecordActionsEventListener {
    public function __construct(
        protected readonly IconFactory $iconFactory
    ) {}

    /**
     * @param ModifyRecordListRecordActionsEventListener $event
     * @return void
     */
    public function __invoke(ModifyRecordListRecordActionsEvent $event) : void {
        if($event -> getTable() !== "fe_users") return;
        // if fe_users add button
        $event -> setAction(
            $this -> getButton($event -> getRecord()["uid"]),
            "bwfeadmin-open-frontend",
            "primary",
            "edit"
        );
    }

    protected function getButton(int $userUid) : string {
        return '<a class="btn btn-default" target="_blank" href="' . $this->buildFrontendUri($userUid) . '"'
        . ' title="Login in FE">'
        . $this -> iconFactory -> getIcon(
            'feadmin-switch-to-user',
                IconSize::SMALL
            )->render() . '</a>';
    }

    /**
     * Erzeugt eine FrontendUri.
     * @return string
     */
    protected function buildFrontendUri(int $userUid) : string {
        // find root page
        /** @var SiteFinder $siteFinder */
        $siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
        $currentPageUid = $_GET["id"];
        $site = $siteFinder -> getSiteByPageId($currentPageUid);

        return $site -> getBase()."?feadmin_uid=".$userUid;
    }
}