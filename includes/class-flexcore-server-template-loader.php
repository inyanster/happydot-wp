<?php
/**
 * Template Loader Class
 *
 * @package FlexCore_Server
 */

class FlexCore_Server_Template_Loader {
    /**
     * Get template part
     *
     * @param string $slug Template slug
     * @param string $name Template name (optional)
     * @param array  $args Arguments to pass to template
     * @return void
     */
    public function get_template_part($slug, $name = '', $args = array()) {
        if ($args && is_array($args)) {
            extract($args);
        }

        $template = '';

        // Look in plugin/templates for template file
        if ($name) {
            $template = locate_template(array(
                "flexcore-server/{$slug}-{$name}.php",
                "flexcore-server/{$slug}.php"
            ));

            if (!$template) {
                $fallback = plugin_dir_path(dirname(__FILE__)) . "public/templates/{$slug}-{$name}.php";
                $template = file_exists($fallback) ? $fallback : '';
            }
        }

        if (!$template) {
            $template = locate_template(array("flexcore-server/{$slug}.php"));

            if (!$template) {
                $fallback = plugin_dir_path(dirname(__FILE__)) . "public/templates/{$slug}.php";
                $template = file_exists($fallback) ? $fallback : '';
            }
        }

        // Allow 3rd party plugins to filter template file from their plugin
        $template = apply_filters('flexcore_server_get_template_part', $template, $slug, $name);

        if ($template) {
            include $template;
        }
    }

    /**
     * Get template HTML
     *
     * @param string $template_name Template name
     * @param array  $args          Arguments to pass to template
     * @param string $template_path Template path (optional)
     * @param string $default_path  Default path (optional)
     * @return string
     */
    public function get_template_html($template_name, $args = array(), $template_path = '', $default_path = '') {
        ob_start();
        $this->get_template($template_name, $args, $template_path, $default_path);
        return ob_get_clean();
    }

    /**
     * Get and include template files
     *
     * @param string $template_name Template name
     * @param array  $args          Arguments to pass to template
     * @param string $template_path Template path (optional)
     * @param string $default_path  Default path (optional)
     * @return void
     */
    public function get_template($template_name, $args = array(), $template_path = '', $default_path = '') {
        if ($args && is_array($args)) {
            extract($args);
        }

        $located = $this->locate_template($template_name, $template_path, $default_path);

        if (!file_exists($located)) {
            _doing_it_wrong(__FUNCTION__, sprintf('<code>%s</code> does not exist.', $located), '1.0.0');
            return;
        }

        // Allow 3rd party plugin filter template file
        $located = apply_filters('flexcore_server_get_template', $located, $template_name, $args, $template_path, $default_path);

        do_action('flexcore_server_before_template_part', $template_name, $template_path, $located, $args);

        include $located;

        do_action('flexcore_server_after_template_part', $template_name, $template_path, $located, $args);
    }

    /**
     * Locate a template and return the path for inclusion
     *
     * @param string $template_name Template name
     * @param string $template_path Template path (optional)
     * @param string $default_path  Default path (optional)
     * @return string
     */
    public function locate_template($template_name, $template_path = '', $default_path = '') {
        if (!$template_path) {
            $template_path = 'flexcore-server/';
        }

        if (!$default_path) {
            $default_path = plugin_dir_path(dirname(__FILE__)) . 'public/templates/';
        }

        // Look within passed path within the theme - this is priority
        $template = locate_template(array(
            trailingslashit($template_path) . $template_name,
            $template_name
        ));

        // Get default template
        if (!$template) {
            $template = $default_path . $template_name;
        }

        // Return what we found
        return apply_filters('flexcore_server_locate_template', $template, $template_name, $template_path);
    }

    /**
     * Get template loader instance
     *
     * @return FlexCore_Server_Template_Loader
     */
    public static function get_instance() {
        static $instance = null;
        if (is_null($instance)) {
            $instance = new self();
        }
        return $instance;
    }
    
    public static function load_template($template, $args = array()) {
        $template_path = plugin_dir_path(dirname(__FILE__)) . 'public/templates/' . $template . '.php';
        
        // Debug logging for template loading
        if (WP_DEBUG) {
            error_log("FlexCore: Loading template: " . $template_path);
        }

        if (!file_exists($template_path)) {
            if (WP_DEBUG) {
                error_log("FlexCore Error: Template not found: " . $template_path);
            }
            return sprintf(
                '<div class="flexcore-error">%s</div>',
                esc_html__('Template not found', 'flexcore-server')
            );
        }

        if ($args && is_array($args)) {
            extract($args);
        }

        ob_start();
        try {
            include $template_path;
            // Use ob_get_contents + ob_end_clean instead of ob_get_clean.
            // ob_get_clean closes the buffer AND returns contents, which
            // BREAKS nested load_template() calls (e.g. register-myinfo.php
            // calls load_template('margedRegistration')).
            // Since PHP OB is stack-based (no true nesting of ob_start()),
            // when the inner template call does ob_get_clean(), it closes
            // the outer's buffer too. Using ob_end_clean() preserves any
            // outer buffer that may be active.
            $content = ob_get_contents();
            ob_end_clean();
            return $content;
        } catch (Throwable $e) {
            ob_end_clean();
            if (WP_DEBUG) {
                error_log("FlexCore Error: Template rendering failed: " . $e->getMessage());
            }
            return sprintf(
                '<div class="flexcore-error">%s</div>',
                esc_html__('Error loading template', 'flexcore-server')
            );
        }
    }
}
