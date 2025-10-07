<?php
/**
 * Revenue Tracking and Monetization Features
 *
 * @package YouTuneAi
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Revenue Analytics Dashboard Widget
 */
function youtuneai_revenue_dashboard_widget() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    ?>
    <div class="youtuneai-revenue-dashboard">
        <h3><?php _e( 'Revenue Overview', 'youtuneai' ); ?></h3>
        
        <?php if ( class_exists( 'WooCommerce' ) ) : ?>
            <div class="revenue-stats">
                <?php
                $orders = wc_get_orders( array(
                    'limit'  => -1,
                    'status' => array( 'completed', 'processing' ),
                    'date_created' => '>' . ( time() - MONTH_IN_SECONDS ),
                ) );
                
                $total_revenue = 0;
                foreach ( $orders as $order ) {
                    $total_revenue += $order->get_total();
                }
                ?>
                <div class="stat-box">
                    <h4><?php _e( 'This Month', 'youtuneai' ); ?></h4>
                    <p class="amount"><?php echo wc_price( $total_revenue ); ?></p>
                    <p class="orders"><?php echo count( $orders ); ?> <?php _e( 'orders', 'youtuneai' ); ?></p>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if ( get_theme_mod( 'enable_ads', false ) ) : ?>
            <div class="ads-status">
                <h4><?php _e( 'AdSense Status', 'youtuneai' ); ?></h4>
                <p class="status-active">
                    <span class="dashicons dashicons-yes-alt"></span>
                    <?php _e( 'Active', 'youtuneai' ); ?>
                </p>
                <p class="publisher-id">
                    <?php _e( 'Publisher ID:', 'youtuneai' ); ?> 
                    <?php echo esc_html( get_theme_mod( 'google_adsense_id' ) ); ?>
                </p>
            </div>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Add Revenue Dashboard Widget
 */
function youtuneai_add_revenue_widget() {
    wp_add_dashboard_widget(
        'youtuneai_revenue_widget',
        __( 'YouTuneAi Revenue Tracking', 'youtuneai' ),
        'youtuneai_revenue_dashboard_widget'
    );
}
add_action( 'wp_dashboard_setup', 'youtuneai_add_revenue_widget' );

/**
 * Affiliate Link Shortcode
 */
function youtuneai_affiliate_link( $atts, $content = null ) {
    $atts = shortcode_atts( array(
        'url'    => '',
        'text'   => '',
        'nofollow' => 'yes',
        'target' => '_blank',
    ), $atts );

    if ( empty( $atts['url'] ) ) {
        return '';
    }

    $text = ! empty( $atts['text'] ) ? $atts['text'] : $atts['url'];
    $rel = $atts['nofollow'] === 'yes' ? 'nofollow noopener' : 'noopener';

    return sprintf(
        '<a href="%s" target="%s" rel="%s" class="affiliate-link">%s</a>',
        esc_url( $atts['url'] ),
        esc_attr( $atts['target'] ),
        esc_attr( $rel ),
        esc_html( $text )
    );
}
add_shortcode( 'affiliate', 'youtuneai_affiliate_link' );

/**
 * Revenue Report Generator
 */
function youtuneai_generate_revenue_report() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( __( 'Unauthorized access', 'youtuneai' ) );
    }

    $report = array(
        'period'    => date( 'F Y' ),
        'generated' => current_time( 'mysql' ),
        'sources'   => array(),
    );

    // WooCommerce Revenue
    if ( class_exists( 'WooCommerce' ) ) {
        $orders = wc_get_orders( array(
            'limit'        => -1,
            'status'       => array( 'completed' ),
            'date_created' => '>' . ( time() - MONTH_IN_SECONDS ),
        ) );

        $wc_revenue = 0;
        foreach ( $orders as $order ) {
            $wc_revenue += $order->get_total();
        }

        $report['sources']['woocommerce'] = array(
            'name'    => 'WooCommerce Sales',
            'revenue' => $wc_revenue,
            'orders'  => count( $orders ),
        );
    }

    // AdSense (placeholder - requires Google AdSense API integration)
    if ( get_theme_mod( 'enable_ads', false ) ) {
        $report['sources']['adsense'] = array(
            'name'    => 'Google AdSense',
            'status'  => 'active',
            'note'    => 'Connect Google AdSense API for detailed reporting',
        );
    }

    return $report;
}

/**
 * AJAX Handler for Revenue Data
 */
function youtuneai_ajax_get_revenue_data() {
    check_ajax_referer( 'youtuneai-nonce', 'nonce' );

    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( __( 'Unauthorized', 'youtuneai' ) );
    }

    $report = youtuneai_generate_revenue_report();
    wp_send_json_success( $report );
}
add_action( 'wp_ajax_youtuneai_revenue_data', 'youtuneai_ajax_get_revenue_data' );

/**
 * Add Revenue Menu Page
 */
function youtuneai_add_revenue_menu() {
    add_theme_page(
        __( 'Revenue Tracking', 'youtuneai' ),
        __( 'Revenue', 'youtuneai' ),
        'manage_options',
        'youtuneai-revenue',
        'youtuneai_revenue_page'
    );
}
add_action( 'admin_menu', 'youtuneai_add_revenue_menu' );

/**
 * Revenue Page Content
 */
function youtuneai_revenue_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $report = youtuneai_generate_revenue_report();
    ?>
    <div class="wrap youtuneai-revenue-page">
        <h1><?php _e( 'Revenue Tracking & Monetization', 'youtuneai' ); ?></h1>
        
        <div class="revenue-overview">
            <h2><?php _e( 'Revenue Report', 'youtuneai' ); ?> - <?php echo esc_html( $report['period'] ); ?></h2>
            
            <?php if ( ! empty( $report['sources'] ) ) : ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e( 'Revenue Source', 'youtuneai' ); ?></th>
                            <th><?php _e( 'Amount', 'youtuneai' ); ?></th>
                            <th><?php _e( 'Details', 'youtuneai' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $report['sources'] as $key => $source ) : ?>
                            <tr>
                                <td><strong><?php echo esc_html( $source['name'] ); ?></strong></td>
                                <td>
                                    <?php 
                                    if ( isset( $source['revenue'] ) ) {
                                        echo wc_price( $source['revenue'] );
                                    } else {
                                        echo '—';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                    if ( isset( $source['orders'] ) ) {
                                        echo esc_html( $source['orders'] ) . ' ' . __( 'orders', 'youtuneai' );
                                    } elseif ( isset( $source['note'] ) ) {
                                        echo esc_html( $source['note'] );
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p><?php _e( 'No revenue sources configured yet.', 'youtuneai' ); ?></p>
            <?php endif; ?>
        </div>

        <div class="revenue-settings">
            <h2><?php _e( 'Monetization Settings', 'youtuneai' ); ?></h2>
            <p><?php _e( 'Configure your revenue settings in the', 'youtuneai' ); ?> 
                <a href="<?php echo admin_url( 'customize.php?autofocus[section]=youtuneai_revenue' ); ?>">
                    <?php _e( 'Theme Customizer', 'youtuneai' ); ?>
                </a>
            </p>
        </div>
    </div>
    <?php
}
