<?php

/**
 * SiteSearch classes
 *
 * @package sitesearch
 */
class SiteSearch
{
    const URL = 'http://www.google.com/search';

    private $id;
    private $query = '';
    private $language;
    private $response;
    private $resultCount = 0;

    /**
     * Init the SiteSearch class
     *
     * @param   string  $id
     */
    public function __construct($id = null)
    {
        $this->id = $id;
    }

    /**
     * Create a SiteSearch object
     *
     * @param   string  $id
     * @return  self
     */
    public static function create($id = null)
    {
        return new self($id);
    }

    /**
     * Set the Google Site Search identifier
     *
     * @param   string  $value
     * @return  self
     */
    public function setId($value)
    {
        $this->id = $value;
        return $this;
    }

    /**
     * Get the Google Site Search identifier
     *
     * @return  string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the search query
     *
     * @param   string  $value
     * @return  self
     */
    public function setQuery($value)
    {
        $this->query = $value;
        return $this;
    }

    /**
     * Get the search query
     *
     * @return  string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Set the search query language
     *
     * @param   string  $value
     * @return  self
     */
    public function setLanguage($value)
    {
        $this->language = $value;
        return $this;
    }

    /**
     * Get the search query language
     *
     * @return  string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Get the raw search query response
     *
     * @return  string
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Get the raw search query response
     *
     * @return  string
     */
    public function getResultCount()
    {
        return $this->resultCount;
    }

    /**
     * Make a search request
     *
     * @param   string  $value
     * @param   int  $start
     * @param   int  $limit
     * @return  array
     */
    public function search($value, $start = 0, $limit = 25)
    {
        $url = self::formatUrl($value, $this->id, $this->language, $start, $limit);
        $this->response = self::request($url);

        $this->resultCount = self::parseResultCount($this->response);

        return self::parseResult($this->response, $this->language);
    }

    /**
     * Format URL for request
     *
     * @param   string  $query
     * @param   string  $id
     * @param   string  $language
     * @param   int  $start
     * @param   int  $limit
     * @return  string
     */
    private static function formatUrl($query, $id, $language = null, $start = 0, $limit = 25)
    {
        $url = self::URL . '?';

        $url .= 'cx=' . $id;
        $url .= '&client=google-csbe';
        $url .= '&start=' . $start;
        $url .= '&num=' . $limit;
        $url .= '&output=xml_no_dtd';

        if (isset($language)) {
            $url .= '&hl=' . $language;
        }

        $url .= '&q=' . str_replace('+', '%2B', urlencode($query));

        return $url;
    }

    /**
     * Parse result into an array
     *
     * @param   string  $result
     * @param   string  $language
     * @return  array
     */
    public static function parseResult($result, $language = null) {
        $result = new SimpleXMLElement($result);
        $result = json_decode(json_encode($result), true);

        $retval = array();
        if (isset($result['RES']['R']) && is_array($result['RES']['R'])) {
            foreach($result['RES']['R'] as $value) {
                if ($language === null || strtolower(substr($language, 0, 2)) === $value['LANG']) {
                    $item = array(
                        'link' => $value['U'],
                        'title' => $value['T'],
                        'description' => $value['S'],
                        'lang' => $value['LANG'],
                        'image' => null,
                        'thumbnail' => null
                    );

                    // Images and thumbnails
                    if (isset($value['PageMap']['DataObject'])) {
                        foreach($value['PageMap']['DataObject'] as $object) {
                            if (isset($object['@attributes']['type'])) {
                                switch($object['@attributes']['type']) {
                                    case 'cse_thumbnail':
                                        $item['thumbnail'] = self::parseImage($object);
                                        break;
                                    case 'cse_image':
                                        $item['image'] = self::parseImage($object);
                                        break;
                                }
                            }
                        }
                    }

                    $retval[] = $item;
                }
            }
        }

        return $retval;
    }

    /**
     * Parse an image object into an array
     *
     * @param   array  $oject
     * @return  array
     */
    private static function parseImage($oject)
    {
        $retval = array();

        $parseAttribute = function($attribute) use (&$retval) {
            if (isset($attribute['@attributes'])) {
                $attribute = $attribute['@attributes'];
                if (isset($attribute['name']) && isset($attribute['value'])) {
                    switch($attribute['name']) {
                        case 'width':
                            $retval['width'] = $attribute['value'];
                            break;
                        case 'height':
                            $retval['height'] = $attribute['value'];
                            break;
                        case 'src':
                            $retval['src'] = $attribute['value'];
                            break;
                    }
                }
            }
        };

        if (isset($oject['Attribute']) && is_array($oject['Attribute'])) {
            foreach($oject['Attribute'] as $attribute) {
                $parseAttribute($attribute);
            }
        } else if (isset($oject['0'])) {
            foreach($oject as $attribute) {
                $parseAttribute($attribute);
            }
        }

        return $retval;
    }

    /**
     * Parse the result count
     *
     * @param   string  $response
     * @return  int
     */
    private static function parseResultCount($response)
    {
        preg_match('/<M>(\d*)<\/M>/', $response, $match);

        if (isset($match[1])) {
            return (int)$match[1];
        }

        return 0;
    }

    /**
     * Make the request
     *
     * @param   string  $url
     * @return  string
     */
    private static function request($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
}