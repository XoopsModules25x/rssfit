<?php

namespace XoopsModules\Rssfit;

/**
 * Class Publisher
 * @package XoopsModules\Rssfit\Plugins
 */
interface PluginInterface
{

    /**
     * @return \XoopsModule
     */
    public function loadModule(): ?\XoopsModule;

    /**
     * @param \XoopsMySQLDatabase $xoopsDB
     * @return array
     */
    public function grabEntries(\XoopsMySQLDatabase $xoopsDB): ?array;
}
