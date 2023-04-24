<?php

namespace spark\drivers\Plugin;

/**
*
*/
class PluginManager
{
    /**
     * If the plugins are loaded or not
     *
     * @var boolean
     */
    protected $loaded = false;

    /**
     * Active plugins array from the database
     *
     * @var array
     */
    protected $activePlugins;

    /**
     * All plugin list
     *
     * @var array
     */
    protected $pluginList;


    /**
     * Load active plugins
     *
     * @return
     */
    public function load()
    {
        if ($this->loaded) {
            return false;
        }

        // Thats it
        foreach ($this->getEnabledPlugins() as $plugin) {
            require $this->buildPluginPath($plugin);
        }

        $this->loaded = true;
    }

    /**
     * Set/update active plugin's list in DB
     *
     * @param  array  $newPlugins
     * @param  array  $pluginsToRemove
     *
     * @return boolean
     */
    public function togglePlugins(array $newPlugins, array $pluginsToRemove = [])
    {
        $plugins = array_merge($this->getEnabledPlugins(), $newPlugins);

        foreach ($pluginsToRemove as $removal) {
            $plugins = array_remove_value($plugins, $removal);
        }

        $plugins = array_unique($plugins);
        return set_option('active_plugins', json_encode($plugins));
    }


    /**
     * Get an array of active plugin folder names from the database
     *
     * @return array
     */
    public function getEnabledPlugins()
    {
        if (is_array($this->activePlugins)) {
            return $this->activePlugins;
        }

        $activePlugins = json_decode(get_option('active_plugins'), true);

        if (!is_array($activePlugins)) {
            $activePlugins = [];
        }

        $this->activePlugins = array_unique($activePlugins);

        return $this->activePlugins;
    }

    /**
     * List all plugins
     *
     * @return array
     */
    public function listPlugins()
    {
        // sorry but we'll scan only once!
        if (is_array($this->pluginList)) {
            return $this->pluginList;
        }

        // Set plugin list as array
        $this->pluginList = [];

        // Glob the folders
        $folders = @glob(trailingslashit(basepath(PLUGIN_DIR)) . "*", GLOB_ONLYDIR);

        // Nothing? fine
        if (empty($folders)) {
            return $this->pluginList;
        }


        foreach ($folders as $pluginDir) {
            $plugin = basename($pluginDir);

            // no plugin file? fuck off
            if (!$this->pluginExists($plugin)) {
                continue;
            }

            // Build the data
            $data = [
                'key'    => $plugin,
                'path'   => $this->buildPluginPath($plugin),
                'active' => false,
                'meta'   => []
            ];

            // mark plugin as activated
            if ($this->isEnabled($plugin)) {
                $data['active'] = true;
            }

            $data['meta'] = $this->getPluginMeta($plugin);

            //
            $this->pluginList[$plugin] = $data;
        }

        return $this->pluginList;
    }

    public function listActivePlugins()
    {
    }

    /**
     * Get total plugins count
     *
     * @return integer
     */
    public function getPluginsCount()
    {
        $this->listPlugins();

        return count($this->pluginList);
    }

    /**
     * Check if a plugin is enabled or not
     *
     * @param  string  $plugin
     * @return boolean
     */
    public function isEnabled($plugin)
    {
        $plugins = $this->getEnabledPlugins();
        return in_array($plugin, $plugins);
    }

    /**
     * Build full path to the plugin file
     *
     * @param  string $plugin
     * @return string
     */
    public function buildPluginPath($plugin)
    {
        return trailingslashit(basepath(PLUGIN_DIR)) . trailingslashit($plugin) . $plugin . '.php';
    }

    /**
     * Load a specific plugin
     *
     * @param  string $plugin
     * @return
     */
    public function loadPlugin($plugin)
    {
        require_once $this->buildPluginPath($plugin);
    }

    /**
     * Parses and returns plugin meta-data
     *
     * @param  string $plugin
     * @return array
     */
    public function getPluginMeta($plugin)
    {
        $defaultMeta = [
            'name' => 'Plugin Name',
            'uri' => 'Plugin URI',
            'version' => 'Version',
            'description' => 'Description',
            'author' => 'Author',
            'author_uri' => 'Author URI',
        ];

        $pluginFile = $this->buildPluginPath($plugin);
        $metaData   = get_file_data($pluginFile, $defaultMeta);

        if (empty($metaData['name'])) {
            $metaData['name'] = $plugin;
        }

        return $metaData;
    }

    /**
     * Check if a plugin exists on the disk
     *
     * @param  string $plugin
     * @return boolean
     */
    public function pluginExists($plugin)
    {
        return @is_file($this->buildPluginPath($plugin));
    }
}
