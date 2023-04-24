<?php

namespace spark\controllers\Dashboard;

use spark\controllers\Controller;
use spark\drivers\Nav\ArrayPagination;
use spark\drivers\Nav\Pagination;
use PclZip;

/**
* DashboardPluginsController
*
* @package spark
*/
class DashboardPluginsController extends DashboardController
{
    public function __construct()
    {
        parent::__construct();

        /**
         * @event Fires before DashboardPluginsController is initialized
         */
        do_action('dashboard.plugins_controller_init_before');

        // this is it
        if (!current_user_can('manage_plugins')) {
            sp_not_permitted();
        }

        breadcrumb_add('dashboard.plugins', __('Plugins'), url_for('dashboard.plugins'));

        view_set('plugins__active', 'active');

        /**
         * @event Fires after DashboardPluginsController is initialized
         */
        do_action('dashboard.plugins_controller_init_after');
    }

    /**
     * List entries
     *
     * @return
     */
    public function index()
    {
        $app = app();

        // Current page number
        $currentPage = (int) $app->request->get('page', 1);

        // Items per page
        $itemsPerPage = (int) $app->config('dashboard.items_per_page');

        // Total item count
        $totalCount = $app->plugins->getPluginsCount();

        $queryStr = request_build_query(['page']);

        $arrayPagination = new ArrayPagination;

        // Pagination instance
        $pagination = new Pagination($totalCount, $currentPage, $itemsPerPage);
        $pagination->setUrl("?page=@id@{$queryStr}");

        // Generated HTML
        $paginationHtml = $pagination->renderHtml();

        // Offset value based on current page
        $offset = $pagination->offset();

        $plugins = $app->plugins->listPlugins();

        // List entries
        $entries = $arrayPagination->generate($plugins, $currentPage, $itemsPerPage);

        // Template data
        $data = [
            'list_entries'    => $entries,
            'total_items'     => $totalCount,
            'offset'          => $offset === 0 ? 1 : $offset,
            'current_page'    => $currentPage,
            'items_per_page'  => $itemsPerPage,
            'current_items'   => $itemsPerPage * $currentPage,
            'pagination_html' => $paginationHtml,
            'query_str'       => $queryStr
        ];
        return view('admin::plugins/index.php', $data);
    }

    /**
     * Add new plugin page
     *
     * @return
     */
    public function create()
    {
        // Set breadcrumb trails
        breadcrumb_add('dashboard.plugins.create', __('Add New Plugin'));

        $data = [];
        return view('admin::plugins/create.php', $data);
    }

