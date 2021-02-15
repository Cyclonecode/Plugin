# Classes for use with plugins

This repository contains classes that can be used when creating Wordpress plugins.

## Singleton

There is a Singleton class that can be used like this:

```php
namespace Vendor;

class Example_Plugin extends \Cyclonecode\Plugin\Singleton
{
  public function init()
  {
    // Triggered when instance is created
    add_action('admin_menu', array($this, 'doMenu'));
  }
}
```

Then in your main plugin file you get an instance like this:

```php
add_action('plugins_loaded', function () {
    \Vendor\Example_Plugin\::getInstance();
});
```

## Settings

The Settings class can be used to handle the configuration for your plugin.

Example:

```php
namespace Vendor;

use Cyclonecode\Plugin\Settings;

class Example_Plugin extends Cyclonecode\Plugin\Singleton
{
   const SETTINGS_NAME = 'example_plugin_settings';
   const VERSION = '1.0.0';

   protected $settings;

   public static function delete()
   {
     $settings = new Settings(self::SETTINGS_NAME);
     $settings->delete();
   }
   
   public function init()
   {
      // Create and make sure we have default settings
      $this->settings = new Settings(self::SETTINGS_NAME);
      $this->settings->setFromArray($this->getDefaults());
      add_action('admin_menu', array($this, 'doMenu');
      add_action('admin_post_example_plugin_save_settings', array($this, 'saveSettings');
   }
   
   public function doMenu()
   {
        add_submenu_page(
            'tools.php',
            __('Example Plugin', self::TEXT_DOMAIN),
            __('Example Plugin', self::TEXT_DOMAIN),
            'manage_options',
            'example_plugin',
            array($this, 'doSettingsPage')
        );
   }
  
   public function getDefaultSettings()
   {
     return array(
       'apiKey' => '',
       'version' => self::VERSION,
     );
   }
   
   public function doSettingsPage()
   {
   ?>
   <div class="wrap">
   <form method="POST" action="<?php echo admin_url('admin-post.php'); ?>">
   <input type="hidden" name="action" value="example_plugin_save_settings" />
   <?php wp_nonce_field('example_plugin_action'); ?>
   <input type="text" name="apiKey" value="<?php echo $this->settings->get('apiKey'); ?>" />
   <?php echo get_submit_button(__('Save', self::TEXT_DOMAIN)); ?>
   </form>
   </div>
   <?php
   }
   
   public function saveSettings()
   {
      check_admin_referer('example_plugin_action');
      
      // Filter and save settings
      $this->settings
        ->set('apiKey', filter_input(
          INPUT_POST,
          'apiKey',
          FILTER_SANITIZE_STRING
        ))
       ->save();
   }
}
```
