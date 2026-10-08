// ***********************************************************
// This example support/index.js is processed and
// loaded automatically before your test files.
//
// This is a great place to put global configuration and
// behavior that modifies Cypress.
//
// You can change the location of this file or turn off
// automatically serving support files with the
// 'supportFile' configuration option.
//
// You can read more here:
// https://on.cypress.io/configuration
// ***********************************************************
import 'cypress-file-upload';
import '@10up/cypress-wp-utils';
import 'cypress-plugin-tab';

// Import commands.js using ES2015 syntax:
import './commands';

// WordPress 7.x admin uses View Transitions, and navigating away mid transition rejects with
// "Transition was aborted". That comes from core rather than the plugin, so it should not fail tests.
Cypress.on( 'uncaught:exception', ( err ) => {
	if ( err.message.includes( 'Transition was aborted' ) ) {
		return false;
	}
} );
