<?php
namespace ChristianEssl\PlaceholderImages\Controller;

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

use ChristianEssl\PlaceholderImages\Exception\InvalidConfigurationException;
use ChristianEssl\PlaceholderImages\Exception\MissingArgumentsException;
use ChristianEssl\PlaceholderImages\Resource\Placeholder\AbstractProcessor;
use ChristianEssl\PlaceholderImages\Resource\Placeholder\CustomSourceProcessor;
use ChristianEssl\PlaceholderImages\Resource\Placeholder\LocalImageProcessor;
use ChristianEssl\PlaceholderImages\Resource\Placeholder\PlaceholderComProcessor;
use ChristianEssl\PlaceholderImages\Utility\ConfigurationUtility;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Handles generating and uploading placeholder images
 */
class PlaceholderController
{
    /**
     * AJAX endpoint for creating an image and storing it as a sys_file record
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function createAction(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $targetFolderIdentifier = $request->getParsedBody()['targetFolder'];
        $imageSettings = $this->getImageSettings($request);
        $data = [];

        try {
            $file = $this->getFile($imageSettings, $targetFolderIdentifier);
            $data['file'] = $file->getUid();
        } catch (InvalidConfigurationException $e) {
            $data['error'] = $this->getTranslation('tx_placeholderimages.image.error.invalid_configuration');
        } catch (FileDoesNotExistException $e) {
            $data['error'] = $this->getTranslation('tx_placeholderimages.image.error.unknown_error');
        } catch (MissingArgumentsException $e) {
            $data['error'] = $this->getTranslation('tx_placeholderimages.image.error.empty_request');
        }

        return $this->getJsonResponse($response, $data);
    }

    /**
     * @param ResponseInterface $response
     * @param array $data
     *
     * @return ResponseInterface
     */
    protected function getJsonResponse(ResponseInterface $response, array $data) : ResponseInterface
    {
        $response->getBody()->write(json_encode($data));
        return $response;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return string[]
     */
    protected function getImageSettings(ServerRequestInterface $request) : array
    {
        $width = (int) $request->getParsedBody()['width'];
        $height = (int) $request->getParsedBody()['height'];

        if (!$width) {
            $width = $height;
        }
        if (!$height) {
            $height = $width;
        }

        return [
            'width' => $width,
            'height' => $height,
            'format' => $request->getParsedBody()['format'],
            'placeholder' => $request->getParsedBody()['placeholder'],
            'bgcolor' => $request->getParsedBody()['bgcolor'],
            'textcolor' => $request->getParsedBody()['textcolor'],
        ];
    }

    /**
     * @param array $imageSettings
     * @param string $targetFolderIdentifier
     *
     * @return File|null
     * @throws MissingArgumentsException
     * @throws InvalidConfigurationException
     * @throws FileDoesNotExistException
     */
    protected function getFile($imageSettings, $targetFolderIdentifier)
    {
        if (empty($imageSettings['width'])) {
            throw new MissingArgumentsException('No arguments were given to placeholder controller');
        }

        $processor = $this->getProcessor();
        $file = $processor->processFile($imageSettings, $targetFolderIdentifier);

        if (!$file instanceof File) {
            throw new FileDoesNotExistException('Could not process a file');
        }

        return $file;
    }

    /**
     * @return AbstractProcessor
     * @throws InvalidConfigurationException
     */
    protected function getProcessor() : AbstractProcessor
    {
        $configuration = ConfigurationUtility::getExtensionConfiguration();

        switch ($configuration['imageSource']) {
            case 'placeholder.com':
                $processorClassName = PlaceholderComProcessor::class;
                break;
            case 'imagemagick':
                $processorClassName = LocalImageProcessor::class;
                break;
            case 'custom':
                $processorClassName = CustomSourceProcessor::class;
                break;
            default:
                throw new InvalidConfigurationException('Invalid image source configuration!');
        }

        /** @var AbstractProcessor $processor */
        $processor = GeneralUtility::makeInstance($processorClassName);
        return $processor;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    protected function getTranslation($key) : string
    {
        return $GLOBALS['LANG']->sL('LLL:EXT:placeholder_images/Resources/Private/Language/locallang_db.xlf:'.$key);
    }

}