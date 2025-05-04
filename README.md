# Alertify Blocks

A collection of beautiful alert/notice blocks for the WordPress block editor. Perfect for displaying important messages, warnings, success notifications, and more on your website.

![Alertify Blocks](assets/images/banner.png)

## Features

- **Multiple Alert Types**
  - Info - For general information messages
  - Success - For positive feedback and confirmations
  - Warning - For important notices that need attention
  - Error - For critical messages and errors

- **Dismissible Notices**
  - Customizable dismiss duration
  - Persistent across sessions using localStorage
  - Smooth fade-out animation

- **Rich Customization**
  - Color customization support
  - Typography controls
  - Spacing controls
  - Clean and modern design

## Requirements

- WordPress 5.0 or higher
- PHP 8.0 or higher
- Modern web browser with localStorage support

## Installation

### From WordPress.org
1. Go to Plugins > Add New in your WordPress admin
2. Search for "Alertify Blocks"
3. Click "Install Now" and then "Activate"

### Manual Installation
1. Download the plugin zip file
2. Go to Plugins > Add New > Upload Plugin
3. Upload the zip file and activate the plugin

### From GitHub
```bash
cd wp-content/plugins/
git clone https://github.com/hmbashar/alertify-blocks.git
cd alertify-blocks
npm install
npm run build