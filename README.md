kornstar-magento-varnish
========================

This is a Magento module to manage the Varnsih HTTP frontend cache.
Currrently, this is a work in progress.

Know bugs:
- When updating a product, magento may crash since the kornstar_uri table has to many matchs, and PHP runs out of memory.
- Currently, there is not support for item stock status ie. Varnsih will not clear if a item goes out of stock


For more info please visit:
http://kornster.com (kornstar.com was already taken (sad-face))
