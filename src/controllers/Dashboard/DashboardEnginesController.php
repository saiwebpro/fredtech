<?php

namespace spark\controllers\Dashboard;

use Valitron\Validator;
use spark\controllers\Controller;
use spark\drivers\Nav\Pagination;
use spark\models\EngineModel;

/**
* DashboardEnginesController
*
* @package spark
*/
class DashboardEnginesController extends DashboardController
{
    public function __construct()
    {
        parent::__construct();

        /**
         * @hook Fires before DashboardEnginesController is initialized
         */
        do_action('dashboard.engines_controller_init_before');

        // this is it
        if (!current_user_can('manage_engines')) {
            sp_not_permitted();
        }

        breadcrumb_add('dashboard.engines', __('Engines'), url_for('dashboard.engines'));
        view_set('engines__active', 'active');

        /**
         * @hook Fires after DashboardEnginesController is initialized
         */
        do_action('dashboard.engines_controller_init_after');
    }

    /**
     * List entries
     *
     * @return
     */
    public function index()
    {
        $app = app();

        // Model instance
        $engineModel = new EngineModel;

        // Current page number
        $currentPage = (int) $app->request->get('page', 1);

        // Items per page
        $itemsPerPage = (int) config('dashboard.items_per_page');

        // Total item count
        $totalCount = $engineModel->countRows();

        // Sort value
        $sort = $app->request->get('sort', null);

        // Ensure the target sort type is allowed
        if (!$engineModel->isSortAllowed($sort)) {
            $sort = 'newest';
        }

        $sortRules = $engineModel->getAllowedSorting();

        // Filters
        $filters = [
            'sort' => e_attr($sort)
        ];

        $queryStr = request_build_query(['page', 'sort']);
        // Pagination instance
        $pagination = new Pagination($totalCount, $currentPage, $itemsPerPage);
        $pagination->setUrl("?page=@id@&sort={$sort}{$queryStr}");

        // Generated HTML
        $paginationHtml = $pagination->renderHtml();

        // Offset value based on current page
        $offset = $pagination->offset();

        // List entries
        $entries = $engineModel->readMany(
            ['*'],
            $offset,
            $itemsPerPage,
            $filters
        );

        // Template data
        $data = [
            'list_entries'    => $entries,
            'total_items'     => $totalCount,
            'offset'          => $offset === 0 ? 1 : $offset,
            'current_page'    => $currentPage,
            'items_per_page'  => $itemsPerPage,
            'current_items'   => $itemsPerPage * $currentPage,
            'sort_type'       => $sort,
            'pagination_html' => $paginationHtml,
            'sorting_rules'   => $sortRules,
            'query_str'       => $queryStr,
            'default_engine' => (int) get_option('default_engine', 0)
        ];
        return view('admin::engines/index.php', $data);
    }

    /**
     * Create new entry
     *
     * @return
     */
    public function create()
    {
        // Load form validator
        sp_enqueue_script('parsley', 2, ['dashboard-core-js']);

        // Set breadcrumb trails
        breadcrumb_add('dashboard.engines.create', __('Create Engine'));

        $data = [];
        return view('admin::engines/create.php', $data);
    }

