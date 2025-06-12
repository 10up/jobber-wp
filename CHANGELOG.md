# Changelog

All notable changes to this project will be documented in this file, per [the Keep a Changelog standard](http://keepachangelog.com/), and will adhere to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased] - TBD

## [1.0.0] - 2025-06-12

Initial release of the Jobber plugin. 🎉

### Added

- Setup initial plugin structure (props [@TylerB24890](https://github.com/TylerB24890), [@faisal-alvi](https://github.com/faisal-alvi), [@dkotter](https://github.com/dkotter) via [#3](https://github.com/10up/jobber-wp/pull/3), [#4](https://github.com/10up/jobber-wp/pull/4), [#21](https://github.com/10up/jobber-wp/pull/21)).
- Jobber block for easily embedding Jobber booking/request forms (props [@faisal-alvi](https://github.com/faisal-alvi), [@vikrampm1](https://github.com/vikrampm1), [@dkotter](https://github.com/dkotter) via [#7](https://github.com/10up/jobber-wp/pull/7)).
- Full disconnect flow (props [@TylerB24890](https://github.com/TylerB24890), [@faisal-alvi](https://github.com/faisal-alvi), [@dkotter](https://github.com/dkotter) via [#28](https://github.com/10up/jobber-wp/pull/28), [#38](https://github.com/10up/jobber-wp/pull/38)).
- Disconnect your Jobber account when the plugin is deactivated (props [@dkotter](https://github.com/dkotter), [@faisal-alvi](https://github.com/faisal-alvi) via [#51](https://github.com/10up/jobber-wp/pull/51)).
- Verify our initial nonce before setting the authenticated state (props [@dkotter](https://github.com/dkotter), [@faisal-alvi](https://github.com/faisal-alvi) via [#54](https://github.com/10up/jobber-wp/pull/54)).
- Plugin documentation (props [@jeffpaul](https://github.com/jeffpaul), [@dkotter](https://github.com/dkotter) via [#37](https://github.com/10up/jobber-wp/pull/37)).
- Link to the plugin settings page from the plugin action links (props [@dkotter](https://github.com/dkotter), [@faisal-alvi](https://github.com/faisal-alvi) via [#14](https://github.com/10up/jobber-wp/pull/14)).

### Changed

- Load Jobber form using embed code for responsive frontend display (props [@faisal-alvi](https://github.com/faisal-alvi), [@dkotter](https://github.com/dkotter) via [#25](https://github.com/10up/jobber-wp/pull/25)).
- Always get fresh API data when rendering the block in the editor (props [@dkotter](https://github.com/dkotter), [@faisal-alvi](https://github.com/faisal-alvi) via [#46](https://github.com/10up/jobber-wp/pull/46)).
- Improved error messaging in Jobber block with contextual help and settings link when iframe fails to load (props [@dkotter](https://github.com/dkotter), [@faisal-alvi](https://github.com/faisal-alvi) via [#17](https://github.com/10up/jobber-wp/pull/17)).
- Update settings page with some helpful next step guidance (props [@dkotter](https://github.com/dkotter), [@faisal-alvi](https://github.com/faisal-alvi) via [#15](https://github.com/10up/jobber-wp/pull/15), [#26](https://github.com/10up/jobber-wp/pull/26)).
- Update plugin copy (props [@dkotter](https://github.com/dkotter), [@ankitguptaindia](https://github.com/ankitguptaindia), [@vikrampm1](https://github.com/vikrampm1) via [#53](https://github.com/10up/jobber-wp/pull/53)).
- Update middleware URL (props [@dkotter](https://github.com/dkotter), [@faisal-alvi](https://github.com/faisal-alvi) via [#34](https://github.com/10up/jobber-wp/pull/34)).
- Instead of showing error messages, show nothing on front-end when we don't have a proper API response (props [@dkotter](https://github.com/dkotter), [@faisal-alvi](https://github.com/faisal-alvi) via [#16](https://github.com/10up/jobber-wp/pull/16)).

### Fixed

- Request form content not fully visible in the block editor (props [@faisal-alvi](https://github.com/faisal-alvi), [@ankitguptaindia](https://github.com/ankitguptaindia), [@dkotter](https://github.com/dkotter) via [#45](https://github.com/10up/jobber-wp/pull/45)).
- Better error handling when an API request fails (props [@dkotter](https://github.com/dkotter), [@faisal-alvi](https://github.com/faisal-alvi) via [#49](https://github.com/10up/jobber-wp/pull/49)).
- Added `ABSPATH` check to all executable PHP files to prevent direct access (props [@dkotter](https://github.com/dkotter), [@faisal-alvi](https://github.com/faisal-alvi) via [#48](https://github.com/10up/jobber-wp/pull/48)).

### Security

- Bump `esbuild` from 0.24.2 to 0.25.1 (props [@dependabot](https://github.com/apps/dependabot), [@TylerB24890](https://github.com/TylerB24890), [@jeffpaul](https://github.com/jeffpaul), [@dkotter](https://github.com/dkotter) via [#5](https://github.com/10up/jobber-wp/pull/5)).
- Bump `http-proxy-middleware` from 2.0.7 to 2.0.9 (props [@dependabot](https://github.com/apps/dependabot), [@dkotter](https://github.com/dkotter) via [#31](https://github.com/10up/jobber-wp/pull/31)).
- Bump `tar-fs` from 2.1.2 to 3.0.9 (props [@dependabot](https://github.com/apps/dependabot), [@dkotter](https://github.com/dkotter) via [#56](https://github.com/10up/jobber-wp/pull/56)).

### Developer

- Add in all of our GitHub Action workflows (props [@dkotter](https://github.com/dkotter), [@faisal-alvi](https://github.com/faisal-alvi) via [#13](https://github.com/10up/jobber-wp/pull/13)).
- Documentation for inserting and configuring the Jobber block in the WordPress block editor (props [@faisal-alvi](https://github.com/faisal-alvi), [@dkotter](https://github.com/dkotter) via [#29](https://github.com/10up/jobber-wp/pull/29)).
- Fix our PHPCS workflow (props [@dkotter](https://github.com/dkotter), [@faisal-alvi](https://github.com/faisal-alvi) via [#52](https://github.com/10up/jobber-wp/pull/52)).
- Update readmes with screenshots (props [@dkotter](https://github.com/dkotter), [@vikrampm1](https://github.com/vikrampm1) via [#57](https://github.com/10up/jobber-wp/pull/57)).

[Unreleased]: https://github.com/10up/jobber-wp/compare/trunk...develop
[1.0.0]: https://github.com/10up/jobber-wp/releases/tag/1.0.0
