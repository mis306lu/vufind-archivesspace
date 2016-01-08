<?php
/**
 * User comments tab
 *
 * PHP version 5
 *
 * Copyright (C) Villanova University 2010.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category VuFind2
 * @package  RecordTabs
 * @author   Michelle Suranofsky <michelle.suranofsky@lehigh.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:record_tabs Wiki
 */
namespace VuFind\RecordTab;
use \VuFind\Connection\ArchivesSpaceConnection;
use Zend\Json\Json;

/**
 * Finding Aid tab
 *
 * @category VuFind2
 * @package  RecordTabs
 * @author   Michelle Suranofsky <michelle.suranofsky@lehigh.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:record_tabs Wiki
 */
class ArchivesSpace extends AbstractBase  implements \VuFindHttp\HttpServiceAwareInterface
{

    use \VuFindHttp\HttpServiceAwareTrait;

    /**
     * ArchivesSpaceConnection
     *
     * @var ArchivesSpaceConnection
     */
    protected $connector;
    /**
     * api session id
     * required for all archivesspace api interaction
     *
     * @var string
    */
    protected $sessionkey;


    /**
     * Constructor
     *
     * @param ArchivesSpaceConnection $connection
    */
    public function __construct(\VuFind\Connection\ArchivesSpaceConnection $connection)
    {
           $this->connector = $connection;
    }

    /**
     * authentication & save session id
     *
     * @return void
    */
    public function initSession()
    {
            $baseurl = $this->connector->config['settings']['baseapiurl'];
            $userid =  $this->connector->config['settings']['userid'];
            $password = $this->connector->config['settings']['password'];

            $url = $baseurl . "/users/" . $userid . "/login";

            $client = $this->connector->client;
            $client->setUri($url);
            $client->setMethod('POST');
            $client->setParameterPost(array(
               'password' => $password
            ));
            $response = $client->send();
            if ($response->isSuccess()) {
                  $phpNative = Json::decode($response->getBody(), Json::TYPE_OBJECT);
                  $sessionid = $phpNative->session;
                  $this->sessionkey = $sessionid;
            }
            //else {
            //  TODO: WHAT TO RETURN?  DEALING W/PROBLEMS? AND HOW TO LOG.
            //
            //}
    }

    /**
     * request from api the resource at the highest level
     *
     * @return stdClass
    */
    public function getSummaryInfo()
    {
         //THE URL IN THE RECORD (555 FIELD) IS THE PUBLIC
         //URL FOR THIS FINDING AID.
         //THIS CODE GRABS THE END OF IT & COMBINES IT WITH
         //THE BASE URL FOR THE API
         //TODO: BETTER WAY TO DO THIS?
         $baseresourceurl = $this->getRecordDriver()->getFindingAidUrl()[0];
         $arr = explode('edu', $baseresourceurl);
         $baseresourceurl = $arr[1];
         $baseurl = $this->connector->config['settings']['baseapiurl'];
         $url = $baseurl . $baseresourceurl;
         $resource = $this->callAPI($url);
         return $resource;
    }

    /**
     * Is this tab active?
     *
     * @return bool
     */
    public function isActive()
    {
        //is the ArchiveSpace Connector enabled in config file?:
        if (!$this->connector->config['settings']['enabled']) return false;
        //TODO: better way to do this?
        //check the 555 fields for an archivesspace url
        //get the base url from the archivesspace config file
        $baseurl = $this->connector->config['settings']['url'];
        //look for the baseurl in the 555 fields
        $matches = preg_grep("/" . $baseurl . "/", $this->getRecordDriver()->getFindingAids());
        return empty($matches) ? false : true;
    }

    /**
     * Get the on-screen description for this tab.
     *
     * @return string
     */
    public function getDescription()
    {
        return 'Finding Aid';
    }


    /**
     * combines base url with param $url
     * triggers call to api
     *
     * @param string
     *
     * @return stdClass
     *
     */
    public function makeRequestFor($url)
    {
        $baseurl = $this->connector->config['settings']['baseapiurl'];
        $combinedUrl = $baseurl . $url;
        $response = $this->callAPI($combinedUrl);
        return $response;
    }


    /**
     * makes call to api
     * using param $url
     *
     * @param string
     *
     * @return stdClass
     *
    */
    public function callAPI($url) {
        if ($this->sessionkey == null) $this->initSession();
        $client = $this->connector->client;
        $client->setUri($url);
        $client->setMethod('GET');
        $client->setHeaders(array('X-ArchivesSpace-Session'=>$this->sessionkey));
        $response = $client->send();
        $responseBody = Json::decode($response->getBody(), Json::TYPE_OBJECT);
        return $responseBody;
    }

}