    /**
     * Create new entry action
     *
     * @return
     */
    public function createPOST()
    {
        if (is_demo()) {
            flash('engines-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return redirect_to('dashboard.engines');
        }

        $app = app();
        $req = $app->request;
        $data = [
            'engine_name' => trim($req->post('engine_name')),
            'engine_cse_id' => trim($req->post('engine_cse_id')),
            'engine_is_image' => sp_int_bool($req->post('engine_is_image')),
            'engine_show_thumb' => sp_int_bool($req->post('engine_show_thumb')),
        ];


        // Basic validation is basic * ding *
        // Go checkout CinemaSins on YouTube
        $v = (new Validator($data))
          ->rule('lengthMax', 'engine_name', 200)
          ->rule('lengthMax', 'engine_cse_id', 200)
          ->rule('required', [
                "engine_name",
                "engine_cse_id",
                "engine_is_image",
                "engine_show_thumb"
            ]);

        if (!$v->validate()) {
            $errors = sp_valitron_errors($v->errors());
            flash('engines-danger', $errors);
            sp_store_post($data);
            return redirect_to_current_route();
        }

        $engineModel = new EngineModel;
        $data['engine_name'] = sp_strip_tags($data['engine_name']);
        $data['engine_cse_id'] = sp_strip_tags($data['engine_cse_id']);
        $id = $engineModel->create($data);

        $isDefault = sp_int_bool($req->post('default_engine'));
        
        // Mark the engine as default
        if ($isDefault) {
            set_option('default_engine', $id);
        }

        flash('engines-success', __('Engine was created successfully'));
        return redirect_to('dashboard.engines');
    }

    /**
     * Update entry page
     *
     * @param mixed $id
     * @return
     */
    public function update($id)
    {
        // Load form validator
        sp_enqueue_script('parsley', 2, ['dashboard-core-js']);

        // Set breadcrumb trails
        breadcrumb_add('dashboard.engines.update', __('Update Engine'));

        $engineModel = new EngineModel;

        $engine = $engineModel->read($id);

        if (!$engine) {
            flash('engines-danger', __('No such engine found.'));
            return redirect_to('dashboard.engines');
        }


        $data = [
            'engine' => $engine,
            'default_engine' => get_option('default_engine') == $engine['engine_id'],
        ];

        return view('admin::engines/update.php', $data);
    }

    /**
     * Update entry action
     *
     * @param mixed $id
     * @return
     */
    public function updatePOST($id)
    {
        if (is_demo()) {
            flash('engines-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return redirect_to('dashboard.engines');
        }

        $engineModel = new EngineModel;

        $engine = $engineModel->read($id);

        if (!$engine) {
            flash('engines-danger', __('No such engine found.'));
            return redirect_to('dashboard.engines');
        }

        $app = app();
        $req = $app->request;
        $data = [
            'engine_name' => trim($req->post('engine_name')),
            'engine_cse_id' => trim($req->post('engine_cse_id')),
            'engine_is_image' => sp_int_bool($req->post('engine_is_image')),
            'engine_show_thumb' => sp_int_bool($req->post('engine_show_thumb')),
        ];


        // Basic validation is basic * ding *
        // Go checkout CinemaSins on YouTube
        $v = (new Validator($data))
          ->rule('lengthMax', 'engine_name', 200)
          ->rule('lengthMax', 'engine_cse_id', 200)
          ->rule('required', [
                "engine_name",
                "engine_cse_id",
                "engine_is_image",
                "engine_show_thumb"
            ]);

        if (!$v->validate()) {
            $errors = sp_valitron_errors($v->errors());
            flash('engines-danger', $errors);
            sp_store_post($data);
            return redirect_to_current_route();
        }

        $isDefault = sp_int_bool($req->post('default_engine'));

        $data['engine_name'] = sp_strip_tags($data['engine_name']);
        $data['engine_cse_id'] = sp_strip_tags($data['engine_cse_id']);
        $engineModel->update($id, $data);

        // Mark the engine as default
        if ($isDefault) {
            set_option('default_engine', $id);
        }

        flash('engines-success', __('Engine was updated successfully'));
        return redirect_to_current_route();
    }

    /**
     * Delete entry page
     *
     * @param mixed $id
     * @return
     */
    public function delete($id)
    {
        // Load form validator
        sp_enqueue_script('parsley', 2, ['dashboard-core-js']);

        // Set breadcrumb trails
        breadcrumb_add('dashboard.engines.update', __('Delete Engine'));

        $engineModel = new EngineModel;

        $engine = $engineModel->read($id);

        if (!$engine) {
            flash('engines-danger', __('No such engine found.'));
            return redirect_to('dashboard.engines');
        }

        $data = [
            'engine' => $engine,
        ];
        return view('admin::engines/delete.php', $data);
    }

    /**
     * Delete entry action
     *
     * @param mixed $id
     * @return
     */
    public function deletePOST($id)
    {
        if (is_demo()) {
            if (is_ajax()) {
                return;
            }
            flash('engines-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return redirect_to('dashboard.engines');
        }

        $engineModel = new EngineModel;

        $engine = $engineModel->read($id);

        if (!$engine) {
            flash('engines-danger', __('No such engine found.'));

            if (is_ajax()) {
                return;
            }

            return redirect_to('dashboard.engines');
        }
        
        $isDefault = get_option('default_engine') == $id;

        if ($isDefault) {
            flash('engines-danger', __('You can\'t delete the default engine.'));

            if (is_ajax()) {
                return;
            }

            return redirect_to('dashboard.engines');
        }

        $engineModel->delete($id);

        flash('engines-success', __('Engine was deleted successfully'));

        if (is_ajax()) {
            return;
        }

        return redirect_to('dashboard.engines');
    }

    public function setDefaultPOST($id)
    {
        if (is_demo()) {
            flash('engines-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return redirect_to('dashboard.engines');
        }

        $engineModel = new EngineModel;

        $engine = $engineModel->read($id);

        if (!$engine) {
            flash('engines-danger', __('No such engine found.'));
            return redirect_to('dashboard.engines');
        }

        set_option('default_engine', $id);

        flash('engines-success', __('Default engine was set successfully.'));
        return redirect_to('dashboard.engines');
    }
}
