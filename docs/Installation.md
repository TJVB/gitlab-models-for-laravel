# Installation

1. You can install the package via composer:
```bash
composer require tjvb/gitlab-models-for-laravel
```

2. You can publish and run the migrations for GitLab Models for Laravel and GitLab Webhooks with:

```bash
php artisan vendor:publish --provider="TJVB\GitLabWebhooks\GitLabWebhooksServiceProvider" --tag="migrations"
php artisan vendor:publish --provider="TJVB\GitlabModelsForLaravel\Providers" --tag="migrations"
php artisan migrate
```

3. Set the `GITLAB_WEBHOOK_SECRET` env variable (most used version is to set it in the .env file) to have the token you use for your webhook. See [https://docs.gitlab.com/ee/user/project/integrations/webhooks.html#secret-token](https://docs.gitlab.com/ee/user/project/integrations/webhooks.html#secret-token "the GitLab documentation") for more information.
4. Create a webhook in GitLab.
   You can create a webhook in GitLab for your project, group or system. The default url is `<application.tld>/gitlabwebhook` this can be changed in the configuration from [tjvb/gitlab-webhooks-receiver-for-laravel](https://gitlab.com/tjvb/gitlab-webhooks-receiver-for-laravel/).
5. Optional configure the package.
6. Listen to the events

### Manual register the service provider.
If you disable the package discovery you need to add `\TJVB\GitlabModelsForLaravel\Providers\GitlabModelsProvider::class,` and `\TJVB\GitLabWebhooks\GitLabWebhooksServiceProvider::class,` to the providers array in `config/app.php`.

