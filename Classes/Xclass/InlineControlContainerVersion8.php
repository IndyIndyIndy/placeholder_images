<?php
namespace ChristianEssl\PlaceholderImages\Xclass;

/***
 *
 * This file is part of the "PlaceholderImages" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 Christian Eßl <indy.essl@gmail.com>, https://christianessl.at
 *
 ***/

use ChristianEssl\PlaceholderImages\Utility\ConfigurationUtility;
use ChristianEssl\PlaceholderImages\Utility\InlineControlButtonUtility;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\OnlineMediaHelperRegistry;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;

/**
 * Xclass for TYPO3 8.7
 */
class InlineControlContainerVersion8 extends \TYPO3\CMS\Backend\Form\Container\InlineControlContainer
{

    /**
     * Generate a link that opens an element browser in a new window.
     * For group/db there is no way to use a "selector" like a <select>|</select>-box.
     *
     * @param array $inlineConfiguration TCA inline configuration of the parent(!) field
     * @return string A HTML link that opens an element browser in a new window
     */
    protected function renderPossibleRecordsSelectorTypeGroupDB(array $inlineConfiguration)
    {
        $backendUser = $this->getBackendUserAuthentication();
        $languageService = $this->getLanguageService();
        $groupFieldConfiguration = $inlineConfiguration['selectorOrUniqueConfiguration']['config'];
        $foreign_table = $inlineConfiguration['foreign_table'];
        $allowed = $groupFieldConfiguration['allowed'];
        $currentStructureDomObjectIdPrefix = $this->inlineStackProcessor->getCurrentStructureDomObjectIdPrefix($this->data['inlineFirstPid']);
        $objectPrefix = $currentStructureDomObjectIdPrefix . '-' . $foreign_table;
        $nameObject = $currentStructureDomObjectIdPrefix;
        $mode = 'db';
        $showUpload = false;
        $elementBrowserEnabled = true;
        if (!empty($inlineConfiguration['appearance']['createNewRelationLinkTitle'])) {
            $createNewRelationText = htmlspecialchars($languageService->sL($inlineConfiguration['appearance']['createNewRelationLinkTitle']));
        } else {
            $createNewRelationText = htmlspecialchars($languageService->sL('LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:cm.createNewRelation'));
        }
        if (is_array($groupFieldConfiguration['appearance'])) {
            if (isset($groupFieldConfiguration['appearance']['elementBrowserType'])) {
                $mode = $groupFieldConfiguration['appearance']['elementBrowserType'];
            }
            if ($mode === 'file') {
                $showUpload = true;
            }
            if (isset($inlineConfiguration['appearance']['fileUploadAllowed'])) {
                $showUpload = (bool)$inlineConfiguration['appearance']['fileUploadAllowed'];
            }
            if (isset($groupFieldConfiguration['appearance']['elementBrowserAllowed'])) {
                $allowed = $groupFieldConfiguration['appearance']['elementBrowserAllowed'];
            }
            if (isset($inlineConfiguration['appearance']['elementBrowserEnabled'])) {
                $elementBrowserEnabled = (bool)$inlineConfiguration['appearance']['elementBrowserEnabled'];
            }
        }
        $browserParams = '|||' . $allowed . '|' . $objectPrefix . '|inline.checkUniqueElement||inline.importElement';
        $onClick = 'setFormValueOpenBrowser(' . GeneralUtility::quoteJSvalue($mode) . ', ' . GeneralUtility::quoteJSvalue($browserParams) . '); return false;';
        $buttonStyle = '';
        if (isset($inlineConfiguration['inline']['inlineNewRelationButtonStyle'])) {
            $buttonStyle = ' style="' . $inlineConfiguration['inline']['inlineNewRelationButtonStyle'] . '"';
        }
        $item = '';
        if ($elementBrowserEnabled) {
            $item .= '
			<a href="#" class="btn btn-default inlineNewRelationButton ' . $this->inlineData['config'][$nameObject]['md5'] . '"
				' . $buttonStyle . ' onclick="' . htmlspecialchars($onClick) . '" title="' . $createNewRelationText . '">
				' . $this->iconFactory->getIcon('actions-insert-record', Icon::SIZE_SMALL)->render() . '
				' . $createNewRelationText . '
			</a>';
        }

        $isDirectFileUploadEnabled = (bool)$backendUser->uc['edit_docModuleUpload'];
        $allowedArray = GeneralUtility::trimExplode(',', $allowed, true);
        $onlineMediaAllowed = OnlineMediaHelperRegistry::getInstance()->getSupportedFileExtensions();
        if (!empty($allowedArray)) {
            $onlineMediaAllowed = array_intersect($allowedArray, $onlineMediaAllowed);
        }
        if ($showUpload && $isDirectFileUploadEnabled) {
            $folder = $backendUser->getDefaultUploadFolder(
                $this->data['parentPageRow']['uid'],
                $this->data['tableName'],
                $this->data['fieldName']
            );
            if (
                $folder instanceof Folder
                && $folder->getStorage()->checkUserActionPermission('add', 'File')
            ) {
                ### CUSTOM PLACEHOLDER EXT CODE BEGIN ###

                if (ConfigurationUtility::isCurrentTYPO3ContextAllowed()) {
                    InlineControlButtonUtility::loadJavaScript();
                    $item .= InlineControlButtonUtility::getPlaceholderButton($this->inlineData, $nameObject, $objectPrefix, $folder);
                }

                ### CUSTOM PLACEHOLDER EXT CODE END ###

                $maxFileSize = GeneralUtility::getMaxUploadFileSize() * 1024;
                $item .= ' <a href="#" class="btn btn-default t3js-drag-uploader inlineNewFileUploadButton ' . $this->inlineData['config'][$nameObject]['md5'] . '"
					' . $buttonStyle . '
					data-dropzone-target="#' . htmlspecialchars(StringUtility::escapeCssSelector($currentStructureDomObjectIdPrefix)) . '"
					data-insert-dropzone-before="1"
					data-file-irre-object="' . htmlspecialchars($objectPrefix) . '"
					data-file-allowed="' . htmlspecialchars($allowed) . '"
					data-target-folder="' . htmlspecialchars($folder->getCombinedIdentifier()) . '"
					data-max-file-size="' . htmlspecialchars($maxFileSize) . '"
					>';
                $item .= $this->iconFactory->getIcon('actions-upload', Icon::SIZE_SMALL)->render() . ' ';
                $item .= htmlspecialchars($languageService->sL('LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:file_upload.select-and-submit'));
                $item .= '</a>';
                $this->requireJsModules[] = ['TYPO3/CMS/Backend/DragUploader' => 'function(dragUploader){dragUploader.initialize()}'];
                if (!empty($onlineMediaAllowed)) {
                    $buttonStyle = '';
                    if (isset($inlineConfiguration['inline']['inlineOnlineMediaAddButtonStyle'])) {
                        $buttonStyle = ' style="' . $inlineConfiguration['inline']['inlineOnlineMediaAddButtonStyle'] . '"';
                    }
                    $this->requireJsModules[] = 'TYPO3/CMS/Backend/OnlineMedia';
                    $buttonText = htmlspecialchars($languageService->sL('LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:online_media.new_media.button'));
                    $placeholder = htmlspecialchars($languageService->sL('LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:online_media.new_media.placeholder'));
                    $buttonSubmit = htmlspecialchars($languageService->sL('LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:online_media.new_media.submit'));
                    $allowedMediaUrl = htmlspecialchars($languageService->sL('LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:cm.allowEmbedSources'));
                    $item .= '
						<span class="btn btn-default t3js-online-media-add-btn ' . $this->inlineData['config'][$nameObject]['md5'] . '"
							' . $buttonStyle . '
							data-file-irre-object="' . htmlspecialchars($objectPrefix) . '"
							data-online-media-allowed="' . htmlspecialchars(implode(',', $onlineMediaAllowed)) . '"
							data-online-media-allowed-help-text="' . $allowedMediaUrl . '"
							data-target-folder="' . htmlspecialchars($folder->getCombinedIdentifier()) . '"
							title="' . $buttonText . '"
							data-btn-submit="' . $buttonSubmit . '"
							data-placeholder="' . $placeholder . '"
							>
							' . $this->iconFactory->getIcon('actions-online-media-add', Icon::SIZE_SMALL)->render() . '
							' . $buttonText . '</span>';
                }
            }
        }
        $item = '<div class="form-control-wrap">' . $item . '</div>';
        $allowedList = '';
        $allowedLabel = htmlspecialchars($languageService->sL('LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:cm.allowedFileExtensions'));
        foreach ($allowedArray as $allowedItem) {
            $allowedList .= '<span class="label label-success">' . strtoupper($allowedItem) . '</span> ';
        }
        if (!empty($allowedList)) {
            $item .= '<div class="help-block">' . $allowedLabel . '<br>' . $allowedList . '</div>';
        }
        $item = '<div class="form-group t3js-formengine-validation-marker">' . $item . '</div>';
        return $item;
    }

}