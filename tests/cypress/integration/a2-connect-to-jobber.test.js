describe( 'Can connect to Jobber via OAuth', () => {
    beforeEach( () => {
		cy.login();
	} );

    it( 'Connect to Jobber', () => {
        // Visit the settings page.
        cy.visit( '/wp-admin/options-general.php?page=jobber_settings' );

        // Visit the settings page with special param to update the Auth settings.
        cy.visit('/wp-admin/options-general.php?page=jobber_settings&e2e_set_jobber_auth=1');

        // Re-visit the settings page.
        cy.visit( '/wp-admin/options-general.php?page=jobber_settings' );
        cy.get( '.jobber-settings__connection' ).should( 'contain', 'Disconnect' );

        // Verify the connection status is updated
        cy.get('.jobber-settings__connection').should('contain', 'Disconnect');
    });
});
