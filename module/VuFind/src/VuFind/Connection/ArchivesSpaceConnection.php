<?php
/**
 * Open Library Utilities
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
 * @package  Connections
 * @author   Michelle Suranofsky <michelle.suranofsky@lehigh.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace VuFind\Connection;
use Zend\Json\Json;

/**
 * Open Library Utilities
 *
 * Class for accessing helpful Open Library APIs.
 *
 * @category VuFind2
 * @package  Connections
 * @author   Michelle Suranofsky <michelle.suranofsky@lehigh.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class ArchivesSpaceConnection
{
    /**
     * HTTP client
     *
     * @var \Zend\Http\Client
     */
    public $client;

    /**
     * config options
     *
     * @var \Zend\Config
     */
    public $config;

    /**
     * Constructor
     *
     * @param \Zend\Http\Client $client HTTP client
     * @param  \Zend\Config $configObject
     */
    public function __construct(\Zend\Http\Client $client,$configObject)
    {
        $this->client = $client;
        $this->config = $configObject;

    }

}