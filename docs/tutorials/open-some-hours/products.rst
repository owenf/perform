Products
========
(Again, maybe more of an intro. 'Now that we've got our blank system to work with, we can begin to add features. We'll start with an inventory system to manage our shop products'. Just a bit more person-friendly? Easier to follow?)

Let's create a doctrine entity to represent products in the inventory, and use Perform to create an interface to manage them.

Add the maker bundle
--------------------

Symfony provides a handy ``maker-bundle`` to generate files quickly.
Let's add it to create the doctrine entity quickly.

.. code-block:: bash

   composer require --dev maker-bundle

Create a doctrine entity
------------------------

Run the ``make:entity`` command to generate the Product entity:

.. code-block:: bash

   ./bin/console make:entity Product

The command will prompt you for property names and their types.
Declare three fields with the following database types:

* ``name`` - `string`
* ``quantity`` - `integer`
* ``description`` - `text`

(Suggesting I create a migration with make:migration? I assume there's something later in the tutorial to accomplish this or it's not important? Ignored ti anyway.)

Connecting to a database
------------------------

We now need a database to connect to. (Why? To store the inventory information? Or something else?)

For this tutorial, we'll use PostgreSQL running in a docker container.
You're welcome to use any database that Doctrine ORM supports.

Run Postgres in a new terminal (Where do I run this? Initially, I did it in my root directory, but that went bad. Went back into open some hours. Also, got permission denied error, had to sudo. Maybe worth noting?):

.. code-block:: bash

   docker run -ti --rm --name pg -p 5432:5432 -e POSTGRES_PASSWORD=postgres postgres

Now update the doctrine configuration in ``config/packages/doctrine.yaml``:

.. code-block:: diff

      doctrine:
          dbal:
    -         # configure these for your database server
    -         driver: 'pdo_pgsql'
    -         server_version: '5.7'
    -         charset: utf8mb4
    -         default_table_options:
    -             charset: utf8mb4
    -             collate: utf8mb4_unicode_ci

              url: '%env(resolve:DATABASE_URL)%'

And update the database connection URL in ``.env``:
(This stopped me in my tracks for a minute. Was frantically looking for .env in the file. Twigged it's in the project folder eventually. May or may not be worth mentioning - up to you!)

.. code-block:: diff

    - DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name
    + DATABASE_URL=pgsql://postgres:postgres@127.0.0.1:5432/open-some-hours

Next, create the database if it doesn't exist: (How do I know if it exists? Since we've just made it, will it not always not exist yet anyway?)

.. code-block:: bash

   ./bin/console doctrine:database:create

.. note::

    If you're using PostgreSQL, make sure the ``uuid-ossp`` extension is installed:

    .. code-block:: bash

        ./bin/console doctrine:query:sql 'create extension "uuid-ossp"'

With an empty database created, we can now update the database schema to create the new products table:

.. code-block:: bash

   ./bin/console doctrine:schema:update --force --dump-sql

Enable the ``timestampable`` doctrine extension in ``config/packages/stof_doctrine_extensions.yaml``:

.. code-block:: yaml

      stof_doctrine_extensions:
          default_locale: en_US
    +     orm:
    +         default:
    +             timestampable: true

Create a crud class
-------------------

Add the following to ``src/Crud/ProductCrud.php``:
(Should I need to make this folder and file myself or should it already be here? I've taken a punt and just added it myself, but this needs to be clearer or there's a file missing...)

.. code-block:: php

    <?php

    namespace App\Crud;

    use Perform\BaseBundle\Crud\AbstractCrud;
    use Perform\BaseBundle\Config\FieldConfig;

    class ProductCrud extends AbstractCrud
    {
        public function configureFields(FieldConfig $config)
        {
            $config->add('name', [
                'type' => 'string',
            ])->add('quantity', [
                'type' => 'integer',
            ])->add('description', [
                'type' => 'text',
            ]);
        }
    }

This crud class manages the ``name``, ``quantity``, and ``description`` properties of ``Product``.

.. note::

   For an in-depth look at what crud classes can do, see the :doc:`crud documentation <../../base-bundle/crud/index>`.

Create routes
-------------

We'll use Perform's ``crud`` routing type to create some routes to manage products.
Add to ``config/routes.yaml``:

.. code-block:: yaml

    products:
        resource: product
        type: crud
        prefix: /products

Add a menu link
---------------

Add a new entry to ``perform_base:menu:simple`` in ``config/packages/perform_base.yaml``:
(It's the same thing again - this file doesn't exist. It definitely looks like it should exist this time rather than need me to create. Think something's amiss.)

.. code-block:: diff

      perform_base:
          menu:
    +         simple:
    +             products:
    +                 crud: product
    +                 icon: "shopping-basket"

And create a label for it in ``translations/PerformBaseBundle.en.yml``:
(Same again - doesn't exist. Also guessing this is meant to be 'yaml', not 'yml')

.. code-block:: yaml

   menu:
       products: 'Products'

Enabling actions
----------------

Add to ``routes.yaml``: (In config. ``config/routes.yaml``?)

.. code-block:: yaml

    actions:
        resource: '@PerformBaseBundle/Resources/config/routing/actions.yml'


Enable the crud security voter in ``config/packages/perform_base.yaml`` so basic actions like viewing, editing, and deleting are available:

.. code-block:: diff

      perform_base:
    +     security:
    +         crud_voter: true
          menu:
              simple:


To use the voter, we have to set the security strategy to ``unanimous`` in ``config/packages/security.yaml``:

.. code-block:: diff

      security:
          providers:
              in_memory: { memory: ~ }
    +     access_decision_manager:
    +         strategy: unanimous


The crud voter grants access to all entities that have a crud, for attributes like ``VIEW``, ``EDIT``, and ``DELETE``.
Without this voter, these access decisions will be denied unless you register a voter yourself.

.. note::

   Security is a deep topic that we only skim over in this tutorial.
   Don't worry if you don't understand everything that is going on here; our aim is to get up and running quickly.

Results
-------

Now head to http://localhost:8000/products to see an empty list of products.

You can view, edit, and delete existing products, as well as creating new products.
The table listing can be sorted by different columns, and widgets can be deleted in batch.

In only a few steps, we have successfully created a new product entity and generated routes to view, create, edit, and delete them.

This will be the foundation of our application; now let's customize it to fit the needs of the business.

(So, this has worked with me creating the files that I assumed I was meant to create, so that's probably what I was meant to do. This needs to be a lot clearer though. Especially with the perform_base.yaml one in the add a menu link section.)

(This is really cool though - I've got a menu. Tested it out, Few issues... Hit an error. Try to put in a massive quantiy - gives error. Fair enough, but once I fix it and shorten it, it then won't save for me and doesn;t display an error message.
Then getting a massive scary error page - try to add a product with the quantity 9876545678909876. Bad times!
Can't seem to do any actions or delete anything either. Bad times.)

(Also, your closing sentences here are what I was talking about at the top of the page with the whole 'human friendly justification; sort of thing, i.e. 'now we're going to customise it to suit the needs of the business'. Really like this bit.)