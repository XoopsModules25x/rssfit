<?php

namespace XoopsModules\Rssfit;

/**
 * Class Publisher
 * @package XoopsModules\Rssfit\Plugins
 */
interface PluginInterface
{
    public function loadModule(): ?\XoopsModule;

    public function grabEntries(\XoopsMySQLDatabase $xoopsDB): ?array;
}
