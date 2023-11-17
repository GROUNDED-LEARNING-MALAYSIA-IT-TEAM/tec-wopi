<?php

declare(strict_types=1);

namespace EaglenavigatorSystem\Wopi\Service;

use EaglenavigatorSystem\Wopi\Exception\WopiDiscoveryException;
use Cake\Cache\Cache;
use Cake\Utility\Xml;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\InternalErrorException; // Import the exception class
use Cake\Http\Exception\NotFoundException;
use Cake\Log\Log;
use SimpleXMLElement;

class WopiDiscoveryService
{
    private $xmlData; // Store the loaded XML data here

    public $wopiEndpoint;

    public function __construct()
    {
        // Load and parse the XML configuration file here (e.g., 'discovery.xml')
        // You can use CakePHP's file handling and XML parsing functions for this
        $xmlString = file_get_contents(CONFIG . 'discovery.xml');
        $this->xmlData = Xml::build($xmlString);
    }

    /**
     * @param string $rawXmlString
     * @return SimpleXMLElement
     * @throws InternalErrorException
     */
    public function discover(string $rawXmlString): SimpleXMLElement
    {
        // todo implement cache

        // Attempt to load the XML string into a SimpleXMLElement
        $simpleXmlElement = new SimpleXMLElement($rawXmlString);

        // Check if the XML parsing was successful
        if ($simpleXmlElement === false) {
            throw new InternalErrorException('Unable to parse the "discovery.xml" file.');
        }

        return $simpleXmlElement;
    }

    public function discoverAction(string $extension, string $name = 'edit'): ?array
    {
        // Implement logic to query XML data for actions based on extension and name
        // Use CakePHP's XML querying functions

        // Example code to query XML data:
        $action = $this->xmlData->xpath("//net-zone/app/action[@ext='{$extension}' and @name='{$name}']");

        if (!$action) {
            return null;
        }

        // Process and return the action data as an array
        // You can adapt the code from your Laravel class to structure the data
    }

    /**
     * @param string $extension
     * @return array
     */
    public function discoverExtension(string $extension): array
    {
        $appElements = $this->queryXPath('//net-zone/app');

        $extensions = [];

        foreach ($appElements as $app) {
            $actions = $app->xpath("action[@ext='{$extension}']");

            if (empty($actions)) {
                continue;
            }

            foreach ($actions as $action) {
                $actionAttributes = $action->attributes();

                $extensionData = [
                    'name' => (string) $app['name'],
                    'favIconUrl' => (string) $app['favIconUrl']
                ];

                if ($actionAttributes) {
                    $extensionData = array_merge((array) reset($actionAttributes), $extensionData);
                }

                $extensions[] = $extensionData;
            }
        }

        return $extensions;
    }

    /**
     * @return SimpleXMLElement[]|null
     * @throws InternalErrorException
     */
    private function queryXPath(string $expression)
    {
        // Load and parse the XML configuration file (e.g., 'discovery.xml')
        $xmlString = file_get_contents(CONFIG . 'discovery.xml');

        if (!$xmlString) {
            throw new InternalErrorException('Failed to read the XML configuration file.');
        }

        // Parse the XML string into a SimpleXMLElement
        $xmlData = new \SimpleXMLElement($xmlString);

        // Execute the XPath query
        $appElements = $xmlData->xpath($expression);

        if (empty($appElements)) {
            throw new InternalErrorException('Could not find app element. Make sure to have the proper configuration file.');
        }

        return $appElements;
    }

    /**
     * @param string $mimeType
     * @return array
     */
    public function discoverMimeType(string $mimeType): array
    {
        $appElements = $this->queryXPath("//net-zone/app[@name='{$mimeType}']");

        $mimeTypes = [];

        foreach ($appElements as $app) {
            $actions = $app->xpath('action');

            if (empty($actions)) {
                continue;
            }

            foreach ($actions as $action) {
                $actionAttributes = $action->attributes();

                $mimeTypeData = [
                    'name' => (string) $app['name']
                ];

                if ($actionAttributes) {
                    $mimeTypeData = array_merge((array) reset($actionAttributes), $mimeTypeData);
                }

                $mimeTypes[] = $mimeTypeData;
            }
        }

        return $mimeTypes;
    }

    /**
     * @return string
     */
    public function getCapabilitiesUrl(): string
    {
        $capabilities = $this->queryXPath("//net-zone/app[@name='Capabilities']");

        if (empty($capabilities)) {
            return '';
        }

        $capabilities = reset($capabilities);

        return (string) $capabilities->action['urlsrc'];
    }

    /**
     * @return string
     */
    public function getPublicKey(): string
    {
        $value = $this->queryXPath('//proof-key/@value')[0] ?? null;
        return (string) $value;
    }

    /**
     * @return string
     */
    public function getOldPublicKey(): string
    {
        $value = $this->queryXPath('//proof-key/@oldvalue')[0] ?? null;
        return (string) $value;
    }

    /**
     * @return string
     */
    public function getProofModulus(): string
    {
        $value = $this->queryXPath('//proof-key/@modulus')[0] ?? null;
        return (string) $value;
    }

    /**
     * @return string
     */
    public function getProofExponent(): string
    {
        $value = $this->queryXPath('//proof-key/@exponent')[0] ?? null;
        return (string) $value;
    }

    /**
     * @return string
     */
    public function getOldProofModulus(): string
    {
        $value = $this->queryXPath('//proof-key/@oldmodulus')[0] ?? null;
        return (string) $value;
    }

    /**
     * @return string
     */
    public function getOldProofExponent(): string
    {
        $value = $this->queryXPath('//proof-key/@oldexponent')[0] ?? null;
        return (string) $value;
    }

    private function getWopiEndpoints(): SimpleXMLElement
    {
        $wopiXML = Cache::read('wopiDiscoveryData', 'long_term');

        if (!$wopiXML) {
            // Fetch and parse discovery data
            $wopiXML = $this->fetchDiscoveryData();
            if ($wopiXML) {
                Cache::write('wopiDiscoveryData', $wopiXML, 'long_term');
            } else {
                // Handle the case where discovery data could not be fetched
                Log::error(__FUNCTION__ . " : Failed discovering office end points");
                throw new WopiDiscoveryException("Failed discovering office end points");
            }
        } else {
            Log::debug(__FUNCTION__ . " : Got discovery end points from cache");
        }

        $this->wopiEndpoint = new SimpleXMLElement($wopiXML);

        return $this->wopiEndpoint;
    }
}
