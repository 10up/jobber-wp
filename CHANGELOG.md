# Changelog

All notable changes to this project will be documented in this file, per [the Keep a Changelog standard](http://keepachangelog.com/), and will adhere to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased] - TBD

## [1.1.0] - 2026-10-22

### Added

- Choose any form from your connected Jobber account in the Jobber block, instead of only a Booking or Request form. Every enabled form is listed by name, with the account default labelled and preselected (props [@faisal-alvi](https://github.com/faisal-alvi), [@joemcgill](https://github.com/joemcgill), [@nicholasio](https://github.com/nicholasio), [@qasumitbagthariya](https://github.com/qasumitbagthariya) via [#72](https://github.com/10up/jobber-wp/pull/72)).
- `jobber_pre_query` filter to short circuit middleware requests, for local development and end to end tests (props [@faisal-alvi](https://github.com/faisal-alvi), [@joemcgill](https://github.com/joemcgill), [@nicholasio](https://github.com/nicholasio), [@qasumitbagthariya](https://github.com/qasumitbagthariya) via [#72](https://github.com/10up/jobber-wp/pull/72)).
- `jobber_form_height` filter to change the height used when a form renders in an iframe (props [@faisal-alvi](https://github.com/faisal-alvi), [@joemcgill](https://github.com/joemcgill), [@nicholasio](https://github.com/nicholasio), [@qasumitbagthariya](https://github.com/qasumitbagthariya) via [#72](https://github.com/10up/jobber-wp/pull/72)).

### Changed

- Blocks added before this update keep showing the form they were set to, with a note in the editor explaining that any form can now be chosen (props [@faisal-alvi](https://github.com/faisal-alvi), [@joemcgill](https://github.com/joemcgill), [@nicholasio](https://github.com/nicholasio), [@qasumitbagthariya](https://github.com/qasumitbagthariya) via [#72](https://github.com/10up/jobber-wp/pull/72)).
- If a block's form is turned off or deleted in Jobber, visitors see the account's default form instead (props [@faisal-alvi](https://github.com/faisal-alvi), [@joemcgill](https://github.com/joemcgill), [@nicholasio](https://github.com/nicholasio), [@qasumitbagthariya](https://github.com/qasumitbagthariya) via [#72](https://github.com/10up/jobber-wp/pull/72)).
- The editor preview sizes itself to the form, and booking forms render shorter than request forms (props [@faisal-alvi](https://github.com/faisal-alvi), [@joemcgill](https://github.com/joemcgill), [@nicholasio](https://github.com/nicholasio), [@qasumitbagthariya](https://github.com/qasumitbagthariya) via [#72](https://github.com/10up/jobber-wp/pull/72)).
- Bump WordPress "tested up to" version to 7.1 (props [@faisal-alvi](https://github.com/faisal-alvi), [@joemcgill](https://github.com/joemcgill) via [#73](https://github.com/10up/jobber-wp/pull/73)).
- Bump WordPress minimum supported version to 6.9 (props [@faisal-alvi](https://github.com/faisal-alvi), [@joemcgill](https://github.com/joemcgill), [@qasumitbagthariya](https://github.com/qasumitbagthariya) via [#74](https://github.com/10up/jobber-wp/pull/74)).

### Fixed

- Several Jobber forms on one page now each resize to their own height (props [@faisal-alvi](https://github.com/faisal-alvi), [@joemcgill](https://github.com/joemcgill), [@nicholasio](https://github.com/nicholasio), [@qasumitbagthariya](https://github.com/qasumitbagthariya) via [#72](https://github.com/10up/jobber-wp/pull/72)).
- Deactivating the plugin now removes all cached form data, including data left by earlier versions (props [@faisal-alvi](https://github.com/faisal-alvi), [@joemcgill](https://github.com/joemcgill), [@nicholasio](https://github.com/nicholasio), [@qasumitbagthariya](https://github.com/qasumitbagthariya) via [#72](https://github.com/10up/jobber-wp/pull/72)).

### Developer

- Add end to end tests with Cypress (props [@faisal-alvi](https://github.com/faisal-alvi), [@dkotter](https://github.com/dkotter) via [#58](https://github.com/10up/jobber-wp/pull/58)).
- Fix the E2E and Plugin Check workflows (props [@faisal-alvi](https://github.com/faisal-alvi), [@joemcgill](https://github.com/joemcgill) via [#73](https://github.com/10up/jobber-wp/pull/73)).

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
[1.1.0]: https://github.com/10up/jobber-wp/compare/1.0.0...1.1.0
[1.0.0]: https://github.com/10up/jobber-wp/releases/tag/1.0.0
