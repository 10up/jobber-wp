#!/bin/bash

# Activate the plugin
wp plugin activate jobber

# Install and activate required plugins
wp plugin install classic-editor --activate

# Set up permalinks
wp rewrite structure '/%postname%/'
wp rewrite flush --hard

# Create test content
wp post create --post_type=page --post_title='Test Page' --post_status=publish

# Clear cache
wp cache flush 