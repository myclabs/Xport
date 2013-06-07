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
# One sheet named "Contacts"
sheet:
  label: Contacts

  # Containing one table with 2 columns
  table:
    columns:
      name:
        label: Name
        path: name
      phoneNumber:
        label: Phone number
        path: phoneNumber
    lines:
      path: contacts
```

Usage:

```php
$export = new ExcelExporter($contactBook);
$export->render('myFile.xslx');
```

The table will be filled with each item in the array `$contacts = $contactBook->getContacts()`.

The `path` configuration is a [PropertyAccess](http://symfony.com/doc/master/components/property_access/index.html) path, e.g. the `phoneNumber` path can resolve to `$contact->getPhoneNumber()`, `$contact->phoneNumber` or `$contact['phoneNumber']`.

### Dynamic example

You can use the `forEach` item to generate dynamic content.

Here is an example:

```yaml
# Create one sheet per company
forEach(companies):
  sheet:
```

```php
$data = [
    'companies' => ['Foo', 'Bar']
];

$export = new ExcelExporter($data);
$export->render('myFile.xslx');
```

This will generate an Excel file containing 2 empty sheets.

Here is a more complete example:

```yaml
# Create one sheet per company
forEach(companies):

  sheet:

    # One table per product
    forEach(products):

      # The table will contain one sale entry per line
      table:
        columns:
          path:
            label: Product
            helper: fullProductName
          date:
            label: Date
            path: date
          salesman:
            label: Salesman
            path: salesman.name
          price:
            label: Price
            path: price
        lines:
          path: sales
```

Usage:

```php
$export = new ExcelExporter($data);

$export->addHelper('fullProductName', function(SaleEntry $saleEntry) {
    return strtoupper($saleEntry->getProduct()->getName());
});

$export->render('myFile.xslx');
```
