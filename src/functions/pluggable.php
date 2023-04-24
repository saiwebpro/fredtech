<?php


/**
* Hooks a function or method to a specific filter action.
*
* @param string $tag The name of the filter to hook the $function_to_add to.
* @param callback $function_to_add The name of the function to be called when the filter is applied.
* @param int $priority optional. Used to specify the order in which the functions associated with a particular
*                                action are executed (default: 10). Lower numbers correspond with earlier execution,
*                                and functions with the same priority are executed in the order in which they were
*                                added to the action.
* @param int $accepted_args optional. The number of arguments the function accept (default 1).
* @return boolean true
*/
function add_filter($tag, $function_to_add, $priority = 10, $accepted_args = 10)
{
    return app()->event->addFilter($tag, $function_to_add, $priority, $accepted_args);
}

/**
* Removes a function from a specified filter hook.
*
* @param string $tag The filter hook to which the function to be removed is hooked.
* @param callback $function_to_remove The name of the function which should be removed.
* @param int $priority optional. The priority of the function (default: 10).
* @param int $accepted_args optional. The number of arguments the function accepts (default: 1).
* @return boolean Whether the function existed before it was removed.
*/
function remove_filter($tag, $function_to_remove, $priority = 10)
{
    return app()->event->removeFilter($tag, $function_to_remove, $priority);
}

/**
* Remove all of the hooks from a filter.
*
* @param string $tag The filter to remove hooks from.
* @param int $priority The priority number to remove.
* @return bool true when finished.
*/
function remove_all_filters($tag, $priority = false)
{
    return app()->event->removeAllFilters($tag, $priority);
}

/**
* Check if any filter has been registered for a hook.
*
* @param string $tag The name of the filter hook.
* @param callback $function_to_check optional.
* @return mixed If $function_to_check is omitted, returns boolean for whether the hook has anything
*         registered. When checking a specific function, the priority of that hook is returned,
*         or false if the function is not attached. When using the $function_to_check argument, this
*         function may return a non-boolean value that evaluates to false (e.g.) 0, so use
*         the === operator for testing the return value.
*/
function has_filter($tag, $function_to_check = false)
{
    return app()->event->hasFilter($tag, $function_to_check);
}

/**
* Call the functions added to a filter hook.
*
* @param string $tag The name of the filter hook.
* @param mixed $value The value on which the filters hooked to <tt>$tag</tt> are applied on.
* @param mixed $var,... Additional variables passed to the functions hooked to <tt>$tag</tt>.
* @return mixed The filtered value after all hooked functions are applied to it.
*/
function apply_filters($tag, $value, $args = '')
{
    return call_user_func_array([app()->event, "applyFilters"], func_get_args());
}

/**
* Execute functions hooked on a specific filter hook, specifying arguments in an array.
*
* @param string $tag The name of the filter hook.
* @param array $args The arguments supplied to the functions hooked to <tt>$tag</tt>
* @return mixed The filtered value after all hooked functions are applied to it.
*/
function apply_filters_ref_array($tag, $args)
{
    return app()->event->applyFiltersRefArray($tag, $args);
}

/**
* Hooks a function on to a specific action.
*
* @param string $tag The name of the action to which the $function_to_add is hooked.
* @param callback $function_to_add The name of the function you wish to be called.
* @param int $priority optional. Used to specify the order in which the functions associated with a
*                      particular action are executed (default: 10). Lower numbers correspond with
*                      earlier execution, and functions with the same priority are executed in the
*                      order in which they were added to the action.
* @param int $accepted_args optional. The number of arguments the function accept (default 1).
* @return mixed
*/
function add_action($tag, $function_to_add, $priority = 10, $accepted_args = 10)
{
    return app()->event->addAction($tag, $function_to_add, $priority, $accepted_args);
}


