/**
 * YouTuneAi Theme JavaScript
 *
 * @package YouTuneAi
 * @since 1.0.0
 */

(function($) {
    'use strict';

    // Mobile Menu Toggle
    $('.menu-toggle').on('click', function() {
        $(this).toggleClass('active');
        $('.main-navigation ul').toggleClass('active');
        $('body').toggleClass('menu-open');
    });

    // Smooth Scroll for Anchor Links
    $('a[href*="#"]').not('[href="#"]').not('[href="#0"]').on('click', function(event) {
        if (location.pathname.replace(/^\//, '') === this.pathname.replace(/^\//, '') &&
            location.hostname === this.hostname) {
            var target = $(this.hash);
            target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
            
            if (target.length) {
                event.preventDefault();
                $('html, body').animate({
                    scrollTop: target.offset().top - 80
                }, 1000);
            }
        }
    });

    // Header Scroll Effect
    $(window).on('scroll', function() {
        var scroll = $(window).scrollTop();
        if (scroll >= 100) {
            $('.site-header').addClass('scrolled');
        } else {
            $('.site-header').removeClass('scrolled');
        }
    });

    // Add to Cart AJAX (WooCommerce)
    $(document.body).on('added_to_cart', function(event, fragments, cart_hash, button) {
        // Update cart count
        $('.cart-count').text(fragments.cart_count || 0);
        
        // Show notification
        showNotification('Product added to cart!', 'success');
    });

    // Notification System
    function showNotification(message, type) {
        var notification = $('<div class="notification ' + type + '">' + message + '</div>');
        $('body').append(notification);
        
        setTimeout(function() {
            notification.addClass('show');
        }, 100);
        
        setTimeout(function() {
            notification.removeClass('show');
            setTimeout(function() {
                notification.remove();
            }, 300);
        }, 3000);
    }

    // Lazy Load Images
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver(function(entries, observer) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    const src = img.getAttribute('data-src');
                    
                    if (src) {
                        img.src = src;
                        img.removeAttribute('data-src');
                        imageObserver.unobserve(img);
                    }
                }
            });
        });

        document.querySelectorAll('img[data-src]').forEach(function(img) {
            imageObserver.observe(img);
        });
    }

    // Form Validation
    $('form').on('submit', function(e) {
        var form = $(this);
        var isValid = true;

        form.find('input[required], textarea[required]').each(function() {
            if (!$(this).val()) {
                $(this).addClass('error');
                isValid = false;
            } else {
                $(this).removeClass('error');
            }
        });

        if (!isValid) {
            e.preventDefault();
            showNotification('Please fill in all required fields', 'error');
        }
    });

    // Back to Top Button
    var backToTop = $('<button class="back-to-top" aria-label="Back to top">↑</button>');
    $('body').append(backToTop);

    $(window).on('scroll', function() {
        if ($(window).scrollTop() > 300) {
            backToTop.addClass('show');
        } else {
            backToTop.removeClass('show');
        }
    });

    backToTop.on('click', function() {
        $('html, body').animate({
            scrollTop: 0
        }, 800);
    });

    // Initialize on Document Ready
    $(document).ready(function() {
        // Add animation classes on scroll
        const animateElements = document.querySelectorAll('.feature-card, .post-card, .product-card');
        
        if ('IntersectionObserver' in window) {
            const animationObserver = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-in');
                        animationObserver.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1
            });

            animateElements.forEach(function(element) {
                animationObserver.observe(element);
            });
        }

        console.log('YouTuneAi Theme Loaded!');
    });

})(jQuery);
