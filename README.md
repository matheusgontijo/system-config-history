# System Config History for Shopware 6

<!---
@TODO: ADD BUILD STATUS HERE

https://github.com/nextcloud/server
https://github.com/shopwareLabs/psh

https://shields.io/
-->

>👉&nbsp;&nbsp;*Monitor, compare & revert system configs to previous versions, via admin, with just few clicks.*

*Would you please give a GitHub star ⭐ to this project? Thank you so much for your support!*

![php 7.4+](https://img.shields.io/badge/php-min%207.4-green.svg)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](https://github.com/matheusgontijo/system-config-history/blob/master/LICENSE)
[![Author](https://img.shields.io/badge/author-@mhgontijo-blue.svg)](https://twitter.com/mhgontijo)
[![CI Status](https://github.com/matheusgontijo/system-config-history/workflows/CI/badge.svg?branch=main&event=push)](https://phpunit.de/build-status.html) <!-- @TODO: update -->
[![Type Coverage](https://shepherd.dev/github/matheusgontijo/system-config-history/coverage.svg)](https://shepherd.dev/github/matheusgontijo/system-config-history)
[![Total Downloads](https://img.shields.io/packagist/dt/matheusgontijo/system-config-history.svg)](https://packagist.org/packages/matheusgontijo/system-config-history)
[![Latest Stable Version](https://img.shields.io/packagist/v/matheusgontijo/system-config-history)](https://packagist.org/packages/matheusgontijo/system-config-history)
[![Code Coverage](https://codecov.io/gh/matheusgontijo/system-config-history/branch/main/graph/badge.svg)](https://codecov.io/gh/matheusgontijo/system-config-history/branch/main) <!-- @TODO: update -->
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/matheusgontijo/system-config-history/badges/build.png?b=main)](https://scrutinizer-ci.com/g/matheusgontijo/system-config-history/build-status/main)

<!-- @TODO: ADD IMAGE LIKE THAT: https://github.com/DenverCoder1/readme-typing-svg
<a href="https://clipboardjs.com/"><img width="728" src="https://cloud.githubusercontent.com/assets/398893/16165747/a0f6fc46-349a-11e6-8c9b-c5fd58d9099c.png" alt="Demo"></a>
-->

## 💡 Why

System configurations can be accidentally changed or removed. There are even cases where dev/staging/production credentials are lost **forever** 😭

The idea of this simple plugin is to keep record of all system configuration: *additions, modifications and removals*. If something unexpected happens, we are safe, there is a backup 🙏

This plugin provides a quick and easy way to revert system configurations to previous versions in the history. The entire process is done from admin, with a few clicks 👍

In addition to that, it's a good idea to keep track about who (admin user) is changing system configs as well as what time it was changed. 💡

<br/>

## 🚀 Features

- Open-source & Free Software
- High-quality code (_PHPCS, PHP CS Fixer, PHPStan, Psalm, PHPUnit_)
- Cronjob to clean up legacy system configuration from time to time

<br/>

## 📥 Download the plugin

There are two methods to download the plugin:

### 1) First method, via composer (recommend)

Run the following commands on the root directory:

```
composer require matheusgontijo/system-config-history
```

### 2) Second method, manually

You can download the [plugin ZIP file here](https://www.github.com/matheusgontijo/system-config-history) and extract the files on `custom/plugins/` directory.

<br/>

### ⚡ Install

After files were downloaded, run the following commands:

```
php bin/console plugin:refresh
php bin/console plugin:install --activate MatheusGontijoSystemConfigHistory
```

<br/>

## ⚙ Requirements

| Requirement | Version |
|---- |----|
| PHP | +7.4 |
| Shopware | +6.4 |

<br/>

<!-- @TODO: UPDATE IT
## 🎉 Online Demo

You navigate on admin and see the plugin working by yourself.

Keep in mind that every 30 minutes the data is reset.

Link: https://system-config-history.matheusgontijo.com/

<br/>

<table>
    <tr>
        <td colspan="2"><strong>Admin credentials</strong></td>
    </tr>
    <tr>
        <td>Login</td>
        <td><code>admin</code></td>
    </tr>
    <tr>
        <td>Password</td>
        <td><code>Admin@123456</code></td>
    </tr>
</table>

<br/>
-->

<!-- @TODO: UPDATE IT
## 🎥 Video tutorial

[![Video tutorial](https://i.ibb.co/LP8sMKG/screenshot-20220824-203700.jpg)](https://i.ibb.co/LP8sMKG/screenshot-20220824-203700.jpg)

<br/>
-->

## 🙋 FAQ - Frequently Asked Questions

Please visit the [FAQ (Frequently Asked Questions)](https://www.matheusgontijo.com) page. There are +15 questions answered. <!-- @TODO: change qty here -->

<br/>

## 🔧 Support

Feel free to contribute by submiting a [Pull Request](https://github.com/matheusgontijo/system-config-history/pulls). <!-- @TODO: LINK TO GUIDELINES: Please follow the guidelines. -->

In case you find a bug, please reach out to matheus at matheusgontijo.com

<br/>

## 💡 Author

<table>
    <tr>
        <td rowspan="3"><a href="https://www.matheusgontijo.com"><img src="https://secure.gravatar.com/avatar/23a5d82888604edac73d84fbde4f7ffd?s=120" /></a></td>
        <td><strong>Matheus Gontijo</strong></td>
    </tr>
    <tr>
        <td><a href="https://www.matheusgontijo.com">matheusgontijo.com</a></td>
    </tr>
    <tr>
        <td><a href="https://twitter.com/mhgontijo">@mhgontijo</a></td>
    </tr>
</table>

<br/>

## 📄 License

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](https://github.com/matheusgontijo/system-config-history/blob/master/LICENSE)

[MIT License](https://github.com/matheusgontijo/system-config-history/blob/master/LICENSE) by [Matheus Gontijo](https://www.matheusgontijo.com)