/**
* Check if any action has been registered for a hook.
*
* @param string $tag The name of the action hook.
* @param callback $function_to_check optional.
* @return mixed If $function_to_check is omitted, returns boolean for whether the hook has anything
*                  registered. When checking a specific function, the priority of that hook is returned,
*                  or false if the function is not attached. When using the $function_to_check argument,
*                  this function may return a non-boolean value that evaluates to false. (e.g.) 0, so use
*                  the === operator for testing the return value.
*/
function has_action($tag, $function_to_check = false)
{
    return app()->event->hasAction($tag, $function_to_check);
}

/**
* Removes a function from a specified action hook.
*
* @param string $tag The action hook to which the function to be removed is hooked.
* @param callback $function_to_remove The name of the function which should be removed.
* @param int $priority optional The priority of the function (default: 10).
* @return boolean Whether the function is removed.
*/
function remove_action($tag, $function_to_remove, $priority = 10)
{
    return app()->event->removeAction($tag, $function_to_remove, $priority);
}

/**
* Remove all of the hooks from an action.
*
* @param string $tag The action to remove hooks from.
* @param int $priority The priority number to remove them from.
* @return bool True when finished.
*/
function remove_all_actions($tag, $priority = false)
{
    return app()->event->removeAllActions($tag, $priority);
}

/**
* Execute functions hooked on a specific action hook.
*
* @param string $tag The name of the action to be executed.
* @param mixed $arg,... Optional additional arguments which are passed on to the
*                       functions hooked to the action.
* @return null Will return null if $tag does not exist in $filter array
*/
function do_action($tag, $arg = '')
{
    return call_user_func_array([app()->event, "doAction"], func_get_args());
}

/**
* Execute functions hooked on a specific action hook, specifying arguments in an array.
*
* @param string $tag The name of the action to be executed.
* @param array $args The arguments supplied to the functions hooked to <tt>$tag</tt>
* @return null Will return null if $tag does not exist in $filter array
*/
function do_action_ref_array($tag, $args)
{
    return app()->event->doActionRefArray($tag, $args);
}

/**
* Retrieve the number of times an action is fired.
*
* @param string $tag The name of the action hook.
* @return int The number of times action hook <tt>$tag</tt> is fired
*/
function did_action($tag)
{
    return app()->event->didAction($tag);
}

/**
* Retrieve the name of the current filter or action.
*
* @return string Hook name of the current filter or action.
*/
function current_filter()
{
    return app()->event->currentFilter();
}

/**
* Retrieve the name of the current action.
*
* @return string Hook name of the current action.
*/
function current_action()
{
    return app()->event->currentAction();
}

/**
* Retrieve the name of a filter currently being processed.
*
* current_filter() only returns the most recent filter or action
* being executed. did_action() returns true once the action is initially
* processed. This function allows detection for any filter currently being
* executed (despite not being the most recent filter to fire, in the case of
* hooks called from hook callbacks) to be verified.
*
* @param null|string $filter Optional. Filter to check. Defaults to null, which
*                            checks if any filter is currently being run.
* @return bool Whether the filter is currently in the stack
*/
function doing_filter($filter = null)
{
    return app()->event->doingFilter($filter);
}

/**
* Retrieve the name of an action currently being processed.
*
* @param string|null $action Optional. Action to check. Defaults to null, which checks
*                            if any action is currently being run.
* @return bool Whether the action is currently in the stack.
*/
function doing_action($action = null)
{
     return app()->event->doingAction($action);
}

/**
 * Adds action to fire when plugin is enabled
 *
 * @param  string $file the php magical __FILE__ constant from the base plugin file
 * @param  mixed $callable
 * @return mixed
 */
function fire_on_enable($file, $callable)
{
    $plugin = sp_plugin_basename($file);
    return add_action("{$plugin}OnEnable", $callable);
}

/**
 * Adds action to fire when plugin is disabled
 *
 * @param  string $file the php magical __FILE__ constant from the base plugin file
 * @param  mixed $callable
 * @return mixed
 */
