# Xport

Xport is an import/export library for PHP.

It is targeted to support the following formats:

- Excel/OpenOffice
- PDF (to be implemented)
- XML (to be implemented)

It provides an object model for different formats (spreadsheet, document, XML…) and a language based on YAML to map your data (arrays, objects, …) onto the model.

## Spreadsheet

### Simple example

Simple mapping file (YAML file):

```yaml
sheets:
    # An empty sheet named "Home"
  - label: Home

    # Another sheet named "Contacts"
  - label: Contacts

    tables:
        # Containing one table with 2 columns
      - lines:
          foreach: contacts as contact
        columns:
          name:
            label: Name
            path: contact.name
          phoneNumber:
            label: Phone number
            path: contact.phoneNumber
```

Usage:

```php
$modelBuilder = new SpreadsheetModelBuilder();
$export = new SpreadsheetExporter();

$modelBuilder->bind('contacts', $contacts);

$export->export($modelBuilder->build('mapping.yml'), 'myFile.xslx');
```

The table will be filled with each item in the array `$contacts`.

The `path` configuration is a [PropertyAccess](http://symfony.com/doc/master/components/property_access/index.html) path, e.g. the `contact.phoneNumber` path can resolve to `$contact->getPhoneNumber()` or `$contact->phoneNumber`.

### Dynamic example

You can use the `foreach` expression to generate dynamic content.

You can also use Twig templating language.

Here is an example:

```yaml
# Create one sheet per company
sheets:
  - foreach: companies as i => company
    label: "{{ i + 1 }} - {{ company.name }}" # Twig expression, will result in (for example): "1 - Company Name"
```

```php
$modelBuilder = new SpreadsheetModelBuilder();
$export = new SpreadsheetExporter();

$modelBuilder->bind('companies', $companies);

$export->export($modelBuilder->build('mapping.yml'), 'myFile.xslx');
```

Here is a more complete example:

```yaml
sheets:
    # Create one sheet per company
  - foreach: companies as company
    label: "{{company.name}}"
    tables:

        # One table per product
      - foreach: company.products as product
        lines:
          foreach: product.sales as sale
        columns:
          - label: Product
            path: product.name
          - label: Price
            path: sale.price
          - label: Salesman
            path: sale.salesman.name
```

### File format

You can choose which file format to use through PHPExcel writers:

```php
// ...

$export->export($spreadsheet, 'myFile.xslx', new PHPExcel_Writer_Excel2007());
```

Writers available:

- Excel 2007 (.xlsx): `PHPExcel_Writer_Excel2007`
- Excel classic (.xls): `PHPExcel_Writer_Excel5`
- CSV (.csv): `PHPExcel_Writer_CSV`
