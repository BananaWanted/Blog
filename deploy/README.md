# Deploy

This folder contains deploy and maintenance tools and required server settings for the site.

## Command Line tools

### `quick-install`
Run this bash script to install/reinstall/update the maintenance tools, as well as the server setting files.

### `blog-deploy`
Installed by the `quick-install` script.

Use this command to deploy the site.

This command will update the site files in the server document root directory, and call `blog-fixpermission`.

### `blog-fixpermission`
Installed by the `quick-install` script.

Use this command to recursively fix permission of the server document root directory. That is, assign mode 644 to regular files and mode 755 to directories.

## Server settings
The server settings includes nginx setting files. These files will be installed by the `quick-install` script automatically. The most important part is described below.

### `nginx.conf`
Nginx configure file.

### `conf.d`
Will be included in the `http{....}` section of the `nginx.conf` file.

### `default.d`
Will be included in the `http{server{....}}` section of the `nginx.conf` file.

### `default.d/markdown.conf`
Wrap all `.md` request to the `markdown.php` script using `proxy` directive. For example, an http request to `http://this.site/articles/001.md` will be proxied to `http://127.0.0.1/markdown.php?path=articles/001.md`.

Thanks to the proxy feature of nginx, I can just work with a normal `.md` to write my blog post, and leaves the job of generate the web page to the `markdown.php` script completely. Besides, I can also link to other articles in my blog post with just an simple site URI. For example, "[/index.md](/index.md)" points to the `index.md` page locate in the server root directory. And "[/articles/013](/articles/013)" points to an normal blog article.

Unlike [Jekyll](https://github.com/jekyll/jekyll), it's not required to add a "metadata section" to the header of the `.md` file. So any standard markdown document would work fine in the site, since an optional **metadata block** can also be used to provide additional information.

Check [this post](/articles/014.md) to learn more about the **metadata block**.
