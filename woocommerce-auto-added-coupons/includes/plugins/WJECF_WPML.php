<?php

// phpcs:ignore

if (defined('ABSPATH') && !class_exists('WJECF_WPML')) {
    /**
     * Class to make WJECF compatible with WPML.
     */
    class WJECF_WPML extends Abstract_WJECF_Plugin
    {
        public function __construct()
        {
            $this->set_plugin_data(
                [
                    'description' => __('Compatiblity with WPML.', 'woocommerce-jos-autocoupon'),
                    'dependencies' => [],
                    'can_be_disabled' => true,
                ],
            );
        }

        public function init_hook()
        {
            global $sitepress;
            if (isset($sitepress)) {
                // WJECF_Controller hooks
                add_filter('wjecf_get_product_id', [$this, 'filter_get_product_id'], 10);
                add_filter('wjecf_get_product_ids', [$this, 'filter_get_product_ids'], 10);
                add_filter('wjecf_get_product_cat_id', [$this, 'filter_get_product_cat_id'], 10);
                add_filter('wjecf_get_product_cat_ids', [$this, 'filter_get_product_cat_ids'], 10);
                add_filter('woocommerce_coupon_get_description', [$this, 'filter_get_coupon_description'], 10, 2);
            }
        }

        // HOOKS

        public function filter_get_product_ids($product_ids)
        {
            return $this->get_translated_object_ids($product_ids, 'product');
        }

        public function filter_get_product_cat_ids($product_cat_ids)
        {
            return $this->get_translated_object_ids($product_cat_ids, 'product_cat');
        }

        public function filter_get_product_id($product_id)
        {
            return $this->get_translated_object_id($product_id, 'product');
        }

        public function filter_get_coupon_description($description, $object)
        {
            // phpcs:ignore
            return __($description, 'woocommerce-jos-autocoupon');
        }

        // FUNCTIONS

        /**
         * Get the ids of all the translations. Otherwise return the original array.
         *
         * @param mixed $object_ids
         * @param mixed $object_type
         *
         * @return array The product ids of all translations
         */
        public function get_translated_object_ids($object_ids, $object_type)
        {
            // Make sure it's an array
            if (!is_array($object_ids)) {
                $object_ids = [$object_ids];
            }

            $translated_object_ids = [];
            foreach ($object_ids as $object_id) {
                $translated_object_ids[] = apply_filters('wpml_object_id', $object_id, $object_type, true); // true: return original if missing.
            }
            $this->log('debug', 'Translated '.$object_type.': '.implode(',', $object_ids).' to: '.implode(',', $translated_object_ids));

            return $translated_object_ids;
        }

        /**
         * Get translated object id.
         *
         * @param int    $object_id
         * @param string $object_type
         *
         * @return bool|int false if not found
         */
        public function get_translated_object_id($object_id, $object_type)
        {
            $translated_object_ids = $this->get_translated_object_ids([$object_id], $object_type);
            if (empty($translated_object_ids)) {
                return false;
            }

            return reset($translated_object_ids);
        }
    }
}
