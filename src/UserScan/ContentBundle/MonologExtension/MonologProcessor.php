<?php

namespace UserScan\ContentBundle\MonologExtension;

class MonologProcessor
{
    protected $serverData;

    /**
     * @param mixed $serverData array or object w/ ArrayAccess that provides access to the $_SERVER data
     */
    public function __construct($serverData = null)
    {
        if (null === $serverData) {
            $this->serverData =& $_SERVER;
        } elseif (is_array($serverData) || $serverData instanceof \ArrayAccess) {
            $this->serverData = $serverData;
        } else {
            throw new \UnexpectedValueException('$serverData must be an array or object implementing ArrayAccess.');
        }
    }

    /**
     * @param array $record
     * @return array
     */
    public function __invoke(array $record)
    {
        // IntrospectionProcessor
        $trace = debug_backtrace();

        // skip first since it's always the current method
        array_shift($trace);
        // the call_user_func call is also skipped
        array_shift($trace);

        $i = 0;
        while (isset($trace[$i]['class']) && false !== strpos($trace[$i]['class'], 'Monolog\\')) {
            $i++;
        }

        // we should have the call source now
        $record['extra'] = array_merge(
            $record['extra'],
            array(
                'file'      => isset($trace[$i-1]['file']) ? $trace[$i-1]['file'] : null,
                'line'      => isset($trace[$i-1]['line']) ? $trace[$i-1]['line'] : null,
                'class'     => isset($trace[$i]['class']) ? $trace[$i]['class'] : null,
                'function'  => isset($trace[$i]['function']) ? $trace[$i]['function'] : null,
            )
        );

        // skip processing if for some reason request data
        // is not present (CLI or wonky SAPIs)
        if (!isset($this->serverData['REQUEST_URI'])) {
            return $record;
        }

        $record['extra'] = array_merge(
            $record['extra'],
            array(
                'url'         => $this->serverData['REQUEST_URI'],
                'referer'     => (isset($this->serverData['HTTP_REFERER'])) ? $this->serverData['HTTP_REFERER'] : null,
                'user_agent'  => (isset($this->serverData['HTTP_USER_AGENT'])) ? $this->serverData['HTTP_USER_AGENT'] : null,
                'ip'          => $this->serverData['REMOTE_ADDR'],
                'http_method' => $this->serverData['REQUEST_METHOD'],
            )
        );

        return $record;
    }
}