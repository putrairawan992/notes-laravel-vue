<?php
/**
 * Plugin Name:  User Department
 * Description:  Adds a Department field to WordPress user profiles, displays it in the Users list table, and provides optional filtering and department management.
 * Version:      1.0.0
 * Author:       D&O Creative Assessment
 * License:      GPL v2 or later
 * Text Domain:  user-department
 *
 * @package UserDepartment
 */

// Prevent direct access.
if (! defined('ABSPATH')) {
    exit;
}

/*
|--------------------------------------------------------------------------
| WordPress stub declarations
| Disediakan untuk kompatibilitas IDE / static analysis di luar WordPress.
| Saat plugin berjalan DI DALAM WordPress, blok ini dilewati (WPINC sudah
| didefinisikan oleh WP core) — fungsi & class asli WordPress yang dipakai.
|--------------------------------------------------------------------------
*/
if (! defined('WPINC')) {

    // ---------- Stub Classes ----------

    if (! class_exists('WP_User')) {
        class WP_User {
            public $ID = 0;
            public $data;
            public function __construct($id = 0, $name = '', $site_id = '') {}
            public function exists() { return false; }
            public function get($key) { return ''; }
            public function has_prop($key) { return false; }
            public function to_array() { return []; }
        }
    }

    if (! class_exists('WP_User_Query')) {
        class WP_User_Query {
            public $results = [];
            public function __construct($query = null) {}
            public function get_results() { return []; }
            public function set($key, $value) {}
        }
    }

    if (! class_exists('WP_Screen')) {
        class WP_Screen {
            public $id = '';
        }
    }

    // ---------- Stub Functions (Escaping) ----------

    function esc_attr($text)           { return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8'); }
    function esc_html($text)           { return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8'); }
    function esc_html__($text, $d='')  { return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8'); }
    function esc_html_e($text, $d='')  { echo htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8'); }
    function esc_js($text)             { return json_encode((string) $text, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); }
    function esc_url($url)             { return htmlspecialchars((string) $url, ENT_QUOTES, 'UTF-8'); }
    function esc_attr_e($text, $d='')  { echo htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8'); }

    // ---------- Stub Functions (i18n) ----------

    function __($text, $domain = '')           { return $text; }
    function _e($text, $domain = '')           { echo $text; }
    function _x($text, $context, $domain = '') { return $text; }

    // ---------- Stub Functions (Sanitization) ----------

    function sanitize_text_field($str) { return trim(strip_tags((string) $str)); }
    function sanitize_key($key)        { return preg_replace('/[^a-z0-9_\-]/', '', strtolower((string) $key)); }
    function sanitize_email($email)    { return filter_var($email, FILTER_SANITIZE_EMAIL); }
    function wp_unslash($value)        { return is_string($value) ? stripslashes($value) : $value; }
    function wp_kses($str, $allowed)   { return strip_tags((string) $str); }

    // ---------- Stub Functions (User Meta & Options) ----------

    function get_user_meta($uid, $key = '', $single = false) { return $single ? '' : []; }
    function update_user_meta($uid, $key, $value, $prev = '') { return true; }
    function delete_user_meta($uid, $key, $value = '')        { return true; }
    function get_option($opt, $default = false)                { return $default; }
    function update_option($opt, $value, $autoload = null)     { return true; }
    function delete_option($opt)                               { return true; }

    // ---------- Stub Functions (Hooks) ----------

    function add_action($hook, $cb, $pri = 10, $args = 1)     { return true; }
    function add_filter($hook, $cb, $pri = 10, $args = 1)     { return true; }
    function apply_filters($hook, $value, ...$args)            { return $value; }
    function do_action($hook, ...$args)                        {}

    // ---------- Stub Functions (Admin / Settings) ----------

    function add_submenu_page($p, $pt, $mt, $cap, $slug, $cb, $pos = null) { return ''; }
    function register_setting($group, $name, $args = [])                     { return true; }
    function settings_fields($group)                                         {}
    function do_settings_sections($page)                                     {}
    function submit_button($text = null, $type = 'primary', $name = 'submit', $wrap = true, $attr = null) {}
    function selected($selected, $current = true, $echo = true) {
        $result = ($selected == $current) ? ' selected="selected"' : '';
        if ($echo) echo $result;
        return $result;
    }
    function checked($checked, $current = true, $echo = true) {
        $result = ($checked == $current) ? ' checked="checked"' : '';
        if ($echo) echo $result;
        return $result;
    }
    function get_current_screen()  { return null; }
    function is_admin()            { return true; }
    function current_user_can($cap, ...$args) { return true; }
    function wp_die($msg = '', $title = '', $args = []) { exit((string) $msg); }
    function wp_verify_nonce($nonce, $action = -1) { return true; }
    function wp_nonce_field($action = -1, $name = '_wpnonce', $referer = true, $display = true) { return ''; }

} // end if (! defined('WPINC'))

/**
 * Class User_Department
 *
 * Self-contained plugin — no manual DB setup needed.
 * All data stored via WordPress user meta API.
 */
class User_Department
{
    /**
     * Default department options.
     *
     * @var array<string, string>
     */
    private array $default_departments = [
        'management'  => 'Management',
        'designer'    => 'Designer',
        'developer'   => 'Developer',
        'seo_team'    => 'SEO Team',
        'finance'     => 'Finance',
        'operations'  => 'Operations',
    ];

    /**
     * Option key used to store custom departments in wp_options.
     *
     * @var string
     */
    private string $option_key = 'user_department_list';

    /**
     * Bootstrap the plugin.
     */
    public static function init(): void
    {
        $instance = new self();
        $instance->register_hooks();
    }

    /**
     * Register all WordPress action and filter hooks.
     */
    private function register_hooks(): void
    {
        // Show department field on user profile (own + admin edit).
        add_action('show_user_profile', [$this, 'render_department_field']);
        add_action('edit_user_profile', [$this, 'render_department_field']);

        // Save department when profile is updated.
        add_action('personal_options_update', [$this, 'save_department']);
        add_action('edit_user_profile_update', [$this, 'save_department']);

        // Add column to Users list table.
        add_filter('manage_users_columns', [$this, 'add_department_column']);
        add_filter('manage_users_custom_column', [$this, 'render_department_column'], 10, 3);

        // --- Nice-to-have: Filter users by department ---
        add_action('restrict_manage_users', [$this, 'render_department_filter']);
        add_filter('pre_get_users', [$this, 'filter_users_by_department']);

        // --- Nice-to-have: Admin settings page for managing departments ---
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    // -------------------------------------------------------------------------
    // Department Options Helper
    // -------------------------------------------------------------------------

    /**
     * Get the current list of departments (custom + defaults merged).
     *
     * @return array<string, string>
     */
    private function get_departments(): array
    {
        $saved = get_option($this->option_key, []);

        // If nothing saved yet, seed with defaults.
        if (empty($saved)) {
            update_option($this->option_key, $this->default_departments);

            return $this->default_departments;
        }

        // Ensure we always have at least the defaults (in case admin clears them).
        return array_merge($this->default_departments, $saved);
    }

    // -------------------------------------------------------------------------
    // Profile Field — Render
    // -------------------------------------------------------------------------

    /**
     * Render the Department dropdown on the user profile / edit user page.
     *
     * @param WP_User $user The user being edited.
     */
    public function render_department_field(\WP_User $user): void
    {
        $departments = $this->get_departments();
        $current     = get_user_meta($user->ID, 'department', true);
        ?>
        <h3><?php esc_html_e('Department', 'user-department'); ?></h3>
        <table class="form-table">
            <tr>
                <th><label for="user_department"><?php esc_html_e('Department', 'user-department'); ?></label></th>
                <td>
                    <select name="user_department" id="user_department" style="min-width: 220px;">
                        <option value=""><?php esc_html_e('— Select Department —', 'user-department'); ?></option>
                        <?php foreach ($departments as $key => $label): ?>
                            <option value="<?php echo esc_attr($key); ?>"
                                <?php selected($current, $key); ?>>
                                <?php echo esc_html($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description">
                        <?php esc_html_e('Select the department this user belongs to.', 'user-department'); ?>
                    </p>
                </td>
            </tr>
        </table>
        <?php
    }

    // -------------------------------------------------------------------------
    // Profile Field — Save
    // -------------------------------------------------------------------------

    /**
     * Save the department value when a user profile is updated.
     *
     * @param int $user_id The ID of the user being saved.
     */
    public function save_department(int $user_id): void
    {
        // Check capabilities (admin editing another user, or user editing own profile).
        if (! current_user_can('edit_user', $user_id)) {
            return;
        }

        // Verify nonce to prevent CSRF.
        if (! isset($_POST['_wpnonce']) ||
            ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce'])), 'update-user_' . $user_id)) {
            return;
        }

        // phpcs:ignore WordPress.Security.NonceVerification.Missing — verified above
        $department = isset($_POST['user_department'])
            ? sanitize_text_field(wp_unslash($_POST['user_department']))
            : '';

        if ($department === '') {
            delete_user_meta($user_id, 'department');
        } else {
            update_user_meta($user_id, 'department', $department);
        }
    }

    // -------------------------------------------------------------------------
    // Users List Table — Column
    // -------------------------------------------------------------------------

    /**
     * Add the "Department" column to the Users list table.
     *
     * @param array<string, string> $columns Existing columns.
     * @return array<string, string>
     */
    public function add_department_column(array $columns): array
    {
        // Insert after the "Email" column.
        $new_columns = [];
        foreach ($columns as $key => $value) {
            $new_columns[$key] = $value;
            if ($key === 'email') {
                $new_columns['department'] = __('Department', 'user-department');
            }
        }

        return $new_columns;
    }

    /**
     * Render the department value for a user row in the Users list table.
     *
     * @param string $output      Custom column output (empty by default).
     * @param string $column_name The column name.
     * @param int    $user_id     The current user ID.
     * @return string
     */
    public function render_department_column(string $output, string $column_name, int $user_id): string
    {
        if ($column_name !== 'department') {
            return $output;
        }

        $departments = $this->get_departments();
        $user_dept   = get_user_meta($user_id, 'department', true);

        if ($user_dept && isset($departments[$user_dept])) {
            return esc_html($departments[$user_dept]);
        }

        return '<span aria-hidden="true">—</span>';
    }

    // -------------------------------------------------------------------------
    // Nice-to-have: Filter Users by Department
    // -------------------------------------------------------------------------

    /**
     * Render a department filter dropdown above the Users list table.
     */
    public function render_department_filter(): void
    {
        // Only render on the users list page.
        $screen = get_current_screen();
        if (! $screen || $screen->id !== 'users') {
            return;
        }

        $departments     = $this->get_departments();
        $selected        = isset($_GET['user_department_filter'])
            ? sanitize_text_field(wp_unslash($_GET['user_department_filter']))
            : '';

        echo '<select name="user_department_filter" style="float:none; min-width:180px;">';
        echo '<option value="">' . esc_html__('All Departments', 'user-department') . '</option>';
        foreach ($departments as $key => $label) {
            printf(
                '<option value="%s" %s>%s</option>',
                esc_attr($key),
                selected($selected, $key, false),
                esc_html($label)
            );
        }
        echo '</select>';
    }

    /**
     * Modify the WP_User_Query to filter by department.
     *
     * @param WP_User_Query $query The current user query.
     */
    public function filter_users_by_department(\WP_User_Query $query): void
    {
        global $pagenow;

        if ($pagenow !== 'users.php') {
            return;
        }

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $filter = isset($_GET['user_department_filter'])
            ? sanitize_text_field(wp_unslash($_GET['user_department_filter']))
            : '';

        if ($filter === '') {
            return;
        }

        $query->set('meta_key', 'department');
        $query->set('meta_value', $filter);
    }

    // -------------------------------------------------------------------------
    // Nice-to-have: Settings Page
    // -------------------------------------------------------------------------

    /**
     * Register the admin settings submenu page.
     */
    public function add_settings_page(): void
    {
        add_submenu_page(
            'users.php',                               // Parent slug.
            __('Department Settings', 'user-department'), // Page title.
            __('Departments', 'user-department'),        // Menu title.
            'manage_options',                            // Capability.
            'user-department-settings',                  // Menu slug.
            [$this, 'render_settings_page']              // Callback.
        );
    }

    /**
     * Register the plugin's setting and sanitization callback.
     */
    public function register_settings(): void
    {
        register_setting(
            'user_department_settings_group',
            $this->option_key,
            [
                'type'              => 'array',
                'sanitize_callback' => [$this, 'sanitize_departments'],
                'default'           => $this->default_departments,
            ]
        );
    }

    /**
     * Sanitize the departments array from the settings form.
     *
     * @param array|null $input Raw input from the form.
     * @return array<string, string>
     */
    public function sanitize_departments(?array $input): array
    {
        if (! is_array($input)) {
            return $this->default_departments;
        }

        $sanitized = [];
        foreach ($input as $key => $label) {
            $key   = sanitize_key($key);
            $label = sanitize_text_field($label);

            if ($key !== '' && $label !== '') {
                $sanitized[$key] = $label;
            }
        }

        return ! empty($sanitized) ? $sanitized : $this->default_departments;
    }

    /**
     * Render the settings page HTML.
     */
    public function render_settings_page(): void
    {
        if (! current_user_can('manage_options')) {
            wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'user-department'));
        }

        $departments = $this->get_departments();
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Department Settings', 'user-department'); ?></h1>
            <p><?php esc_html_e('Manage the list of departments available for user assignment.', 'user-department'); ?></p>

            <form method="post" action="options.php">
                <?php
                settings_fields('user_department_settings_group');
                do_settings_sections('user_department_settings_group');
                ?>
                <table class="form-table" id="department-list-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Key (internal)', 'user-department'); ?></th>
                            <th><?php esc_html_e('Display Label', 'user-department'); ?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($departments as $key => $label): ?>
                            <tr>
                                <td>
                                    <code><?php echo esc_html($key); ?></code>
                                </td>
                                <td>
                                    <input type="text"
                                           name="<?php echo esc_attr($this->option_key); ?>[<?php echo esc_attr($key); ?>]"
                                           value="<?php echo esc_attr($label); ?>"
                                           class="regular-text" />
                                </td>
                                <td>
                                    <button type="button" class="button button-secondary remove-department">
                                        <?php esc_html_e('Remove', 'user-department'); ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <p>
                    <button type="button" id="add-department-btn" class="button button-secondary">
                        <?php esc_html_e('+ Add Department', 'user-department'); ?>
                    </button>
                </p>

                <?php submit_button(); ?>
            </form>
        </div>

        <script>
        (function() {
            const tbody = document.querySelector('#department-list-table tbody');
            const addBtn = document.getElementById('add-department-btn');

            addBtn.addEventListener('click', function() {
                const key = prompt('<?php echo esc_js(__('Enter a unique key (lowercase, underscores):', 'user-department')); ?>');
                if (!key) return;
                const label = prompt('<?php echo esc_js(__('Enter the display label:', 'user-department')); ?>');
                if (!label) return;

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>
                        <code>${key}</code>
                    </td>
                    <td>
                        <input type="text"
                               name="<?php echo esc_js($this->option_key); ?>[${key}]"
                               value="${label}"
                               class="regular-text" />
                    </td>
                    <td>
                        <button type="button" class="button button-secondary remove-department">
                            <?php echo esc_js(__('Remove', 'user-department')); ?>
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            });

            // Delegate click for remove buttons (including dynamically added ones).
            tbody.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-department')) {
                    e.target.closest('tr').remove();
                }
            });
        })();
        </script>
        <?php
    }
}

// Bootstrap — hook ke WordPress.
// Di luar WP, fallback add_action (no-op) mencegah error.
add_action('plugins_loaded', ['User_Department', 'init']);
