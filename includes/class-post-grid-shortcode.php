<?php
defined('ABSPATH') || exit;

class Post_Grid_Shortcode {

    /**
     * Initializes the plugin by registering hooks for the settings page,
     * registering the plugin settings, enqueueing assets, and registering
     * the [post-grid] shortcode. It also shows an admin notification when
     * settings are updated.
     *
     * @return void
     */
    public function init() {
        // Hook to create a settings page in the WordPress admin
        add_action('admin_menu', [$this, 'register_settings_page']);

        // Hook to register the plugin settings in the database
        add_action('admin_init', [$this, 'register_settings']);
    
        // Enqueue assets conditionally for both frontend and admin panel
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        
        // Register the [post-grid] shortcode
        add_shortcode('post-grid', [$this, 'render_post_grid']);
        // Show an admin notification when settings are updated
        add_action('admin_notices', [$this, 'show_shortcode_notification']);
    }

    /**
     * Register the settings page in the WordPress admin area.
     *
     * This method creates a new settings page in the WordPress admin area
     * using the add_options_page() function. The page is accessible by
     * users with the manage_options capability (i.e., administrators). The
     * page is identified by the slug 'post-grid-shortcode-settings' and is
     * rendered by the render_settings_page() method.
     *
     * @since 1.0.0
     */
    public function register_settings_page() {
        add_options_page(
            __('Post Grid Shortcode Settings', 'post-grid-shortcode'),
            __('Post Grid Shortcode', 'post-grid-shortcode'),
            'manage_options',
            'post-grid-shortcode-settings',
            [$this, 'render_settings_page']
        );
    }


    /**
     * Render the settings page
     *
     * This method is responsible for generating the HTML for the settings page
     * in the WordPress admin area. It is called when the user visits the
     * settings page.
     *
     * @since 1.0.0
     */
    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Post Grid Shortcode Settings', 'post-grid-shortcode'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('pgs_settings_group');
                do_settings_sections('post-grid-shortcode-settings');
                submit_button();
                ?>
                <!-- Add nonce field to the form -->
                <?php wp_nonce_field('post_grid_nonce_action', 'post_grid_nonce_name'); ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register the plugin settings in the WordPress database.
     *
     * This method is responsible for registering the plugin settings in the
     * WordPress database. It is called when the plugin is activated.
     *
     * @since 1.0.0
     */
    public function register_settings() {
        register_setting('pgs_settings_group', 'pgs_posts_per_page', ['sanitize_callback' => 'intval']);
        register_setting('pgs_settings_group', 'pgs_orderby', ['sanitize_callback' => 'sanitize_text_field']);
        register_setting('pgs_settings_group', 'pgs_order', ['sanitize_callback' => 'sanitize_text_field']);
        register_setting('pgs_settings_group', 'pgs_post_type', ['sanitize_callback' => 'sanitize_text_field']);
        register_setting('pgs_settings_group', 'pgs_post_status', ['sanitize_callback' => 'sanitize_text_field']);
        register_setting('pgs_settings_group', 'pgs_show_featured_image', ['sanitize_callback' => 'absint']);
        register_setting('pgs_settings_group', 'pgs_enable_read_more_button', ['sanitize_callback' => 'absint']);
        register_setting('pgs_settings_group', 'pgs_read_more_text', ['sanitize_callback' => 'sanitize_text_field']);
        register_setting('pgs_settings_group', 'pgs_excerpt_length', ['sanitize_callback' => 'intval']);
        register_setting('pgs_settings_group', 'pgs_columns', ['sanitize_callback' => 'intval']);
        register_setting('pgs_settings_group', 'pgs_featured_image_position', ['sanitize_callback' => 'sanitize_text_field']);
        
        add_settings_field('pgs_show_featured_image', __('Show Featured Image', 'post-grid-shortcode'), [$this, 'render_show_featured_image'], 'post-grid-shortcode-settings', 'pgs_main_settings');
        add_settings_field('pgs_featured_image_position', __('Featured Image Position', 'post-grid-shortcode'), [$this, 'render_featured_image_position'], 'post-grid-shortcode-settings', 'pgs_main_settings');
        add_settings_field('pgs_columns', __('Number of Columns', 'post-grid-shortcode'), [$this, 'render_columns'], 'post-grid-shortcode-settings', 'pgs_main_settings');
        add_settings_section('pgs_main_settings', __('Main Settings', 'post-grid-shortcode'), null, 'post-grid-shortcode-settings');
        add_settings_field('pgs_posts_per_page', __('Posts Per Page', 'post-grid-shortcode'), [$this, 'render_posts_per_page'], 'post-grid-shortcode-settings', 'pgs_main_settings');
        add_settings_field('pgs_orderby', __('Order By', 'post-grid-shortcode'), [$this, 'render_orderby'], 'post-grid-shortcode-settings', 'pgs_main_settings');
        add_settings_field('pgs_order', __('Order', 'post-grid-shortcode'), [$this, 'render_order'], 'post-grid-shortcode-settings', 'pgs_main_settings');
        add_settings_field('pgs_post_type', __('Post Type', 'post-grid-shortcode'), [$this, 'render_post_type'], 'post-grid-shortcode-settings', 'pgs_main_settings');
        add_settings_field('pgs_post_status', __('Post Status', 'post-grid-shortcode'), [$this, 'render_post_status'], 'post-grid-shortcode-settings', 'pgs_main_settings');
        add_settings_field('pgs_show_featured_image', __('Show Featured Image', 'post-grid-shortcode'), [$this, 'render_show_featured_image'], 'post-grid-shortcode-settings', 'pgs_main_settings');
        add_settings_field('pgs_enable_read_more_button', __('Enable Read More Button', 'post-grid-shortcode'), [$this, 'render_enable_read_more_button'], 'post-grid-shortcode-settings', 'pgs_main_settings');
        add_settings_field('pgs_excerpt_length', __('Excerpt Length', 'post-grid-shortcode'), [$this, 'render_excerpt_length'], 'post-grid-shortcode-settings', 'pgs_main_settings');
    }