    /**
     * Process uploaded plugin
     *
     * @return
     */
    public function createPOST()
    {
        if (is_demo()) {
            flash('plugins-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return redirect_to('dashboard.plugins');
        }

        if (empty($_FILES['plugin_archive'])) {
            flash('plugins-danger', __('Please select a file!'));
            return redirect_to_current_route();
        }

        $archive = $_FILES['plugin_archive'];

        // Ensure it's a ZIP file
        if ($archive['type'] !== 'application/zip') {
            flash('plugins-danger', __('Plugin must be a ZIP archive!'));
            return redirect_to_current_route();
        }

        // Where to put our beloved plugin
        $pluginsDir = pluginspath();

        // Assume the archive name as plugin folder/key name at first
        $pluginName = pathinfo($archive['name'], PATHINFO_FILENAME);

        // Fuck, this library is so old
        $zip = new \PclZip($archive['tmp_name']);

        $tempDir = srcpath("var/tmp/" . md5($pluginName));
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $files = $zip->extract(PCLZIP_OPT_PATH, $tempDir, PCLZIP_OPT_REPLACE_NEWER);

        if (!$files) {
            rrmdir($tempDir);
            flash('plugins-danger', __('Unknown Error Occured. Possibly Corrupted ZIP file.'));
            return redirect_to_current_route();
        }

        $tempFiles = glob(trailingslashit($tempDir) . '*');

        $foundPlugins = 0;

        foreach ($tempFiles as $file) {
            // No files are allowed in the root of plugin archive
            if (is_file($file)) {
                continue;
            }

            $plugin = basename($file);
            $pluginPath = trailingslashit($file) . $plugin . '.php';
            // so we're talking to a directory? fine
            // let's check if the directory contains a standard plugin file or not
            if (!file_exists($pluginPath)) {
                // you don't matter to us
                continue;
            }

            // so, we're talking to a standard plugin file?
            // we'll find out soon enough
            $data = get_file_data($pluginPath, ['name' => 'Plugin Name']);

            if (empty(trim($data['name']))) {
                // come on! seriously? after all this you don't even have a name?
                // and we thought we're something special </3
                continue;
            }

            // yahoooo! we found our soul mate
            rmove($file, pluginspath($plugin));
            $foundPlugins++;
        }

        rrmdir($tempDir);

        if ($foundPlugins) {
            flash('plugins-success', sprintf(__('%d plugin(s) were added successfully'), $foundPlugins));
            return redirect_to('dashboard.plugins');
        }

        flash('plugins-danger', __("No valid plugins found in the archive."));
        return redirect_to_current_route();
    }

    /**
     * Enables a plugin
     *
     * @param  string $plugin
     * @return
     */
    public function enable($plugin)
    {
        if (is_demo()) {
            flash('plugins-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return redirect_to('dashboard.plugins');
        }

        $pluginManager = app()->plugins;

        if (!$pluginManager->pluginExists($plugin)) {
            flash('plugins-danger', __('No such plugin exists on the disk'));
        } elseif ($pluginManager->isEnabled($plugin)) {
            flash('plugins-warning', sprintf(__('Plugin %s is already enabled!'), sp_strip_tags($plugin)));
        } else {
            $pluginManager->togglePlugins([$plugin]);

            try {
                $pluginManager->loadPlugin($plugin);

                /**
                * @event Fires after the plugin is enabled
                *
                * @param string $plugin The plugin directory name aka key
                */
                do_action("{$plugin}OnEnable", $plugin);
            } catch (\Exception $e) {
                flash(
                    'plugins-danger',
                    sprintf(
                        __("The following errors occured when trying to enable the plugin %s:<br> <em>%s</em>"),
                        $plugin,
                        $e->getMessage()
                    )
                );

                // Disable the plugin again
                $pluginManager->togglePlugins([], [$plugin]);

                // Log the error
                logger()->critical($e);

                return redirect_to('dashboard.plugins');
            }

            flash('plugins-success', __('Plugin was enabled successfully'));
        }

        return redirect_to('dashboard.plugins');
    }

    /**
     * Disables a plugin
     *
     * @param  string $plugin
     * @return
     */
    public function disable($plugin)
    {
        if (is_demo()) {
            flash('plugins-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return redirect_to('dashboard.plugins');
        }

        $pluginManager = app()->plugins;

        if (!$pluginManager->pluginExists($plugin)) {
            flash('plugins-danger', __('No such plugin exists on the disk'));
        } elseif (!$pluginManager->isEnabled($plugin)) {
            flash('plugins-warning', sprintf(__('Plugin %s is already disabled!'), sp_strip_tags($plugin)));
        } else {
            $pluginManager->togglePlugins([], [$plugin]);
            flash('plugins-success', __('Plugin was disabled successfully'));

            /**
             * @event Fires after the plugin is disabled
             *
             * @param string $plugin The plugin directory name aka key
             */
            do_action("{$plugin}OnDisable", $plugin);
        }

        return redirect_to('dashboard.plugins');
    }

    /**
     * Delete entry page
     *
     * @param string $plugin
     * @return
     */
    public function delete($plugin)
    {
        // Set breadcrumb trails
        breadcrumb_add('dashboard.plugins.delete', __('Delete Plugin'));

        $pluginManager = app()->plugins;

        if (!$pluginManager->pluginExists($plugin)) {
            flash('plugins-danger', __('No such plugin exists on the disk'));
            return redirect_to('dashboard.plugins');
        }

        if ($pluginManager->isEnabled($plugin)) {
            flash('plugins-warning', sprintf(__('Plugin %s is currently enabled, please disable the plugin first'), sp_strip_tags($plugin)));
            return redirect_to('dashboard.plugins');
        }

        $meta = $pluginManager->getPluginMeta($plugin);

        $data = [
            'plugin' => $plugin,
            'meta' => $meta
        ];
        return view('admin::plugins/delete.php', $data);
    }

    /**
     * Performs plugin deletion
     *
     * @param string $plugin
     * @return
     */
    public function deletePOST($plugin)
    {
        if (is_demo()) {
            flash('plugins-info', $GLOBALS['_SPARK_I18N']['demo_mode']);

            if (is_ajax()) {
                return;
            }

            return redirect_to('dashboard.plugins');
        }

        $pluginManager = app()->plugins;

        if (!$pluginManager->pluginExists($plugin)) {
            flash('plugins-danger', __('No such plugin exists on the disk'));
        } elseif ($pluginManager->isEnabled($plugin)) {
            flash('plugins-warning', sprintf(__('Plugin %s is currently enabled, please disable the plugin first'), sp_strip_tags($plugin)));
        } else {
            $pluginManager->loadPlugin($plugin);

            /**
             * @event Fires after the plugin is deleted. Although not actually after deletion, but just right before it.
             *
             *
             * @param string $plugin The plugin directory name aka key
             */
            do_action("{$plugin}OnDelete", $plugin);

            rrmdir(pluginspath($plugin));
            flash('plugins-success', __('Plugin was deleted successfully'));
        }

        if (is_ajax()) {
            return;
        }

        return redirect_to('dashboard.plugins');
    }
}
