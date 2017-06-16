<?php namespace Unicheck\Corporate\Check;

use Unicheck\Corporate\Exception\CheckException;

/**
 * Class CheckParam
 */
class CheckParam
{
    const TYPE_MY_LIBRARY = "my_library";
    const TYPE_WEB = "web";
    const TYPE_EXTERNAL_DB = "external_database";
    const TYPE_DOC_VS_DOC = "doc_vs_docs";
    const TYPE_WEB_AND_MY_LIBRARY = "web_and_my_library";

    /**
     * @var array $typeMap
     */
    protected static $typeMap =
        [
            self::TYPE_MY_LIBRARY,
            self::TYPE_WEB,
            self::TYPE_EXTERNAL_DB,
            self::TYPE_DOC_VS_DOC,
            self::TYPE_WEB_AND_MY_LIBRARY
        ];

    /**
     * @var int
     */
    protected $file_id;

    /**
     * @var int[]
     */
    protected $versus_files = [];

    /**
     * @var string $type
     */
    protected $type;

    /**
     * @var string $callback_url
     */
    protected $callback_url;

    /**
     * @var bool $exclude_citations
     * @default false
     */
    protected $exclude_citations = false;

    /**
     * @var bool $exclude_references (default: false)
     * @default false
     */
    protected $exclude_references = false;

    /**
     * CheckParam constructor.
     * @param $file_id
     * @throws CheckException
     */
    public function __construct($file_id)
    {
        if( is_numeric($file_id) === false )
        {
            throw new CheckException("File ID must be Integer.");
        }

        $this->file_id = $file_id;
        $this->type = self::TYPE_WEB; // set default type - WEB

    }

    /**
     * Method setType description.
     * @param $type
     * @param null $versusFiles
     *
     * @return $this
     * @throws CheckException
     */
    public function setType($type, $versusFiles = null)
    {
        if( array_search($type, self::$typeMap) === false )
        {
            throw new CheckException(
                sprintf(
                    "<b>Set invalid type: '{$type}'</b>. Allowed check type is '%s'",
                    implode("', '", self::$typeMap)
                )
            );
        }

        if( $type === self::TYPE_DOC_VS_DOC )
        {

            if( empty($versusFiles) )
            {
                throw new CheckException("Versus Files can not be empty for check type '{$type}'");
            }
            else
            {
                $this->versus_files = $versusFiles;
            }
        }

        $this->type = $type;
        return $this;
    }

    /**
     * Method setCallbackUrl description.
     * @param $url
     *
     * @return $this
     */
    public function setCallbackUrl($url)
    {
        $this->callback_url = $url;
        return $this;
    }

    /**
     * Method setExcludeCitations description.
     * @param $exclude_citations
     *
     * @return $this
     */
    public function setExcludeCitations($exclude_citations)
    {
        $this->exclude_citations = (bool) $exclude_citations;
        return $this;
    }

    /**
     * Method setExcludeReferences description.
     * @param $exclude_references
     *
     * @return $this
     */
    public function setExcludeReferences($exclude_references)
    {
        $this->exclude_references = (bool) $exclude_references;
        return $this;
    }

    /**
     * Method mergeParams description.
     *
     * @return array
     */
    public function mergeParams()
    {
        $params =
            [
                'file_id' => $this->file_id,
                'type' => $this->type,

                'exclude_citations' => $this->exclude_citations,
                'exclude_references' => $this->exclude_references
            ];

        if( !empty($this->versus_files) )
        {
            $params['versus_files'] = $this->versus_files;
        }

        if( !empty($this->callback_url) )
        {
            $params['callback_url'] = $this->callback_url;
        }

        return $params;
    }


}