    /**
     * Render the columns dropdown field
     *
     * This method renders a dropdown field with options for the number of columns
     * for the post grid. The selected value is determined by the value of the
     * "pgs_columns" option in the WordPress database.
     *
     * @since 1.0.0
     */
    public function render_columns() {
        $value = get_option('pgs_columns', 1); // Default to 1 column
        ?>
        <select name="pgs_columns">
            <option value="1" <?php selected($value, 1); ?>><?php esc_html_e('1 Column', 'post-grid-shortcode'); ?></option>
            <option value="2" <?php selected($value, 2); ?>><?php esc_html_e('2 Columns', 'post-grid-shortcode'); ?></option>
            <option value="3" <?php selected($value, 3); ?>><?php esc_html_e('3 Columns', 'post-grid-shortcode'); ?></option>
        </select>
        <p class="description"><?php esc_html_e('Select the number of columns for the post grid.', 'post-grid-shortcode'); ?></p>
        <?php
    }
    
    /**
     * Render the featured image position radio field
     *
     * This method renders a radio field with options for the position of the
     * featured image (top or left). The selected value is determined by the
     * value of the "pgs_featured_image_position" option in the WordPress
     * database.
     *
     * @since 1.0.0
     */

    public function render_featured_image_position() {
        $value = get_option('pgs_featured_image_position', 'top'); // Default to 'top'
        ?>
        <fieldset>
            <label>
                <input type="radio" name="pgs_featured_image_position" value="top" <?php checked($value, 'top'); ?> />
                <?php esc_html_e('Top', 'post-grid-shortcode'); ?>
            </label>
            <br />
            <label>
                <input type="radio" name="pgs_featured_image_position" value="left" <?php checked($value, 'left'); ?> />
                <?php esc_html_e('Left', 'post-grid-shortcode'); ?>
            </label>
        </fieldset>
        <p class="description"><?php esc_html_e('Select the position of the featured image (top or left).', 'post-grid-shortcode'); ?></p>
        <?php
    }
    

