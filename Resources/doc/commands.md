Vardius - Crud Bundle
======================================

Commands
--------
1. [Generate command](#generate command)

### Generate command
Generate CRUD stubs based on the schema.xml (propel) or from your mapping information (orm)
```
php app/console vardius:crud:generate AppBundle Book Author
```

The `vardius:crud:generate` command allows you to quickly generate CRUD stubs for a given bundle.
Default database driver: ORM the `--propel` parameter changes database driver to Propel.

```
php app/console vardius:crud:generate AppBundle Book Author --propel
```
