<?php
defined('ABSPATH') || exit;

class Post_Grid_Shortcode {

    public function init() {
        add_action('admin_menu', [$this, 'register_settings_page']);
        add_action('admin_init', [$this, 'register_settings']);
    
        // Enqueue assets based on context
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
    
        add_shortcode('post-grid', [$this, 'render_post_grid']);
        add_action('admin_notices', [$this, 'show_shortcode_notification']);
    }

    public function register_settings_page() {
        add_options_page(
            __('Post Grid Shortcode Settings', 'post-grid-shortcode'),
            __('Post Grid Shortcode', 'post-grid-shortcode'),
            'manage_options',
            'post-grid-shortcode-settings',
            [$this, 'render_settings_page']
        );
    }


    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Post Grid Shortcode Settings', 'post-grid-shortcode'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('pgs_settings_group');
                do_settings_sections('post-grid-shortcode-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

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

    public function render_posts_per_page() {
        $value = get_option('pgs_posts_per_page', 5);
        echo "<input type='number' name='pgs_posts_per_page' value='" . esc_attr($value) . "' />";
    }

    public function render_orderby() {
        $value = get_option('pgs_orderby', 'date');
        ?>
        <select name="pgs_orderby">
            <option value="date" <?php selected($value, 'date'); ?>><?php _e('Date', 'post-grid-shortcode'); ?></option>
            <option value="title" <?php selected($value, 'title'); ?>><?php _e('Title', 'post-grid-shortcode'); ?></option>
            <option value="author" <?php selected($value, 'author'); ?>><?php _e('Author', 'post-grid-shortcode'); ?></option>
            <option value="modified" <?php selected($value, 'modified'); ?>><?php _e('Modified', 'post-grid-shortcode'); ?></option>
        </select>
        <?php
    }

    public function render_order() {
        $value = get_option('pgs_order', 'ASC');
        ?>
        <select name="pgs_order">
            <option value="ASC" <?php selected($value, 'ASC'); ?>><?php _e('Ascending', 'post-grid-shortcode'); ?></option>
            <option value="DESC" <?php selected($value, 'DESC'); ?>><?php _e('Descending', 'post-grid-shortcode'); ?></option>
        </select>
        <?php
    }

    public function render_post_type() {
        $value = get_option('pgs_post_type', 'post');
        echo "<input type='text' name='pgs_post_type' value='" . esc_attr($value) . "' />";
        echo '<p class="description">' . __('Default is "post", but you can specify custom post types.', 'post-grid-shortcode') . '</p>';
    }

    public function render_post_status() {
        $value = get_option('pgs_post_status', 'publish');
        ?>
        <select name="pgs_post_status">
            <option value="publish" <?php selected($value, 'publish'); ?>><?php _e('Published', 'post-grid-shortcode'); ?></option>
            <option value="draft" <?php selected($value, 'draft'); ?>><?php _e('Draft', 'post-grid-shortcode'); ?></option>
            <option value="pending" <?php selected($value, 'pending'); ?>><?php _e('Pending', 'post-grid-shortcode'); ?></option>
        </select>
        <?php
    }

    public function render_show_featured_image() {
        $value = get_option('pgs_show_featured_image', 1);
        echo "<input type='checkbox' name='pgs_show_featured_image' value='1' " . checked(1, $value, false) . " />";
        echo '<p class="description">' . __('Check to show the featured image for each post.', 'post-grid-shortcode') . '</p>';
    }

    public function render_enable_read_more_button() {
        $is_enabled = get_option('pgs_enable_read_more_button', 0);
        $read_more_text = get_option('pgs_read_more_text', __('Read More', 'post-grid-shortcode'));
        ?>
        <div>
            <input type="checkbox" name="pgs_enable_read_more_button" value="1" <?php checked(1, $is_enabled); ?> data-toggle-read-more />
            <label><?php _e('Enable Read More Button', 'post-grid-shortcode'); ?></label>
            <input type="text" name="pgs_read_more_text" value="<?php echo esc_attr($read_more_text); ?>" <?php if(!$is_enabled) echo 'style="display:none;"'; ?> placeholder="<?php _e('Read More Text', 'post-grid-shortcode'); ?>" />
        </div>
        <p class="description"><?php _e('Enable the "Read More" button and specify the button text if enabled.', 'post-grid-shortcode'); ?></p>
        <?php
    }
    
    

    public function render_excerpt_length() {
        $value = get_option('pgs_excerpt_length', 20);
        echo "<input type='number' name='pgs_excerpt_length' value='" . esc_attr($value) . "' />";
        echo '<p class="description">' . __('Set the number of words for the excerpt.', 'post-grid-shortcode') . '</p>';
    }

// Enqueue styles and scripts
public function enqueue_assets() {
    // Front-end: Load the stylesheet
    if (!is_admin()) {
        wp_enqueue_style('pgs-style', PGS_PLUGIN_URL . 'assets/css/post-grid-shortcode-admin.css', [], PGS_VERSION);
    }

    // Admin area: Load the stylesheet and script on the settings page
    if (is_admin() && isset($_GET['page']) && $_GET['page'] === 'post-grid-shortcode-settings') {
        //wp_enqueue_style('pgs-admin-style', PGS_PLUGIN_URL . 'assets/css/admin-style.css', [], PGS_VERSION);
        wp_enqueue_script('pgs-script', PGS_PLUGIN_URL . 'assets/js/post-grid-shortcode-admin.js', ['jquery'], PGS_VERSION, true);
    }
}

    
    

    public function render_post_grid($atts) {
        // Retrieve settings from the plugin options
        $posts_per_page = get_option('pgs_posts_per_page', 5);
        $orderby = get_option('pgs_orderby', 'date');
        $order = get_option('pgs_order', 'ASC');
        $post_type = get_option('pgs_post_type', 'post');
        $post_status = get_option('pgs_post_status', 'publish');
        $show_featured_image = get_option('pgs_show_featured_image', 1);
        $read_more_enabled = get_option('pgs_enable_read_more_button', 0);
        $read_more_text = get_option('pgs_read_more_text', __('Read More', 'post-grid-shortcode'));
        $excerpt_length = get_option('pgs_excerpt_length', 20);
    
        // Get the current page number if available
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    
        // Custom WP Query with pagination
        $query = new WP_Query([
            'posts_per_page' => $posts_per_page,
            'paged' => $paged,
            'orderby' => $orderby,
            'order' => $order,
            'post_type' => $post_type,
            'post_status' => $post_status,
        ]);
    
        // If there are no posts, return a message
        if (!$query->have_posts()) {
            return __('No posts found', 'post-grid-shortcode');
        }
    
        // Start output buffering
        ob_start();
        ?>
        <div class="post-grid">
            <?php while ($query->have_posts()): $query->the_post(); ?>
                <div class="post-item">
                    <?php if ($show_featured_image && has_post_thumbnail()): ?>
                        <div class="post-thumbnail">
                            <?php the_post_thumbnail('thumbnail'); ?>
                        </div>
                    <?php endif; ?>
                    <div class="post-content">
                        <h2 class="post-title">
                            <a href="<?php the_permalink(); ?>" class="post-link">
                                <?php the_title(); ?>
                            </a>
                        </h2>
                        <p class="post-excerpt">
                            <?php echo wp_trim_words(get_the_excerpt(), $excerpt_length, '...'); ?>
                        </p>
                        <?php if ($read_more_enabled): ?>
                            <a href="<?php the_permalink(); ?>" class="read-more-btn">
                                <?php echo esc_html($read_more_text); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    
        <div class="pagination">
            <?php
            echo paginate_links([
                'total' => $query->max_num_pages,
                'current' => $paged,
                'prev_text' => __('« Previous', 'post-grid-shortcode'),
                'next_text' => __('Next »', 'post-grid-shortcode')
            ]);
            ?>
        </div>
        <?php
    
        // Reset post data after custom query
        wp_reset_postdata();
    
        // Return the buffered output
        return ob_get_clean();
    }
    

    public function show_shortcode_notification() {
        if (isset($_GET['page']) && $_GET['page'] == 'post-grid-shortcode-settings' && isset($_GET['settings-updated'])) {
            echo '<div class="updated"><p>' . __('Shortcode Output: [post-grid]', 'post-grid-shortcode') . '</p></div>';
        }
    }
    
}