function fire_on_disable($file, $callable)
{
    $plugin = sp_plugin_basename($file);
    return add_action("{$plugin}OnDisable", $callable);
}

/**
 * Adds action to fire when plugin is deleted
 *
 * @param  string $file the php magical __FILE__ constant from the base plugin file
 * @param  mixed $callable
 * @return mixed
 */
function fire_on_delete($file, $callable)
{
    $plugin = sp_plugin_basename($file);
    return add_action("{$plugin}OnDelete", $callable);
}

/**
 * Registers plugin options
 *
 * @param  string $file the php magical __FILE__ constant from the base plugin file
 * @param  string $templateName Path to the options template
 * @param  mixed  $handlerCallback Callback to fire when the option form is submitted
 * @param  string|null $label A label that will be added to the admin settings menu, leave empty to disable this behaviour
 * @return boolean
 */
function register_plugin_options($file, $templateName, $handlerCallback, $label = null)
{
    $plugin = sp_plugin_basename($file);
    registry_store("{$plugin}__options_template", $templateName, true);
    add_action("{$plugin}OnOptionsSubmit", $handlerCallback);

    // To us, no label means the plugin would like to add the menu manually
    if (!$label) {
        return true;
    }

    add_action('dashboard.controller_init_after', function () use ($plugin, $label) {
        sp_add_sidebar_menu(
            "plugin-{$plugin}-options",
            [
            'type'  => 'link',
            'url'   => url_for('dashboard.settings.plugin', ['plugin' => $plugin]),
            'label' => $label,
            ],
            'settings'
        );
    });

    return true;
}

/**
 * Returns if a plugin has options registered or not
 *
 * @param  string $plugin
 * @return boolean
 */
function plugin_has_options($plugin)
{
    if (registry_read("{$plugin}__options_template")) {
        return true;
    }

    return false;
}

/**
 * Returns plugin base file name from absolute plugin file path, basically a basename() wrapper
 *
 * @param  string $file the php magical __FILE__ constant from the base plugin file
 * @return boolean
 */
function sp_plugin_basename($file)
{
    return basename(dirname($file));
}

/**
 * Returns URL plugin base file name from absolute plugin file path
 *
 * @param  string $file the php magical __FILE__ constant from the base plugin file
 * @param  string $path
 * @return boolean
 */
function sp_plugin_uri($file, $path = '')
{
    $location = trailingslashit(PLUGIN_DIR) . trailingslashit(sp_plugin_basename($file)) . unleadingslashit($path);
    return base_uri($location);
}

/**
 * Access path inside current plugin
 *
 * @param  string $file the php magical __FILE__ constant from the base plugin file
 * @param  string $path
 * @return boolean
 */
function sp_plugin_path($file, $path = '')
{
    $plugin = sp_plugin_basename($file);

    return basepath($path, trailingslashit(BASEPATH) . trailingslashit(PLUGIN_DIR) . trailingslashit($plugin));
}

/**
 * Fires when a theme is activated
 *
 * @param  string $file the php magical __FILE__ constant from the base skin file
 * @param  mixed $callable
 * @return boolean
 */
function theme_activation_hook($file, $callable)
{
    $theme = sp_plugin_basename($file);
    return add_action("theme.{$theme}OnEnable", $callable);
}

/**
 * Fires when a theme is deleted (actually before the deletion process)
 *
 * @param  string $file the php magical __FILE__ constant from the base skin file
 * @param  mixed $callable
 * @return boolean
 */
function theme_deletion_hook($file, $callable)
{
    $theme = sp_plugin_basename($file);
    return add_action("theme.{$theme}OnDelete", $callable);
}

/**
* Check if a plugin is enabled or not
*
* @param  string  $plugin
* @return boolean
*/
function is_plugin_enabled($identifier)
{
    return app()->plugins->isEnabled($identifier);
}
