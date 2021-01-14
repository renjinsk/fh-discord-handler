###Installation:
Inside `composer.json` add:
```json
    "repositories": [
        {
            "type": "vcs",
            "no-api": false,
            "url": "git@github.com:renjinsk/fh-discord-handler.git"
        }
    ],
```
and then run `composer require renjinsk/discord-handler`

Add in `config/packages/<env>/monolog.yaml` the following:
```yaml
monolog:
    handlers:
      discord:
        type: service
        action_level: notice #Or the level you want
        id: RenjiNSK\DiscordHandlerBundle\Services\DiscordMonologHandlerService
```
Create a file inside `config/packages` named `discord_handler.yaml` and add the following
```yaml
discord_handler:
    webhook: '%env(resolve:DISCORD_WEBHOOK)%'
    name: 'MY_APP' # The title
    level: notice #Or the level you want
    subName: ':facepalm:' #OPTIONAL - Not used at the moment, but it's a subtitle next to the title you added
```
