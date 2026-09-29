<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Theme updates from GitHub Releases.
 *
 * WordPress checks the repo's latest release (every 12 hours, or via Dashboard → Updates)
 * and offers its solarkraftwerk-theme.zip asset as a normal theme update. Releases are
 * built by .github/workflows/release.yml when a vX.Y.Z tag is pushed.
 *
 * The repo is private, so each site needs a read-only GitHub token in wp-config.php:
 *   define( 'SK_GITHUB_TOKEN', 'github_pat_…' );
 */
require get_template_directory() . '/inc/vendor/plugin-update-checker/plugin-update-checker.php';

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$sk_update_checker = PucFactory::buildUpdateChecker(
	'https://github.com/nicolasmatter/solarkraftwerk-theme/',
	get_template_directory() . '/functions.php',
	get_template()
);

// Only ever install the built zip, never GitHub's raw source archive (which includes .github/ etc.).
// The constant is read off the API instance because another plugin may have loaded a different PUC version.
$sk_github_api = $sk_update_checker->getVcsApi();
$sk_github_api->enableReleaseAssets( '/^solarkraftwerk-theme\.zip$/', constant( get_class( $sk_github_api ) . '::REQUIRE_RELEASE_ASSETS' ) );

if ( defined( 'SK_GITHUB_TOKEN' ) && SK_GITHUB_TOKEN ) {
	$sk_update_checker->setAuthentication( SK_GITHUB_TOKEN );
}
