# Project Marvel Memories

![PHP](https://img.shields.io/badge/PHP-v8.1-828cb7.svg?style=flat-square)
[![marvel](https://img.shields.io/badge/Marvel-828cb7.svg?color=FF2D20)](https://developer.marvel.com/)
[![Badge Twitter](https://img.shields.io/endpoint?url=https%3A%2F%2Ftwbadges.glitch.me%2Fbadges%2Fv2)](https://developer.twitter.com/en/docs/twitter-api)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](licence.md)

A fanmade project in PHP using [API Twitter V2](https://github.com/noweh/twitter-api-v2-php), [Marvel API](https://developer.marvel.com/) and [Github action scheduler](https://github.com/marketplace/actions/schedule-job-action).

## What about?

Posts a random cover with details about Marvel comics history to a Twitter account every hour.

### Procedures

A [file](.github/workflows/run-schedule.yml) in .github/workflows/ folder run the "php project/[run.php](project/run.php)" script every hour.

This script can be manually executed.


### Example:

<div>
    <a href="https://twitter.com/SteveBOTgers/status/1468969530781175816">
        <img alt="Tweet example" width="500px" src="https://raw.githubusercontent.com/noweh/project-marvel-memories/master/assets/tweet-example.png" />
    </a>
</div>