    /**
     * Render the posts per page number field
     *
     * This method renders a number field with the value of the "pgs_posts_per_page" option
     * in the WordPress database. The value is used to determine the number of posts to
     * display in the post grid.
     *
     * @since 1.0.0
     */
    public function render_posts_per_page() {
        $value = get_option('pgs_posts_per_page', 6);
        echo "<input type='number' name='pgs_posts_per_page' value='" . esc_attr($value) . "' />";
    }

    /**
     * Render the orderby dropdown field
     *
     * This method renders a dropdown field with options for the orderby
     * parameter in the WordPress database. The selected value is determined
     * by the value of the "pgs_orderby" option in the WordPress database.
     *
     * @since 1.0.0
     */
    public function render_orderby() {
        $value = get_option('pgs_orderby', 'date');
        ?>
        <select name="pgs_orderby">
            <option value="date" <?php selected($value, 'date'); ?>><?php esc_html_e('Date', 'post-grid-shortcode'); ?></option>
            <option value="title" <?php selected($value, 'title'); ?>><?php esc_html_e('Title', 'post-grid-shortcode'); ?></option>
            <option value="author" <?php selected($value, 'author'); ?>><?php esc_html_e('Author', 'post-grid-shortcode'); ?></option>
            <option value="modified" <?php selected($value, 'modified'); ?>><?php esc_html_e('Modified', 'post-grid-shortcode'); ?></option>
        </select>
        <?php
    }

    /**
     * Render the order dropdown field
     *
     * This method renders a dropdown field with options for the order parameter in the WordPress database.
     * The selected value is determined by the value of the "pgs_order" option in the WordPress database.
     *
     * @since 1.0.0
     */
    public function render_order() {
        $value = get_option('pgs_order', 'ASC');
        ?>
        <select name="pgs_order">
            <option value="ASC" <?php selected($value, 'ASC'); ?>><?php esc_html_e('Ascending', 'post-grid-shortcode'); ?></option>
            <option value="DESC" <?php selected($value, 'DESC'); ?>><?php esc_html_e('Descending', 'post-grid-shortcode'); ?></option>
        </select>
        <?php
    }

    /**
     * Render the post type input field
     *
     * This method renders a text input field with the value of the "pgs_post_type" option
     * in the WordPress database. The default value is "post", but you can specify custom
     * post types if needed.
     *
     * @since 1.0.0
     */
    public function render_post_type() {
        $value = get_option('pgs_post_type', 'post');
        echo "<input type='text' name='pgs_post_type' value='" . esc_attr($value) . "' />";
        echo '<p class="description">' . esc_html__('Default is "post", but you can specify custom post types.', 'post-grid-shortcode') . '</p>';
    }

    /**
     * Render the post status dropdown field
     *
     * This method renders a dropdown field with options for the post status
     * parameter in the WordPress database. The selected value is determined
     * by the value of the "pgs_post_status" option in the WordPress database.
     *
     * @since 1.0.0
     */
    public function render_post_status() {
        $value = get_option('pgs_post_status', 'publish');
        ?>
        <select name="pgs_post_status">
            <option value="publish" <?php selected($value, 'publish'); ?>><?php esc_html_e('Published', 'post-grid-shortcode'); ?></option>
            <option value="draft" <?php selected($value, 'draft'); ?>><?php esc_html_e('Draft', 'post-grid-shortcode'); ?></option>
            <option value="pending" <?php selected($value, 'pending'); ?>><?php esc_html_e('Pending', 'post-grid-shortcode'); ?></option>
        </select>
        <?php
    }

    /**
     * Render the show featured image checkbox field
     *
     * This method renders a checkbox input field with the value of the
     * "pgs_show_featured_image" option in the WordPress database. The
     * default value is 1 (enabled). If the value is 1, the featured image
     * will be shown for each post in the post grid.
     *
     * @since 1.0.0
     */
    public function render_show_featured_image() {
        $value = get_option('pgs_show_featured_image', 1);
        echo "<input type='checkbox' name='pgs_show_featured_image' value='1' " . checked(1, $value, false) . " />";
        echo '<p class="description">' . esc_html__('Check to show the featured image for each post.', 'post-grid-shortcode') . '</p>';
    }

