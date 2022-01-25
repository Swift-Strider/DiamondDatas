# DiamondDatas Pocketmine Virion

<p align=center>This is a configuration virion that simplifies validating✅ and documenting📝 configs</p>

It provides a caching `NeoConfig` class that annotates your yaml files with comments defined in source code. For Example:

```php
class PluginSettings implements IDefaultProvider
{
    /* Important, mark field as public! */
    #[IntType("message-repeat-count", "How many times to repeat message")]
    public int $msgRepeatCount;

    public static function getDefaults(): array
    {
        return [
            "message-repeat-count" => 5
        ]
    }
}
```

<hr>

```php
class Plugin
{
    public function onEnable() {
        $settings = new NeoConfig($this->getDataFolder() . "config.yml", PluginSettings::class);
        /* @var PluginSettings $config */
        $config = $settings->getObject(true); // true means to always fetch from disk, passing no parameters defaults to false
        $this->getLogger()->log("Your number is " . $config->msgRepeatCount);
    }
}
```

NeoConfig is the main addition of this virion. It has the following methods

```php
public function getObject(bool $reload = false): object { ... }
public function setObject(object $object): void { ... }
```

where object **must** be an `instanceOf` the class-string you construct the NeoConfig with.

See [examples](/examples) for complete examples. 🛠
