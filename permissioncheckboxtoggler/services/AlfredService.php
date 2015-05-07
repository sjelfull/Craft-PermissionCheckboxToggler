<?php
namespace Craft;

use \Guzzle\Batch\Batch;
use \Guzzle\Batch\BatchRequestTransfer;
use \Guzzle\Http\Client as Guzzle;

class AlfredService extends BaseApplicationComponent
{
    protected $settings = [];

    function __construct()
    {
        $plugin = craft()->plugins->getPlugin('alfred');
        if ( ! $plugin)
        {
            throw new Exception('Couldn’t find the Alfred plugin!');
        }
        $this->settings = $plugin->getSettings();
    }

    public function settingSections() {
        $label = Craft::t('System');

        $settings[$label]['general'] = array('icon' => 'general', 'label' => Craft::t('General'));
        $settings[$label]['routes'] = array('icon' => 'routes', 'label' => Craft::t('Routes'));

        if (craft()->getEdition() == Craft::Pro)
        {
            $settings[$label]['users'] = array('icon' => 'users', 'label' => Craft::t('Users'));
        }

        $settings[$label]['email'] = array('icon' => 'mail', 'label' => Craft::t('Email'));
        $settings[$label]['plugins'] = array('icon' => 'plugin', 'label' => Craft::t('Plugins'));

        $label = Craft::t('Content');

        $settings[$label]['fields'] = array('icon' => 'field', 'label' => Craft::t('Fields'));
        $settings[$label]['sections'] = array('icon' => 'section', 'label' => Craft::t('Sections'));
        $settings[$label]['assets'] = array('icon' => 'assets', 'label' => Craft::t('Assets'));
        $settings[$label]['globals'] = array('icon' => 'globe', 'label' => Craft::t('Globals'));
        $settings[$label]['categories'] = array('icon' => 'categories', 'label' => Craft::t('Categories'));
        $settings[$label]['tags'] = array('icon' => 'tags', 'label' => Craft::t('Tags'));

        if (craft()->getEdition() == Craft::Pro)
        {
            $settings[$label]['locales'] = array('icon' => 'language', 'label' => Craft::t('Locales'));
        }

        return $settings;
    }

    public function warmCache() {
        // Only include enabled sections
        $enabledSections = array_filter($this->settings->enabledSections, function($item)
        {
            if (!empty($item['enabled'])) return true;
        });

        // Put section handles into an array
        $sectionHandles = array_keys($enabledSections);

        // Get elements
        $criteria = craft()->elements->getCriteria(ElementType::Entry);
        $criteria->setLanguage('en');
        $criteria->section = $sectionHandles;

        // Get entries count
        $count = $criteria->count();

        // Fetch entries
        $entries = $criteria->find();

        $urls = [];

        // Get url's
        foreach($entries as $entry) {
            $urls[] = $entry->getUrl();
        }

        
        try 
        {
            // Create client
            $client = new Guzzle();

            // Create a new pool and send off requests, 20 at a time
            $transferStrategy = new BatchRequestTransfer($this->settings->parallelRequests);
            $divisorStrategy = $transferStrategy;
            $batch = new Batch($transferStrategy, $divisorStrategy);

            // Create requests for every url and add them to the batch
            foreach($urls as $url) {
                $batch->add( $client->get($url) );
            }

            // Flush the queue and retrieve the flushed items
            $arrayOfTransferredRequests = $batch->flush();
        }
        catch (\Guzzle\Http\Exception\CurlException $e) 
        {
            // Throw a craft exception which displays the error cleanly
            throw new HttpException(400, '(Alfred) Internet connection not available');
        }
    }
    
    public function isEnabledForSection($sectionHandle)
    {
        return !empty($this->settings->enabledByDefault[$sectionHandle]);
    }
}

//