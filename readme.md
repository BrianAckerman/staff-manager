# Staff Members WordPress Plugin

**Version:** 1.0.0

This WordPress plugin provides a robust solution for managing and displaying staff members on your website. With features ranging from custom post types to integration with the Oxygen builder, it offers flexibility and scalability.

## Features

- Custom post type for staff members.
- Oxygen Builder integration.
- Vue.js functionality for dynamic content.
- Admin settings for quick contacts, global settings, and individual staff meta.
- REST API enhancements for the custom post type.

## Installation

1. Upload the `staff_members` directory to your `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Visit the 'Staff Members' section in your admin dashboard to add or edit staff entries.

*src folder contains VUE source files. Use only for development purposes.
  
## Usage

**Adding a Staff Member**:

1. Navigate to 'Staff Members' in the WordPress dashboard.
2. Click 'Add New' and fill in the necessary details.

**Using with Oxygen Builder**:

1. Create a template for the staff members custom post type.
2. Add a code block and in the PHP/HTML section, include the `single-oxy-staff_member.php` from the plugin templates directory.

<?php
render_staffh_oxygen_staff_template();
?>

## Folder Structure

- **css/**: Contains the plugin's stylesheets.
- **dist/**: Holds compiled Vue.js files.
- **img/**: Directory for image assets.
- **includes/**: PHP functionalities, divided into admin and other settings.
- **templates/**: Template files for the plugin, including Oxygen builder integration.

## Changelog

### 1.0.0

- Initial release.

## Contributions

If you'd like to contribute, please fork the repository and make changes as you'd like. Pull requests are warmly welcome.

## License

GPLv2 or later

## Support

For support and feature requests, please contact [brian@thebackerman.com](mailto:brian@thebackerman.com).

## Acknowledgements

Special thanks to the contributors and supporters of this project.
