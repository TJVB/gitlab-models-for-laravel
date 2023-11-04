# Configuration

### Env variables
There are a couple of env variables that can be used to change the data that shoulds be stored.

| Key | Default | Description                                               |
|-----|---------|-----------------------------------------------------------|
|`GITLAB_MODELS_STORE_BUILDS`| true| That we want to store the data from the builds.           |
|`GITLAB_MODELS_STORE_DEPLOYMENTS`| true| That we want to store the data from the deployments.      |
|`GITLAB_MODELS_STORE_ISSUES`| true| That we want to store the data from the issues.           |
|`GITLAB_MODELS_STORE_MERGE_REQUESTS`| true| That we want to store the data from the merge requests.   |
|`GITLAB_MODELS_STORE_NOTES`| true| That we want to store the data from the notes (comments). |
|`GITLAB_MODELS_STORE_PIPELINES`| true| That we want to store the data from the pipelines,        |
|`GITLAB_MODELS_STORE_PROJECTS`| true| That we want to store the data from the projects.         |
|`GITLAB_MODELS_STORE_TAGS`| true| That we want to store the data from the tags.             |

### Customization
You can publish the config file with:
```bash
php artisan vendor:publish --provider="TJVB\GitlabModelsForLaravel\Providers\GitlabModelsProvider" --tag="config"
```

### Security
The default configuration validates the `X-Gitlab-Token` in the header of the webhook request. It is possible to add multiple values in the config file or disabling the middleware to stop the verification. If you need more validation for the request (as example and i.p. filter) you can add more middleware to the configuration.
