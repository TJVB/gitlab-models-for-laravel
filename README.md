# GitLab Models for Laravel

[![pipeline status](https://gitlab.com/tjvb/gitlab-models-for-laravel/badges/master/pipeline.svg)](https://gitlab.com/tjvb/gitlab-models-for-laravel/commits/master)
[![coverage report](https://gitlab.com/tjvb/gitlab-models-for-laravel/badges/master/coverage.svg)](https://gitlab.com/tjvb/gitlab-models-for-laravel/commits/master)

GitLab push data for different objects with its webhook, this package provides the option to store that data in your database. Under the hood it use [tjvb/gitlab-webhooks-receiver-for-laravel](https://gitlab.com/tjvb/gitlab-webhooks-receiver-for-laravel/) for receiving the webhook data. After storing the data it will dispatch an event that can be used to update other parts of your data or react on the new input.

## Installation

1. You can install the package via composer:
```bash
composer require tjvb/gitlab-models-for-laravel
```

2. You can publish and run the migrations for GitLab Models for Laravel and GitLab Webhooks with:

```bash
php artisan vendor:publish --provider="TJVB\GitLabWebhooks\GitLabWebhooksServiceProvider" --tag="migrations"
php artisan vendor:publish --provider="TJVB\GitlabModelsForLaravel\Providers\GitlabModelsProvider" --tag="migrations"
php artisan migrate
```

3. Set the `GITLAB_WEBHOOK_SECRET` env variable (most used version is to set it in the .env file) to have the token you use for your webhook. See [https://docs.gitlab.com/ee/user/project/integrations/webhooks.html#secret-token](https://docs.gitlab.com/ee/user/project/integrations/webhooks.html#secret-token "the GitLab documentation") for more information.
4. Create a webhook in GitLab.
   You can create a webhook in GitLab for your project, group or system. The default url is `<application.tld>/gitlabwebhook` this can be changed in the configuration from [tjvb/gitlab-webhooks-receiver-for-laravel](https://gitlab.com/tjvb/gitlab-webhooks-receiver-for-laravel/).
5. Optional configure the package.
6. Listen to the events

### Manual register the service provider.
If you disable the package discovery you need to add `\TJVB\GitlabModelsForLaravel\Providers\GitlabModelsProvider::class,` and `\TJVB\GitLabWebhooks\GitLabWebhooksServiceProvider::class,` to the providers array in `config/app.php`.


## Events

The package dispatched multiple events after saving the received data (a create or update) The event contains the new data.

* `TJVB\GitlabModelsForLaravel\Events\BuildDataReceived`
* `TJVB\GitlabModelsForLaravel\Events\DeploymentDataReceived`
* `TJVB\GitlabModelsForLaravel\Events\IssueDataReceived`
* `TJVB\GitlabModelsForLaravel\Events\MergeRequestDataReceived`
* `TJVB\GitlabModelsForLaravel\Events\NoteDataReceived`
* `TJVB\GitlabModelsForLaravel\Events\PipelineDataReceived`
* `TJVB\GitlabModelsForLaravel\Events\ProjectDataReceived`
* `TJVB\GitlabModelsForLaravel\Events\TagDataReceived`


## Configuration

### Env variables
There are a couple of env variables that can be used to change the data that shoulds be stored.

| Key | Default | Description                                                                                       |
|-----|---------|---------------------------------------------------------------------------------------------------|
|`GITLAB_MODELS_QUEUE_CONNECTION`| null    | The queue connection for the HookStoredListener, if not provided it will use the project default. |
|`GITLAB_MODELS_QUEUE_QUEUE`| null    | The queue for the HookStoredListener, if not provided it will use the project default.      |
|`GITLAB_MODELS_STORE_BUILDS`| true    | That we want to store the data from the builds.                                                   |
|`GITLAB_MODELS_STORE_DEPLOYMENTS`| true    | That we want to store the data from the deployments.                                              |
|`GITLAB_MODELS_STORE_ISSUES`| true    | That we want to store the data from the issues.                                                   |
|`GITLAB_MODELS_STORE_MERGE_REQUESTS`| true    | That we want to store the data from the merge requests.                                           |
|`GITLAB_MODELS_STORE_NOTES`| true    | That we want to store the data from the notes (comments).                                         |
|`GITLAB_MODELS_STORE_PIPELINES`| true    | That we want to store the data from the pipelines,                                                |
|`GITLAB_MODELS_STORE_PROJECTS`| true    | That we want to store the data from the projects.                                                 |
|`GITLAB_MODELS_STORE_TAGS`| true    | That we want to store the data from the tags.                                                     |

### Customization
You can publish the config file with:
```bash
php artisan vendor:publish --provider="TJVB\GitlabModelsForLaravel\Providers\GitlabModelsProvider" --tag="config"
```

### Security
The default configuration validates the `X-Gitlab-Token` in the header of the webhook request. It is possible to add multiple values in the config file or disabling the middleware to stop the verification. If you need more validation for the request (as example and i.p. filter) you can add more middleware to the configuration.

## Changelog
We (try to) document all the changes in [CHANGELOG](CHANGELOG.md) so read it for more information.

## Contributing
You are very welcome to contribute, read about it in [CONTRIBUTING](CONTRIBUTING.md)

## Security
If you discover any security related issues, please email info@tjvb.nl instead of using the issue tracker.

## Credits

- [Tobias van Beek](https://tjvb.nl/about)
- [All Contributors](https://gitlab.com/tjvb/gitlab-models-for-laravel/-/graphs/master)

## Thanks to
- [GitLab](https://gitlab.com) for the great product, without that this package isn't needed.

## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