    /**
     * Render the enable Read More button field
     *
     * This method renders a checkbox input field with the value of the
     * "pgs_enable_read_more_button" option in the WordPress database.
     * The default value is 0 (disabled). If the value is 1, the field
     * for the "Read More" button text is visible and the button is
     * enabled in the post grid. The button text is retrieved from the
     * "pgs_read_more_text" option in the WordPress database, and the
     * default value is "Read More" if the option is empty.
     *
     * @since 1.0.0
     */
    public function render_enable_read_more_button() {
        $is_enabled = get_option('pgs_enable_read_more_button', 0);
        $read_more_text = get_option('pgs_read_more_text', __('Read More', 'post-grid-shortcode'));
        ?>
        <div>
            <input type="checkbox" name="pgs_enable_read_more_button" value="1" <?php checked(1, $is_enabled); ?> data-toggle-read-more />
            <label><?php esc_html_e('Enable Read More Button', 'post-grid-shortcode'); ?></label>
            <input type="text" name="pgs_read_more_text" value="<?php echo esc_attr($read_more_text); ?>" <?php if(!$is_enabled) echo 'style="display:none;"'; ?> placeholder="<?php esc_html_e('Read More Text', 'post-grid-shortcode'); ?>" />
        </div>
        <p class="description"><?php esc_html_e('Enable the "Read More" button and specify the button text if enabled.', 'post-grid-shortcode'); ?></p>
        <?php
    }
    
    

    /**
     * Render the excerpt length field
     *
     * This method renders a number input field with the value of the
     * "pgs_excerpt_length" option in the WordPress database. The default
     * value is 20 if the option is empty. The value is used to determine
     * the number of words to display for each post excerpt in the post
     * grid.
     *
     * @since 1.0.0
     */
    public function render_excerpt_length() {
        $value = get_option('pgs_excerpt_length', 20);
        echo "<input type='number' name='pgs_excerpt_length' value='" . esc_attr($value) . "' />";
        echo '<p class="description">' . esc_html__('Set the number of words for the excerpt.', 'post-grid-shortcode') . '</p>';
    }

    /**
     * Enqueue CSS and JavaScript assets for the plugin
     *
     * This method enqueues the stylesheet for the post grid on the front-end.
     * On the settings page in the admin area, it enqueues the JavaScript file
     * for toggling the "Read More" button text field.
     *
     * @since 1.0.0
     */
    public function enqueue_assets() {
        // Front-end: Load the stylesheet
        if (!is_admin()) {
            wp_enqueue_style('pgs-style', PGS_PLUGIN_URL . 'assets/css/post-grid-shortcode-admin.css', [], PGS_VERSION);
        }

        // Admin area: Load the stylesheet and script on the settings page
        if (is_admin() && isset($_GET['page']) && $_GET['page'] === 'post-grid-shortcode-settings') {
            wp_enqueue_script('pgs-script', PGS_PLUGIN_URL . 'assets/js/post-grid-shortcode-admin.js', ['jquery'], PGS_VERSION, true);
        }
    }

