# Classes for use with plugins

This repository contains classes that can be used when creating Wordpress plugins.

## Singleton

There is a Singleton class that can be used like this:

```php
namespace Vendor;

use Cyclonecode\Plugin\Common\Singleton;

class Example_Plugin extends Singleton
{
  // Called when instance is created
  public function init()
  {
    add_action('admin_menu', array($this, 'doMenu'));
  }
}
```

Then in your main plugin file you get an instance like this:

```php
<?php

/**
 * Plugin Name: Example Plugin
 * Description: Example Plugin Description.
 * Version: 1.0.0
 * Author: YOU
 * Text Domain: example-plugin
**/
 
namespace Vendor;

require_once __DIR__ . '/vendor/autoload.php';

add_action('plugins_loaded', function () {
    Example_Plugin::getInstance();
});
```

## Settings

The Settings class can be used to handle the configuration for your plugin.

Example:

```php
namespace Vendor;

use Cyclonecode\Plugin\Common\Singleton;
use Cyclonecode\Plugin\Settings\Settings;

class Example_Plugin extends Singleton
{
   const SETTINGS_NAME = 'example_plugin_settings';
   const TEXT_DOMAIN = 'example-plugin';
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
      $this->settings->setFromArray($this->getDefaultSettings());
      add_action('admin_menu', array($this, 'doMenu'));
      add_action('admin_post_example_plugin_save_settings', array($this, 'saveSettings'));
   }
   
   public function doMenu()
   {
        add_submenu_page(
            'tools.php',
            __('Example Plugin', self::TEXT_DOMAIN),
            __('Example Plugin', self::TEXT_DOMAIN),
            'manage_options',
            'example-plugin',
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
       
       wp_safe_redirect(add_query_arg(array(
         'page' => 'example-plugin',
       ), 'tools.php'));
   }
}
```

## Request

The Request class can be used to make HTTP requests.

Example:

```php
namespace Vendor;

use Cyclonecode\Plugin\Common\Singleton;
use Cyclonecode\Plugin\Http\RequestInterface;
use Cyclonecode\Plugin\Http\RemoteRequest;

class Example_Plugin extends Singleton
{
    /** @var RequestInterface */
    private $request;
    
    public function init()
    {
      $this->request = new RemoteRequest();
    }
    
    public function saveSettings()
    {
      // Check so the supplied URL is ok.
      try {
        $url = 'https://example.com';
        $response = $this->request->get(
          $url,
          array(
            'body' => array(
              'foo' => 'bar',
            ),
          )
        );
        // Store response in transient
        set_transient('tmp', $response);
      } catch (\Exception $e) {
        // Something went wrong
        var_dump($e->getMessage() . ' ' . $e->getCode());
      }
   }
}
```

## Cache

There is a Transient class that implements the CacheInterface and is used to set and get transient data.

Example:

```php
namespace Vendor;

use Cyclonecode\Plugin\Common\Singleton;
use Cyclonecode\Plugin\Http\RequestInterface;
use Cyclonecode\Plugin\Http\RemoteRequest;
use Cyclonecode\Plugin\Cache\Transient;

class Example_Plugin extends Singleton
{
    /** @var RequestInterface */
    private $request;

    /** @var CacheInterface */
    private $cache;
    
    public function init()
    {
      $this->request = new RemoteRequest();
      $this->cache = Transient::getInstance();
    }
    
    public function saveSettings()
    {
      $key = 'Example_Plugin_Saving';
      if ($this->cache->exists($key)) {
        // We already doing stuff.
        return;
      }
    
      $this->cache->set($key, 1);
      // Check so the supplied URL is ok.
      try {
        $url = 'https://example.com';
        $response = $this->request->get(
          $url,
          array(
            'body' => array(
              'foo' => 'bar',
            ),
          )
        );
        // Store response in transient
        $this->cache->set('tmp', $response);
      } catch (\Exception $e) {
        // Something when wrong
        var_dump($e->getMessage() . ' ' . $e->getCode());
      }
      $this->cache->delete($key);
   }
}
```
