# WP Plugin - calculator

*IMPORTANT*:
- Plugin uses WP RestApi so please, make sure that pretty urls are enabled on your WP environment.
- Designed for (and tested on) Wordpress 6.6 with PHP 7.4

## Repository structure
- `/` - root directory where all enviroment files resides
- `/plugin-dev/` - main file - plugin development
- `/plugin-front/` - contains webpack and frontend assets
- `/wordpress/` - bedrock wordpress starter to run sample wordpress to test plugin
- `/docker-compose.yml` - Wordpress docker configuration file
- `/server/` - contains configuration files for nginx server
- `/php/` - contains PHP configuration files

## How to test the plugin?
If you want to test plugin on your own WP instance then just take .zip package from the repo and install it in your Wordpress plugin dashboard.

0. Install the plugin in your WP instance.
1. Create a page.
2. Add shortcode gutenberg block to a page.
3. Paste the shortcode: `[ngc_calc_form]`.
4. The form should show.
5. To see saved results go to the admin dashboard and check "Calcultations" CPT post list. Edit post to see saved ACF values.

If you want to test plugin with my docker WP starter then you can just start docker project - the `plugin-dev` dir is binded to the Wordpress' plugin dir in `docker-compose.yml`.

## Extra

### Docker

You can test plugin using my Docker wordpress starter setup.
Please read this section espacially if you are unfamiliar with Docker.

#### Installation

To install Docker itself please download and install package from [docker.com](https://www.docker.com/products/overview).

#### Usage

To boot Docker, run command from project's root directory:

```bash
#!bash

docker compose up
```

After finished work it's good to stop all project's containers with command:

```bash
#!bash

docker compose stop
```

#### (optional) Initialize WP env with docker

- setup environment for your project (configure `.env` file in `/website/.env`). Example file is in `/website/.env.example`
- run `docker-compose up`
- install composer depedencies (/weordpress/composer.json) using docker php container terminal `docker exec -it your-project-php-1 /bin/sh`
- docker will be able on `localhost:8000` -> you can use it to login to admin dashboard and configure app

## Author

- Tua Smile