    /**
     * Render a grid of posts based on specified attributes and plugin settings.
     *
     * This method retrieves various settings from the plugin options and uses them
     * to query and display a grid of posts. The grid is customizable with options
     * such as the number of posts per page, columns, order, post type, and status.
     * It also supports showing the featured image, enabling a "Read More" button,
     * and setting the excerpt length. Pagination is included for navigating through
     * multiple pages of posts.
     *
     * @param array $atts Shortcode attributes to customize the grid display.
     * @return string HTML content of the rendered post grid.
     * @since 1.0.0
     */
    public function render_post_grid($atts) {
        // Retrieve settings from the plugin options
        $posts_per_page = get_option('pgs_posts_per_page', 5);
        $columns = get_option('pgs_columns', 1); // Get the number of columns
        $orderby = get_option('pgs_orderby', 'date');
        $order = get_option('pgs_order', 'ASC');
        $post_type = get_option('pgs_post_type', 'post');
        $post_status = get_option('pgs_post_status', 'publish');
        $show_featured_image = get_option('pgs_show_featured_image', 1);
        $featured_image_position = get_option('pgs_featured_image_position', 'top'); // Get featured image position (default 'top')
        $read_more_enabled = get_option('pgs_enable_read_more_button', 0);
        $read_more_text = get_option('pgs_read_more_text', __('Read More', 'post-grid-shortcode'));
        $excerpt_length = get_option('pgs_excerpt_length', 20);

        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

        $query = new WP_Query([
            'posts_per_page' => $posts_per_page,
            'paged' => $paged,
            'orderby' => $orderby,
            'order' => $order,
            'post_type' => $post_type,
            'post_status' => $post_status,
        ]);

        if (!$query->have_posts()) {
            return __('No posts found', 'post-grid-shortcode');
        }

        ob_start();
        ?>
        <div class="pds post-grid <?php echo esc_attr($columns == 2 ? 'two-columns' : ($columns == 3 ? 'three-columns' : 'one-column')); ?>">
            <?php while ($query->have_posts()): $query->the_post(); ?>
                <div class="post-item <?php echo esc_attr($featured_image_position == 'left' ? 'image-left' : 'image-top'); ?>">
                    <?php if ($show_featured_image && has_post_thumbnail()): ?>
                        <div class="post-thumbnail">
                            <?php the_post_thumbnail('full', ['class' => 'img-fluid']); ?>
                        </div>
                    <?php endif; ?>

                    <div class="post-content">
                        <h2 class="post-title">
                            <a href="<?php the_permalink(); ?>" class="post-link">
                                <?php the_title(); ?>
                            </a>
                        </h2>
                        <p class="post-excerpt">
                            <?php 
                            // * Output the trimmed excerpt, escaped for HTML context for security *
                            echo esc_html( wp_trim_words( get_the_excerpt(), $excerpt_length, '...' ) ); 
                            ?>
                        </p>
                        <?php if ($read_more_enabled): ?>
                            <a href="<?php the_permalink(); ?>" class="pds read-more-btn">
                                <?php echo esc_html($read_more_text); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <div class="pds pagination">
            <?php 
            // * Safely output pagination links, allowing necessary HTML tags *
            echo wp_kses_post( paginate_links([
                'total' => $query->max_num_pages,
                'current' => $paged,
                'prev_text' => __('« Previous', 'post-grid-shortcode'),
                'next_text' => __('Next »', 'post-grid-shortcode')
            ]) ); 
            ?>
        </div>

        <?php

        wp_reset_postdata();
        return ob_get_clean();
    }


    /**
     * Shows a notification message with the shortcode output
     * after saving the plugin settings.
     *
     * The message is only displayed if the settings page is
     * the current page and the settings have been updated.
     *
     * @since 1.0.0
     */
    public function show_shortcode_notification() {
        // * Check if the 'page' and 'settings-updated' parameters are set in the URL and match the required values *
        if (isset($_GET['page']) && $_GET['page'] == 'post-grid-shortcode-settings' && isset($_GET['settings-updated'])) {
            // * Display an updated message, escaping the translated text for security *
            echo '<div class="updated"><p>' . esc_html__('Shortcode Output: [post-grid]', 'post-grid-shortcode') . '</p></div>';
        }
    }

    /**
     * Process form submission securely with nonce verification.
     *
     * @since 1.0.0
     */
    public function process_form_submission() {
        if (isset($_POST['pgs_post_form_data'])) {
            // Verify nonce before processing the form submission
            if (!isset($_POST['post_grid_nonce_name']) || !wp_verify_nonce($_POST['post_grid_nonce_name'], 'post_grid_nonce_action')) {
                wp_die(__('Security check failed', 'post-grid-shortcode'));
            }

            // Now, process form data after verification
            $form_data = sanitize_text_field($_POST['pgs_post_form_data']);
            // Handle your form submission logic here...
        }
    }
}
