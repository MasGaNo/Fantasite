<?php

/**
 * Value filter
 */
abstract class FS_Filter
{
    /**
     * Filter the value
     * @param mixed $value Value to filter
     * @return mixed
     */
    abstract public function Filter($pValue);
}

?>
