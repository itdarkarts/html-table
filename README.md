# html-table

This is a PHP language API library for building tables for rendering in HTML.

This library is intended for simplicity and velocity.
Instead of mixing PHP with inline HTML code (which is difficult to read),
or utilizing heavy frameworks (which are a time-sink to get acquainted with),
this API library enables developers to build HTML code for rendering tables with clearly
typed object-oriented PHP code.

This is also useful to update table rows and columns via AJAX.

Minimum required PHP: 8.0

## Example
A simple report table:
```php
<?php
require "table.php";
use ITDarkArts\HTML\Table\table;

$widgets_data = $database->get_widgets_data();

$table = new table(
    id: "widgets_data",
    class: "widgets_css widgets_js",
    caption: "Widgets"
);

$table->thead();
$table->row(["Widget", "Quality", "Stock", "Value"]);

$table->tbody();
foreach($widgets_data as $row) {
    $table->row($row);
}

print $table->html();
```
