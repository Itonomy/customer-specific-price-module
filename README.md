# Customer Specific Pricing for Magento 2

Hey there lonely wanderer. A module for Magento 2 that may spark your interest. And its open-source. It will enable you to price your products for specific customer(s). This implementation is a working template, but certainly not final.

## Summary
This module adds a new price type: customer_price and injects it into the quote calculation which is required by Magento 2. 

The customer prices come from a table itonomy_customerprice which is not accessible (yet) through the admin panel. If you put a customer_id, product_id, website_id, and price in it, it will magically update the prices for that customer. Wow

## Installation

Just the regular stuff

```bash
composer require itonomy/module-customerspecificpricing
```

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
[MIT](https://choosealicense.com/licenses/mit/)
